<?php

namespace App\Service\Api;

class ForumApiService
{
    private ModerationService $moderationService;
    private AiService $aiService;

    public function __construct(
        ModerationService $moderationService,
        AiService $aiService
    ) {
        $this->moderationService = $moderationService;
        $this->aiService = $aiService;
    }

    public function moderatePost(string $content): array
    {
        $result = $this->moderationService->checkToxicity($content);
        
        return [
            'success' => true,
            'is_approved' => !$result['is_toxic'],
            'is_toxic' => $result['is_toxic'],
            'scores' => $result['scores'],
            'recommendation' => $result['recommendation']
        ];
    }

    public function moderateComment(string $content): array
    {
        return $this->moderatePost($content);
    }

    public function autoReply(string $question, string $context = ''): array
    {
        $prompt = "You are a helpful travel assistant on a forum. ";
        if ($context) {
            $prompt .= "Context: $context\n";
        }
        $prompt .= "Question: $question\n\nProvide a helpful, concise answer.";

        $response = $this->aiService->generateResponse($prompt);
        
        return [
            'success' => true,
            'reply' => $response['response'] ?? '',
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }

    public function generatePostSummary(string $content): array
    {
        $response = $this->aiService->summarizeText($content, 50);
        
        return [
            'success' => true,
            'summary' => $response['response'] ?? $content
        ];
    }

    public function analyzeSentiment(string $text): array
    {
        return $this->aiService->analyzeSentiment($text);
    }

    public function getTrendingTopics(array $posts): array
    {
        $keywords = [];
        
        foreach ($posts as $post) {
            $words = preg_split('/\s+/', strtolower($post['title'] ?? ''));
            foreach ($words as $word) {
                if (strlen($word) > 3) {
                    $keywords[$word] = ($keywords[$word] ?? 0) + 1;
                }
            }
        }
        
        arsort($keywords);
        
        return [
            'success' => true,
            'trending' => array_slice($keywords, 0, 10, true)
        ];
    }

    public function generateSmartReply(string $originalPost, string $reply): array
    {
        $moderationResult = $this->moderationService->checkToxicity($reply);
        $sentimentResult = $this->aiService->analyzeSentiment($reply);
        
        return [
            'success' => true,
            'is_safe' => !$moderationResult['is_toxic'],
            'toxicity_score' => $moderationResult['scores']['TOXICITY'] ?? 0,
            'sentiment' => $sentimentResult['sentiment'] ?? 'neutral',
            'reply_length' => strlen($reply)
        ];
    }
}
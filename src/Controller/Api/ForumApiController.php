<?php

namespace App\Controller\Api;

use App\Entity\ForumPost;
use App\Entity\ForumComment;
use App\Service\Api\ModerationService;
use App\Service\Api\TranslationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/forum')]
class ForumApiController extends AbstractController
{
    public function __construct(
        private readonly ModerationService $moderationService,
        private readonly TranslationService $translationService,
    ) {}

    #[Route('/moderate', name: 'forum_api_moderate', methods: ['POST'])]
    public function moderate(Request $request): JsonResponse
    {
        $content = $request->request->get('content', '');
        
        if (empty($content)) {
            return new JsonResponse(['success' => false, 'message' => 'Content required'], 400);
        }

        $result = $this->moderationService->checkToxicity($content);

        return new JsonResponse([
            'success' => true,
            'is_approved' => !$result['is_toxic'],
            'is_toxic' => $result['is_toxic'],
            'scores' => $result['scores'],
            'recommendation' => $result['recommendation'],
        ]);
    }

    #[Route('/translate', name: 'forum_api_translate', methods: ['POST'])]
    public function translate(Request $request): JsonResponse
    {
        $text = $request->request->get('text', '');
        $targetLang = $request->request->get('target', 'en');
        $sourceLang = $request->request->get('source', 'auto');

        if (empty($text)) {
            return new JsonResponse(['success' => false, 'message' => 'Text required'], 400);
        }

        $result = $this->translationService->translate($text, $targetLang, $sourceLang);

        return new JsonResponse([
            'success' => true,
            'original' => $text,
            'translated' => $result['translatedText'] ?? $text,
            'detected_source' => $result['detectedSource'] ?? $sourceLang,
            'target' => $targetLang,
        ]);
    }

    #[Route('/detect-language', name: 'forum_api_detect', methods: ['POST'])]
    public function detectLanguage(Request $request): JsonResponse
    {
        $text = $request->request->get('text', '');

        error_log("ForumApiController: detectLanguage called with text='$text'");

        if (empty($text)) {
            return new JsonResponse(['success' => false, 'message' => 'Text required'], 400);
        }

        $result = $this->translationService->detectLanguage($text);

        error_log("ForumApiController: detectLanguage result=" . json_encode($result));

        return new JsonResponse([
            'success' => true,
            'language' => $result['language'] ?? 'fr',
            'confidence' => $result['confidence'] ?? 0.8,
        ]);
    }

    #[Route('/languages', name: 'forum_api_languages', methods: ['GET'])]
    public function getLanguages(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'languages' => [
                ['code' => 'fr', 'name' => 'Français', 'flag' => '🇫🇷'],
                ['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧'],
                ['code' => 'ar', 'name' => 'العربية', 'flag' => '🇸🇦'],
            ],
        ]);
    }

    #[Route('/batch-moderate', name: 'forum_api_batch_moderate', methods: ['POST'])]
    public function batchModerate(Request $request): JsonResponse
    {
        $contents = $request->request->all()['contents'] ?? [];

        if (empty($contents)) {
            return new JsonResponse(['success' => false, 'message' => 'Contents array required'], 400);
        }

        $results = [];
        foreach ($contents as $index => $content) {
            $result = $this->moderationService->checkToxicity($content);
            $results[$index] = [
                'is_approved' => !$result['is_toxic'],
                'is_toxic' => $result['is_toxic'],
                'scores' => $result['scores'],
            ];
        }

        return new JsonResponse([
            'success' => true,
            'results' => $results,
        ]);
    }

    #[Route('/post/{id}/translate', name: 'forum_api_post_translate', methods: ['POST'])]
    public function translatePost(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $post = $em->getRepository(ForumPost::class)->find($id);
        
        if (!$post) {
            return new JsonResponse(['success' => false, 'message' => 'Post not found'], 404);
        }

        $targetLang = $request->request->get('target', 'en');
        
        $titleResult = $this->translationService->translate($post->getTitle(), $targetLang);
        $contentResult = $this->translationService->translate($post->getContent(), $targetLang);

        return new JsonResponse([
            'success' => true,
            'translated' => [
                'title' => $titleResult['translated_text'] ?? $titleResult['translatedText'] ?? $post->getTitle(),
                'content' => $contentResult['translated_text'] ?? $contentResult['translatedText'] ?? $post->getContent(),
            ],
            'language' => $targetLang,
        ]);
    }

    #[Route('/comment/{id}/translate', name: 'forum_api_comment_translate', methods: ['POST'])]
    public function translateComment(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $comment = $em->getReference(ForumComment::class, $id);
        
        try {
            $content = $comment->getContent();
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Comment not found'], 404);
        }

        $targetLang = $request->request->get('target', 'en');
        $result = $this->translationService->translate($comment->getContent(), $targetLang);

        return new JsonResponse([
            'success' => true,
            'translated' => $result['translated_text'] ?? $result['translatedText'] ?? $comment->getContent(),
            'language' => $targetLang,
        ]);
    }
}
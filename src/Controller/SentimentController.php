<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use App\Service\SentimentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SentimentController extends AbstractController
{
    public function __construct(
        private SentimentService $sentimentService,
        private EntityManagerInterface $em
    ) {}

    #[Route('/api/sentiment/analyze', name: 'sentiment_analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['text'])) {
            return $this->json(['error' => 'Text is required'], 400);
        }

        $analysis = $this->sentimentService->analyze($data['text']);
        
        $keywords = $this->sentimentService->extractKeywords($data['text']);

        return $this->json([
            'success' => true,
            'analysis' => $analysis,
            'keywords' => $keywords
        ]);
    }

    #[Route('/api/sentiment/analyze-review/{id}', name: 'sentiment_analyze_review', methods: ['POST'])]
    public function analyzeReview(int $id): JsonResponse
    {
        $review = $this->em->find(Avis::class, $id);
        
        if (!$review) {
            return $this->json(['error' => 'Review not found'], 404);
        }

        if (empty($review->getCommentaire())) {
            return $this->json(['error' => 'Review has no comment'], 400);
        }

        $analysis = $this->sentimentService->analyze($review->getCommentaire());
        $keywords = $this->sentimentService->extractKeywords($review->getCommentaire());

        $review->setSentimentFromAnalysis($analysis);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'review_id' => $id,
            'analysis' => $analysis,
            'keywords' => $keywords
        ]);
    }

    #[Route('/api/sentiment/reviews', name: 'sentiment_reviews', methods: ['GET'])]
    public function getReviews(Request $request, AvisRepository $avisRepo): JsonResponse
    {
        $hebergementId = $request->query->get('hebergement_id');
        
        if ($hebergementId) {
            $reviews = $avisRepo->findByHebergement((int) $hebergementId, 100);
        } else {
            $reviews = $avisRepo->findUnanalyzedReviews();
        }

        $reviewsData = [];
        foreach ($reviews as $review) {
            $reviewsData[] = [
                'id' => $review->getId(),
                'note' => $review->getNote(),
                'commentaire' => $review->getCommentaire(),
                'auteur' => $review->getAuteur(),
                'createdAt' => $review->getCreatedAt()?->format('Y-m-d H:i:s'),
                'sentiment' => $review->getSentimentData()
            ];
        }

        return $this->json([
            'success' => true,
            'reviews' => $reviewsData,
            'total' => count($reviewsData)
        ]);
    }

    #[Route('/api/sentiment/hotel/{id}/score', name: 'sentiment_hotel_score', methods: ['GET'])]
    public function getHotelScore(int $id, AvisRepository $avisRepo): JsonResponse
    {
        $reviews = $avisRepo->findByHebergement($id, 200);
        
        if (empty($reviews)) {
            return $this->json([
                'success' => true,
                'hebergement_id' => $id,
                'average_score' => 0,
                'average_stars' => 0,
                'total_reviews' => 0,
                'recommendation' => 'N/A'
            ]);
        }

        $reviewsArray = [];
        foreach ($reviews as $review) {
            $reviewsArray[] = [
                'commentaire' => $review->getCommentaire(),
                'note' => $review->getNote()
            ];
        }

        $analysis = $this->sentimentService->analyzeMultiple($reviewsArray);

        $avgSentiment = $avisRepo->getAverageSentimentByHebergement($id);
        
        $distribution = $avisRepo->getSentimentDistributionByHebergement($id);

        return $this->json([
            'success' => true,
            'hebergement_id' => $id,
            'average_score' => $analysis['summary']['average_score'],
            'average_stars' => $analysis['summary']['average_stars'],
            'total_reviews' => $analysis['summary']['total_reviews'],
            'positive_count' => $analysis['summary']['positive_count'],
            'negative_count' => $analysis['summary']['negative_count'],
            'neutral_count' => $analysis['summary']['neutral_count'],
            'satisfaction_rate' => $analysis['summary']['satisfaction_rate'],
            'recommendation' => $analysis['summary']['recommendation'],
            'distribution' => $distribution,
            'source' => $this->sentimentService->isApiEnabled() ? 'huggingface' : ($this->sentimentService->isGoogleApiEnabled() ? 'google_api' : 'local')
        ]);
    }

    #[Route('/api/sentiment/analyze-all', name: 'sentiment_analyze_all', methods: ['POST'])]
    public function analyzeAllReviews(AvisRepository $avisRepo): JsonResponse
    {
        $unanalyzed = $avisRepo->findUnanalyzedReviews();
        
        $analyzed = 0;
        $failed = 0;
        
        foreach ($unanalyzed as $review) {
            try {
                if (!empty($review->getCommentaire())) {
                    $analysis = $this->sentimentService->analyze($review->getCommentaire());
                    $review->setSentimentFromAnalysis($analysis);
                    $analyzed++;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        }
        
        $this->em->flush();

        return $this->json([
            'success' => true,
            'analyzed' => $analyzed,
            'failed' => $failed,
            'remaining' => count($unanalyzed) - $analyzed
        ]);
    }

    #[Route('/api/sentiment/keywords', name: 'sentiment_keywords', methods: ['POST'])]
    public function extractKeywords(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['text'])) {
            return $this->json(['error' => 'Text is required'], 400);
        }

        $keywords = $this->sentimentService->extractKeywords($data['text'], $data['limit'] ?? 5);

        return $this->json([
            'success' => true,
            'keywords' => $keywords
        ]);
    }

    #[Route('/api/sentiment/batch', name: 'sentiment_batch', methods: ['POST'])]
    public function batchAnalyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['reviews'])) {
            return $this->json(['error' => 'Reviews array is required'], 400);
        }

        $results = $this->sentimentService->analyzeMultiple($data['reviews']);

        return $this->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
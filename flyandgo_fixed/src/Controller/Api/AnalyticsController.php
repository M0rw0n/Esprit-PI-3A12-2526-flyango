<?php

namespace App\Controller\Api;

use App\Service\AnalyticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/analytics')]
class AnalyticsController extends AbstractController
{
    public function __construct(
        private AnalyticsService $analyticsService
    ) {}

    #[Route('/summary', name: 'api_analytics_summary', methods: ['GET'])]
    public function getSummary(): JsonResponse
    {
        try {
            return new JsonResponse([
                'success' => true,
                'data' => $this->analyticsService->getDashboardSummary()
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/top-circuits', name: 'api_analytics_top_circuits', methods: ['GET'])]
    public function getTopCircuits(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        
        try {
            return new JsonResponse([
                'success' => true,
                'data' => $this->analyticsService->getTopSellingCircuits($limit)
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversion', name: 'api_analytics_conversion', methods: ['GET'])]
    public function getConversionRate(): JsonResponse
    {
        try {
            return new JsonResponse([
                'success' => true,
                'data' => $this->analyticsService->getConversionRate()
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/destinations', name: 'api_analytics_destinations', methods: ['GET'])]
    public function getTrendingDestinations(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 8);
        
        try {
            return new JsonResponse([
                'success' => true,
                'data' => $this->analyticsService->getTrendingDestinations($limit)
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/forecast', name: 'api_analytics_forecast', methods: ['GET'])]
    public function getForecast(Request $request): JsonResponse
    {
        $months = $request->query->getInt('months', 6);
        
        try {
            return new JsonResponse([
                'success' => true,
                'data' => $this->analyticsService->getRevenueForecasting($months)
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/trend', name: 'api_analytics_trend', methods: ['GET'])]
    public function getMonthlyTrend(): JsonResponse
    {
        try {
            return new JsonResponse([
                'success' => true,
                'data' => $this->analyticsService->getMonthlyTrend()
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/all', name: 'api_analytics_all', methods:['GET'])]
    public function getAll(): JsonResponse
    {
        try {
            return new JsonResponse([
                'success' => true,
                'summary' => $this->analyticsService->getDashboardSummary(),
                'topCircuits' => $this->analyticsService->getTopSellingCircuits(5),
                'conversion' => $this->analyticsService->getConversionRate(),
                'destinations' => $this->analyticsService->getTrendingDestinations(6),
                'forecast' => $this->analyticsService->getRevenueForecasting(6),
                'trend' => $this->analyticsService->getMonthlyTrend()
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
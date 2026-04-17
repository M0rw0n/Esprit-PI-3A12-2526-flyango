<?php

namespace App\Controller;

use App\Service\ChatbotService;
use App\Service\TravelPreparationAssistantService;
use App\Service\OpenRouterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class ChatbotController extends AbstractController
{
    private const MAX_HISTORY = 10;

    public function __construct(
        private ChatbotService $chatbotService,
        private ?TravelPreparationAssistantService $travelAdviceService = null,
        private ?OpenRouterService $openRouterService = null,
        private RequestStack $requestStack
    ) {}

    #[Route('/chatbot', name: 'chatbot_index')]
    public function index(): Response
    {
        return $this->render('chatbot/index.html.twig', [
            'welcomeMessage' => $this->chatbotService->getWelcomeMessage(),
            'quickSuggestions' => $this->chatbotService->getQuickSuggestions(),
            'aiInfo' => $this->chatbotService->prepareForFutureAI()
        ]);
    }

    #[Route('/chatbot/send', name: 'chatbot_send', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        $message = $request->request->get('message', '');

        if (empty(trim($message))) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez entrer un message.'
            ], 400);
        }

        $session = $this->requestStack->getSession();
        $history = $session->get('chatbot_history', []);

        $userContext = null;
        $user = $this->getUser();
        if ($user) {
            $userContext = [
                'name' => method_exists($user, 'getNom') ? $user->getNom() : $user->getUserIdentifier(),
                'role' => in_array('ROLE_ADMIN', $user->getRoles()) ? 'Administrateur' : 'Client',
                'id' => method_exists($user, 'getId') ? $user->getId() : null
            ];
        }

        // Check if we should use OpenRouter for comparison requests
        $lowerMsg = mb_strtolower($message);
        $useOpenRouter = $this->openRouterService && $this->openRouterService->isEnabled() && (
            stripos($lowerMsg, 'compare') !== false ||
            stripos($lowerMsg, 'meilleur') !== false ||
            stripos($lowerMsg, 'choix') !== false ||
            (stripos($lowerMsg, 'hotel') !== false && stripos($lowerMsg, ' vs ') !== false)
        );
        
        error_log("Chatbot send - useOpenRouter: " . ($useOpenRouter ? 'yes' : 'no') . " - message: " . substr($message, 0, 50));

        if ($useOpenRouter) {
            $openRouterResult = $this->openRouterService->chat($message);
            if ($openRouterResult['success']) {
                $history[] = ['content' => $message, 'isUser' => true, 'timestamp' => time()];
                $history[] = ['content' => $openRouterResult['response'], 'isUser' => false, 'timestamp' => time()];
                $session->set('chatbot_history', array_slice($history, -20));

                return $this->json([
                    'success' => true,
                    'response' => $openRouterResult['response'],
                    'action' => 'ai_compare',
                    'filters' => [],
                    'duration_ms' => 0,
                    'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
                ]);
            }
        }

        $start = microtime(true);
        $result = $this->chatbotService->processMessage($message, $history, $userContext);
        $duration = round((microtime(true) - $start) * 1000, 2);

        $history[] = ['content' => $message, 'isUser' => true, 'timestamp' => time()];
        $history[] = ['content' => $result['response'], 'isUser' => false, 'timestamp' => time()];
        
        if (count($history) > self::MAX_HISTORY * 2) {
            $history = array_slice($history, -self::MAX_HISTORY * 2);
        }
        $session->set('chatbot_history', $history);

        return $this->json([
            'success' => true,
            'response' => $result['response'],
            'action' => $result['action'],
            'filters' => $result['filters'],
            'duration_ms' => $duration,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    #[Route('/chatbot/clear', name: 'chatbot_clear', methods: ['POST'])]
    public function clearHistory(): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->remove('chatbot_history');
        
        return $this->json([
            'success' => true,
            'message' => 'Historique effacé'
        ]);
    }

    #[Route('/chatbot/quick-reply', name: 'chatbot_quick_reply', methods: ['POST'])]
    public function quickReply(Request $request): JsonResponse
    {
        $query = $request->request->get('query', '');

        if (empty(trim($query))) {
            return $this->json([
                'success' => false,
                'message' => 'Requête invalide.'
            ], 400);
        }

        $userContext = null;
        $user = $this->getUser();
        if ($user) {
            $userContext = [
                'name' => method_exists($user, 'getNom') ? $user->getNom() : $user->getUserIdentifier(),
                'role' => in_array('ROLE_ADMIN', $user->getRoles()) ? 'Administrateur' : 'Client',
                'id' => method_exists($user, 'getId') ? $user->getId() : null
            ];
        }

        $result = $this->chatbotService->processMessage($query, [], $userContext);

        return $this->json([
            'success' => true,
            'response' => $result['response'],
            'action' => $result['action'],
            'filters' => $result['filters'],
            'query' => $query,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    #[Route('/chatbot/feedback', name: 'chatbot_feedback', methods: ['POST'])]
    public function feedback(Request $request, \App\Repository\FAQRepository $faqRepo, \Doctrine\ORM\EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $faqId = $data['faq_id'] ?? null;
        $type = $data['type'] ?? ''; // 'up' or 'down'

        if (!$faqId) return $this->json(['success' => false], 400);

        $faq = $faqRepo->find($faqId);
        if (!$faq) return $this->json(['success' => false], 404);

        if ($type === 'up') $faq->setFeedbackUp($faq->getFeedbackUp() + 1);
        else if ($type === 'down') $faq->setFeedbackDown($faq->getFeedbackDown() + 1);

        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/chatbot/hotel-advice', name: 'chatbot_hotel_advice', methods: ['POST'])]
    public function hotelAdvice(Request $request): JsonResponse
    {
        $hotelName = $request->request->get('hotel', '');
        $ville = $request->request->get('ville', '');
        $pays = $request->request->get('pays', 'Tunisie');
        $type = $request->request->get('type', 'hôtel');

        if (empty(trim($hotelName))) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez fournir le nom de l\'hébergement.'
            ], 400);
        }

        if (!$this->travelAdviceService) {
            return $this->json([
                'success' => false,
                'message' => 'Service non configuré.'
            ], 503);
        }

        $isEnabled = $this->travelAdviceService->isEnabled();
        
        if (!$isEnabled) {
            $fallback = $this->travelAdviceService->generateFallbackAdvice($hotelName, $ville ?: null, $pays);
            return $this->json([
                'success' => true,
                'hotel' => $fallback['hotel'],
                'city' => $fallback['city'],
                'season' => $fallback['season'],
                'advice' => $fallback['advice'],
                'fallback' => true,
                'message' => "Conseils pour {$hotelName}"
            ]);
        }

        $result = $this->travelAdviceService->generateAdviceFromName($hotelName, $ville ?: null, $pays, $type);

        if (!$result['success']) {
            $fallback = $this->travelAdviceService->generateFallbackAdvice($hotelName, $ville ?: null, $pays);
            return $this->json([
                'success' => true,
                'hotel' => $fallback['hotel'],
                'city' => $fallback['city'],
                'season' => $fallback['season'],
                'advice' => $fallback['advice'],
                'fallback' => true,
                'message' => "Conseils pour {$hotelName}"
            ]);
        }

        return $this->json([
            'success' => true,
            'hotel' => $result['hotel'],
            'city' => $result['city'],
            'season' => $result['season'],
            'advice' => $result['advice'],
            'message' => "Conseils pour votre séjour à {$hotelName}"
        ]);
    }
    
    #[Route('/chatbot/test-api', name: 'chatbot_test_api')]
    public function testApi(): JsonResponse
    {
        $service = $this->travelAdviceService;
        $openRouter = $this->openRouterService;
        
        $testResult = null;
        if ($openRouter && $openRouter->isEnabled()) {
            $testResult = $openRouter->chat('Dis "ok" en un mot');
        }
        
        return $this->json([
            'travel_service_exists' => $service !== null,
            'travel_is_enabled' => $service ? $service->isEnabled() : false,
            'openrouter_exists' => $openRouter !== null,
            'openrouter_enabled' => $openRouter ? $openRouter->isEnabled() : false,
            'test_result' => $testResult
        ]);
    }
}
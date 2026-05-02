<?php

namespace App\Controller;

use App\Service\ChatbotService;
use App\Service\TravelPreparationAssistantService;
use App\Service\OpenRouterService;
use App\Repository\HebergementRepository;
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
        private ?HebergementRepository $hebergementRepository = null,
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
        $isHebergementQuery = stripos($lowerMsg, 'hebergement') !== false || 
                               stripos($lowerMsg, 'hotel') !== false || 
                               stripos($lowerMsg, 'logement') !== false ||
                               stripos($lowerMsg, 'villa') !== false ||
                               stripos($lowerMsg, 'maison hote') !== false;
        
        $isPrixQuery = stripos($lowerMsg, 'prix') !== false || 
                       stripos($lowerMsg, 'tarif') !== false || 
                       stripos($lowerMsg, 'cout') !== false ||
                       stripos($lowerMsg, 'cher') !== false;

        $useOpenRouter = $this->openRouterService && $this->openRouterService->isEnabled() && (
            stripos($lowerMsg, 'compare') !== false ||
            stripos($lowerMsg, 'meilleur') !== false ||
            stripos($lowerMsg, 'choix') !== false ||
            stripos($lowerMsg, 'recommande') !== false ||
            stripos($lowerMsg, 'propose') !== false ||
            $isHebergementQuery ||
            $isPrixQuery
        );

        // For hebergement queries, first get real data from database
        $hebergementsData = [];
        if ($isHebergementQuery && $this->hebergementRepository) {
            $ville = $this->extractVille($message);
            $type = $this->extractType($message);
            $prixMax = $this->extractPrixMax($message);
            
            $hebergements = $this->hebergementRepository->search(null, $ville, $type, null, $prixMax, 'price_asc');
            
            $hebergementsData = array_map(function($h) {
                return [
                    'nom' => $h->getNom(),
                    'ville' => $h->getVille(),
                    'type' => $h->getType(),
                    'prix' => $h->getPrixParNuit(),
                    'description' => mb_substr($h->getDescription() ?? '', 0, 150),
                    'disponible' => $h->isDisponible(),
                    'note' => $h->getNote(),
                    'adresse' => $h->getAdresse()
                ];
            }, $hebergements);
            
            if (count($hebergementsData) > 5) {
                $hebergementsData = array_slice($hebergementsData, 0, 5);
            }
        }

        $openRouterType = 'general';
        if ($isHebergementQuery) {
            $openRouterType = 'hebergement';
        } elseif ($isPrixQuery) {
            $openRouterType = 'prix';
        }
        
        error_log("Chatbot send - useOpenRouter: " . ($useOpenRouter ? 'yes' : 'no') . " - type: $openRouterType - hebergements: " . count($hebergementsData));

        if ($useOpenRouter) {
            // Build prompt with real data if available
            $promptWithData = $message;
            if (!empty($hebergementsData)) {
                $hebergementsList = "Voici les hébergements disponibles:\n\n";
                foreach ($hebergementsData as $i => $heb) {
                    $hebergementsList .= ($i + 1) . ". **" . $heb['nom'] . "** (" . $heb['type'] . ")\n";
                    $hebergementsList .= "   📍 " . $heb['ville'] . " | 💰 " . $heb['prix'] . " TND/nuit";
                    if ($heb['etoiles']) $hebergementsList .= " | ⭐ " . $heb['etoiles'] . " étoiles";
                    $hebergementsList .= "\n";
                    if ($heb['description']) $hebergementsList .= "   📝 " . $heb['description'] . "\n";
                    $hebergementsList .= "\n";
                }
                $promptWithData = $hebergementsList . "\n\nMa question: " . $message;
            }
            
            $openRouterResult = $this->openRouterService->chat($promptWithData, $openRouterType);
            if ($openRouterResult['success']) {
                $history[] = ['content' => $message, 'isUser' => true, 'timestamp' => time()];
                $history[] = ['content' => $openRouterResult['response'], 'isUser' => false, 'timestamp' => time()];
                $session->set('chatbot_history', array_slice($history, -20));

                return $this->json([
                    'success' => true,
                    'response' => $openRouterResult['response'],
                    'action' => 'ai_' . $openRouterType,
                    'hebergements' => $hebergementsData,
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

    private function extractVille(string $message): ?string
    {
        $villes = [
            'djerba' => 'Djerba', 'jerba' => 'Djerba', 'houmt souk' => 'Djerba',
            'hammamet' => 'Hammamet', 'tunis' => 'Tunis', 'sousse' => 'Sousse',
            'monastir' => 'Monastir', 'marrakech' => 'Marrakech', 'fes' => 'Fès',
            'paris' => 'Paris', 'nice' => 'Nice', 'lyon' => 'Lyon'
        ];
        $lower = mb_strtolower($message);
        foreach ($villes as $key => $ville) {
            if (mb_strpos($lower, $key) !== false) {
                return $ville;
            }
        }
        return null;
    }

    private function extractType(string $message): ?string
    {
        $types = ['hotel' => 'Hôtel', 'villa' => 'Villa', 'riad' => 'Riad', 'maison' => 'Maison d\'hôte'];
        $lower = mb_strtolower($message);
        foreach ($types as $key => $type) {
            if (mb_strpos($lower, $key) !== false) {
                return $type;
            }
        }
        return null;
    }

    private function extractPrixMax(string $message): ?float
    {
        if (preg_match('/(\d+)\s*(?:tnd|€|euros?|dinars?)/i', $message, $matches)) {
            return (float) $matches[1];
        }
        if (preg_match('/jusqu?\s*a\s*(\d+)/i', $message, $matches)) {
            return (float) $matches[1];
        }
        if (preg_match('/pas\s*cher|pas\s*cher|low\s*cost|budget/i', $message)) {
            return 100;
        }
        return null;
    }

    #[Route('/chatbot/compare-hebergements', name: 'chatbot_compare_hebergements', methods: ['POST'])]
    public function compareHebergements(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $hotels = $data['hotels'] ?? [];

        if (count($hotels) < 2) {
            return $this->json([
                'success' => false,
                'message' => 'Sélectionnez au moins 2 hébergements'
            ], 400);
        }

        if (!$this->openRouterService || !$this->openRouterService->isEnabled()) {
            // Fallback: return cheapest
            $best = array_reduce($hotels, fn($a, $b) => $a['prix'] < $b['prix'] ? $a : $b);
            return $this->json([
                'success' => true,
                'response' => "🏆 **Mon choix: " . $best['nom'] . "**\n\n💰 Prix: " . $best['prix'] . " TND/nuit\n📍 " . ($best['ville'] ?? '') . "\n\nC'est le meilleur rapport qualité-prix!",
                'best' => $best
            ]);
        }

        // Build detailed prompt
        $prompt = "Tu es un expert en hébergements de voyage. Compare ces hébergements et recommande LE MEILLEUR choix en expliquant pourquoi.\n\n";
        foreach ($hotels as $i => $h) {
            $prompt .= ($i + 1) . ". **" . $h['nom'] . "**\n";
            $prompt .= "   📍 " . ($h['ville'] ?? '') . " | 💰 " . $h['prix'] . " TND/nuit\n";
            if (!empty($h['type'])) $prompt .= "   🏨 Type: " . $h['type'] . "\n";
            if (!empty($h['note'])) $prompt .= "   ⭐ Note: " . $h['note'] . "/5\n";
            if (!empty($h['description'])) $prompt .= "   📝 " . substr($h['description'], 0, 100) . "\n";
            $prompt .= "\n";
        }
        $prompt .= "\nRéponds en français avec:\n🏆 **MON COUP DE CŒUR** (le meilleur choix)\n📋 Comparaison rapide\n💡 Pourquoi ce choix?";

        $result = $this->openRouterService->chat($prompt, 'compare');

        if ($result['success']) {
            return $this->json([
                'success' => true,
                'response' => $result['response']
            ]);
        }

        // Fallback on error
        $best = array_reduce($hotels, fn($a, $b) => $a['prix'] < $b['prix'] ? $a : $b);
        return $this->json([
            'success' => true,
            'response' => "🏆 **Mon choix: " . $best['nom'] . "**\n\n💰 Prix: " . $best['prix'] . " TND/nuit\n📍 " . ($best['ville'] ?? '') . "\n\nC'est le meilleur rapport qualité-prix!",
            'best' => $best
        ]);
    }
}
<?php

namespace App\Controller;

use App\Service\ChatbotService;
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
        
        $result = $this->chatbotService->processMessage($message, $history);

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

        $result = $this->chatbotService->processMessage($query);

        return $this->json([
            'success' => true,
            'response' => $result['response'],
            'action' => $result['action'],
            'filters' => $result['filters'],
            'query' => $query,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }
}
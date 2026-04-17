<?php

namespace App\Controller;

use App\Service\AITravelPuzzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AITravelPuzzleController extends AbstractController
{
    public function __construct(
        private AITravelPuzzleService $puzzleService
    ) {}

    #[Route('/ai-puzzle', name: 'ai_puzzle_index')]
    public function index(): Response
    {
        return $this->render('ai-puzzle/index.html.twig');
    }

    #[Route('/ai-puzzle/play/{difficulty}', name: 'ai_puzzle_play')]
    public function play(Request $request, string $difficulty = 'easy'): Response
    {
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            $difficulty = 'easy';
        }

        $destId = $request->query->get('destination');
        if ($destId) {
            $destination = $this->puzzleService->getDestinationById(strtolower($destId));
            if (!$destination) {
                $destination = $this->puzzleService->getRandomDestination();
            }
        } else {
            $destination = $this->puzzleService->getRandomDestination();
        }

        $gridSize = $this->puzzleService->getGridSize($difficulty);
        $timeLimit = $this->puzzleService->getTimeLimit($difficulty);

        return $this->render('ai-puzzle/game.html.twig', [
            'destination' => $destination,
            'difficulty' => $difficulty,
            'gridSize' => $gridSize,
            'timeLimit' => $timeLimit
        ]);
    }

    #[Route('/ai-puzzle/daily', name: 'ai_puzzle_daily')]
    public function daily(): Response
    {
        $destination = $this->puzzleService->getDailyChallenge();
        $gridSize = $this->puzzleService->getGridSize($destination['difficulty'] ?? 'medium');
        $timeLimit = $this->puzzleService->getTimeLimit($destination['difficulty'] ?? 'medium');

        return $this->render('ai-puzzle/game.html.twig', [
            'destination' => $destination,
            'difficulty' => $destination['difficulty'] ?? 'medium',
            'gridSize' => $gridSize,
            'timeLimit' => $timeLimit,
            'isDaily' => true
        ]);
    }

    #[Route('/ai-puzzle/world-tour', name: 'ai_puzzle_world_tour')]
    public function worldTour(): Response
    {
        $sequence = $this->puzzleService->getWorldTourSequence();
        
        return $this->render('ai-puzzle/world-tour.html.twig', [
            'destinations' => $sequence
        ]);
    }

    #[Route('/ai-puzzle/leaderboard', name: 'ai_puzzle_leaderboard')]
    public function leaderboard(): Response
    {
        return $this->render('ai-puzzle/leaderboard.html.twig');
    }

    #[Route('/ai-puzzle/api/destination', name: 'ai_puzzle_api_destination')]
    public function apiDestination(Request $request): Response
    {
        $id = $request->query->get('id');
        
        if ($id) {
            $destination = $this->puzzleService->getDestinationById($id);
        } else {
            $destination = $this->puzzleService->getRandomDestination();
        }
        
        return $this->json($destination);
    }
}
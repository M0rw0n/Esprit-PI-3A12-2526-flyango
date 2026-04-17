<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private array $quizzes = [
        [
            'question' => 'Quel type d\'hébergement traditionnel tunisien est construit en pisrine?',
            'options' => ['Hôtel', 'Riad', 'Appartement', 'Villa'],
            'answer' => 1,
            'points' => 10
        ],
        [
            'question' => 'Quelle est la célèbre île tunisienne connue pour ses plages?',
            'options' => ['Djerba', 'Kerkennah', 'Galite', 'Zarzis'],
            'answer' => 0,
            'points' => 10
        ],
        [
            'question' => 'Le petit-déjeuner tunisien typique inclut généralement:',
            'options' => ['Cacap', 'Loukou', 'Brika', 'Slither'],
            'answer' => 1,
            'points' => 10
        ],
        [
            'question' => 'Quelle chaîne hôtelière est populaire en Tunisie?',
            'options' => ['Mövenpick', 'El Mouradi', 'Tunisia Palace', 'Toutes'],
            'answer' => 3,
            'points' => 10
        ],
        [
            'question' => 'Que signifie "All Inclusive" dans un hôtel tunisien?',
            'options' => ['Logement seulement', 'Tout inclus', 'Petit-déjeuner seulement', 'Demi-pension'],
            'answer' => 1,
            'points' => 10
        ],
        [
            'question' => 'La capitale de la Tunisie est:',
            'options' => ['Tunis', 'Sousse', 'Djerba', 'Sfax'],
            'answer' => 0,
            'points' => 10
        ],
        [
            'question' => 'Quelle ville tunisienne est connue pour ses ruines romaines?',
            'options' => ['Carthage', 'El Jem', 'Kairouan', 'Tozeur'],
            'answer' => 1,
            'points' => 10
        ],
        [
            'question' => 'Le Plat typique tunisien le plus connu:',
            'options' => ['Couscous', 'Pizza', 'Sushi', 'Burger'],
            'answer' => 0,
            'points' => 10
        ]
    ];

    private array $dailyChallenges = [
        'search_3_hebergements' => [
            'title' => 'Explorer',
            'description' => 'Voir 3 hébergements différents',
            'points' => 50,
            'icon' => 'fa-compass'
        ],
        'make_reservation' => 'Réserver',
        'add_favorite' => 'Favori',
        'write_review' => 'Avis',
        'share' => 'Partager',
        'daily_login' => 'Connexion'
    ];

    #[Route('/jeux', name: 'games_index')]
    public function index(): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route('/jeux/quiz', name: 'games_quiz')]
    public function quiz(): Response
    {
        $questions = array_slice($this->quizzes, 0, 5);
        shuffle($questions);
        
        return $this->render('game/quiz.html.twig', [
            'questions' => $questions
        ]);
    }

    #[Route('/jeux/spin', name: 'games_spin')]
    public function spin(): Response
    {
        $prizes = [
            '-10%' => 10,
            '-5%' => 25,
            'Free night' => 5,
            '50 TND' => 3,
            'Upgrade' => 10,
            'Try again' => 20,
            '-15%' => 8,
            'Mystery' => 9
        ];
        
        return $this->render('game/spin.html.twig', [
            'prizes' => $prizes
        ]);
    }

    #[Route('/jeux/memory', name: 'games_memory')]
    public function memory(): Response
    {
        $images = [
            'hotel', 'beach', 'desert', 'medina', 
            'cactus', 'mosque', 'pool', 'food'
        ];
        
        return $this->render('game/memory.html.twig', [
            'images' => $images
        ]);
    }

    #[Route('/jeux/price', name: 'games_price')]
    public function price(): Response
    {
        return $this->render('game/price.html.twig');
    }

    #[Route('/api/quiz/check', name: 'api_quiz_check')]
    public function checkQuiz(): JsonResponse
    {
        $data = [
            'success' => true,
            'score' => rand(2, 5),
            'points' => rand(20, 50),
            'message' => 'Bravo! Vous avez bien répondu!'
        ];
        
        return $this->json($data);
    }

    #[Route('/api/spin/wheel', name: 'api_spin_wheel')]
    public function spinWheel(): JsonResponse
    {
        $prizes = [
            ['name' => '-10%', 'weight' => 10],
            ['name' => '-5%', 'weight' => 25],
            ['name' => 'Free night', 'weight' => 5],
            ['name' => '50 TND', 'weight' => 3],
            ['name' => 'Upgrade', 'weight' => 10],
            ['name' => 'Try again', 'weight' => 20],
            ['name' => '-15%', 'weight' => 8],
            ['name' => 'Mystery', 'weight' => 9]
        ];
        
        $totalWeight = array_sum(array_column($prizes, 'weight'));
        $random = rand(1, $totalWeight);
        
        $current = 0;
        foreach ($prizes as $prize) {
            $current += $prize['weight'];
            if ($random <= $current) {
                return $this->json([
                    'success' => true,
                    'prize' => $prize['name'],
                    'points' => $prize['name'] === 'Try again' ? 0 : 25
                ]);
            }
        }
        
        return $this->json(['success' => true, 'prize' => 'Try again', 'points' => 0]);
    }

    #[Route('/api/quiz/submit', name: 'api_quiz_submit', methods: ['POST'])]
    public function submitQuiz(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $answers = $data['answers'] ?? [];
        
        $score = 0;
        $total = count($this->quizzes);
        
        foreach ($this->quizzes as $index => $quiz) {
            if (isset($answers[$index]) && $answers[$index] === $quiz['answer']) {
                $score++;
            }
        }
        
        $points = $score * 10;
        
        return $this->json([
            'success' => true,
            'score' => $score,
            'total' => $total,
            'points' => $points,
            'message' => $score >= 4 ? 'Excellent! 🎉' : 'Bien joué!继续练习!'
        ]);
    }
}
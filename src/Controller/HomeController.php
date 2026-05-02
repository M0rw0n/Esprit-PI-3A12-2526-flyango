<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\CircuitRepository;
use App\Repository\ForumPostRepository;
use App\Repository\HebergementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        HebergementRepository $hebRepo,
        CircuitRepository $circRepo,
        ActivityRepository $actRepo,
        ForumPostRepository $forumRepo,
    ): Response {
        $hebergements = $hebRepo->findBy(['disponible' => true], ['createdAt' => 'DESC'], 6);
        $circuits = $circRepo->findBy(['actif' => true], ['createdAt' => 'DESC'], 4);
        $topCircuits = $circRepo->getTopCircuitsByFavorites(4);
        $activities = $actRepo->findBy(['actif' => true], ['createdAt' => 'DESC'], 6);
        $topActivities = $actRepo->getTopActivitiesByFavorites(6);
        $posts = $forumRepo->findBy(['status' => 'APPROVED'], ['createdAt' => 'DESC'], 4);
        $villesData = $hebRepo->getDistinctVilles();
        $villes = array_values(array_filter(array_column($villesData, 'ville')));

        return $this->render('home/index.html.twig', [
            'hebergements' => $hebergements,
            'circuits' => $circuits,
            'topCircuits' => $topCircuits,
            'activities' => $activities,
            'topActivities' => $topActivities,
            'posts' => $posts,
            'villes' => $villes,
            'totalHebergements' => $hebRepo->count(['disponible' => true]),
            'totalCircuits' => $circRepo->count(['actif' => true]),
            'totalActivities' => $actRepo->count(['actif' => true]),
            'totalPosts' => $forumRepo->count(['status' => 'APPROVED']),
        ]);
    }
}

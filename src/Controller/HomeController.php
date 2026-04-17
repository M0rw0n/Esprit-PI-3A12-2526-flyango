<?php

<<<<<<< HEAD
declare(strict_types=1);

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\CircuitRepository;
use App\Repository\ForumPostRepository;
use App\Repository\HebergementRepository;
=======
namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\ForumPostRepository;
use App\Repository\PlaceRepository;
use App\Repository\ReviewRepository;
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
<<<<<<< HEAD
        HebergementRepository $hebRepo,
        CircuitRepository $circRepo,
        ActivityRepository $actRepo,
        ForumPostRepository $forumRepo,
    ): Response {
        $hebergements = $hebRepo->findBy(['disponible' => true], ['createdAt' => 'DESC'], 6);
        $circuits = $circRepo->findBy(['actif' => true], ['createdAt' => 'DESC'], 4);
        $activities = $actRepo->findBy(['actif' => true], ['createdAt' => 'DESC'], 6);
        $posts = $forumRepo->findBy(['status' => 'APPROVED'], ['createdAt' => 'DESC'], 4);
        $villesData = $hebRepo->getDistinctVilles();
        $villes = array_values(array_filter(array_column($villesData, 'ville')));

        return $this->render('home/index.html.twig', [
            'hebergements' => $hebergements,
            'circuits' => $circuits,
            'activities' => $activities,
            'posts' => $posts,
            'villes' => $villes,
            'totalHebergements' => $hebRepo->count(['disponible' => true]),
            'totalCircuits' => $circRepo->count(['actif' => true]),
            'totalActivities' => $actRepo->count(['actif' => true]),
            'totalPosts' => $forumRepo->count(['status' => 'APPROVED']),
=======
        ActivityRepository  $activityRepo,
        ForumPostRepository $forumRepo,
        PlaceRepository     $placeRepo,
        ReviewRepository    $reviewRepo
    ): Response {
        // Activities: top 6 for homepage
        $activities = $activityRepo->findBy([], ['id' => 'DESC'], 6);

        // Recent approved forum posts
        $recentPosts = $forumRepo->findBy(
            ['status' => 'APPROVED'],
            ['createdAt' => 'DESC'],
            4
        );

        // All places/destinations
        $places = $placeRepo->findAll();

        // Recent reviews
        $reviews = $reviewRepo->findBy([], ['createdAt' => 'DESC'], 6);

        return $this->render('home/index.html.twig', [
            'activities'  => $activities,
            'recentPosts' => $recentPosts,
            'places'      => $places,
            'reviews'     => $reviews,
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
        ]);
    }
}

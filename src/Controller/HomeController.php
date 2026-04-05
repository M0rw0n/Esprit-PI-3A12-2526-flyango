<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\ForumPostRepository;
use App\Repository\PlaceRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
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
        ]);
    }
}

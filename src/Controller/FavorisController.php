<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FavoriteActivityRepository;
use App\Repository\FavoriteCircuitRepository;
use App\Repository\FavoriteHebergementRepository;
use App\Repository\FavoritePostRepository;
use App\Repository\FavoriteTransportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    #[Route('/favoris', name: 'favoris_all')]
    public function index(
        FavoriteHebergementRepository $favHebRepo,
        FavoriteCircuitRepository $favCircRepo,
        FavoriteActivityRepository $favActRepo,
        FavoriteTransportRepository $favTransRepo,
        FavoritePostRepository $favPostRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('favoris/index.html.twig', [
            'hebergements' => $favHebRepo->findByUser($user),
            'circuits' => $favCircRepo->findByUser($user),
            'activities' => $favActRepo->findByUser($user),
            'transports' => $favTransRepo->findByUser($user),
            'posts' => $favPostRepo->findByUser($user),
        ]);
    }

    #[Route('/favoris/hebergements', name: 'favoris_hebergements')]
    public function hebergements(FavoriteHebergementRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('favoris/hebergements.html.twig', [
            'favorites' => $repo->findByUser($user),
        ]);
    }

    #[Route('/favoris/circuits', name: 'favoris_circuits')]
    public function circuits(FavoriteCircuitRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('favoris/circuits.html.twig', [
            'favorites' => $repo->findByUser($user),
        ]);
    }

    #[Route('/favoris/activites', name: 'favoris_activities')]
    public function activities(FavoriteActivityRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('favoris/activities.html.twig', [
            'favorites' => $repo->findByUser($user),
        ]);
    }

    #[Route('/favoris/transport', name: 'favoris_transports')]
    public function transports(FavoriteTransportRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('favoris/transports.html.twig', [
            'favorites' => $repo->findByUser($user),
        ]);
    }

    #[Route('/favoris/posts', name: 'favoris_posts')]
    public function posts(FavoritePostRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('favoris/posts.html.twig', [
            'favorites' => $repo->findByUser($user),
        ]);
    }
}

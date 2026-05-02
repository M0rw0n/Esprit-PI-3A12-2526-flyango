<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FavoriteActivityRepository;
use App\Repository\FavoriteCircuitRepository;
use App\Repository\FavoriteHebergementRepository;
use App\Repository\FavoritePostRepository;
use App\Repository\FavoriteTransportRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    #[Route('/favoris', name: 'favoris_all')]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        FavoriteHebergementRepository $favHebRepo,
        FavoriteCircuitRepository $favCircRepo,
        FavoriteActivityRepository $favActRepo,
        FavoriteTransportRepository $favTransRepo,
        FavoritePostRepository $favPostRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $hebQb = $favHebRepo->createQueryBuilder('f')
            ->leftJoin('f.hebergement', 'h')
            ->where('f.user = :user')
            ->andWhere('h.id IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('f.id', 'DESC');
        
        $circQb = $favCircRepo->createQueryBuilder('f')
            ->leftJoin('f.circuit', 'c')
            ->where('f.user = :user')
            ->andWhere('c.id IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('f.id', 'DESC');
        
        $actQb = $favActRepo->createQueryBuilder('f')
            ->leftJoin('f.activity', 'a')
            ->where('f.user = :user')
            ->andWhere('a.id IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('f.id', 'DESC');
        
        $transQb = $favTransRepo->createQueryBuilder('f')
            ->leftJoin('f.transport', 't')
            ->where('f.user = :user')
            ->andWhere('t.id IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('f.id', 'DESC');
        
        $postQb = $favPostRepo->createQueryBuilder('f')
            ->leftJoin('f.post', 'p')
            ->where('f.user = :user')
            ->andWhere('p.id IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('f.id', 'DESC');

        return $this->render('favoris/index.html.twig', [
            'hebergements' => $paginator->paginate($hebQb->getQuery(), $request->query->getInt('page', 1), 6, ['sort' => '']),
            'circuits' => $paginator->paginate($circQb->getQuery(), $request->query->getInt('page', 1), 6, ['sort' => '']),
            'activities' => $paginator->paginate($actQb->getQuery(), $request->query->getInt('page', 1), 6, ['sort' => '']),
            'transports' => $paginator->paginate($transQb->getQuery(), $request->query->getInt('page', 1), 6, ['sort' => '']),
            'posts' => $paginator->paginate($postQb->getQuery(), $request->query->getInt('page', 1), 6, ['sort' => '']),
        ]);
    }

    #[Route('/favoris/hebergements', name: 'favoris_hebergements')]
    public function hebergements(Request $request, PaginatorInterface $paginator, FavoriteHebergementRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $qb = $repo->createQueryBuilder('f')->where('f.user = :user')->setParameter('user', $user)->orderBy('f.id', 'DESC');
        $favorites = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, ['sort' => '']);

        return $this->render('favoris/hebergements.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/favoris/circuits', name: 'favoris_circuits')]
    public function circuits(Request $request, PaginatorInterface $paginator, FavoriteCircuitRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $qb = $repo->createQueryBuilder('f')->where('f.user = :user')->setParameter('user', $user)->orderBy('f.id', 'DESC');
        $favorites = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 6, ['sort' => '']);

        return $this->render('favoris/circuits.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/favoris/activites', name: 'favoris_activities')]
    public function activities(Request $request, PaginatorInterface $paginator, FavoriteActivityRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $qb = $repo->createQueryBuilder('f')->where('f.user = :user')->setParameter('user', $user)->orderBy('f.id', 'DESC');
        $favorites = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, ['sort' => '']);

        return $this->render('favoris/activities.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/favoris/transport', name: 'favoris_transports')]
    public function transports(Request $request, PaginatorInterface $paginator, FavoriteTransportRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $qb = $repo->createQueryBuilder('f')->where('f.user = :user')->setParameter('user', $user)->orderBy('f.id', 'DESC');
        $favorites = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, ['sort' => '']);

        return $this->render('favoris/transports.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/favoris/posts', name: 'favoris_posts')]
    public function posts(Request $request, PaginatorInterface $paginator, FavoritePostRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $qb = $repo->createQueryBuilder('f')->where('f.user = :user')->setParameter('user', $user)->orderBy('f.id', 'DESC');
        $favorites = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, ['sort' => '']);

        return $this->render('favoris/posts.html.twig', [
            'favorites' => $favorites,
        ]);
    }
}

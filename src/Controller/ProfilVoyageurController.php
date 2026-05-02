<?php

namespace App\Controller;

use App\Entity\ProfilVoyageur;
use App\Entity\User;
use App\Form\ProfilVoyageurType;
use App\Repository\ProfilVoyageurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilVoyageurController extends AbstractController
{
    /* ═══════════════════════════════════════════════════════════
     * ADMIN ROUTES
     * ═══════════════════════════════════════════════════════════ */
    
    #[Route('/admin/profils', name: 'admin_profils')]
    public function index(
        Request $request,
        ProfilVoyageurRepository $repo,
        PaginatorInterface $paginator
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $q = $request->query->get('q', '');
        $type = $request->query->get('type', '');
        $budgetMin = $request->query->get('budget_min', '');
        $budgetMax = $request->query->get('budget_max', '');
        $tri = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u');

        if ($q) {
            $qb->andWhere('LOWER(p.destinationPreferee) LIKE LOWER(:q) OR LOWER(u.nom) LIKE LOWER(:q) OR LOWER(u.prenom) LIKE LOWER(:q)')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($type) {
            $qb->andWhere('p.typeVoyage = :type')->setParameter('type', $type);
        }

        if ($budgetMin !== '') {
            $qb->andWhere('p.budget >= :min')->setParameter('min', (float) $budgetMin);
        }

        if ($budgetMax !== '') {
            $qb->andWhere('p.budget <= :max')->setParameter('max', (float) $budgetMax);
        }

        $sortField = match($tri) {
            'budget_asc' => ['p.budget', 'ASC'],
            'budget_desc' => ['p.budget', 'DESC'],
            'destination' => ['p.destinationPreferee', 'ASC'],
            'type' => ['p.typeVoyage', 'ASC'],
            default => ['p.id', 'DESC'],
        };
        $qb->orderBy($sortField[0], $sortField[1]);

        $profils = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10, ['sort' => '']);

        return $this->render('admin/profil_voyageur/index.html.twig', [
            'profils' => $profils,
            'types' => ProfilVoyageur::TYPES,
            'typeLabels' => ProfilVoyageur::TYPE_LABELS,
            'filters' => [
                'q' => $q,
                'type' => $type,
                'budget_min' => $budgetMin,
                'budget_max' => $budgetMax,
                'tri' => $tri,
            ],
        ]);
    }

    #[Route('/admin/profils/stats', name: 'admin_profils_stats', methods: ['GET'])]
    public function stats(ProfilVoyageurRepository $repo): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->json($repo->getStatistics());
    }

    #[Route('/admin/profils/{id}', name: 'admin_profils_show', methods: ['GET'])]
    public function show(ProfilVoyageur $profil): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/profil_voyageur/show.html.twig', [
            'profil' => $profil,
            'typeLabels' => ProfilVoyageur::TYPE_LABELS,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     * USER ROUTES
     * ═══════════════════════════════════════════════════════════ */

    #[Route('/mon-espace/profil-voyageur', name: 'user_profil_voyageur')]
    public function userProfile(Request $request, ProfilVoyageurRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $profil = $repo->findByUser($user->getId());

        if (!$profil) {
            $profil = new ProfilVoyageur();
            $profil->setUser($user);
        }

        $form = $this->createForm(ProfilVoyageurType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($profil);
            $em->flush();
            $this->addFlash('success', 'Profil voyageur enregistré avec succès !');
            return $this->redirectToRoute('user_profil_voyageur');
        }

        return $this->render('user/profil_voyageur/edit.html.twig', [
            'form' => $form->createView(),
            'profil' => $profil,
            'typeLabels' => ProfilVoyageur::TYPE_LABELS,
        ]);
    }

    #[Route('/mon-espace/profil-voyageur/voir', name: 'user_profil_voyageur_view')]
    public function viewProfile(ProfilVoyageurRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();
        $profil = $repo->findByUser($user->getId());

        if (!$profil) {
            $this->addFlash('warning', 'Vous n\'avez pas encore de profil voyageur. Créez-le !');
            return $this->redirectToRoute('user_profil_voyageur');
        }

        return $this->render('user/profil_voyageur/show.html.twig', [
            'profil' => $profil,
            'typeLabels' => ProfilVoyageur::TYPE_LABELS,
        ]);
    }
}

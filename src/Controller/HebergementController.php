<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\FavoriteHebergementRepository;
use App\Repository\HebergementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hebergements')]
class HebergementController extends AbstractController
{
    #[Route('', name: 'hebergement_index', methods: ['GET'])]
    public function index(Request $request, HebergementRepository $repo): Response
    {
        $q = $request->query->get('q');
        $ville = $request->query->get('ville');
        $type = $request->query->get('type');
        $prixMin = $request->query->get('prix_min') ? (float) $request->query->get('prix_min') : null;
        $prixMax = $request->query->get('prix_max') ? (float) $request->query->get('prix_max') : null;
        $sort = $request->query->get('sort', 'recent');

        $hebergements = $repo->search($q, $ville, $type, $prixMin, $prixMax, $sort);
        $villes = array_column($repo->getDistinctVilles(), 'ville');

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['count' => count($hebergements)]);
        }

        return $this->render('hebergement/index.html.twig', [
            'hebergements' => $hebergements,
            'villes' => $villes,
            'q' => $q,
            'ville' => $ville,
            'type' => $type,
            'prixMin' => $prixMin,
            'prixMax' => $prixMax,
            'sort' => $sort,
        ]);
    }

    #[Route('/top', name: 'hebergement_top', methods: ['GET'])]
    public function top(FavoriteHebergementRepository $favRepo): Response
    {
        $top = $favRepo->getTopHebergements(10);
        return $this->render('hebergement/top.html.twig', ['topHebergements' => $top]);
    }

    #[Route('/{id}', name: 'hebergement_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(int $id, HebergementRepository $repo, FavoriteHebergementRepository $favRepo): Response
    {
        $h = $repo->find($id);
        if (!$h) {
            throw $this->createNotFoundException();
        }

        $isFavorited = false;
        if ($this->getUser()) {
            $isFavorited = $favRepo->isFavorited($this->getUser(), $h);
        }

        return $this->render('hebergement/show.html.twig', [
            'hebergement' => $h,
            'isFavorited' => $isFavorited,
        ]);
    }

    #[Route('/{id}/reserver', name: 'hebergement_reserver', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function reserver(int $id, Request $request, HebergementRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $h = $repo->find($id);
        if (!$h) {
            throw $this->createNotFoundException();
        }

        $dateDebutRaw = (string) $request->request->get('date_debut');
        $dateFinRaw = (string) $request->request->get('date_fin');

        try {
            $dateDebut = new \DateTimeImmutable($dateDebutRaw);
            $dateFin = new \DateTimeImmutable($dateFinRaw);
        } catch (\Throwable) {
            $this->addFlash('error', 'Dates invalides.');
            return $this->redirectToRoute('hebergement_show', ['id' => $id]);
        }

        if ($dateFin <= $dateDebut) {
            $this->addFlash('error', 'La date de départ doit être après la date d\'arrivée.');
            return $this->redirectToRoute('hebergement_show', ['id' => $id]);
        }

        /** @var User $user */
        $user = $this->getUser();
        $nuits = max(1, $dateDebut->diff($dateFin)->days);
        $montant = $nuits * $h->getPrixParNuit();

        $res = new Reservation();
        $res->setHebergement($h)
            ->setUser($user)
            ->setDateDebut($dateDebut)
            ->setDateFin($dateFin)
            ->setMontantTotal($montant)
            ->setNomClient($user->getFullName())
            ->setEmailClient($user->getEmail())
            ->setTelephoneClient($user->getTelephone())
            ->setNombrePersonnes((int) $request->request->get('nb_personnes', 1))
            ->setStatut('EN_ATTENTE');

        $em->persist($res);
        $em->flush();

        $this->addFlash('success', 'Réservation enregistrée dans votre espace personnel.');
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/{id}/avis', name: 'hebergement_avis', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function addAvis(int $id, Request $request, HebergementRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $h = $repo->find($id);
        if (!$h) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $avis = $em->getRepository(Avis::class)->findOneBy(['hebergement' => $h, 'user' => $user]) ?? new Avis();

        $avis->setHebergement($h)
            ->setUser($user)
            ->setAuteur($user->getFullName())
            ->setNote((int) $request->request->get('note', 5))
            ->setCommentaire((string) $request->request->get('commentaire', ''));

        if ($avis->getId() === null) {
            $em->persist($avis);
        }
        $em->flush();

        $this->addFlash('success', 'Votre avis a bien été enregistré.');
        return $this->redirectToRoute('user_avis');
    }
}

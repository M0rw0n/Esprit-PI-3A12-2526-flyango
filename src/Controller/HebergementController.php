<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Hebergement;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\HebergementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hebergement')]
class HebergementController extends AbstractController
{
    #[Route('', name: 'hebergement_index', methods: ['GET'])]
    public function index(Request $request, HebergementRepository $repo): Response
    {
        $q = $request->query->get('q');
        $lieu = $request->query->get('lieu');
        $prixMax = $request->query->get('prix_max') ? (float) $request->query->get('prix_max') : null;
        $ville = $request->query->get('ville');
        $type = $request->query->get('type');
        $tri = $request->query->get('tri');

        $qb = $repo->createQueryBuilder('h');

        if ($q) {
            $qb->andWhere('h.nom LIKE :q OR h.description LIKE :q')->setParameter('q', '%' . $q . '%');
        }
        if ($lieu) {
            $qb->andWhere('h.lieu = :lieu')->setParameter('lieu', $lieu);
        }
        if ($prixMax) {
            $qb->andWhere('h.prix <= :prixMax')->setParameter('prixMax', $prixMax);
        }
        if ($ville) {
            $qb->andWhere('h.ville = :ville')->setParameter('ville', $ville);
        }
        if ($type) {
            $qb->andWhere('h.type = :type')->setParameter('type', $type);
        }

        if ($tri === 'price_asc') {
            $qb->orderBy('h.prixParNuit', 'ASC');
        } elseif ($tri === 'price_desc') {
            $qb->orderBy('h.prixParNuit', 'DESC');
        } else {
            $qb->orderBy('h.id', 'DESC');
        }

        $hebergements = $qb->setMaxResults(50)->getQuery()->getResult();

        $villesData = $repo->createQueryBuilder('h')
            ->select('DISTINCT h.ville')
            ->setMaxResults(200)
            ->getQuery()
            ->getResult();
        $villes = array_values(array_filter(array_column($villesData, 'ville')));

        return $this->render('hebergement/index.html.twig', [
            'hebergements' => $hebergements,
            'q' => $q,
            'lieu' => $lieu,
            'ville' => $ville,
            'type' => $type,
            'tri' => $tri,
            'prixMax' => $request->query->get('prix_max'),
            'villes' => $villes,
        ]);
    }

    #[Route('/{id}', name: 'hebergement_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, HebergementRepository $repo, AvisRepository $avisRepo): Response
    {
        $hebergement = $repo->find($id);
        if (!$hebergement) throw $this->createNotFoundException();

        $avis = $avisRepo->findByHebergement($hebergement->getId(), 50);

        return $this->render('hebergement/show.html.twig', [
            'hebergement' => $hebergement,
            'avis' => $avis,
        ]);
    }

    #[Route('/{id}/reserver', name: 'hebergement_book', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function book(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $hebergement = $em->getReference(Hebergement::class, $id);
        if (!$hebergement) throw $this->createNotFoundException();

        /** @var User $user */
        $user = $this->getUser();
        $dateDebut = $request->request->get('date_debut');
        $dateFin = $request->request->get('date_fin');
        $personnes = (int) $request->request->get('personnes', 1);

        $reservation = new Reservation();
        $reservation->setHebergement($hebergement)
                   ->setUser($user)
                   ->setNomClient($user->getFullName())
                   ->setEmailClient($user->getEmail())
                   ->setTelephoneClient($user->getTelephone())
                   ->setDateDebut(new \DateTime($dateDebut))
                   ->setDateFin(new \DateTime($dateFin))
                   ->setNombrePersonnes($personnes)
                   ->setStatut('CONFIRMED');

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Réservation confirmée avec succès!');
        return $this->redirectToRoute('hebergement_show', ['id' => $id]);
    }

    #[Route('/{id}/avis', name: 'hebergement_review', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addReview(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $hebergement = $em->getReference(Hebergement::class, $id);
        if (!$hebergement) throw $this->createNotFoundException();

        /** @var User $user */
        $user = $this->getUser();

        $avis = new Avis();
        $avis->setHebergement($hebergement)
             ->setUser($user)
             ->setNote((int) $request->request->get('rating', 5))
             ->setCommentaire((string) $request->request->get('comment', ''));

        $em->persist($avis);
        $em->flush();

        $this->addFlash('success', 'Votre avis a été enregistré.');
        return $this->redirectToRoute('hebergement_show', ['id' => $id]);
    }
}

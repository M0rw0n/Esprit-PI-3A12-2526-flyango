<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Hebergement;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\HebergementRepository;
<<<<<<< HEAD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
=======
use App\Repository\FavoriteHebergementRepository;
use App\Repository\ReservationRepository;
use App\Service\SentimentService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
>>>>>>> testsisi
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

<<<<<<< HEAD
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
=======
class HebergementController extends AbstractController
{
    public function __construct(
        private ?SentimentService $sentimentService = null
    ) {}

    #[Route('/hebergements', name: 'hebergement_index')]
    public function index(Request $request, HebergementRepository $repo, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);
        $tri = $request->query->get('tri', 'nom');
        $search = $request->query->get('q', '');
        $ville = $request->query->get('ville', '');
        $type = $request->query->get('type', '');
        $prixMin = $request->query->get('prix_min', '');
        $prixMax = $request->query->get('prix_max', '');
        
        $qb = $repo->createQueryBuilder('h');
        
        if ($search) {
            $qb->andWhere('h.nom LIKE :s OR h.description LIKE :s OR h.adresse LIKE :s')
               ->setParameter('s', "%{$search}%");
        }
        
        if ($ville) {
            $qb->andWhere('h.ville = :ville')->setParameter('ville', $ville);
        }
        
        if ($type) {
            $qb->andWhere('h.type = :type')->setParameter('type', $type);
        }
        
        if ($prixMin) {
            $qb->andWhere('h.prixParNuit >= :prixMin')->setParameter('prixMin', (float)$prixMin);
        }
        
        if ($prixMax) {
            $qb->andWhere('h.prixParNuit <= :prixMax')->setParameter('prixMax', (float)$prixMax);
        }
        
        $heb = $paginator->paginate($qb->getQuery(), $page, 12);
        
        $villes = $repo->createQueryBuilder('h')->select('DISTINCT h.ville')->getQuery()->getResult();
        $villes = array_column($villes, 'ville');
        
        return $this->render('hebergement/index.html.twig', [
            'hebergements' => $heb,
            'tri' => $tri,
            'search' => $search,
            'q' => $search,
            'ville' => $ville,
            'type' => $type,
            'villes' => $villes,
            'prixMin' => $prixMin,
            'prixMax' => $prixMax
        ]);
    }

    #[Route('/hebergement/top', name: 'hebergement_top')]
    public function top(HebergementRepository $repo): Response
    {
        $heb = $repo->findBy(['disponible' => true], ['prixParNuit' => 'DESC'], 12);
        return $this->render('hebergement/top.html.twig', ['hebergements' => $heb]);
    }

    #[Route('/hebergement/{id}', name: 'hebergement_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, HebergementRepository $repo, FavoriteHebergementRepository $favRepo): Response
    {
        $h = $repo->find($id);
        if (!$h) throw $this->createNotFoundException();

        $isFavorited = false;
        if ($this->getUser()) {
            $isFavorited = $favRepo->isFavorited($this->getUser(), $h);
        }

        return $this->render('hebergement/show.html.twig', [
            'hebergement' => $h,
            'isFavorited' => $isFavorited,
        ]);
    }

    #[Route('/hebergement/{id}/avis', name: 'hebergement_avis', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addAvis(int $id, Request $request, HebergementRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $h = $repo->find($id);
        if (!$h) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $existingAvis = $em->getRepository(Avis::class)->findOneBy(['hebergement' => $h, 'user' => $user]);
        if ($existingAvis) {
            $this->addFlash('error', 'Vous avez déjà laissé un avis pour cet hébergement.');
            return $this->redirectToRoute('hebergement_show', ['id' => $id]);
        }

        $commentaire = (string) $request->request->get('commentaire', '');
        
        $avis = new Avis();
        $avis->setHebergement($h)
            ->setUser($user)
            ->setAuteur($user->getFullName())
            ->setNote((int) $request->request->get('note', 5))
            ->setCommentaire($commentaire);

        if ($this->sentimentService && !empty($commentaire)) {
            $analysis = $this->sentimentService->analyze($commentaire);
            $avis->setSentimentFromAnalysis($analysis);
        }
>>>>>>> testsisi

        $em->persist($avis);
        $em->flush();

<<<<<<< HEAD
        $this->addFlash('success', 'Votre avis a été enregistré.');
        return $this->redirectToRoute('hebergement_show', ['id' => $id]);
=======
        $this->addFlash('success', 'Votre avis a bien été enregistré.');
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/hebergement/{id}/reserver', name: 'hebergement_reserver', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function reserver(int $id, Request $request, HebergementRepository $repo, EntityManagerInterface $em): Response
    {
        $h = $repo->find($id);
        if (!$h || !$h->isDisponible()) {
            throw $this->createNotFoundException();
        }

        $nomClient = $request->request->get('nom_client');
        $emailClient = $request->request->get('email_client');
        $telephone = $request->request->get('telephone', '');
        $dateDebut = $request->request->get('date_debut');
        $dateFin = $request->request->get('date_fin');
        $nbPersonnes = (int) $request->request->get('nb_personnes', 1);
        $total = (float) $request->request->get('montant_total', 0);

        if (!$nomClient || !$emailClient || !$dateDebut || !$dateFin) {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            return $this->redirectToRoute('hebergement_show', ['id' => $id]);
        }

        $reservation = new Reservation();
        $reservation->setHebergement($h);
        
        if ($this->getUser()) {
            $reservation->setUser($this->getUser());
        }
        
        $reservation->setNomClient($nomClient);
        $reservation->setEmailClient($emailClient);
        $reservation->setTelephone($telephone);
        $reservation->setDateDebut(new \DateTime($dateDebut));
        $reservation->setDateFin(new \DateTime($dateFin));
        $reservation->setNombrePersonnes($nbPersonnes);
        $reservation->setMontantTotal($total);
        $reservation->setStatut('en_attente');
        $reservation->setCreatedAt(new \DateTime());

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a été enregistrée! Nous vous contacterons bientôt.');
        return $this->redirectToRoute('user_reservations');
>>>>>>> testsisi
    }
}

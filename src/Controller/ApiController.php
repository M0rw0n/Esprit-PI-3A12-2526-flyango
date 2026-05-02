<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Circuit;
use App\Entity\Hebergement;
use App\Entity\TransportOffer;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\CircuitRepository;
use App\Repository\HebergementRepository;
use App\Repository\TransportOfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {}

    private function getTokenUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();
        return $user instanceof \App\Entity\User ? $user : null;
    }

    #[Route('/user/me', name: 'api_user_me', methods: ['GET'])]
    public function getMe(): JsonResponse
    {
        $user = $this->getTokenUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getPrenom() . ' ' . $user->getNom(),
            'email' => $user->getEmail(),
            'avatar' => $user->getAvatar(),
        ]);
    }

    #[Route('/user/online', name: 'api_user_online', methods: ['GET'])]
    public function getOnline(): JsonResponse
    {
        $user = $this->getTokenUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        return new JsonResponse([
            'online' => [$user->getId()],
        ]);
    }

    #[Route('/activities', name: 'api_activities', methods: ['GET'])]
    public function activities(Request $request, ActivityRepository $repo): JsonResponse
    {
        $q = $request->query->get('q');
        $category = $request->query->get('category');
        $location = $request->query->get('location');
        $prixMax = $request->query->get('prix_max');

        $qb = $repo->createQueryBuilder('a')->andWhere('a.actif = 1');

        if ($q) {
            $qb->andWhere('a.title LIKE :q OR a.description LIKE :q')->setParameter('q', "%$q%");
        }
        if ($category) {
            $qb->andWhere('a.category = :cat')->setParameter('cat', $category);
        }
        if ($location) {
            $qb->andWhere('a.location LIKE :loc')->setParameter('loc', "%$location%");
        }
        if ($prixMax) {
            $qb->andWhere('a.price <= :pm')->setParameter('pm', (float) $prixMax);
        }

        $activities = $qb->getQuery()->getResult();

        return $this->json([
            'success' => true,
            'count' => count($activities),
            'data' => array_map(fn(Activity $a) => [
                'id' => $a->getId(),
                'title' => $a->getTitle(),
                'description' => $a->getDescription(),
                'price' => $a->getPrice(),
                'duration' => $a->getDuration(),
                'location' => $a->getLocation(),
                'imageUrl' => $a->getImage(),
                'rating' => $a->getNoteMoyenne(),
            ], $activities)
        ]);
    }

    #[Route('/activities/{id}', name: 'api_activity_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function activityDetail(int $id, ActivityRepository $repo): JsonResponse
    {
        $activity = $repo->find($id);
        
        if (!$activity) {
            return $this->json(['success' => false, 'error' => 'Activity not found'], 404);
        }

        return $this->json([
            'success' => true,
            'data' => [
                'id' => $activity->getId(),
                'title' => $activity->getTitle(),
                'description' => $activity->getDescription(),
                'price' => $activity->getPrice(),
                'duration' => $activity->getDuration(),
                'location' => $activity->getLocation(),
                'category' => $activity->getCategory(),
                'imageUrl' => $activity->getImageUrl(),
                'rating' => $activity->getNoteMoyenne(),
                'nbAvis' => $activity->getNbAvis(),
            ]
        ]);
    }

    #[Route('/circuits', name: 'api_circuits', methods: ['GET'])]
    public function circuits(Request $request, CircuitRepository $repo): JsonResponse
    {
        $q = $request->query->get('q');
        $destination = $request->query->get('destination');
        $difficulte = $request->query->get('difficulte');
        $prixMax = $request->query->get('prix_max');

        $qb = $repo->createQueryBuilder('c')->andWhere('c.actif = 1');

        if ($q) {
            $qb->andWhere('c.titre LIKE :q OR c.description LIKE :q')->setParameter('q', "%$q%");
        }
        if ($destination) {
            $qb->andWhere('c.destination LIKE :dest')->setParameter('dest', "%$destination%");
        }
        if ($difficulte) {
            $qb->andWhere('c.difficulte = :diff')->setParameter('diff', $difficulte);
        }
        if ($prixMax) {
            $qb->andWhere('c.prix <= :pm')->setParameter('pm', (float) $prixMax);
        }

        $circuits = $qb->getQuery()->getResult();

        return $this->json([
            'success' => true,
            'count' => count($circuits),
            'data' => array_map(fn(Circuit $c) => [
                'id' => $c->getId(),
                'title' => $c->getTitre(),
                'description' => $c->getDescription(),
                'price' => $c->getPrix(),
                'duree' => $c->getDuree(),
                'destination' => $c->getDestination(),
                'depart' => $c->getDepart(),
                'difficulte' => $c->getDifficulte(),
                'imageUrl' => $c->getImage(),
                'rating' => $c->getMoyenneNotes(),
            ], $circuits)
        ]);
    }

    #[Route('/hebergements', name: 'api_hebergements', methods: ['GET'])]
    public function hebergements(Request $request, HebergementRepository $repo): JsonResponse
    {
        $q = $request->query->get('q');
        $ville = $request->query->get('ville');
        $type = $request->query->get('type');
        $prixMax = $request->query->get('prix_max');

        $qb = $repo->createQueryBuilder('h');

        if ($q) {
            $qb->andWhere('h.nom LIKE :q OR h.description LIKE :q')->setParameter('q', "%$q%");
        }
        if ($ville) {
            $qb->andWhere('h.ville LIKE :ville')->setParameter('ville', "%$ville%");
        }
        if ($type) {
            $qb->andWhere('h.type = :type')->setParameter('type', $type);
        }
        if ($prixMax) {
            $qb->andWhere('h.prix <= :pm')->setParameter('pm', (float) $prixMax);
        }

        $hebergements = $qb->getQuery()->getResult();

        return $this->json([
            'success' => true,
            'count' => count($hebergements),
            'data' => array_map(fn($h) => [
                'id' => $h->getId(),
                'nom' => $h->getNom(),
                'description' => $h->getDescription(),
                'prix' => $h->getPrix(),
                'ville' => $h->getVille(),
                'type' => $h->getType(),
                'adresse' => $h->getAdresse(),
                'imageUrl' => $h->getImage(),
                'rating' => $h->getMoyenneNotes(),
            ], $hebergements)
        ]);
    }

    #[Route('/transports', name: 'api_transports', methods: ['GET'])]
    public function transports(Request $request, TransportOfferRepository $repo): JsonResponse
    {
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $type = $request->query->get('type');

        $qb = $repo->createQueryBuilder('t');

        if ($from) {
            $qb->andWhere('t.departureCity LIKE :from')->setParameter('from', "%$from%");
        }
        if ($to) {
            $qb->andWhere('t.arrivalCity LIKE :to')->setParameter('to', "%$to%");
        }
        if ($type) {
            $qb->andWhere('t.transportType = :type')->setParameter('type', $type);
        }

        $transports = $qb->getQuery()->getResult();

        return $this->json([
            'success' => true,
            'count' => count($transports),
            'data' => array_map(fn($t) => [
                'id' => $t->getId(),
                'company' => $t->getCompanyName(),
                'type' => $t->getTransportType(),
                'route' => $t->getRoute(),
                'from' => $t->getDepartureCity(),
                'to' => $t->getArrivalCity(),
                'price' => $t->getPrice(),
                'departure' => $t->getDepartureDatetime()?->format('Y-m-d H:i'),
                'arrival' => $t->getArrivalDatetime()?->format('Y-m-d H:i'),
                'imageUrl' => $t->getImagePath(),
            ], $transports)
        ]);
    }

    #[Route('/search', name: 'api_search', methods: ['GET'])]
    public function search(Request $request, ActivityRepository $actRepo, CircuitRepository $circRepo, HebergementRepository $hebRepo): JsonResponse
    {
        $q = $request->query->get('q', '');
        $type = $request->query->get('type', 'all');

        $results = [];

        if ($type === 'all' || $type === 'activities') {
            $activities = $actRepo->createQueryBuilder('a')
                ->andWhere('a.title LIKE :q OR a.description LIKE :q')
                ->setParameter('q', "%$q%")
                ->setMaxResults(10)
                ->getQuery()->getResult();
            $results['activities'] = array_map(fn(Activity $a) => [
                'id' => $a->getId(),
                'title' => $a->getTitle(),
                'price' => $a->getPrice(),
                'location' => $a->getLocation(),
                'type' => 'activity'
            ], $activities);
        }

        if ($type === 'all' || $type === 'circuits') {
            $circuits = $circRepo->createQueryBuilder('c')
                ->andWhere('c.titre LIKE :q OR c.destination LIKE :q')
                ->setParameter('q', "%$q%")
                ->setMaxResults(10)
                ->getQuery()->getResult();
            $results['circuits'] = array_map(fn(Circuit $c) => [
                'id' => $c->getId(),
                'title' => $c->getTitre(),
                'price' => $c->getPrix(),
                'destination' => $c->getDestination(),
                'type' => 'circuit'
            ], $circuits);
        }

        if ($type === 'all' || $type === 'hebergements') {
            $hebergements = $hebRepo->createQueryBuilder('h')
                ->andWhere('h.nom LIKE :q OR h.ville LIKE :q')
                ->setParameter('q', "%$q%")
                ->setMaxResults(10)
                ->getQuery()->getResult();
            $results['hebergements'] = array_map(fn($h) => [
                'id' => $h->getId(),
                'title' => $h->getNom(),
                'price' => $h->getPrix(),
                'location' => $h->getVille(),
                'type' => 'hebergement'
            ], $hebergements);
        }

        return $this->json([
            'success' => true,
            'query' => $q,
            'results' => $results
        ]);
    }
}
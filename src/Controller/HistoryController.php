<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FavoriteActivityRepository;
use App\Repository\FavoriteCircuitRepository;
use App\Repository\FavoriteHebergementRepository;
use App\Repository\FavoritePostRepository;
use App\Repository\FavoriteTransportRepository;
use App\Repository\BookingRepository;
use App\Repository\CircuitAvisRepository;
use App\Repository\CircuitRepository;
use App\Repository\ReservationCircuitRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use App\Entity\FavoriteHebergement;
use App\Entity\FavoriteCircuit;
use App\Entity\FavoriteActivity;
use App\Entity\FavoritePost;
use App\Entity\Review;
use App\Entity\CircuitAvis;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    #[Route('/historique', name: 'user_history')]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        ReservationRepository $resHebRepo,
        BookingRepository $bookingRepo,
        ReservationCircuitRepository $resCircRepo,
        FavoriteHebergementRepository $favHebRepo,
        FavoriteCircuitRepository $favCircRepo,
        FavoriteActivityRepository $favActRepo,
        FavoriteTransportRepository $favTransRepo,
        FavoritePostRepository $favPostRepo,
        ReviewRepository $reviewRepo,
        CircuitAvisRepository $circuitAvisRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();
        
        $filter = $request->query->get('filter', 'all');
        $type = $request->query->get('type');

        $data = [];

        if ($filter === 'all' || $filter === 'reservations') {
            $hebergements = $resHebRepo->findByUser($user, ['createdAt' => 'DESC']);
            $activities = $bookingRepo->findBy(['user' => $user], ['id' => 'DESC']);
            $circuits = $resCircRepo->findByUser($user);
            
            foreach ($hebergements as $r) {
                $data[] = [
                    'id' => 'res_heberg_' . $r->getId(),
                    'entity' => 'reservation_hebergement',
                    'entityId' => $r->getId(),
                    'type' => 'hebergement_reservation',
                    'date' => $r->getCreatedAt(),
                    'title' => 'Réservation: ' . ($r->getHebergement()?->getNom() ?? 'N/A'),
                    'status' => $r->getStatut(),
                    'amount' => $r->getMontantTotal(),
                    'link' => $this->generateUrl('hebergement_show', ['id' => $r->getHebergement()?->getId()]),
                ];
            }
            
            foreach ($activities as $r) {
                $data[] = [
                    'id' => 'booking_' . $r->getId(),
                    'entity' => 'booking',
                    'entityId' => $r->getId(),
                    'type' => 'activity_reservation',
                    'date' => $r->getCreatedAt(),
                    'title' => 'Activité: ' . ($r->getActivity()?->getTitle() ?? 'N/A'),
                    'status' => $r->getStatus(),
                    'amount' => $r->getTotalPrice(),
                    'link' => $this->generateUrl('activity_show', ['id' => $r->getActivity()?->getId()]),
                ];
            }
            
            foreach ($circuits as $r) {
                $data[] = [
                    'id' => 'res_circuit_' . $r->getId(),
                    'entity' => 'reservation_circuit',
                    'entityId' => $r->getId(),
                    'type' => 'circuit_reservation',
                    'date' => $r->getDateReservation(),
                    'title' => 'Circuit: ' . ($r->getCircuit()?->getTitre() ?? 'N/A'),
                    'status' => $r->getStatut(),
                    'amount' => $r->getMontantTotal(),
                    'link' => $this->generateUrl('circuit_show', ['id' => $r->getCircuit()?->getId()]),
                ];
            }
        }

        if ($filter === 'all' || $filter === 'favorites') {
            foreach ($favHebRepo->findByUser($user) as $f) {
                $data[] = [
                    'id' => 'fav_heberg_' . $f->getId(),
                    'entity' => 'favorite_hebergement',
                    'entityId' => $f->getId(),
                    'type' => 'favorite_hebergement',
                    'date' => $f->getCreatedAt(),
                    'title' => 'Favori: ' . ($f->getHebergement()?->getNom() ?? 'N/A'),
                    'status' => 'favorite',
                    'link' => $this->generateUrl('hebergement_show', ['id' => $f->getHebergement()?->getId()]),
                ];
            }
            
            foreach ($favCircRepo->findByUser($user) as $f) {
                $data[] = [
                    'id' => 'fav_circuit_' . $f->getId(),
                    'entity' => 'favorite_circuit',
                    'entityId' => $f->getId(),
                    'type' => 'favorite_circuit',
                    'date' => $f->getCreatedAt(),
                    'title' => 'Favori: ' . ($f->getCircuit()?->getTitre() ?? 'N/A'),
                    'status' => 'favorite',
                    'link' => $this->generateUrl('circuit_show', ['id' => $f->getCircuit()?->getId()]),
                ];
            }
            
            foreach ($favActRepo->findByUser($user) as $f) {
                $data[] = [
                    'id' => 'fav_activity_' . $f->getId(),
                    'entity' => 'favorite_activity',
                    'entityId' => $f->getId(),
                    'type' => 'favorite_activity',
                    'date' => $f->getCreatedAt(),
                    'title' => 'Favori: ' . ($f->getActivity()?->getTitle() ?? 'N/A'),
                    'status' => 'favorite',
                    'link' => $this->generateUrl('activity_show', ['id' => $f->getActivity()?->getId()]),
                ];
            }
            
            foreach ($favPostRepo->findByUser($user) as $f) {
                $data[] = [
                    'id' => 'fav_post_' . $f->getId(),
                    'entity' => 'favorite_post',
                    'entityId' => $f->getId(),
                    'type' => 'favorite_forum',
                    'date' => $f->getCreatedAt(),
                    'title' => 'Favori forum: ' . ($f->getPost()?->getTitle() ?? 'N/A'),
                    'status' => 'favorite',
                    'link' => $this->generateUrl('forum_show', ['id' => $f->getPost()?->getId()]),
                ];
            }
        }

        if ($filter === 'all' || $filter === 'reviews') {
            foreach ($reviewRepo->findBy(['user' => $user]) as $r) {
                $data[] = [
                    'id' => 'review_act_' . $r->getId(),
                    'entity' => 'review',
                    'entityId' => $r->getId(),
                    'type' => 'review_activity',
                    'date' => $r->getCreatedAt(),
                    'title' => 'Avis: ' . ($r->getActivity()?->getTitle() ?? 'N/A'),
                    'status' => 'published',
                    'link' => $this->generateUrl('activity_show', ['id' => $r->getActivity()?->getId()]),
                ];
            }
            
            foreach ($circuitAvisRepo->findBy(['user' => $user]) as $r) {
                $data[] = [
                    'id' => 'review_circ_' . $r->getId(),
                    'entity' => 'circuit_avis',
                    'entityId' => $r->getId(),
                    'type' => 'review_circuit',
                    'date' => $r->getCreatedAt(),
                    'title' => 'Avis circuit: ' . ($r->getCircuit()?->getTitre() ?? 'N/A'),
                    'status' => 'published',
                    'link' => $this->generateUrl('circuit_show', ['id' => $r->getCircuit()?->getId()]),
                ];
            }
        }

        usort($data, fn($a, $b) => $b['date'] <=> $a['date']);

        if ($type) {
            $data = array_filter($data, fn($item) => str_contains($item['type'], $type));
            $data = array_values($data);
        }

        $pagination = $paginator->paginate($data, $request->query->getInt('page', 1), 10, ['sort' => '']);

        return $this->render('user/history.html.twig', [
            'history' => $pagination,
            'filter' => $filter,
            'type' => $type,
        ]);
    }

    #[Route('/historique/delete', name: 'history_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $em,
        FavoriteHebergementRepository $favHebRepo,
        FavoriteCircuitRepository $favCircRepo,
        FavoriteActivityRepository $favActRepo,
        FavoritePostRepository $favPostRepo,
        FavoriteTransportRepository $favTransRepo
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $content = $request->getContent();
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse([
                'success' => false, 
                'message' => 'Invalid JSON: ' . json_last_error_msg(),
                'raw' => substr($content, 0, 200)
            ], 400);
        }
        
        $items = $data['items'] ?? [];
        
        if (empty($items)) {
            return new JsonResponse(['success' => false, 'message' => 'Aucun élément sélectionné'], 400);
        }
        
        $deleted = 0;
        $errors = [];
        
        foreach ($items as $index => $itemData) {
            $entity = $itemData['entity'] ?? '';
            $entityId = isset($itemData['entityId']) ? (int) $itemData['entityId'] : 0;
            
            if (!$entity || !$entityId) {
                $errors[] = "Item $index: missing entity or entityId";
                continue;
            }
            
            $item = null;
            
            switch ($entity) {
                case 'favorite_hebergement':
                    $item = $favHebRepo->find($entityId);
                    break;
                case 'favorite_circuit':
                    $item = $favCircRepo->find($entityId);
                    break;
                case 'favorite_activity':
                    $item = $favActRepo->find($entityId);
                    break;
                case 'favorite_post':
                    $item = $favPostRepo->find($entityId);
                    break;
                case 'favorite_transport':
                    $item = $favTransRepo->find($entityId);
                    break;
                default:
                    $errors[] = "Unknown entity type: $entity";
            }
            
            if ($item) {
                try {
                    $em->remove($item);
                    $em->flush();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Error deleting $entity $entityId: " . $e->getMessage();
                }
            } else {
                $errors[] = "Item not found: $entity with ID $entityId";
            }
        }
        
        return new JsonResponse([
            'success' => $deleted > 0,
            'deleted' => $deleted,
            'message' => $deleted > 0 ? "$deleted élément(s) supprimé(s)" : null,
            'errors' => $errors
        ]);
    }

    #[Route('/historique/clear', name: 'history_clear', methods: ['POST'])]
    public function clearAll(
        EntityManagerInterface $em,
        FavoriteHebergementRepository $favHebRepo,
        FavoriteCircuitRepository $favCircRepo,
        FavoriteActivityRepository $favActRepo,
        FavoritePostRepository $favPostRepo
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();
        
        try {
            foreach ($favHebRepo->findByUser($user) as $f) { $em->remove($f); }
            foreach ($favCircRepo->findByUser($user) as $f) { $em->remove($f); }
            foreach ($favActRepo->findByUser($user) as $f) { $em->remove($f); }
            foreach ($favPostRepo->findByUser($user) as $f) { $em->remove($f); }
            $em->flush();
            
            return new JsonResponse(['success' => true, 'message' => 'Historique des favoris effacé']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
}

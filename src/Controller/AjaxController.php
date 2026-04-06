<?php

namespace App\Controller;

use App\Entity\FavoriteActivity;
use App\Entity\FavoriteCircuit;
use App\Entity\FavoriteHebergement;
use App\Entity\FavoritePost;
use App\Entity\FavoriteTransport;
use App\Entity\LikeDislike;
use App\Entity\User;
use App\Repository\FavoriteActivityRepository;
use App\Repository\FavoriteCircuitRepository;
use App\Repository\FavoriteHebergementRepository;
use App\Repository\FavoritePostRepository;
use App\Repository\FavoriteTransportRepository;
use App\Repository\LikeDislikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ajax')]
class AjaxController extends AbstractController
{
    #[Route('/favorite/{type}/{id}', name: 'ajax_favorite_toggle', methods: ['POST'])]
    public function toggleFavorite(string $type, int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $entityClass = match($type) {
            'hebergement' => ['entity' => 'App\Entity\Hebergement', 'favClass' => FavoriteHebergement::class, 'repo' => FavoriteHebergementRepository::class],
            'circuit' => ['entity' => 'App\Entity\Circuit', 'favClass' => FavoriteCircuit::class, 'repo' => FavoriteCircuitRepository::class],
            'activity' => ['entity' => 'App\Entity\Activity', 'favClass' => FavoriteActivity::class, 'repo' => FavoriteActivityRepository::class],
            'transport' => ['entity' => 'App\Entity\TransportOffer', 'favClass' => FavoriteTransport::class, 'repo' => FavoriteTransportRepository::class],
            'post' => ['entity' => 'App\Entity\ForumPost', 'favClass' => FavoritePost::class, 'repo' => FavoritePostRepository::class],
            default => null,
        };

        if (!$entityClass) {
            return new JsonResponse(['success' => false, 'message' => 'Type invalide'], 400);
        }

        $target = $em->find($entityClass['entity'], $id);
        if (!$target) {
            return new JsonResponse(['success' => false, 'message' => 'Élément non trouvé'], 404);
        }

        $favRepo = $em->getRepository($entityClass['favClass']);
        $favField = match($type) {
            'hebergement' => 'hebergement', 'circuit' => 'circuit', 'activity' => 'activity',
            'transport' => 'transport', 'post' => 'post', default => null,
        };

        $existing = $favRepo->findOneBy(['user' => $user, $favField => $target]);

        if ($existing) {
            $em->remove($existing);
            $em->flush();
            return new JsonResponse(['success' => true, 'favorited' => false, 'message' => 'Retiré des favoris']);
        }

        $favorite = new $entityClass['favClass']();
        $favorite->setUser($user);
        $setter = 'set' . ucfirst($favField);
        $favorite->$setter($target);
        $em->persist($favorite);
        $em->flush();

        return new JsonResponse(['success' => true, 'favorited' => true, 'message' => 'Ajouté aux favoris']);
    }

    #[Route('/favorite/hebergement/{id}', name: 'ajax_favorite_hebergement', methods: ['POST'])]
    public function favoriteHebergement(int $id, Request $request, FavoriteHebergementRepository $favRepo, EntityManagerInterface $em): JsonResponse
    {
        return $this->toggleFavorite('hebergement', $id, $request, $em);
    }

    #[Route('/favorite/circuit/{id}', name: 'ajax_favorite_circuit', methods: ['POST'])]
    public function favoriteCircuit(int $id, Request $request, FavoriteCircuitRepository $favRepo, EntityManagerInterface $em): JsonResponse
    {
        return $this->toggleFavorite('circuit', $id, $request, $em);
    }

    #[Route('/favorite/activity/{id}', name: 'ajax_favorite_activity', methods: ['POST'])]
    public function favoriteActivity(int $id, Request $request, FavoriteActivityRepository $favRepo, EntityManagerInterface $em): JsonResponse
    {
        return $this->toggleFavorite('activity', $id, $request, $em);
    }

    #[Route('/favorite/transport/{id}', name: 'ajax_favorite_transport', methods: ['POST'])]
    public function favoriteTransport(int $id, Request $request, FavoriteTransportRepository $favRepo, EntityManagerInterface $em): JsonResponse
    {
        return $this->toggleFavorite('transport', $id, $request, $em);
    }

    #[Route('/favorite/post/{id}', name: 'ajax_favorite_post', methods: ['POST'])]
    public function favoritePost(int $id, Request $request, FavoritePostRepository $favRepo, EntityManagerInterface $em): JsonResponse
    {
        return $this->toggleFavorite('post', $id, $request, $em);
    }

    #[Route('/like/{type}/{id}', name: 'ajax_like', methods: ['POST'])]
    public function like(string $type, int $id, Request $request, LikeDislikeRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $existing = $repo->findOneBy(['user' => $user, 'targetType' => $type, 'targetId' => $id]);
        $vote = (int) $request->request->get('vote', LikeDislike::LIKE);

        if ($existing) {
            if ($existing->getVote() === $vote) {
                $em->remove($existing);
                $em->flush();
                $counts = $repo->getCount($type, $id);
                return new JsonResponse([
                    'success' => true, 'userVote' => 0,
                    'likes' => $counts['likes'], 'dislikes' => $counts['dislikes'], 'score' => $counts['score'],
                ]);
            }
            $existing->setVote($vote);
        } else {
            $like = new LikeDislike();
            $like->setUser($user)->setTargetType($type)->setTargetId($id)->setVote($vote);
            $em->persist($like);
        }

        $em->flush();
        $counts = $repo->getCount($type, $id);

        return new JsonResponse([
            'success' => true, 'userVote' => $vote,
            'likes' => $counts['likes'], 'dislikes' => $counts['dislikes'], 'score' => $counts['score'],
        ]);
    }

    #[Route('/check-availability', name: 'ajax_check_availability', methods: ['POST'])]
    public function checkAvailability(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $hebergementId = (int) $request->request->get('hebergement_id');
        $dateDebut = $request->request->get('date_debut');
        $dateFin = $request->request->get('date_fin');

        if (!$hebergementId || !$dateDebut || !$dateFin) {
            return new JsonResponse(['available' => false, 'message' => 'Paramètres manquants'], 400);
        }

        $reservations = $em->createQueryBuilder()
            ->select('r')
            ->from('App\Entity\Reservation', 'r')
            ->where('r.hebergement = :heb')
            ->andWhere('r.statut NOT IN (:cancelled)')
            ->andWhere('(r.dateDebut <= :fin AND r.dateFin >= :debut)')
            ->setParameter('heb', $hebergementId)
            ->setParameter('fin', new \DateTime($dateFin))
            ->setParameter('debut', new \DateTime($dateDebut))
            ->setParameter('cancelled', ['ANNULEE', 'Annulée', 'annulée'])
            ->getQuery()
            ->getResult();

        return new JsonResponse([
            'available' => empty($reservations),
            'message' => empty($reservations) ? 'Dates disponibles' : 'Dates non disponibles',
        ]);
    }

    #[Route('/apply-promo', name: 'ajax_apply_promo', methods: ['POST'])]
    public function applyPromo(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $code = $request->request->get('code');
        $originalPrice = (float) $request->request->get('price', 0);

        if (!$code) {
            return new JsonResponse(['success' => false, 'message' => 'Code promo requis'], 400);
        }

        $promo = $em->getRepository('App\Entity\PromoCode')->findOneBy(['code' => strtoupper($code)]);

        if (!$promo || !$promo->isValid()) {
            return new JsonResponse(['success' => false, 'message' => 'Code promo invalide ou expiré'], 400);
        }

        $reduction = $promo->calculateReduction($originalPrice);
        $finalPrice = max(0, $originalPrice - $reduction);

        return new JsonResponse([
            'success' => true, 'originalPrice' => $originalPrice, 'reduction' => $reduction, 'finalPrice' => $finalPrice,
            'promoType' => $promo->getType(), 'promoValue' => $promo->getValue(),
            'message' => "Réduction de {$promo->getValue()}" . ($promo->getType() === 'percentage' ? '%' : ' TND') . " appliquée !",
        ]);
    }

    #[Route('/rating/refresh/{type}/{id}', name: 'ajax_refresh_rating', methods: ['GET'])]
    public function refreshRating(string $type, int $id, EntityManagerInterface $em): JsonResponse
    {
        if ($type === 'hebergement') {
            $heb = $em->find('App\Entity\Hebergement', $id);
            $avg = $heb?->getMoyenneNotes() ?? 0;
            $count = $heb?->getAvis()->count() ?? 0;
        } elseif ($type === 'activity') {
            $act = $em->find('App\Entity\Activity', $id);
            $avg = $act?->getMoyenneNotes() ?? 0;
            $count = $act?->getReviews()->count() ?? 0;
        } else {
            return new JsonResponse(['success' => false], 404);
        }

        return new JsonResponse([
            'success' => true, 'average' => $avg, 'count' => $count, 'stars' => $this->generateStars($avg),
        ]);
    }

    private function generateStars(float $rating): string
    {
        $full = floor($rating);
        $half = $rating - $full >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;
        return str_repeat('★', $full) . ($half ? '☆' : '') . str_repeat('☆', $empty);
    }
}

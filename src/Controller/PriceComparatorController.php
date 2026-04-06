<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Circuit;
use App\Entity\Hebergement;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\CircuitRepository;
use App\Repository\HebergementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PriceComparatorController extends AbstractController
{
    #[Route('/comparateur-prix', name: 'price_comparator')]
    public function index(Request $request, HebergementRepository $hebergementRepository, CircuitRepository $circuitRepository, ActivityRepository $activityRepository): Response
    {
        $destination = trim((string) $request->query->get('destination', ''));
        $category = (string) $request->query->get('category', 'all');
        $sort = (string) $request->query->get('sort', 'price_asc');
        $partners = ['Booking.com', 'TravelTodo', 'Expedia', 'TUI', 'Airbnb', 'Viator'];

        $offers = [];

        if (in_array($category, ['all', 'hebergement'], true)) {
            foreach ($hebergementRepository->search($destination ?: null, $destination ?: null, null, null, null, 'recent') as $item) {
                if (!$item instanceof Hebergement) continue;
                $offers[] = $this->buildOffer('hebergement', $item->getId(), $item->getNom(), $item->getVille(), $item->getPrixParNuit(), $partners[array_rand($partners)], $item->getType(), 'hebergement_show');
            }
        }

        if (in_array($category, ['all', 'circuit'], true)) {
            $circuits = $circuitRepository->searchVisible($destination ?: null, null, 'recent', $this->getUser() instanceof User ? $this->getUser() : null);
            foreach ($circuits as $item) {
                if (!$item instanceof Circuit || $item->getSourceType() !== 'admin') continue;
                $offers[] = $this->buildOffer('circuit', $item->getId(), $item->getTitre(), $item->getDestination() ?? 'Tunisie', $item->getPrix(), $partners[array_rand($partners)], $item->getDuree() ?? 'Circuit', 'circuit_show');
            }
        }

        if (in_array($category, ['all', 'activite'], true)) {
            foreach ($activityRepository->search($destination ?: null, $destination ?: null) as $item) {
                if (!$item instanceof Activity) continue;
                $offers[] = $this->buildOffer('activite', $item->getId(), $item->getTitle(), $item->getLieu() ?? 'Tunisie', $item->getPrice(), $partners[array_rand($partners)], $item->getDuration() ?? 'Activité', 'activity_show');
            }
        }

        usort($offers, function (array $a, array $b) use ($sort) {
            return match ($sort) {
                'price_desc' => $b['price'] <=> $a['price'],
                'discount_desc' => $b['discount'] <=> $a['discount'],
                default => $a['price'] <=> $b['price'],
            };
        });

        $countsByPartner = [];
        foreach ($offers as $offer) {
            $countsByPartner[$offer['partner']] = ($countsByPartner[$offer['partner']] ?? 0) + 1;
        }

        return $this->render('comparator/index.html.twig', [
            'offers' => $offers,
            'destination' => $destination,
            'category' => $category,
            'sort' => $sort,
            'partners' => $partners,
            'countsByPartner' => $countsByPartner,
        ]);
    }

    private function buildOffer(string $module, int $id, string $title, string $destination, float $basePrice, string $partner, string $meta, string $route): array
    {
        $multiplier = [
            'Booking.com' => 1.00,
            'TravelTodo' => 0.97,
            'Expedia' => 1.08,
            'TUI' => 1.03,
            'Airbnb' => 1.05,
            'Viator' => 0.92,
        ][$partner] ?? 1;

        $originalPrice = round($basePrice * ($multiplier + 0.08), 3);
        $price = round($basePrice * $multiplier, 3);
        $discount = max(0, (int) round((1 - ($price / max(1, $originalPrice))) * 100));

        return [
            'module' => $module,
            'title' => $title,
            'destination' => $destination,
            'meta' => $meta,
            'partner' => $partner,
            'price' => $price,
            'original_price' => $originalPrice,
            'discount' => $discount,
            'link' => $this->generateUrl($route, ['id' => $id]),
        ];
    }
}

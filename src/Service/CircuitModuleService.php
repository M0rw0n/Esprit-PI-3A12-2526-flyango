<?php

namespace App\Service;

use App\Dto\AdminCircuitData;
use App\Dto\AdminCircuitFilterInput;
use App\Dto\AdminReservationFilterInput;
use App\Dto\AdminReviewFilterInput;
use App\Dto\CircuitSearchInput;
use App\Dto\ComparatorInput;
use App\Dto\CustomCircuitRequest;
use App\Dto\ReservationFilterInput;
use App\Dto\ReservationRequest;
use App\Dto\ReviewRequest;
use Doctrine\DBAL\Connection;

class CircuitModuleService
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function getTypes(): array
    {
        $rows = $this->connection->fetchFirstColumn('SELECT DISTINCT type FROM circuit WHERE type IS NOT NULL AND type <> "" ORDER BY type ASC');

        return array_values(array_filter($rows));
    }

    public function searchCircuits(CircuitSearchInput $input): array
    {
        $where = ['status = :status'];
        $params = ['status' => 'actif'];

        if ($input->q) {
            $where[] = '(LOWER(title) LIKE :q OR LOWER(destination) LIKE :q OR LOWER(description) LIKE :q OR LOWER(type) LIKE :q)';
            $params['q'] = '%' . mb_strtolower($input->q) . '%';
        }

        if ($input->type && $input->type !== 'Tous') {
            $where[] = 'type = :type';
            $params['type'] = $input->type;
        }

        if ($input->maxPrice !== null) {
            $where[] = 'prix_par_personne <= :maxPrice';
            $params['maxPrice'] = $input->maxPrice;
        }

        if ($input->maxDuration !== null) {
            $where[] = 'duree <= :maxDuration';
            $params['maxDuration'] = $input->maxDuration;
        }

        $orderBy = match ($input->sort) {
            'prix_asc' => 'prix_par_personne ASC, note_moyenne DESC',
            'prix_desc' => 'prix_par_personne DESC, note_moyenne DESC',
            'note_desc' => 'note_moyenne DESC, popularite_score DESC',
            'duree_asc' => 'duree ASC, note_moyenne DESC',
            default => 'popularite_score DESC, note_moyenne DESC, created_at DESC',
        };

        $sql = sprintf(
            'SELECT id_circuit, title, description, type, destination, duree, image_url, prix_par_personne, note_moyenne, nb_avis, popularite_score, start_date
             FROM circuit
             WHERE %s
             ORDER BY %s',
            implode(' AND ', $where),
            $orderBy
        );

        return $this->connection->fetchAllAssociative($sql, $params);
    }

    public function getCircuitById(int $id): ?array
    {
        $circuit = $this->connection->fetchAssociative('SELECT * FROM circuit WHERE id_circuit = :id', ['id' => $id]);
        if (!$circuit) {
            return null;
        }

        $circuit['jours'] = $this->connection->fetchAllAssociative(
            'SELECT id_jour, numero_jour, titre, activites, hebergement, transport, budget_jour
             FROM jour_circuit WHERE id_circuit = :id ORDER BY numero_jour ASC',
            ['id' => $id]
        );
        $circuit['reviews'] = $this->connection->fetchAllAssociative(
            'SELECT r.id, r.comment, r.rating, r.helpful_count, r.verified_purchase, r.created_at,
                    u.nom, u.prenom
             FROM circuit_review r
             LEFT JOIN user u ON u.id = r.user_id
             WHERE r.id_circuit = :id
             ORDER BY datetime(r.created_at) DESC
             LIMIT 6',
            ['id' => $id]
        );
        $circuit['meteo_info'] = $this->buildWeatherSummary((string) $circuit['destination'], $circuit['start_date'] ?? null);
        $circuit['plan_b'] = $this->buildPlanB((string) $circuit['destination'], (string) $circuit['type']);
        $circuit['pricing'] = $this->calculateReservation((float) ($circuit['prix_par_personne'] ?? 0), 2);

        return $circuit;
    }

    public function saveCustomCircuit(CustomCircuitRequest $dto, int $userId): void
    {
        $this->connection->insert('circuit_personnalise', [
            'user_id' => $userId,
            'destination' => $dto->destination,
            'date_depart' => $dto->dateDepart,
            'duree' => $dto->duree,
            'budget_min' => $dto->budgetMin,
            'budget_max' => $dto->budgetMax,
            'style_voyage' => $dto->styleVoyage,
            'centres_interet' => implode(', ', $dto->centresInteret),
            'niveau_fatigue' => $dto->niveauFatigue,
            'etape' => 4,
            'statut' => 'SOUMIS',
            'meteo_info' => $this->buildWeatherSummary($dto->destination ?? '', $dto->dateDepart),
            'plan_b' => $this->buildPlanB($dto->destination ?? '', $dto->styleVoyage ?? ''),
        ]);
    }

    public function calculateReservation(float $pricePerPerson, int $travellers): array
    {
        $subtotal = round($pricePerPerson * $travellers, 2);
        $taxes = round($subtotal * 0.10, 2);
        $total = round($subtotal + $taxes, 2);

        return [
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'total' => $total,
            'eur' => round($total * 0.29, 2),
            'usd' => round($total * 0.34, 2),
        ];
    }

    public function createReservation(int $circuitId, ReservationRequest $dto, int $userId): int
    {
        $circuit = $this->connection->fetchAssociative('SELECT id_circuit, prix_par_personne, destination, type FROM circuit WHERE id_circuit = :id', ['id' => $circuitId]);
        if (!$circuit) {
            throw new \RuntimeException('Circuit introuvable.');
        }

        $pricing = $this->calculateReservation((float) $circuit['prix_par_personne'], $dto->nbTravelers);

        $this->connection->insert('circuit_reservation', [
            'id_circuit' => $circuitId,
            'user_id' => $userId,
            'nb_travelers' => $dto->nbTravelers,
            'total_price' => $pricing['total'],
            'status' => 'CONFIRME',
            'date_depart' => $dto->dateDepart,
            'meteo_info' => $this->buildWeatherSummary((string) $circuit['destination'], $dto->dateDepart),
            'plan_b' => $this->buildPlanB((string) $circuit['destination'], (string) $circuit['type']),
            'reserved_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function getReservations(int $userId, ReservationFilterInput $input): array
    {
        $where = ['r.user_id = :userId'];
        $params = ['userId' => $userId];

        if ($input->q) {
            $where[] = '(LOWER(c.title) LIKE :q OR LOWER(c.destination) LIKE :q OR LOWER(r.status) LIKE :q)';
            $params['q'] = '%' . mb_strtolower($input->q) . '%';
        }

        if ($input->status && $input->status !== 'Tous') {
            $where[] = 'r.status = :status';
            $params['status'] = $input->status;
        }

        $sql = sprintf(
            'SELECT r.id, r.nb_travelers, r.total_price, r.status, r.reserved_at, r.date_depart, r.meteo_info, r.plan_b,
                    c.id_circuit, c.title, c.destination, c.image_url, c.prix_par_personne
             FROM circuit_reservation r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             WHERE %s
             ORDER BY datetime(r.reserved_at) DESC',
            implode(' AND ', $where)
        );

        return $this->connection->fetchAllAssociative($sql, $params);
    }

    public function getReservationStats(int $userId): array
    {
        $total = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE user_id = :userId', ['userId' => $userId]);
        $confirmed = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE user_id = :userId AND status = :status', ['userId' => $userId, 'status' => 'CONFIRME']);
        $spent = (float) $this->connection->fetchOne('SELECT COALESCE(SUM(total_price), 0) FROM circuit_reservation WHERE user_id = :userId AND status <> :status', ['userId' => $userId, 'status' => 'ANNULE']);
        $cancelled = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE user_id = :userId AND status = :status', ['userId' => $userId, 'status' => 'ANNULE']);
        $pending = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE user_id = :userId AND status = :status', ['userId' => $userId, 'status' => 'EN_ATTENTE']);

        return compact('total', 'confirmed', 'spent', 'cancelled', 'pending');
    }

    public function cancelReservation(int $reservationId, int $userId): void
    {
        $this->connection->update('circuit_reservation', ['status' => 'ANNULE'], ['id' => $reservationId, 'user_id' => $userId]);
    }

    public function getUserReviews(int $userId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT r.id, r.comment, r.rating, r.created_at, r.helpful_count, r.verified_purchase,
                    c.id_circuit, c.title, c.destination
             FROM circuit_review r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             WHERE r.user_id = :userId
             ORDER BY datetime(r.created_at) DESC',
            ['userId' => $userId]
        );
    }

    public function createReview(int $circuitId, ReviewRequest $dto, int $userId): void
    {
        $this->connection->insert('circuit_review', [
            'id_circuit' => $circuitId,
            'user_id' => $userId,
            'comment' => $dto->comment,
            'rating' => $dto->rating,
            'helpful_count' => 0,
            'verified_purchase' => 1,
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        $stats = $this->connection->fetchAssociative(
            'SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total_reviews FROM circuit_review WHERE id_circuit = :id',
            ['id' => $circuitId]
        );

        $this->connection->update('circuit', [
            'note_moyenne' => (float) ($stats['avg_rating'] ?? 0),
            'nb_avis' => (int) ($stats['total_reviews'] ?? 0),
        ], ['id_circuit' => $circuitId]);
    }

    public function getRatingDashboard(): array
    {
        $globalAvg = (float) $this->connection->fetchOne('SELECT COALESCE(ROUND(AVG(rating), 1), 0) FROM circuit_review');
        $totalReviews = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review');
        $positive = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review WHERE rating >= 4');
        $previousAvg = (float) $this->connection->fetchOne("SELECT COALESCE(AVG(rating), 0) FROM circuit_review WHERE datetime(created_at) < datetime('now', '-30 days') AND datetime(created_at) >= datetime('now', '-60 days')");
        $currentAvg = (float) $this->connection->fetchOne("SELECT COALESCE(AVG(rating), 0) FROM circuit_review WHERE datetime(created_at) >= datetime('now', '-30 days')");
        $trend = round($currentAvg - $previousAvg, 1);

        $distributionRows = $this->connection->fetchAllAssociative('SELECT rating, COUNT(*) AS total FROM circuit_review GROUP BY rating ORDER BY rating DESC');
        $distribution = [];
        for ($i = 5; $i >= 1; --$i) {
            $distribution[$i] = 0;
        }
        foreach ($distributionRows as $row) {
            $distribution[(int) $row['rating']] = (int) $row['total'];
        }

        $topCircuits = $this->connection->fetchAllAssociative(
            'SELECT c.id_circuit, c.title, c.destination, ROUND(AVG(r.rating), 1) AS avg_rating, COUNT(r.id) AS review_count
             FROM circuit_review r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             GROUP BY c.id_circuit, c.title, c.destination
             ORDER BY avg_rating DESC, review_count DESC
             LIMIT 5'
        );

        return [
            'globalAvg' => $globalAvg,
            'totalReviews' => $totalReviews,
            'positiveRate' => $totalReviews > 0 ? round(($positive / $totalReviews) * 100, 1) : 0,
            'trend' => $trend,
            'distribution' => $distribution,
            'sentiments' => [
                'positifs' => $positive,
                'neutres' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review WHERE rating = 3'),
                'negatifs' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review WHERE rating <= 2'),
            ],
            'topCircuits' => $topCircuits,
        ];
    }

    public function comparePrices(ComparatorInput $input): array
    {
        $circuits = $this->connection->fetchAllAssociative(
            'SELECT id_circuit, title, destination, type, prix_par_personne, note_moyenne
             FROM circuit
             WHERE LOWER(title) LIKE :q OR LOWER(destination) LIKE :q
             ORDER BY popularite_score DESC, note_moyenne DESC
             LIMIT 8',
            ['q' => '%' . mb_strtolower((string) $input->destination) . '%']
        );

        if (!$circuits) {
            $circuits = $this->connection->fetchAllAssociative('SELECT id_circuit, title, destination, type, prix_par_personne, note_moyenne FROM circuit ORDER BY popularite_score DESC LIMIT 6');
        }

        $agencies = [
            ['name' => 'Booking.com', 'category' => 'Hôtels', 'coef' => 1.18, 'color' => '🔵'],
            ['name' => 'TravelTodo', 'category' => 'Circuits', 'coef' => 1.00, 'color' => '🟢'],
            ['name' => 'Expedia', 'category' => 'Transport', 'coef' => 1.09, 'color' => '🟡'],
            ['name' => 'TUI', 'category' => 'Circuits', 'coef' => 1.14, 'color' => '🔴'],
            ['name' => 'Airbnb', 'category' => 'Hôtels', 'coef' => 0.96, 'color' => '🩷'],
            ['name' => 'Viator', 'category' => 'Activités', 'coef' => 0.88, 'color' => '🎭'],
        ];

        $offers = [];
        foreach ($circuits as $index => $circuit) {
            foreach ($agencies as $agencyIndex => $agency) {
                if ($input->categorie !== 'Tous' && $input->categorie !== $agency['category']) {
                    continue;
                }

                $base = (float) $circuit['prix_par_personne'] * $input->voyageurs;
                $modifier = 1 + ((($index + $agencyIndex) % 5) - 2) * 0.03;
                $price = round($base * $agency['coef'] * $modifier, 2);
                $offers[] = [
                    'agency' => $agency['name'],
                    'badge' => $agency['color'],
                    'category' => $agency['category'],
                    'title' => $circuit['title'],
                    'destination' => $circuit['destination'],
                    'price' => $price,
                    'oldPrice' => round($price * 1.08, 2),
                    'score' => (float) $circuit['note_moyenne'],
                    'discount' => max(2, min(12, (int) round((($agencyIndex + 1) * 1.7) + ($index % 4)))),
                    'circuitId' => (int) $circuit['id_circuit'],
                ];
            }
        }

        usort($offers, static fn (array $a, array $b) => $a['price'] <=> $b['price']);

        $bestPrice = $offers[0]['price'] ?? 0;
        $avgPrice = count($offers) > 0 ? round(array_sum(array_column($offers, 'price')) / count($offers), 2) : 0;
        $maxPrice = count($offers) > 0 ? max(array_column($offers, 'price')) : 0;

        return [
            'offers' => $offers,
            'stats' => [
                'count' => count($offers),
                'bestPrice' => $bestPrice,
                'averagePrice' => $avgPrice,
                'spread' => round($maxPrice - $bestPrice, 2),
                'bestAgency' => $offers[0]['agency'] ?? null,
            ],
        ];
    }

    public function getReviewSummary(int $userId): array
    {
        $total = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review WHERE user_id = :userId', ['userId' => $userId]);
        $average = (float) $this->connection->fetchOne('SELECT COALESCE(ROUND(AVG(rating), 1), 0) FROM circuit_review WHERE user_id = :userId', ['userId' => $userId]);
        $helpful = (int) $this->connection->fetchOne('SELECT COALESCE(SUM(helpful_count), 0) FROM circuit_review WHERE user_id = :userId', ['userId' => $userId]);

        return ['total' => $total, 'average' => $average, 'helpful' => $helpful];
    }

    public function getFeaturedCircuitsForReview(): array
    {
        return $this->connection->fetchAllAssociative('SELECT id_circuit, title, destination FROM circuit ORDER BY note_moyenne DESC, popularite_score DESC LIMIT 8');
    }

    public function getUserHomepageData(int $userId): array
    {
        $reservationStats = $this->getReservationStats($userId);
        $reviewSummary = $this->getReviewSummary($userId);
        $customCount = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_personnalise WHERE user_id = :userId', ['userId' => $userId]);
        $featuredCircuits = $this->connection->fetchAllAssociative(
            'SELECT id_circuit, title, destination, type, duree, image_url, prix_par_personne, note_moyenne, nb_avis, popularite_score
             FROM circuit
             WHERE status = :status
             ORDER BY popularite_score DESC, note_moyenne DESC
             LIMIT 4',
            ['status' => 'actif']
        );
        $recentReservations = $this->connection->fetchAllAssociative(
            'SELECT r.id, r.status, r.total_price, r.date_depart, c.id_circuit, c.title, c.destination
             FROM circuit_reservation r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             WHERE r.user_id = :userId
             ORDER BY datetime(r.reserved_at) DESC
             LIMIT 3',
            ['userId' => $userId]
        );
        $recentReviews = $this->connection->fetchAllAssociative(
            'SELECT r.rating, r.comment, r.created_at, c.title
             FROM circuit_review r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             WHERE r.user_id = :userId
             ORDER BY datetime(r.created_at) DESC
             LIMIT 2',
            ['userId' => $userId]
        );
        $upcomingReservation = $this->connection->fetchAssociative(
            'SELECT r.date_depart, c.title, c.destination
             FROM circuit_reservation r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             WHERE r.user_id = :userId AND r.status <> :status
             ORDER BY date(r.date_depart) ASC
             LIMIT 1',
            ['userId' => $userId, 'status' => 'ANNULE']
        );

        $score = ($reservationStats['confirmed'] * 18)
            + ($reviewSummary['total'] * 12)
            + ($customCount * 8)
            + ($reservationStats['spent'] >= 15000 ? 17 : (int) round($reservationStats['spent'] / 1000));

        return [
            'stats' => [
                'reservations' => $reservationStats['total'],
                'spent' => $reservationStats['spent'],
                'reviews' => $reviewSummary['total'],
                'custom' => $customCount,
                'score' => min(999, max(0, $score)),
            ],
            'featuredCircuits' => $featuredCircuits,
            'recentReservations' => $recentReservations,
            'recentReviews' => $recentReviews,
            'highlights' => [
                'offres' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit WHERE status = :status', ['status' => 'actif']),
                'categories' => count($this->getTypes()),
                'support' => '24/7',
                'satisfaction' => $reviewSummary['average'] > 0 ? min(100, (int) round($reviewSummary['average'] * 20)) : 100,
            ],
            'loyalty' => [
                'tier' => $score >= 220 ? 'Gold' : ($score >= 160 ? 'Silver' : 'Bronze'),
                'points' => min(999, ($score * 3)),
                'status' => 'Connecté',
            ],
            'upcomingReservation' => $upcomingReservation,
        ];
    }

    public function getAdminDashboard(): array
    {
        $circuitsCount = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit');
        $activeCircuits = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit WHERE status = :status', ['status' => 'actif']);
        $inactiveCircuits = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit WHERE status <> :status', ['status' => 'actif']);
        $reservationCount = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation');
        $reviewCount = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review');
        $revenue = (float) $this->connection->fetchOne('SELECT COALESCE(SUM(total_price), 0) FROM circuit_reservation WHERE status <> :status', ['status' => 'ANNULE']);
        $travellers = (int) $this->connection->fetchOne('SELECT COALESCE(SUM(nb_travelers), 0) FROM circuit_reservation WHERE status <> :status', ['status' => 'ANNULE']);
        $averageRating = (float) $this->connection->fetchOne('SELECT COALESCE(ROUND(AVG(rating), 1), 0) FROM circuit_review');
        $pendingCustom = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_personnalise WHERE statut = :status', ['status' => 'SOUMIS']);

        $statusDistribution = [
            'CONFIRME' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE status = :status', ['status' => 'CONFIRME']),
            'EN_ATTENTE' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE status = :status', ['status' => 'EN_ATTENTE']),
            'ANNULE' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE status = :status', ['status' => 'ANNULE']),
        ];

        $monthlyRows = $this->connection->fetchAllAssociative(
            "SELECT strftime('%Y', reserved_at) AS year_num, strftime('%m', reserved_at) AS month_num, COUNT(*) AS total
             FROM circuit_reservation
             WHERE datetime(reserved_at) >= datetime('now', '-5 months')
             GROUP BY year_num, month_num
             ORDER BY year_num, month_num
             LIMIT 6"
        );
        $monthlyReservations = array_map(function (array $row): array {
            $monthLabel = $this->monthLabel((int) $row['month_num']);

            return [
                'month_label' => $monthLabel,
                'total' => (int) $row['total'],
            ];
        }, $monthlyRows);

        $weeklyActivity = $this->buildWeeklyActivity();

        $recentReservations = $this->connection->fetchAllAssociative(
            'SELECT r.id, r.status, r.total_price, r.nb_travelers, r.reserved_at, c.title, c.destination,
                    u.prenom, u.nom
             FROM circuit_reservation r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             LEFT JOIN user u ON u.id = r.user_id
             ORDER BY datetime(r.reserved_at) DESC
             LIMIT 6'
        );

        $topDestinations = $this->connection->fetchAllAssociative(
            'SELECT c.destination, COUNT(r.id) AS total
             FROM circuit_reservation r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             GROUP BY c.destination
             ORDER BY total DESC, c.destination ASC
             LIMIT 5'
        );

        $topCircuits = $this->connection->fetchAllAssociative(
            'SELECT id_circuit, title, destination, note_moyenne, nb_avis, popularite_score
             FROM circuit
             ORDER BY note_moyenne DESC, popularite_score DESC
             LIMIT 5'
        );

        $nextReminder = $this->connection->fetchAssociative(
            'SELECT title, destination, start_date
             FROM circuit
             WHERE status = :status
             ORDER BY date(start_date) ASC, popularite_score DESC
             LIMIT 1',
            ['status' => 'actif']
        ) ?: ['title' => 'Aucun départ planifié', 'destination' => '—', 'start_date' => null];

        $completionPercent = $reservationCount > 0 ? (int) round(($statusDistribution['CONFIRME'] / $reservationCount) * 100) : 0;

        return [
            'global' => [
                'circuits' => $circuitsCount,
                'activeCircuits' => $activeCircuits,
                'inactiveCircuits' => $inactiveCircuits,
                'pendingCustom' => $pendingCustom,
                'reservations' => $reservationCount,
                'reviews' => $reviewCount,
                'revenue' => $revenue,
                'travellers' => $travellers,
                'averageRating' => $averageRating,
            ],
            'statusDistribution' => $statusDistribution,
            'monthlyReservations' => $monthlyReservations,
            'weeklyActivity' => $weeklyActivity,
            'recentReservations' => $recentReservations,
            'topDestinations' => $topDestinations,
            'topCircuits' => $topCircuits,
            'reminder' => [
                'title' => $nextReminder['title'],
                'subtitle' => trim(($nextReminder['destination'] ?? '') . ' · départ ' . ($nextReminder['start_date'] ?? 'à planifier')),
            ],
            'completionPercent' => $completionPercent,
        ];
    }

    public function getAdminCircuitStats(): array
    {
        return [
            'total' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit'),
            'active' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit WHERE status = :status', ['status' => 'actif']),
            'inactive' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit WHERE status <> :status', ['status' => 'actif']),
            'avgPrice' => (float) $this->connection->fetchOne('SELECT COALESCE(AVG(prix_par_personne), 0) FROM circuit'),
        ];
    }

    public function getAdminCircuitOptions(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id_circuit, title, destination, prix_par_personne, type
             FROM circuit
             ORDER BY title ASC, id_circuit ASC'
        );
    }

    public function getAdminUsers(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, prenom, nom, email
             FROM user
             ORDER BY prenom ASC, nom ASC, id ASC'
        );
    }

    public function getAdminCircuits(AdminCircuitFilterInput $input): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($input->q) {
            $where[] = '(LOWER(c.title) LIKE :q OR LOWER(c.destination) LIKE :q OR LOWER(c.description) LIKE :q OR LOWER(c.type) LIKE :q)';
            $params['q'] = '%' . mb_strtolower($input->q) . '%';
        }

        if ($input->type !== 'Tous') {
            $where[] = 'c.type = :type';
            $params['type'] = $input->type;
        }

        if ($input->status !== 'Tous') {
            $where[] = 'c.status = :status';
            $params['status'] = $input->status;
        }

        $orderBy = match ($input->sort) {
            'prix_asc' => 'c.prix_par_personne ASC, c.title ASC',
            'prix_desc' => 'c.prix_par_personne DESC, c.title ASC',
            'note_desc' => 'c.note_moyenne DESC, c.nb_avis DESC',
            'popularite' => 'c.popularite_score DESC, c.note_moyenne DESC',
            default => 'datetime(c.created_at) DESC, c.id_circuit DESC',
        };

        $sql = sprintf(
            'SELECT c.id_circuit, c.title, c.destination, c.description, c.type, c.duree, c.image_url, c.prix_par_personne,
                    c.note_moyenne, c.nb_avis, c.popularite_score, c.start_date, c.status, c.budget,
                    (SELECT COUNT(*) FROM circuit_reservation r WHERE r.id_circuit = c.id_circuit) AS reservation_count
             FROM circuit c
             WHERE %s
             ORDER BY %s',
            implode(' AND ', $where),
            $orderBy
        );

        return $this->connection->fetchAllAssociative($sql, $params);
    }

    public function getAdminCircuitById(int $id): ?array
    {
        $circuit = $this->connection->fetchAssociative('SELECT * FROM circuit WHERE id_circuit = :id', ['id' => $id]);

        return $circuit ?: null;
    }

    public function createAdminCircuit(AdminCircuitData $dto): int
    {
        $payload = [
            'title' => $dto->title,
            'destination' => $dto->destination,
            'description' => $dto->description,
            'type' => $dto->type,
            'duree' => $dto->duree,
            'image_url' => $dto->imageUrl,
            'prix_par_personne' => $dto->prixParPersonne,
            'note_moyenne' => 0,
            'nb_avis' => 0,
            'popularite_score' => $dto->populariteScore,
            'start_date' => $dto->startDate,
            'status' => $dto->status,
            'budget' => $dto->budget > 0 ? $dto->budget : $dto->prixParPersonne,
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];

        $this->connection->insert('circuit', $payload);

        return (int) $this->connection->lastInsertId();
    }

    public function updateAdminCircuit(int $id, AdminCircuitData $dto): void
    {
        $payload = [
            'title' => $dto->title,
            'destination' => $dto->destination,
            'description' => $dto->description,
            'type' => $dto->type,
            'duree' => $dto->duree,
            'image_url' => $dto->imageUrl,
            'prix_par_personne' => $dto->prixParPersonne,
            'popularite_score' => $dto->populariteScore,
            'start_date' => $dto->startDate,
            'status' => $dto->status,
            'budget' => $dto->budget > 0 ? $dto->budget : $dto->prixParPersonne,
        ];

        $this->connection->update('circuit', $payload, ['id_circuit' => $id]);
    }

    public function deleteAdminCircuit(int $id): array
    {
        $reservationCount = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE id_circuit = :id', ['id' => $id]);
        $reviewCount = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review WHERE id_circuit = :id', ['id' => $id]);

        if ($reservationCount > 0 || $reviewCount > 0) {
            return [
                'deleted' => false,
                'message' => 'Suppression bloquée : ce pack possède déjà des réservations ou des avis.',
            ];
        }

        $this->connection->delete('jour_circuit', ['id_circuit' => $id]);
        $this->connection->delete('circuit', ['id_circuit' => $id]);

        return [
            'deleted' => true,
            'message' => 'Le pack a été supprimé avec succès.',
        ];
    }

    public function getAdminReservationStats(): array
    {
        return [
            'total' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation'),
            'confirmed' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE status = :status', ['status' => 'CONFIRME']),
            'pending' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE status = :status', ['status' => 'EN_ATTENTE']),
            'cancelled' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE status = :status', ['status' => 'ANNULE']),
            'revenue' => (float) $this->connection->fetchOne('SELECT COALESCE(SUM(total_price), 0) FROM circuit_reservation WHERE status <> :status', ['status' => 'ANNULE']),
            'travellers' => (int) $this->connection->fetchOne('SELECT COALESCE(SUM(nb_travelers), 0) FROM circuit_reservation WHERE status <> :status', ['status' => 'ANNULE']),
        ];
    }

    public function getAdminReservations(AdminReservationFilterInput $input): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($input->q) {
            $where[] = '(LOWER(c.title) LIKE :q OR LOWER(c.destination) LIKE :q OR LOWER(r.status) LIKE :q OR LOWER(COALESCE(u.prenom, "") || " " || COALESCE(u.nom, "")) LIKE :q)';
            $params['q'] = '%' . mb_strtolower($input->q) . '%';
        }

        if ($input->status !== 'Tous') {
            $where[] = 'r.status = :status';
            $params['status'] = $input->status;
        }

        $sql = sprintf(
            'SELECT r.id, r.id_circuit, r.user_id, r.nb_travelers, r.total_price, r.status, r.reserved_at, r.date_depart,
                    c.title, c.destination, c.prix_par_personne,
                    u.prenom, u.nom, u.email
             FROM circuit_reservation r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             LEFT JOIN user u ON u.id = r.user_id
             WHERE %s
             ORDER BY datetime(r.reserved_at) DESC',
            implode(' AND ', $where)
        );

        return $this->connection->fetchAllAssociative($sql, $params);
    }

    public function getAdminReservationById(int $id): ?array
    {
        $reservation = $this->connection->fetchAssociative('SELECT * FROM circuit_reservation WHERE id = :id', ['id' => $id]);

        return $reservation ?: null;
    }

    public function createAdminReservation(\App\Dto\AdminReservationData $dto): int
    {
        $circuit = $this->connection->fetchAssociative('SELECT id_circuit, prix_par_personne, destination, type FROM circuit WHERE id_circuit = :id', ['id' => $dto->circuitId]);
        if (!$circuit) {
            throw new \RuntimeException('Le pack sélectionné est introuvable.');
        }

        $userExists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM user WHERE id = :id', ['id' => $dto->userId]);
        if ($userExists === 0) {
            throw new \RuntimeException('Le client sélectionné est introuvable.');
        }

        $pricing = $this->calculateReservation((float) $circuit['prix_par_personne'], $dto->nbTravelers);

        $this->connection->insert('circuit_reservation', [
            'id_circuit' => $dto->circuitId,
            'user_id' => $dto->userId,
            'nb_travelers' => $dto->nbTravelers,
            'total_price' => $pricing['total'],
            'status' => $dto->status,
            'date_depart' => $dto->dateDepart,
            'meteo_info' => $this->buildWeatherSummary((string) $circuit['destination'], $dto->dateDepart),
            'plan_b' => $this->buildPlanB((string) $circuit['destination'], (string) $circuit['type']),
            'reserved_at' => $dto->reservedAt ? str_replace('T', ' ', $dto->reservedAt) . ':00' : (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function updateAdminReservation(int $id, \App\Dto\AdminReservationData $dto): void
    {
        $reservation = $this->getAdminReservationById($id);
        if (!$reservation) {
            throw new \RuntimeException('Réservation introuvable.');
        }

        $circuit = $this->connection->fetchAssociative('SELECT id_circuit, prix_par_personne, destination, type FROM circuit WHERE id_circuit = :id', ['id' => $dto->circuitId]);
        if (!$circuit) {
            throw new \RuntimeException('Le pack sélectionné est introuvable.');
        }

        $userExists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM user WHERE id = :id', ['id' => $dto->userId]);
        if ($userExists === 0) {
            throw new \RuntimeException('Le client sélectionné est introuvable.');
        }

        $pricing = $this->calculateReservation((float) $circuit['prix_par_personne'], $dto->nbTravelers);

        $this->connection->update('circuit_reservation', [
            'id_circuit' => $dto->circuitId,
            'user_id' => $dto->userId,
            'nb_travelers' => $dto->nbTravelers,
            'total_price' => $pricing['total'],
            'status' => $dto->status,
            'date_depart' => $dto->dateDepart,
            'meteo_info' => $this->buildWeatherSummary((string) $circuit['destination'], $dto->dateDepart),
            'plan_b' => $this->buildPlanB((string) $circuit['destination'], (string) $circuit['type']),
            'reserved_at' => $dto->reservedAt ? str_replace('T', ' ', $dto->reservedAt) . ':00' : ($reservation['reserved_at'] ?? (new \DateTimeImmutable())->format('Y-m-d H:i:s')),
        ], ['id' => $id]);
    }

    public function deleteAdminReservation(int $id): void
    {
        $this->connection->delete('circuit_reservation', ['id' => $id]);
    }

    public function getAdminReviewSummary(): array
    {
        $total = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review');
        $average = (float) $this->connection->fetchOne('SELECT COALESCE(ROUND(AVG(rating), 1), 0) FROM circuit_review');
        $verified = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit_review WHERE verified_purchase = 1');
        $helpful = (int) $this->connection->fetchOne('SELECT COALESCE(SUM(helpful_count), 0) FROM circuit_review');

        return compact('total', 'average', 'verified', 'helpful');
    }

    public function getAdminReviews(AdminReviewFilterInput $input): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($input->q) {
            $where[] = '(LOWER(c.title) LIKE :q OR LOWER(c.destination) LIKE :q OR LOWER(r.comment) LIKE :q OR LOWER(COALESCE(u.prenom, "") || " " || COALESCE(u.nom, "")) LIKE :q)';
            $params['q'] = '%' . mb_strtolower($input->q) . '%';
        }

        if ($input->rating !== 'Tous') {
            $where[] = 'r.rating = :rating';
            $params['rating'] = (int) $input->rating;
        }

        $sql = sprintf(
            'SELECT r.id, r.id_circuit, r.user_id, r.comment, r.rating, r.helpful_count, r.verified_purchase, r.created_at,
                    c.title, c.destination,
                    u.prenom, u.nom, u.email
             FROM circuit_review r
             INNER JOIN circuit c ON c.id_circuit = r.id_circuit
             LEFT JOIN user u ON u.id = r.user_id
             WHERE %s
             ORDER BY datetime(r.created_at) DESC',
            implode(' AND ', $where)
        );

        return $this->connection->fetchAllAssociative($sql, $params);
    }

    public function getAdminReviewById(int $id): ?array
    {
        $review = $this->connection->fetchAssociative('SELECT * FROM circuit_review WHERE id = :id', ['id' => $id]);

        return $review ?: null;
    }

    public function createAdminReview(\App\Dto\AdminReviewData $dto): int
    {
        $circuitExists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit WHERE id_circuit = :id', ['id' => $dto->circuitId]);
        if ($circuitExists === 0) {
            throw new \RuntimeException('Le pack sélectionné est introuvable.');
        }

        $userExists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM user WHERE id = :id', ['id' => $dto->userId]);
        if ($userExists === 0) {
            throw new \RuntimeException('Le client sélectionné est introuvable.');
        }

        $this->connection->insert('circuit_review', [
            'id_circuit' => $dto->circuitId,
            'user_id' => $dto->userId,
            'comment' => $dto->comment,
            'rating' => $dto->rating,
            'helpful_count' => $dto->helpfulCount,
            'verified_purchase' => $dto->verifiedPurchase ? 1 : 0,
            'created_at' => $dto->createdAt ? str_replace('T', ' ', $dto->createdAt) . ':00' : (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        $this->refreshCircuitReviewStats($dto->circuitId);

        return (int) $this->connection->lastInsertId();
    }

    public function updateAdminReview(int $id, \App\Dto\AdminReviewData $dto): void
    {
        $review = $this->getAdminReviewById($id);
        if (!$review) {
            throw new \RuntimeException('Avis introuvable.');
        }

        $circuitExists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM circuit WHERE id_circuit = :id', ['id' => $dto->circuitId]);
        if ($circuitExists === 0) {
            throw new \RuntimeException('Le pack sélectionné est introuvable.');
        }

        $userExists = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM user WHERE id = :id', ['id' => $dto->userId]);
        if ($userExists === 0) {
            throw new \RuntimeException('Le client sélectionné est introuvable.');
        }

        $oldCircuitId = (int) $review['id_circuit'];

        $this->connection->update('circuit_review', [
            'id_circuit' => $dto->circuitId,
            'user_id' => $dto->userId,
            'comment' => $dto->comment,
            'rating' => $dto->rating,
            'helpful_count' => $dto->helpfulCount,
            'verified_purchase' => $dto->verifiedPurchase ? 1 : 0,
            'created_at' => $dto->createdAt ? str_replace('T', ' ', $dto->createdAt) . ':00' : ($review['created_at'] ?? (new \DateTimeImmutable())->format('Y-m-d H:i:s')),
        ], ['id' => $id]);

        $this->refreshCircuitReviewStats($oldCircuitId);
        if ($oldCircuitId !== $dto->circuitId) {
            $this->refreshCircuitReviewStats($dto->circuitId);
        }
    }

    public function deleteAdminReview(int $id): void
    {
        $review = $this->getAdminReviewById($id);
        if (!$review) {
            return;
        }

        $this->connection->delete('circuit_review', ['id' => $id]);
        $this->refreshCircuitReviewStats((int) $review['id_circuit']);
    }

    private function refreshCircuitReviewStats(int $circuitId): void
    {
        $stats = $this->connection->fetchAssociative(
            'SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total_reviews
             FROM circuit_review
             WHERE id_circuit = :id',
            ['id' => $circuitId]
        ) ?: ['avg_rating' => 0, 'total_reviews' => 0];

        $this->connection->update('circuit', [
            'note_moyenne' => (float) ($stats['avg_rating'] ?? 0),
            'nb_avis' => (int) ($stats['total_reviews'] ?? 0),
        ], ['id_circuit' => $circuitId]);
    }

    private function buildWeeklyActivity(): array
    {
        $raw = $this->connection->fetchAllAssociative(
            "SELECT strftime('%w', reserved_at) AS day_num, COUNT(*) AS total
             FROM circuit_reservation
             WHERE datetime(reserved_at) >= datetime('now', '-6 days')
             GROUP BY day_num"
        );

        $indexed = [];
        foreach ($raw as $row) {
            $indexed[(int) $row['day_num']] = (int) $row['total'];
        }

        $labels = [0 => 'S', 1 => 'M', 2 => 'T', 3 => 'W', 4 => 'T', 5 => 'F', 6 => 'S'];
        $days = [];
        foreach ($labels as $index => $label) {
            $days[] = [
                'label' => $label,
                'total' => $indexed[$index] ?? 0,
                'highlight' => in_array($index, [1, 2, 3], true),
            ];
        }

        return $days;
    }

    private function monthLabel(int $month): string
    {
        $labels = [
            1 => 'Jan',
            2 => 'Fév',
            3 => 'Mar',
            4 => 'Avr',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juil',
            8 => 'Août',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Déc',
        ];

        return $labels[$month] ?? '--';
    }

    private function buildWeatherSummary(string $destination, ?string $date): string
    {
        $month = $date ? (int) date('n', strtotime($date)) : (int) date('n');
        $temp = match (true) {
            $month <= 2 => '18°C',
            $month <= 5 => '24°C',
            $month <= 8 => '30°C',
            default => '22°C',
        };

        $destinationLower = mb_strtolower($destination);
        $condition = match (true) {
            str_contains($destinationLower, 'bali') => 'ciel dégagé, humidité 55%',
            str_contains($destinationLower, 'tokyo') => 'temps chaud, météo stable',
            str_contains($destinationLower, 'paris') => 'ciel dégagé, vent léger',
            str_contains($destinationLower, 'istanbul') => 'temps sec, soirées douces',
            str_contains($destinationLower, 'marrakech') => 'ensoleillé, air sec',
            default => 'conditions favorables pour les visites',
        };

        return sprintf('🌤️ %s | %s | %s', $destination ?: 'Destination', $temp, $condition);
    }

    private function buildPlanB(string $destination, string $style): string
    {
        $destination = trim($destination);

        return sprintf('✅ Plan B prêt pour %s — alternatives indoor, transferts flexibles et activités %s en cas d’imprévu météo.', $destination !== '' ? $destination : 'votre séjour', mb_strtolower($style));
    }
}

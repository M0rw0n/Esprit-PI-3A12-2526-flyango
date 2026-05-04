<?php

namespace App\Service;

<<<<<<< HEAD
use App\Service\Api\MistralService;
use App\Service\Api\SherpaService;

class CircuitAiPlanner
{
    public function __construct(
        private readonly MistralService $mistralService,
        private readonly SherpaService $sherpaService
    ) {}

    private const TUNISIA_LOCATIONS = [
        'tunis' => ['lat' => 36.8065, 'lng' => 10.1815, 'region' => 'Tunis'],
        'djerba' => ['lat' => 33.8074, 'lng' => 10.8588, 'region' => 'Médenine'],
        'sousse' => ['lat' => 35.8254, 'lng' => 10.6087, 'region' => 'Sousse'],
        'hammamet' => ['lat' => 36.4071, 'lng' => 10.6198, 'region' => 'Nabeul'],
        'sfax' => ['lat' => 34.7406, 'lng' => 10.7603, 'region' => 'Sfax'],
        'kairouan' => ['lat' => 35.6741, 'lng' => 10.1033, 'region' => 'Kairouan'],
        'tozeur' => ['lat' => 33.9197, 'lng' => 8.1335, 'region' => 'Tozeur'],
        'douz' => ['lat' => 33.4613, 'lng' => 9.0286, 'region' => 'Kébili'],
        'matmata' => ['lat' => 33.5428, 'lng' => 9.9688, 'region' => 'Gabès'],
        'monastir' => ['lat' => 35.7826, 'lng' => 10.8263, 'region' => 'Monastir'],
        'mahdia' => ['lat' => 35.5037, 'lng' => 11.0622, 'region' => 'Mahdia'],
        'gabès' => ['lat' => 33.8815, 'lng' => 10.0986, 'region' => 'Gabès'],
        'bizerte' => ['lat' => 37.2744, 'lng' => 9.7939, 'region' => 'Bizerte'],
        'tabarka' => ['lat' => 36.9554, 'lng' => 8.7580, 'region' => 'Jendouba'],
        'nabeul' => ['lat' => 36.4561, 'lng' => 10.7376, 'region' => 'Nabeul'],
        'kelibia' => ['lat' => 36.8491, 'lng' => 11.0934, 'region' => 'Nabeul'],
        'tataouine' => ['lat' => 32.9300, 'lng' => 10.4518, 'region' => 'Tataouine'],
        'chenini' => ['lat' => 32.9808, 'lng' => 10.0663, 'region' => 'Tataouine'],
        'carthage' => ['lat' => 36.8525, 'lng' => 10.3233, 'region' => 'Tunis'],
        'sidi bou said' => ['lat' => 36.7279, 'lng' => 10.3353, 'region' => 'Tunis'],
        'ksar ghilane' => ['lat' => 32.9808, 'lng' => 10.0663, 'region' => 'Tataouine'],
        'chebika' => ['lat' => 35.8637, 'lng' => 10.3182, 'region' => 'Sidi Bouzid'],
        'tamerza' => ['lat' => 34.4133, 'lng' => 7.9183, 'region' => 'Tozeur'],
        'mides' => ['lat' => 34.4536, 'lng' => 7.9183, 'region' => 'Tozeur'],
        'bardo' => ['lat' => 36.8103, 'lng' => 10.1340, 'region' => 'Tunis'],
    ];

    private const POINTS_INTERET = [
        'Djerba' => [
            ['nom' => 'Houmt Souk', 'desc' => 'Le cœur historique de Djerba avec ses ruelles étroites et ses souks colorés', 'type' => 'Médina', 'duree' => '2-3h', 'lat' => 33.8754, 'lng' => 10.8603],
            ['nom' => 'Synagogue de la Ghriba', 'desc' => 'Plus ancienne synagogue d\'Afrique, lieu de pèlerinage annuel', 'type' => 'Religieux', 'duree' => '1h', 'lat' => 33.2804, 'lng' => 11.0206],
            ['nom' => 'Musée de Guellala', 'desc' => 'Musée de la poterie et des traditions djerbiennes', 'type' => 'Musée', 'duree' => '1-2h', 'lat' => 33.4977, 'lng' => 10.8937],
            ['nom' => 'Village de Guellala', 'desc' => 'Village artisanal célèbre pour sa poterie traditionnelle', 'type' => 'Artisanat', 'duree' => '1h', 'lat' => 33.4977, 'lng' => 10.8937],
            ['nom' => 'Plage de Sidi Mahrez', 'desc' => 'Magnifique plage de sable fin aux eaux cristallines', 'type' => 'Plage', 'duree' => '3-4h', 'lat' => 33.8089, 'lng' => 10.9612],
            ['nom' => 'Fort Ghazi Mustapha', 'desc' => 'Forteresse historique avec vue panoramique sur la mer', 'type' => 'Historique', 'duree' => '1h', 'lat' => 33.8797, 'lng' => 10.8549],
            ['nom' => 'Djerbahood', 'desc' => 'Village djerbien transformé en galerie d\'art urbain international', 'type' => 'Art', 'duree' => '2h', 'lat' => 33.2704, 'lng' => 11.0206],
            ['nom' => 'Parc Djerba Explore', 'desc' => 'Ferme aux crocodiles et village troglodyte reconstitué', 'type' => 'Famille', 'duree' => '3h', 'lat' => 33.5141, 'lng' => 10.9179],
            ['nom' => 'Lagune de Djerba', 'desc' => 'Zone naturelle protégée idéale pour les excursions en bateau', 'type' => 'Nature', 'duree' => '2h', 'lat' => 33.7500, 'lng' => 10.8500],
            ['nom' => 'Cité romaine de Meninx', 'desc' => 'Vestiges archéologiques de l\'ancienne cité punique et romaine', 'type' => 'Archéologie', 'duree' => '1-2h', 'lat' => 33.6204, 'lng' => 10.7506],
            ['nom' => 'Centre équestre', 'desc' => 'Balade à cheval sur la plage au coucher du soleil', 'type' => 'Sport', 'duree' => '2h', 'lat' => 33.8089, 'lng' => 10.9612],
            ['nom' => 'Aqua Park Djerba', 'desc' => 'Parc aquatique pour toute la famille', 'type' => 'Famille', 'duree' => '4h', 'lat' => 33.5077, 'lng' => 10.8907],
            ['nom' => 'Marché nocturne', 'desc' => 'Marché nocturne en été avec musique et artisanat', 'type' => 'Local', 'duree' => '2h', 'lat' => 33.8754, 'lng' => 10.8603],
            ['nom' => 'Café loungen', 'desc' => 'Cafés en bord de mer avec chicha', 'type' => 'Détente', 'duree' => '2h', 'lat' => 33.8754, 'lng' => 10.8603],
            ['nom' => 'Djerba Golf Club', 'desc' => 'Parcours de golf 18 trous face à la mer', 'type' => 'Sport', 'duree' => '4h', 'lat' => 33.5177, 'lng' => 10.8907],
        ],
        'Tunis' => [
            ['nom' => 'Médina de Tunis', 'desc' => 'Vieille ville historique classée UNESCO', 'type' => 'Médina', 'duree' => '3-4h', 'lat' => 36.7783, 'lng' => 10.1656],
            ['nom' => 'Musée du Bardo', 'desc' => 'Le plus grand musée de mosaïques romaines', 'type' => 'Musée', 'duree' => '2-3h', 'lat' => 36.8103, 'lng' => 10.1340],
            ['nom' => 'Avenue Habib Bourguiba', 'desc' => 'La plus célèbre avenue de Tunis', 'type' => 'Promenade', 'duree' => '1h', 'lat' => 36.8065, 'lng' => 10.1815],
            ['nom' => 'Carthage', 'desc' => 'Sites archéologiques puniques et romains', 'type' => 'Historique', 'duree' => '3h', 'lat' => 36.8525, 'lng' => 10.3233],
            ['nom' => 'Sidi Bou Saïd', 'desc' => 'Le plus beau village de Tunisie', 'type' => 'Culture', 'duree' => '2h', 'lat' => 36.7279, 'lng' => 10.3353],
        ],
        'Hammamet' => [
            ['nom' => 'Médina de Hammamet', 'desc' => 'Petite médina pittoresque', 'type' => 'Médina', 'duree' => '1-2h', 'lat' => 36.4071, 'lng' => 10.6198],
            ['nom' => 'Kasbah de Hammamet', 'desc' => 'Ancienne forteresse avec vue sur la baie', 'type' => 'Historique', 'duree' => '1h', 'lat' => 36.4115, 'lng' => 10.6231],
            ['nom' => 'Plage de Hammamet', 'desc' => 'L\'une des plus belles plages de Tunisie', 'type' => 'Plage', 'duree' => '3-4h', 'lat' => 36.4071, 'lng' => 10.6198],
            ['nom' => 'Safari Parc', 'desc' => 'Excursion en jeep dans les collines', 'type' => 'Aventure', 'duree' => '4h', 'lat' => 36.4500, 'lng' => 10.6000],
        ],
        'Sousse' => [
            ['nom' => 'Ribat de Sousse', 'desc' => 'Forteresse byzantine du VIIIe siècle', 'type' => 'Historique', 'duree' => '1-2h', 'lat' => 35.8254, 'lng' => 10.6087],
            ['nom' => 'Médina de Sousse', 'desc' => 'Labyrinthe de ruelles avec artisans', 'type' => 'Médina', 'duree' => '2-3h', 'lat' => 35.8254, 'lng' => 10.6087],
            ['nom' => 'Catacombes de Sousse', 'desc' => 'Galeries souterraines romaines', 'type' => 'Archéologie', 'duree' => '1h', 'lat' => 35.8312, 'lng' => 10.6151],
        ],
        'Kairouan' => [
            ['nom' => 'Grande Mosquée de Kairouan', 'desc' => 'La plus prestigieuse mosquée de Tunisie', 'type' => 'Religieux', 'duree' => '1-2h', 'lat' => 35.6741, 'lng' => 10.1033],
            ['nom' => 'Mausolée de Sidi Sahab', 'desc' => 'Sanctuaire aux portes sculptées', 'type' => 'Religieux', 'duree' => '45min', 'lat' => 35.6761, 'lng' => 10.1043],
            ['nom' => 'Souk des Teinturiers', 'desc' => 'Teintureries traditionnelles', 'type' => 'Artisanat', 'duree' => '45min', 'lat' => 35.6741, 'lng' => 10.1033],
        ],
        'Tozeur' => [
            ['nom' => 'Oasis de Tozeur', 'desc' => 'Magnifique oasis de dattiers', 'type' => 'Nature', 'duree' => '2h', 'lat' => 33.9197, 'lng' => 8.1335],
            ['nom' => 'Chott el Jerid', 'desc' => 'Le plus grand chott salé du Sahara', 'type' => 'Nature', 'duree' => '2h', 'lat' => 33.6500, 'lng' => 8.3500],
            ['nom' => 'Palmeraie de Tozeur', 'desc' => 'Vaste palmeraie de 200 000 dattiers', 'type' => 'Nature', 'duree' => '1-2h', 'lat' => 33.9197, 'lng' => 8.1335],
        ],
        'Tataouine' => [
            ['nom' => 'Ksar de Tataouine', 'desc' => 'Le plus grand ksar de Tunisie', 'type' => 'Historique', 'duree' => '2h', 'lat' => 32.9300, 'lng' => 10.4518],
            ['nom' => 'Ksar Ouled Soltane', 'desc' => 'Le plus photogénique, Star Wars', 'type' => 'Historique', 'duree' => '1h', 'lat' => 32.9808, 'lng' => 10.0663],
            ['nom' => 'Chenini', 'desc' => 'Village troglodyte sur une colline', 'type' => 'Culture', 'duree' => '2h', 'lat' => 32.9808, 'lng' => 10.0663],
        ],
    ];

    private const ACTIVITES_PAR_STYLE = [
        'Aventure' => ['Safari en jeep', 'Randonnée dans les montagnes', 'Quad dans le désert', 'Camping sous les étoiles', 'Balade en dromadaire'],
        'Romantique' => ['Dîner aux chandelles', 'Coucher de soleil en bord de mer', 'Spa et bien-être', 'Balade en bateau', 'Picnic dans une oasis'],
        'Famille' => ['Visite des sites historiques', 'Parcs et plages', 'Ateliers artisanat', 'Sports nautiques', 'Excursion en famille'],
        'Découverte' => ['Visites guidées', 'Marchés locaux', 'Musées', 'Patrimoine architectural', 'Gastronomie locale'],
        'Détente' => ['Journées spa', 'Plages paisibles', 'Yoga au bord de l\'eau', 'Massages', 'Repos total'],
        'Culturel' => ['Visites de médinas', 'Sites archéologiques', 'Ateliers d\'artisans', 'Histoire locale', 'Rencontres avec les habitants'],
    ];

=======
class CircuitAiPlanner
{
>>>>>>> testsisi
    public function generate(array $data): array
    {
        $destination = trim((string) ($data['destination'] ?? 'Destination'));
        $depart = trim((string) ($data['depart'] ?? 'Tunis'));
        $style = trim((string) ($data['style'] ?? 'Découverte'));
        $budget = trim((string) ($data['budget'] ?? 'Moyen'));
        $participants = max(1, (int) ($data['participants'] ?? 2));
        $jours = max(1, (int) ($data['jours'] ?? 3));
        $dateDepart = (string) ($data['date_depart'] ?? date('Y-m-d'));
        $dateRetour = (string) ($data['date_retour'] ?? date('Y-m-d', strtotime('+' . max(1, $jours - 1) . ' days')));
<<<<<<< HEAD
        $stopsJson = $data['stops'] ?? '[]';
        $stops = json_decode($stopsJson, true) ?: [];

        // Get destination coordinates
        $destCoords = $this->getCoordinates($destination);
        $departCoords = $this->getCoordinates($depart);

        // Difficulty mapping
=======

>>>>>>> testsisi
        $difficulty = match (mb_strtolower($style)) {
            'aventure' => 'Difficile',
            'romantique', 'détente', 'detente' => 'Facile',
            'famille' => 'Facile',
<<<<<<< HEAD
            'culturel' => 'Facile',
            default => 'Modéré',
        };

        // Budget calculation
        $budgetMultiplier = match (mb_strtolower($budget)) {
            'economique', 'économique' => 100,
            'premium', 'luxe' => 300,
            default => 180,
        };

        $hebergementMultiplier = match (mb_strtolower($data['hebergement'] ?? 'hôtel')) {
            'riad' => 1.3,
            'camp' => 0.6,
            'guesthouse' => 0.8,
            default => 1.0,
        };

        $pricePerPerson = $budgetMultiplier * $hebergementMultiplier;
        $totalPrice = round(($pricePerPerson * $jours * $participants) + ($participants * 50), 3);

        $title = sprintf('%s — Circuit %d jours à %s', ucfirst($style), $jours, $destination);

        // Get AI generated program from Mistral
        $aiProgram = $this->mistralService->generateProgram($destination, $data);
        
        // Get detailed Visa and Health requirements for the whole circuit from Sherpa
        $waypoints = array_merge([$depart], $stops, [$destination]);
        $circuitRequirements = $this->sherpaService->getCircuitRequirements($waypoints);

        // Generate detailed daily itinerary
        $dailyPlan = $this->generateDailyItinerary($destination, $depart, $stops, $style, $jours, $budgetMultiplier, $participants, $totalPrice);

        // Generate highlights
        $highlights = $this->generateHighlights($style, $destination);

        // Generate included services
        $included = $this->generateIncludedServices($style, $budget);

        // Calculate estimated distances
        $totalDistance = $this->estimateTotalDistance($depart, $destination, $stops);

        $description = "# 🌍 Circuit sur mesure — {$destination}\n\n";
        $description .= "**{$jours} jours | {$style} | {$budget} | {$participants} voyageurs**\n\n";
        $description .= "**Dates:** Du " . $this->formatDate($dateDepart) . " au " . $this->formatDate($dateRetour) . "\n\n";
        $description .= "---\n\n";
        
        if (strlen($aiProgram) > 100) {
            $description .= "## 🧠 Programme généré par Mistral AI\n\n";
            $description .= $aiProgram . "\n\n";
        } else {
            $description .= implode("", $dailyPlan);
        }

        $description .= "\n---\n\n";
        $description .= "# 🛂 Exigences Voyage & Passages Frontières (Sherpa)\n";
        foreach ($circuitRequirements as $req) {
            $description .= "### 📍 De {$req['origin']} à {$req['destination']}\n";
            $description .= "- **Visa:** {$req['visa']}\n";
            $description .= "- **Santé:** {$req['health']}\n\n";
        }
        $description .= "---\n\n";
        $description .= "# ✅ Inclus dans le circuit\n";
        foreach ($included as $inc) {
            $description .= "- {$inc}\n";
        }
        $description .= "\n---\n\n";
        $description .= "# ℹ️ Informations pratiques\n";
        $description .= "- 📍 **Destination:** {$destination} ({$destCoords['lat']}, {$destCoords['lng']})\n";
        $description .= "- 🚗 **Distance totale:** {$totalDistance} km\n";
        $description .= "- 📅 **Meilleure période:** Toute l'année\n";
        $description .= "- 👥 **Conseillé pour:** " . ucfirst($style) . "\n";
        $description .= "- 💰 **Budget moyen/jour:** " . round($totalPrice / $jours / $participants, 0) . " TND\n\n";
        $description .= "_🧠 Circuit généré automatiquement par Fly&Go IA_";
=======
            default => 'Modéré',
        };

        $budgetMultiplier = match (mb_strtolower($budget)) {
            'economique', 'économique', 'low' => 120,
            'premium', 'haut', 'luxe' => 280,
            default => 180,
        };

        $price = round(($budgetMultiplier * $jours) + ($participants * 35), 3);
        $title = sprintf('%s — %s %d jours', ucfirst($style), $destination, $jours);

        $highlights = match (mb_strtolower($style)) {
            'aventure' => ['roadbook actif', 'expériences outdoor', 'spots photo au lever du soleil'],
            'romantique' => ['hôtel charme', 'coucher de soleil', 'dîner spécial couple'],
            'famille' => ['rythme doux', 'activités adaptées enfants', 'temps libre équilibré'],
            'détente', 'detente' => ['spa & bien-être', 'transferts optimisés', 'temps libre premium'],
            default => ['visites incontournables', 'temps libre', 'bon équilibre entre culture et détente'],
        };

        $dailyPlan = [];
        for ($day = 1; $day <= $jours; $day++) {
            if ($day === 1) {
                $dailyPlan[] = "Jour {$day} — Départ de {$depart}, arrivée à {$destination}, installation et briefing personnalisé.";
            } elseif ($day === $jours) {
                $dailyPlan[] = "Jour {$day} — Dernières découvertes à {$destination}, shopping libre puis retour.";
            } else {
                $dailyPlan[] = "Jour {$day} — Programme {$style} à {$destination} : visite guidée, pause locale et activité recommandée par l'IA.";
            }
        }

        $description = "Circuit sur mesure généré automatiquement pour {$destination}.\n"
            . "Style choisi : {$style}. Budget : {$budget}. Voyageurs : {$participants}.\n"
            . "Période suggérée : du {$dateDepart} au {$dateRetour}.\n\n"
            . "Points forts :\n- " . implode("\n- ", $highlights) . "\n\n"
            . "Programme proposé :\n- " . implode("\n- ", $dailyPlan) . "\n\n"
            . "Conseil IA : prévoyez une marge de temps pour les transferts, gardez une journée flexible et confirmez les disponibilités 48h avant le départ.";
>>>>>>> testsisi

        return [
            'titre' => $title,
            'description' => $description,
            'duree' => $jours . ' jours',
<<<<<<< HEAD
            'prix' => $totalPrice,
=======
            'prix' => $price,
>>>>>>> testsisi
            'difficulte' => $difficulty,
            'depart' => $depart,
            'destination' => $destination,
            'places' => max(2, $participants),
<<<<<<< HEAD
            'latitude' => $destCoords['lat'] ?? 36.8065,
            'longitude' => $destCoords['lng'] ?? 10.1815,
            'stops' => $stops,
            'distance' => $totalDistance,
=======
>>>>>>> testsisi
            'generated_context' => json_encode([
                'destination' => $destination,
                'depart' => $depart,
                'style' => $style,
                'budget' => $budget,
                'participants' => $participants,
                'jours' => $jours,
                'date_depart' => $dateDepart,
                'date_retour' => $dateRetour,
<<<<<<< HEAD
                'stops' => $stops,
                'coordinates' => $destCoords,
                'difficulty' => $difficulty,
                'price_per_person' => $pricePerPerson,
            ], JSON_UNESCAPED_UNICODE),
        ];
    }

    private function getCoordinates(string $location): array
    {
        $key = mb_strtolower(trim($location));
        return self::TUNISIA_LOCATIONS[$key] ?? ['lat' => 36.8065, 'lng' => 10.1815, 'region' => 'Tunisie'];
    }

    private function estimateTotalDistance(string $depart, string $destination, array $stops): int
    {
        $locations = array_merge([$depart], $stops, [$destination]);
        $total = 0;
        
        for ($i = 0; $i < count($locations) - 1; $i++) {
            $coord1 = $this->getCoordinates($locations[$i]);
            $coord2 = $this->getCoordinates($locations[$i + 1]);
            
            $lat1 = deg2rad($coord1['lat']);
            $lat2 = deg2rad($coord2['lat']);
            $lng1 = deg2rad($coord1['lng']);
            $lng2 = deg2rad($coord2['lng']);
            
            $dlat = $lat2 - $lat1;
            $dlng = $lng2 - $lng1;
            
            $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlng / 2) ** 2;
            $c = 2 * asin(sqrt($a));
            $r = 6371; // Earth radius in km
            
            $total += round($c * $r);
        }
        
        return $total;
    }

    private function generateDailyItinerary(string $destination, string $depart, array $stops, string $style, int $jours, int $budgetMultiplier = 180, int $participants = 2, float $totalPrice = 0): array
    {
        $plan = [];
        
        $detailedDays = $this->getDetailedItinerary($destination, $style, $jours);
        
        $plan[] = "# 🗓️ {$jours} JOURS À {$destination}\n\n";
        
        foreach ($detailedDays as $day) {
            $dayNum = $day['num'] ?? 1;
            $plan[] = "## 🏝️ JOUR {$dayNum} — {$day['title']}\n\n";
            
            $plan[] = "### 🌅 MATIN\n\n";
            foreach ($day['morning'] as $place) {
                $plan[] = "📍 **{$place['name']}**\n";
                $plan[] = "_{$place['desc']}_\n";
                $plan[] = "🎯 {$place['detail']}\n\n";
            }
            
            $plan[] = "### 🍽️ DÉJEUNER\n\n";
            $plan[] = "- 🍴 **{$day['lunch']['name']}** — {$day['lunch']['desc']}\n";
            $plan[] = "- 💰 {$day['lunch']['budget']}\n\n";
            
            $plan[] = "### 🌇 APRÈS-MIDI\n\n";
            foreach ($day['afternoon'] as $place) {
                $plan[] = "📍 **{$place['name']}**\n";
                $plan[] = "_{$place['desc']}_\n";
                $plan[] = "🎯 {$place['detail']}\n\n";
            }
            
            $plan[] = "### 🌙 SOIR\n\n";
            $plan[] = "- 🌅 {$day['evening']['sunset']}\n";
            $plan[] = "- ☕ {$day['evening']['chill']}\n";
            $plan[] = "- 🍽️ **{$day['dinner']['name']}** — {$day['dinner']['desc']}\n\n";
        }
        
        $plan[] = "# 💡 CONSEILS PRO\n\n";
        $plan[] = "- 🚗 Louer une voiture pour flexibilité maximale\n";
        $plan[] = "- ☀️ Éviter les sorties 12h–16h en été\n";
        $plan[] = "- 💧 Toujours avoir de l'eau en quantité\n";
        $plan[] = "- 📱 Télécharger Maps.me avec carte离线\n";
        $plan[] = "- 💰 Cash recommandé pour marchés\n";
        $plan[] = "- 📸 Meilleurs moments photo : lever & coucher\n";
        $plan[] = "- 🧴 Crème solaire + chapeau obligatoires\n";
        $plan[] = "- 🍽️ Réserver pour dîners premium\n\n";
        
        $plan[] = "_🧠 Circuit généré automatiquement par Fly&Go IA_";
        
        return $plan;
    }
    
    private function getDetailedItinerary(string $destination, string $style, int $jours): array
    {
        $itineraries = [
            'Djerba' => [
                1 => ['title' => 'Arrivée & Découverte', 'icon' => '🏝️', 'places_summary' => 'Houmt Souk, Port, Corniche',
                    'morning' => [
                        ['name' => 'Houmt Souk — Médina', 'desc' => 'Le cœur historique de Djerba avec ses ruelles étroites et souks colorés', 'detail' => 'Explorer les souks d\'artisanat, bijouterie et poteries. Flâner dans les ruelles jusqu\'au port de pêche.'],
                        ['name' => 'Port de Houmt Souk', 'desc' => 'Port animé où les pêcheurs vendent le poisson frais du jour', 'detail' => 'Observer le débarquement des poissons. Acheter des poissons frais pour le déjeuner.'],
                    ],
                    'lunch' => ['name' => 'Restaurant Le Tornado', 'desc' => 'Poisson grillé frais en bord de mer avec vue panoramique sur Houmt Souk', 'budget' => '💸 30-50 TND'],
                    'afternoon' => [
                        ['name' => 'Plage de Sidi Mahrez', 'desc' => 'Magnifique plage de sable fin aux eaux cristallines turquoise', 'detail' => 'Baignade, bronzette, Location de parasol. Sports nautiques possibles (jet ski, paddle).'],
                        ['name' => 'Centre équestre Djerba', 'desc' => 'Balade à cheval sur la plage au coucher du soleil', 'detail' => 'Activité romantique ou familiale. Réservation recommandée.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil sur la plage de Sidi Mahrez — moment magique', 'chill' => 'Café loungen en bord de mer avec chicha et thé à la menthe'],
                    'dinner' => ['name' => 'Restaurant Essofra', 'desc' => 'Cuisine traditionnelle djerbienne dans une ambiance cosy. Couscous et plats locaux'],
                ],
                2 => ['title' => 'Culture & Histoire', 'icon' => '🏛️', 'places_summary' => 'Synagogue Ghriba, Musée Guellala, Guellala',
                    'morning' => [
                        ['name' => 'Synagogue de la Ghriba', 'desc' => 'Plus ancienne synagogue d\'Afrique, lieu de pèlerinage annuel (juillet)', 'detail' => 'Site historique et spirituel. Tenue correcte exigée. Photos autorisées extérieur.'],
                        ['name' => 'Village de Guellala', 'desc' => 'Village artisanal célèbre pour sa poterie traditionnelle en terre rouge', 'detail' => 'Visiter les ateliers de poterie. Acheter des pièces uniques directement chez l\'artisan.'],
                    ],
                    'lunch' => ['name' => 'Dar Zarrouk', 'desc' => 'Maison traditionnelle djerbienne servant des plats typiques. Cadre authentique', 'budget' => '💸 25-40 TND'],
                    'afternoon' => [
                        ['name' => 'Musée de Guellala', 'desc' => 'Musée de la poterie et des traditions djerbiennes', 'detail' => 'Découvrir l\'histoire de l\'artisanat local. Expositions temporaires intéressantes.'],
                        ['name' => 'Djerbahood — Erriadh', 'desc' => 'Village transformé en galerie d\'art urbain international par des artistes du monde entier', 'detail' => 'Explorer les ruelles et admirer les fresques. Activité photo parfaite.'],
                    ],
                    'evening' => ['sunset' => 'Promenade dans Djerbahood pour capturer les dernières lumières', 'chill' => 'Thé chez l\'habitant à Erriadh. Ambiance authentique garantie.'],
                    'dinner' => ['name' => 'Restaurant Haroun', 'desc' => 'Fruits de mer premium en front de mer. Qualité exceptionnelle. Réservation conseillée'],
                ],
                3 => ['title' => 'Nature & Aventure', 'icon' => '🌿', 'places_summary' => 'Lagune, Quad, Dromadaire',
                    'morning' => [
                        ['name' => 'Lagune de Djerba', 'desc' => 'Zone naturelle protégée entre mer et oasis', 'detail' => 'Excursion en bateau dans la lagune. Observation des oiseaux migrateurs.'],
                        ['name' => 'Excursion Quad/Dromadaire', 'desc' => 'Aventures dans les pistas autour de Djerba', 'detail' => 'Sensations fortes garanties. École de conduite disponible.'],
                    ],
                    'lunch' => ['name' => 'Restaurant La Marmite', 'desc' => 'Cuisine locale dans un cadre ombragé. Plats mijotés et tajines', 'budget' => '💸 20-35 TND'],
                    'afternoon' => [
                        ['name' => 'Oasis de Bergech', 'desc' => 'Petite oasis préservée idéale pour la détente', 'detail' => 'Balade sous les palmiers. Photographies natures.'],
                        ['name' => 'Parc Djerba Explore', 'desc' => 'Ferme aux crocodiles (300+) et village troglodyte reconstitué', 'detail' => 'Activité familiale parfaite. Photographier les reptiles et explorer le village.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil depuis le Fort Ghazi Mustapha', 'chill' => 'Marché nocturne d\'été — musique live et artisanat'],
                    'dinner' => ['name' => 'Restaurant Le Palais', 'desc' => 'Gastronomie fusion tunisienne. Cadre élégant avec vue mer'],
                ],
                4 => ['title' => 'Plages & Détente', 'icon' => '🏖️', 'places_summary' => 'Plages, Spa, Shopping',
                    'morning' => [
                        ['name' => 'Plage de Cedria', 'desc' => 'Plage calme au nord de Djerba, moins fréquentée', 'detail' => 'Baignade tranquille. Ambiance exclusive. Location parasol possible.'],
                        ['name' => 'Centre thalasso & Spa', 'desc' => 'Institut de beauté pour soins et massages relaxants', 'detail' => 'Séance de hammam traditionnel + massage. Réservation obligatoire.'],
                    ],
                    'lunch' => ['name' => 'Beach Club La Calanque', 'desc' => 'Restaurant de plage avec cuisine méditerranéenne. Ambiance branchée', 'budget' => '💸 40-70 TND'],
                    'afternoon' => [
                        ['name' => 'Shopping souks Houmt Souk', 'desc' => 'Les meilleurs souks de Djerba pour artisanat et souvenirs', 'detail' => 'Négocier les prix. Acheter : poteries, tapis, bijouterie, épices, dattes.'],
                        ['name' => 'Marché central', 'desc' => 'Marché local pour produits frais et épices', 'detail' => 'Dattes de Djerba, huile d\'olive, herbes aromatiques. Bons prix.'],
                    ],
                    'evening' => ['sunset' => 'Promenade romantique en bord de mer à Houmt Souk', 'chill' => 'Café Le Petit Navire — vue port + chicha + atmosphère locale'],
                    'dinner' => ['name' => 'Restaurant L\'Olivier', 'desc' => 'Cuisine française en pareja. Cadre intime avec jardin'],
                ],
                5 => ['title' => 'Patrimoine & Traditions', 'icon' => '🕌', 'places_summary' => 'Cité Meninx, Fort, Hôtels troglodytes',
                    'morning' => [
                        ['name' => 'Cité romaine de Meninx', 'desc' => 'Vestiges archéologiques de l\'ancienne cité punique et romaine', 'detail' => ' mosaïques, thermes, théâtre. Site en cours de fouilles.'],
                        ['name' => 'Fort Ghazi Mustapha', 'desc' => 'Forteresse historique avec vue panoramique sur la mer', 'detail' => 'Histoire militaire ottomane. Photos exceptionnelles depuis les remparts.'],
                    ],
                    'lunch' => ['name' => 'Restaurant Chez Amor', 'desc' => 'Spécialités de poissons dans un cadre traditionnel', 'budget' => '💸 30-50 TND'],
                    'afternoon' => [
                        ['name' => 'Hôtel troglodyte Sidi Driss (près Matmata)', 'desc' => 'Habitat troglodyte fonctionnel — Star Wars a été tourné ici', 'detail' => 'Visite du village souterrain. Expérience unique au monde. Photos indispensables.'],
                        ['name' => 'Villages traditionnels', 'desc' => 'Immersion dans la vie berbère djerbienne', 'detail' => 'Rencontrer les habitants. Écouter les histoires locales.'],
                    ],
                    'evening' => ['sunset' => ' dernière soirée à Djerba — special goodbye dinner', 'chill' => 'Shopping dernières minutes. Preparer les souvenirs.'],
                    'dinner' => ['name' => 'Restaurant Le Grand Bleu', 'desc' => 'Fine dining en front de mer. Fruits de mer premium. Menu degustation'],
                ],
            ],
            'Gabès' => [
                1 => ['title' => 'Arrivée & Oasis', 'icon' => '🌴', 'places_summary' => 'Oasis maritime, Centre-ville',
                    'morning' => [
                        ['name' => 'Oasis maritime de Gabès', 'desc' => 'La plus grande oasis de Tunisia, verdoyante et fascinante', 'detail' => 'Balade sous les palmiers. Dégustation de dattes fraîches. Photos natures.'],
                        ['name' => 'Pêche au port', 'desc' => 'Activité authentique au port de Gabès', 'detail' => 'Observer les pêcheurs. Acheter du poisson ultra-frais direct filet.'],
                    ],
                    'lunch' => ['name' => 'Sea Palace Gabès', 'desc' => 'Restaurant de fruits de mer premium en bord de mer', 'budget' => '💸 35-60 TND'],
                    'afternoon' => [
                        ['name' => 'Médina de Gabès', 'desc' => 'Vieille ville avec marchés traditionnels authentique', 'detail' => 'Se perdre dans les ruelles. Marchés d\'épices et tissus.'],
                        ['name' => 'Corniche de Gabès', 'desc' => 'Promenade en bord de mer au coucher du soleil', 'detail' => 'Moment magique. Les locaux viennent se promener en famille.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil sur la mer depuis la corniche', 'chill' => 'Thé à la menthe dans un café local. Écouter les histoires de Gabès.'],
                    'dinner' => ['name' => 'Restaurant Le Royal', 'desc' => 'Cuisine raffinée dans un cadre élégant. Spécialités de la région'],
                ],
                2 => ['title' => 'Exploration', 'icon' => '🗺️', 'places_summary' => 'Souk Jara, Chenini, Café hopping',
                    'morning' => [
                        ['name' => 'Souk Jara (si jour de marché)', 'desc' => 'Grand marché hebdomadaire animé', 'detail' => 'Si c\'est le bon jour : expérience incontournable. Vêtements, animaux, alimentation.'],
                        ['name' => 'Chenini Gabès', 'desc' => 'Balades profondes dans les quartiers traditionnels', 'detail' => 'Immersion totale. Rencontrer les artisans locaux.'],
                    ],
                    'lunch' => ['name' => 'Restaurant La Luna', 'desc' => 'Cuisine internationale dans une ambiance chill. Terrasse agréable', 'budget' => '💸 25-45 TND'],
                    'afternoon' => [
                        ['name' => 'Café hopping', 'desc' => 'Circuit des meilleurs cafés de Gabès', 'detail' => 'Café el Ahrar, Café de la Paix, Café Sidi Brahim.'],
                        ['name' => 'Shopping artisanal', 'desc' => 'Acheter les produits locaux : huile d\'olive, dattes, savons naturels', 'detail' => 'Marchés locaux pour meilleurs prix.'],
                    ],
                    'evening' => ['sunset' => 'Dernier coucher de soleil à Gabès', 'chill' => 'Dîner relax dans un restaurant du front de mer'],
                    'dinner' => ['name' => 'Restaurant Le52', 'desc' => 'Cuisine moderne dans un cadre rénové. Bons cocktails'],
                ],
            ],
            'Tunis' => [
                1 => ['title' => 'Médina & Culture', 'icon' => '🏛️', 'places_summary' => 'Médina, Souks, Bab Bhar',
                    'morning' => [
                        ['name' => 'Médina de Tunis', 'desc' => 'Vieille ville historique classée UNESCO avec ses souks labyrinthiques', 'detail' => 'Explorer les souks : textiles, cuir, cuivre, épice. Se perdre est recommandé !'],
                        ['name' => 'Souk des Etoffes', 'desc' => 'Le plus célèbre souk pour les tissus traditionnels tunisiens', 'detail' => 'Négocier les prix. Acheter des robes, burnous ou tissus de qualité.'],
                    ],
                    'lunch' => ['name' => 'Restaurant Dar el Jeld', 'desc' => 'Fine dining tunisienne dans un palais restauré. Cuisine traditionnelle premium', 'budget' => '💸 50-100 TND'],
                    'afternoon' => [
                        ['name' => 'Avenue Habib Bourguiba', 'desc' => 'La plus célèbre avenue de Tunis, bordée de cafés et bâtiments coloniaux', 'detail' => 'Flâner. Prendre un café在地中海咖啡馆. Observer la vie locale.'],
                        ['name' => 'Bab Bhar', 'desc' => 'Porte historique de la médina donnant sur le lac de Tunis', 'detail' => 'Photos du lac. Traverser vers le quartier modern.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil sur le lac de Tunis', 'chill' => 'Café sur l\'avenue Bourguiba. Profiter de l\'ambiance'],
                    'dinner' => ['name' => 'Restaurant Le Castle', 'desc' => 'Gastronomie française avec vue sur le lac. Menu découverte'],
                ],
                2 => ['title' => 'UNESCO & Villages', 'icon' => '🕌', 'places_summary' => 'Carthage, Sidi Bou Saïd, Musée Bardo',
                    'morning' => [
                        ['name' => 'Musée du Bardo', 'desc' => 'Le plus grand musée de mosaïques romaines au monde', 'detail' => '2-3h minimum. Chef-d\'œuvres antiques. Guide recommandé pour comprendre.'],
                        ['name' => 'Carthage', 'desc' => 'Sites archéologiques puniques et romains classés UNESCO', 'detail' => 'Colline de Byrsa, thermes de Antonin, ports puniques.'],
                    ],
                    'lunch' => ['name' => 'Café des Délices (Sidi Bou Saïd)', 'desc' => 'Café historique où Camus a écrit. Vue exceptionnelle', 'budget' => '💸 30-50 TND'],
                    'afternoon' => [
                        ['name' => 'Sidi Bou Saïd', 'desc' => 'Le plus beau village de Tunisia, bleu et blanc emblématique', 'detail' => 'Se perdre dans les ruelles. Photos à chaque coin. Vues spectaculaires sur la mer.'],
                        ['name' => 'Galerie d\'art', 'desc' => 'Découvrir les artistes locaux et acheter des œuvres', 'detail' => 'Tableaux, sculptures, Jewelry artisanale.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil depuis Sidi Bou Saïd — le plus beau de Tunis', 'chill' => 'Dîner dans un restaurant du village avec vue mer'],
                    'dinner' => ['name' => 'Restaurant La Goulette', 'desc' => 'Poisson grillé en face du port de la Goulette. Authentique'],
                ],
            ],
            'Tozeur' => [
                1 => ['title' => 'Oasis & Palmeraie', 'icon' => '🌴', 'places_summary' => 'Oasis, Palmeraie, Chott el Jerid',
                    'morning' => [
                        ['name' => 'Oasis de Tozeur', 'desc' => 'Magnifique oasis de dattiers avec promenades ombragées', 'detail' => 'Balade guidée possible. Observer la vie traditionnelle.'],
                        ['name' => 'Chott el Jerid', 'desc' => 'Le plus grand chott salé du Sahara. Effet mirage garanti', 'detail' => 'Photos illusions d\'optique. Arrêt obligatoire. Attention chaleur.'],
                    ],
                    'lunch' => ['name' => 'Restaurant Ksar Jawhar', 'desc' => ' cuisine du désert dans un ksar authentique. Tajines et couscous', 'budget' => '💸 25-40 TND'],
                    'afternoon' => [
                        ['name' => 'Palmeraie de Tozeur', 'desc' => 'Vaste palmeraie de 200 000 dattiers', 'detail' => 'Balade à pied ou en calèche. Dégustation de dattes de plusieurs variétés.'],
                        ['name' => 'Musée des Palmiers', 'desc' => 'Musée botanique sur les dattes et les palmiers', 'detail' => 'Comprendre la culture du dattier. Boutique de dattes BIO.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil sur le Chott — moment irréel', 'chill' => 'Thé berbère et repos avant l\'aventure désert.'],
                    'dinner' => ['name' => 'Restaurant El Bassatin', 'desc' => 'Dans l\'oasis. Cuisine traditionnelle. Ambiance unique'],
                ],
            ],
            'Douz' => [
                1 => ['title' => 'Désert & Ksours', 'icon' => '🏜️', 'places_summary' => 'Centre Touareg, Désert, Camp',
                    'morning' => [
                        ['name' => 'Centre Culturel Touareg', 'desc' => 'Découvrir la culture berbère et touareg. Bijoux, textiles, artefacts', 'detail' => 'Ecouter les histoires. Acheter des souvenirs authentiques.'],
                        ['name' => 'Balade en dromadaire', 'desc' => 'Dans les dunes autour de Douz', 'detail' => 'Sensations uniques. Guide local pour sécurité.'],
                    ],
                    'lunch' => ['name' => 'Restaurant Ksar Safia', 'desc' => 'Dans un camp de luxe. Déjeuner typique sous les tentes', 'budget' => '💸 30-50 TND'],
                    'afternoon' => [
                        ['name' => 'Excursion désert', 'desc' => 'Quad ou 4x4 dans les grandes dunes', 'detail' => 'Adrénaline garantie. Arrêt pour photos dans les dunes.'],
                        ['name' => 'Sandboarding', 'desc' => 'Sport de glisse sur les dunes de sable', 'detail' => 'Location équipement possible. Sensation de liberté totale.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil dans le désert — inoubliable', 'chill' => 'Nuit en camp désert ⭐ (option recommandée)'],
                    'dinner' => ['name' => 'Camp sous les étoiles', 'desc' => 'Dîner berbère. Musique live. Nuit en bivouac sous les étoiles'],
                ],
            ],
            'Matmata' => [
                1 => ['title' => 'Culture Troglodyte', 'icon' => '🏠', 'places_summary' => 'Sidi Driss, Maisons souterraines',
                    'morning' => [
                        ['name' => 'Hôtel troglodyte Sidi Driss', 'desc' => 'Habitat souterrain fonctionnel. Star Wars y a été tourné', 'detail' => 'Visite du village. Photos dans les chambres troglodytes.'],
                        ['name' => 'Cité troglodyte de Matmata', 'desc' => 'Maisons creusées dans le sol. Habitat unique au monde', 'detail' => 'Immersion totale dans ce mode de vie ancestral.'],
                    ],
                    'lunch' => ['name' => 'Restaurant Sidi Driss', 'desc' => 'Dans l\'hôtel troglodyte. Cuisine locale', 'budget' => '💸 20-35 TND'],
                    'afternoon' => [
                        ['name' => 'Ksar de Toujane', 'desc' => 'Ancien ksar avec vue spectaculaire sur les montagnes', 'detail' => 'Randonnée jusqu\'au ksar. Panorama à 360°.'],
                        ['name' => 'Canyon de Mides', 'desc' => 'Gorges impressionnantes dans le désert', 'detail' => 'Road trip spectaculaire. Photos mémorables.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil depuis les hauteurs de Matmata', 'chill' => 'Nuit en gîte troglodyte. Expérience authentique'],
                    'dinner' => ['name' => 'Gîte Toujane', 'desc' => 'Dîner berbère dans un cadre troglodyte. Ambiance familiale'],
                ],
            ],
            'Tataouine' => [
                1 => ['title' => 'Ksours & Villages', 'icon' => '🧭', 'places_summary' => 'Ksar Tataouine, Chenini, Ksar Ouled Soltane',
                    'morning' => [
                        ['name' => 'Ksar de Tataouine', 'desc' => 'Le plus grand ksar de Tunisia. Fortifications berbères impressionnantes', 'detail' => 'Explorer les ruelles. Comprendre la vie ksourienne.'],
                        ['name' => 'Ksar Ouled Soltane', 'desc' => 'Le plus photogénique. Star Wars y a été tourné', 'detail' => 'Photos inoubliables. Histoire fascinante.'],
                    ],
                    'lunch' => ['name' => 'Restaurant Ksar Said', 'desc' => 'Cuisine traditionnelle dans un ksar. Ambiance unique', 'budget' => '💸 20-35 TND'],
                    'afternoon' => [
                        ['name' => 'Village Chenini', 'desc' => 'Village troglodyte perché sur une colline', 'detail' => 'Randonnée jusqu\'au village. Rencontrer les derniers habitants.'],
                        ['name' => 'Ksar Ghilane', 'desc' => 'Oasis avec source thermale dans le désert', 'detail' => 'Baignade dans les eaux chaudes naturelles. Expérience insolite.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil sur les ksours', 'chill' => 'Nuit en bivouac ou gîte local'],
                    'dinner' => ['name' => 'Restaurant Chenini', 'desc' => 'Dans une maison troglodyte. Accueil familial'],
                ],
            ],
            'Hammamet' => [
                1 => ['title' => 'Plages & Médina', 'icon' => '🏖️', 'places_summary' => 'Médina, Kasbah, Plages',
                    'morning' => [
                        ['name' => 'Médina de Hammamet', 'desc' => 'Petite médina pittoresque avec artists et cafés', 'detail' => 'Explorer les ruelles. Galerie d\'art locale.'],
                        ['name' => 'Kasbah de Hammamet', 'desc' => 'Ancienne forteresse avec vue sur la baie', 'detail' => 'Histoire ottomane. Photos panoramiques.'],
                    ],
                    'lunch' => ['name' => 'Restaurant Le Jasmine', 'desc' => 'Fruits de mer en front de mer. Vue exceptionelle', 'budget' => '💸 40-70 TND'],
                    'afternoon' => [
                        ['name' => 'Plage de Hammamet', 'desc' => 'L\'une des plus belles plages de Tunisia', 'detail' => 'Baignade. Sports nautiques. Location parasol.'],
                        ['name' => 'Safari Parc', 'desc' => 'Excursion en jeep dans les collines avoisinantes', 'detail' => 'Paysages à couper le souffle. Rencontres villages.'],
                    ],
                    'evening' => ['sunset' => 'Coucher de soleil depuis la kasbah', 'chill' => 'Café en médina. Ambiance nocturne'],
                    'dinner' => ['name' => 'Restaurant Dar Khit', 'desc' => 'Maison traditionnelle. Cuisine hammametoise authentique'],
                ],
            ],
        ];
        
        $defaultDay = [
            'title' => 'Découverte locale',
            'icon' => '📍',
            'places_summary' => 'Centre-ville, Quartiers',
            'morning' => [
                ['name' => 'Centre-ville', 'desc' => 'Balade découverte du centre', 'detail' => 'Explorer les ruelles principales.'],
                ['name' => 'Marché local', 'desc' => 'Marché traditionnel du coin', 'detail' => 'Produits frais et artisanat local.'],
            ],
            'lunch' => ['name' => 'Restaurant local', 'desc' => 'Cuisine traditionnelle régionale', 'budget' => '💸 20-35 TND'],
            'afternoon' => [
                ['name' => 'Quartier historique', 'desc' => 'Patrimoine local', 'detail' => 'Visites et photos.'],
            ],
            'evening' => ['sunset' => 'Coucher de soleil local', 'chill' => 'Promenade en soirée'],
            'dinner' => ['name' => 'Restaurant typique', 'desc' => 'Dîner traditionnel'],
        ];
        
        $destKey = ucfirst($destination);
        $destItinerary = $itineraries[$destKey] ?? null;
        
        $days = [];
        for ($i = 1; $i <= $jours; $i++) {
            if ($destItinerary && isset($destItinerary[$i])) {
                $dayData = $destItinerary[$i];
            } elseif ($destItinerary) {
                $dayData = $destItinerary[array_rand($destItinerary)];
                $dayData['num'] = $i;
                $dayData['title'] = $dayData['title'] . " (Jour {$i})";
            } else {
                $dayData = $defaultDay;
                $dayData['num'] = $i;
            }
            
            if (!isset($dayData['num'])) {
                $dayData['num'] = $i;
            }
            
            $days[] = $dayData;
        }
        
        return $days;
    }

    private function generateHighlights(string $style, string $destination): array
    {
        $baseHighlights = [
            'Circuit personnalisé selon vos préférences',
            'Itinéraire détaillé jour par jour',
            'Coordonnées GPS pour chaque étape',
            'Estimation des distances de trajet',
        ];
        
        $styleHighlights = match (mb_strtolower($style)) {
            'aventure' => ['Safari et excursions', 'Sport outdoor', 'Rencontres authentiques'],
            'romantique' => ['Moments en couple', 'Couchers de soleil', 'Tables romantiques'],
            'famille' => ['Activités familiales', 'Rythme adapté', 'Souvenirs partagés'],
            'détente', 'detente' => ['Repos et bien-être', 'Plages calmes', 'Détente totale'],
            'culturel' => ['Patrimoine historique', 'Artisanat local', 'Traditions'],
            default => ['Sites incontournables', 'Culture locale', 'Gastronomie'],
        };
        
        return array_merge($baseHighlights, $styleHighlights);
    }

    private function generateIncludedServices(string $style, string $budget): array
    {
        $base = [
            'Hébergement selon le niveau choisi',
            'Petit-déjeuner quotidien',
            'Transferts entre étapes',
            'Carnet de voyage digital',
            'Assistance 24/7',
        ];
        
        if (mb_strtolower($budget) === 'premium') {
            $base[] = 'Guide local francophone';
            $base[] = 'Repas complets';
            $base[] = 'Activités exclusives';
        }
        
        return $base;
    }

    private function formatDate(string $date): string
    {
        try {
            $d = new \DateTime($date);
            return $d->format('d/m/Y');
        } catch (\Exception $e) {
            return $date;
        }
    }
=======
            ], JSON_UNESCAPED_UNICODE),
        ];
    }
>>>>>>> testsisi
}

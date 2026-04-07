<?php

declare(strict_types=1);

$target = $argv[1] ?? (__DIR__ . '/../var/flyandgo.db');
$directory = dirname($target);

if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}

if (file_exists($target)) {
    unlink($target);
}

$pdo = new PDO('sqlite:' . $target);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

$schema = <<<SQL
CREATE TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prenom TEXT NOT NULL,
    nom TEXT NOT NULL,
    email TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE circuit (
    id_circuit INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    type TEXT NOT NULL,
    destination TEXT NOT NULL,
    duree INTEGER NOT NULL,
    image_url TEXT,
    prix_par_personne REAL NOT NULL,
    note_moyenne REAL DEFAULT 0,
    nb_avis INTEGER DEFAULT 0,
    popularite_score INTEGER DEFAULT 50,
    start_date TEXT,
    status TEXT NOT NULL DEFAULT 'actif',
    budget REAL DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jour_circuit (
    id_jour INTEGER PRIMARY KEY AUTOINCREMENT,
    id_circuit INTEGER NOT NULL,
    numero_jour INTEGER NOT NULL,
    titre TEXT NOT NULL,
    activites TEXT NOT NULL,
    hebergement TEXT,
    transport TEXT,
    budget_jour REAL DEFAULT 0,
    FOREIGN KEY (id_circuit) REFERENCES circuit(id_circuit) ON DELETE CASCADE
);

CREATE TABLE circuit_reservation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_circuit INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    nb_travelers INTEGER NOT NULL,
    total_price REAL NOT NULL,
    status TEXT NOT NULL,
    reserved_at TEXT DEFAULT CURRENT_TIMESTAMP,
    date_depart TEXT,
    meteo_info TEXT,
    plan_b TEXT,
    FOREIGN KEY (id_circuit) REFERENCES circuit(id_circuit) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE circuit_review (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_circuit INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    comment TEXT NOT NULL,
    rating INTEGER NOT NULL,
    helpful_count INTEGER DEFAULT 0,
    verified_purchase INTEGER DEFAULT 1,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_circuit) REFERENCES circuit(id_circuit) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE circuit_personnalise (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    destination TEXT NOT NULL,
    date_depart TEXT,
    duree INTEGER NOT NULL,
    budget_min REAL DEFAULT 0,
    budget_max REAL DEFAULT 0,
    style_voyage TEXT NOT NULL,
    centres_interet TEXT,
    niveau_fatigue INTEGER DEFAULT 2,
    etape INTEGER DEFAULT 1,
    statut TEXT NOT NULL DEFAULT 'SOUMIS',
    meteo_info TEXT,
    plan_b TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id)
);
SQL;

$pdo->exec($schema);

$users = [
    ['id' => 1, 'prenom' => 'Admin', 'nom' => 'FlyGo', 'email' => 'admin@flygo.tn'],
    ['id' => 2, 'prenom' => 'Utilisateur', 'nom' => 'Demo', 'email' => 'utilisateur@flygo.tn'],
    ['id' => 3, 'prenom' => 'Amine', 'nom' => 'Ben Salem', 'email' => 'amine@flygo.tn'],
    ['id' => 4, 'prenom' => 'Sarra', 'nom' => 'Trabelsi', 'email' => 'sarra@flygo.tn'],
    ['id' => 5, 'prenom' => 'Youssef', 'nom' => 'Mejri', 'email' => 'youssef@flygo.tn'],
];

$stmtUser = $pdo->prepare('INSERT INTO user (id, prenom, nom, email) VALUES (:id, :prenom, :nom, :email)');
foreach ($users as $user) {
    $stmtUser->execute($user);
}

$images = [
    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1400&q=80',
    'https://images.unsplash.com/photo-1493558103817-58b2924bce98?auto=format&fit=crop&w=1400&q=80',
    'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1400&q=80',
    'https://images.unsplash.com/photo-1503220317375-aaad61436b1b?auto=format&fit=crop&w=1400&q=80',
    'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1400&q=80',
    'https://images.unsplash.com/photo-1527631746610-bca00a040d60?auto=format&fit=crop&w=1400&q=80',
    'https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=1400&q=80',
    'https://images.unsplash.com/photo-1530521954074-e64f6810b32d?auto=format&fit=crop&w=1400&q=80',
];

$destinations = [
    ['Bali Escape Premium', 'Bali, Indonésie', 'Plage', 7, 1280, 86],
    ['Tokyo Discovery Sprint', 'Tokyo, Japon', 'Culture', 6, 1120, 84],
    ['Marrakech Desert Mood', 'Marrakech, Maroc', 'Aventure', 5, 890, 79],
    ['Istanbul City Layers', 'Istanbul, Turquie', 'Culture', 4, 760, 76],
    ['Rome Dolce Vita', 'Rome, Italie', 'Culture', 5, 980, 81],
    ['Dubai Skyline Escape', 'Dubai, Émirats', 'Luxe', 5, 1680, 90],
    ['Santorini Sunset Route', 'Santorin, Grèce', 'Détente', 6, 1420, 88],
    ['Cappadocia Balloon Trip', 'Cappadoce, Turquie', 'Aventure', 4, 1040, 80],
    ['Paris Signature Weekend', 'Paris, France', 'Culture', 3, 930, 74],
    ['Phuket Ocean Reset', 'Phuket, Thaïlande', 'Plage', 7, 1190, 82],
    ['Doha Premium Stopover', 'Doha, Qatar', 'Luxe', 3, 870, 72],
    ['Andalusia Heritage Loop', 'Séville, Espagne', 'Culture', 6, 990, 77],
    ['Alps Soft Adventure', 'Chamonix, France', 'Aventure', 5, 1110, 78],
    ['Muscat Coastal Calm', 'Mascate, Oman', 'Détente', 5, 970, 71],
    ['Lisbon Food & Walks', 'Lisbonne, Portugal', 'Gastronomie', 4, 850, 75],
    ['Maldives Overwater Days', 'Maldives', 'Luxe', 6, 2050, 93],
    ['Jordan Petra Explorer', 'Amman, Jordanie', 'Aventure', 6, 1180, 83],
    ['Kuala Lumpur Smart Break', 'Kuala Lumpur, Malaisie', 'Ville', 4, 810, 68],
    ['Seoul Pop Culture Mix', 'Séoul, Corée du Sud', 'Culture', 6, 1210, 85],
    ['Cairo Nile Signature', 'Le Caire, Égypte', 'Histoire', 5, 920, 70],
    ['Vienna Classic Notes', 'Vienne, Autriche', 'Culture', 4, 940, 73],
    ['Zanzibar Blue Escape', 'Zanzibar, Tanzanie', 'Plage', 7, 1330, 87],
    ['Montreal Winter Lights', 'Montréal, Canada', 'Ville', 5, 1010, 69],
    ['Barcelona Creative Pulse', 'Barcelone, Espagne', 'Culture', 4, 890, 80],
];

$baseDate = new DateTimeImmutable('2026-04-20');
$stmtCircuit = $pdo->prepare('INSERT INTO circuit (title, description, type, destination, duree, image_url, prix_par_personne, note_moyenne, nb_avis, popularite_score, start_date, status, budget, created_at) VALUES (:title, :description, :type, :destination, :duree, :image_url, :prix_par_personne, :note_moyenne, :nb_avis, :popularite_score, :start_date, :status, :budget, :created_at)');
$stmtDay = $pdo->prepare('INSERT INTO jour_circuit (id_circuit, numero_jour, titre, activites, hebergement, transport, budget_jour) VALUES (:id_circuit, :numero_jour, :titre, :activites, :hebergement, :transport, :budget_jour)');

$circuitIds = [];
foreach ($destinations as $index => [$title, $destination, $type, $duree, $price, $popularity]) {
    $startDate = $baseDate->modify('+' . ($index * 3) . ' days')->format('Y-m-d');
    $status = $index < 12 ? 'actif' : 'inactif';
    $description = sprintf('%s est un pack moderne Fly & Go pensé pour une expérience %s, avec hébergements soignés, transferts fluides et activités premium adaptées à votre rythme.', $title, mb_strtolower($type));
    $stmtCircuit->execute([
        'title' => $title,
        'description' => $description,
        'type' => $type,
        'destination' => $destination,
        'duree' => $duree,
        'image_url' => $images[$index % count($images)],
        'prix_par_personne' => $price,
        'note_moyenne' => 0,
        'nb_avis' => 0,
        'popularite_score' => $popularity,
        'start_date' => $startDate,
        'status' => $status,
        'budget' => $price * max(2, min(4, (int) ceil($duree / 2))),
        'created_at' => $baseDate->modify('-' . (30 - $index) . ' days')->format('Y-m-d H:i:s'),
    ]);

    $circuitId = (int) $pdo->lastInsertId();
    $circuitIds[] = $circuitId;

    for ($day = 1; $day <= min($duree, 3); $day++) {
        $stmtDay->execute([
            'id_circuit' => $circuitId,
            'numero_jour' => $day,
            'titre' => "Jour $day - Expérience clé",
            'activites' => 'Accueil, exploration guidée, pause locale et activité signature Fly & Go.',
            'hebergement' => 'Hôtel partenaire 4★',
            'transport' => $day === 1 ? 'Transfert privé' : 'Navette premium',
            'budget_jour' => round(($price * 1.15) / max(1, $duree), 2),
        ]);
    }
}

$reservations = [
    ['id_circuit' => 1, 'user_id' => 2, 'nb_travelers' => 3, 'total_price' => 4230, 'status' => 'CONFIRME', 'reserved_at' => '2026-03-28 10:30:00', 'date_depart' => '2026-04-20'],
    ['id_circuit' => 2, 'user_id' => 2, 'nb_travelers' => 3, 'total_price' => 3876, 'status' => 'CONFIRME', 'reserved_at' => '2026-03-20 09:15:00', 'date_depart' => '2026-04-26'],
    ['id_circuit' => 7, 'user_id' => 2, 'nb_travelers' => 3, 'total_price' => 4620, 'status' => 'EN_ATTENTE', 'reserved_at' => '2026-02-25 16:45:00', 'date_depart' => '2026-05-08'],
    ['id_circuit' => 8, 'user_id' => 2, 'nb_travelers' => 3, 'total_price' => 3180, 'status' => 'CONFIRME', 'reserved_at' => '2026-01-17 13:10:00', 'date_depart' => '2026-05-11'],
    ['id_circuit' => 5, 'user_id' => 2, 'nb_travelers' => 2, 'total_price' => 2560, 'status' => 'ANNULE', 'reserved_at' => '2026-01-04 11:00:00', 'date_depart' => '2026-04-05'],
    ['id_circuit' => 3, 'user_id' => 3, 'nb_travelers' => 2, 'total_price' => 1958, 'status' => 'CONFIRME', 'reserved_at' => '2026-03-12 08:40:00', 'date_depart' => '2026-04-30'],
    ['id_circuit' => 4, 'user_id' => 4, 'nb_travelers' => 2, 'total_price' => 1672, 'status' => 'CONFIRME', 'reserved_at' => '2026-02-19 15:55:00', 'date_depart' => '2026-05-02'],
    ['id_circuit' => 6, 'user_id' => 5, 'nb_travelers' => 2, 'total_price' => 3696, 'status' => 'CONFIRME', 'reserved_at' => '2026-03-03 18:20:00', 'date_depart' => '2026-05-05'],
    ['id_circuit' => 9, 'user_id' => 3, 'nb_travelers' => 1, 'total_price' => 1023, 'status' => 'EN_ATTENTE', 'reserved_at' => '2026-03-30 19:30:00', 'date_depart' => '2026-05-14'],
    ['id_circuit' => 10, 'user_id' => 4, 'nb_travelers' => 2, 'total_price' => 2618, 'status' => 'CONFIRME', 'reserved_at' => '2026-01-28 07:25:00', 'date_depart' => '2026-05-17'],
    ['id_circuit' => 11, 'user_id' => 5, 'nb_travelers' => 2, 'total_price' => 1914, 'status' => 'CONFIRME', 'reserved_at' => '2026-02-08 21:05:00', 'date_depart' => '2026-05-20'],
    ['id_circuit' => 12, 'user_id' => 3, 'nb_travelers' => 3, 'total_price' => 3267, 'status' => 'CONFIRME', 'reserved_at' => '2026-03-07 12:10:00', 'date_depart' => '2026-05-23'],
];

$stmtReservation = $pdo->prepare('INSERT INTO circuit_reservation (id_circuit, user_id, nb_travelers, total_price, status, reserved_at, date_depart, meteo_info, plan_b) VALUES (:id_circuit, :user_id, :nb_travelers, :total_price, :status, :reserved_at, :date_depart, :meteo_info, :plan_b)');
foreach ($reservations as $reservation) {
    $destination = $destinations[$reservation['id_circuit'] - 1][1];
    $type = $destinations[$reservation['id_circuit'] - 1][2];
    $stmtReservation->execute($reservation + [
        'meteo_info' => '🌤️ ' . $destination . ' | 24°C | conditions favorables pour les visites',
        'plan_b' => '✅ Plan B prêt pour ' . $destination . ' — alternatives indoor, transferts flexibles et activités ' . mb_strtolower($type) . ' en cas d’imprévu météo.',
    ]);
}

$reviews = [
    ['id_circuit' => 1, 'user_id' => 2, 'comment' => 'Très belle organisation, service fluide et hôtel parfait.', 'rating' => 5, 'helpful_count' => 12, 'verified_purchase' => 1, 'created_at' => '2026-03-26 09:00:00'],
    ['id_circuit' => 7, 'user_id' => 2, 'comment' => 'Application claire et circuit très bien présenté.', 'rating' => 4, 'helpful_count' => 7, 'verified_purchase' => 1, 'created_at' => '2026-03-18 14:30:00'],
    ['id_circuit' => 2, 'user_id' => 3, 'comment' => 'Guide top, planning respecté et destination superbe.', 'rating' => 5, 'helpful_count' => 9, 'verified_purchase' => 1, 'created_at' => '2026-03-10 11:00:00'],
    ['id_circuit' => 6, 'user_id' => 4, 'comment' => 'Expérience premium bien calibrée pour un court séjour.', 'rating' => 4, 'helpful_count' => 5, 'verified_purchase' => 1, 'created_at' => '2026-02-25 10:20:00'],
    ['id_circuit' => 10, 'user_id' => 5, 'comment' => 'Plage superbe et infos utiles avant le départ.', 'rating' => 5, 'helpful_count' => 6, 'verified_purchase' => 1, 'created_at' => '2026-02-08 16:00:00'],
    ['id_circuit' => 8, 'user_id' => 3, 'comment' => 'Ballons magnifiques, transport impeccable.', 'rating' => 4, 'helpful_count' => 4, 'verified_purchase' => 1, 'created_at' => '2026-01-28 09:50:00'],
];

$stmtReview = $pdo->prepare('INSERT INTO circuit_review (id_circuit, user_id, comment, rating, helpful_count, verified_purchase, created_at) VALUES (:id_circuit, :user_id, :comment, :rating, :helpful_count, :verified_purchase, :created_at)');
foreach ($reviews as $review) {
    $stmtReview->execute($review);
}

$customs = [
    ['user_id' => 2, 'destination' => 'Séoul', 'date_depart' => '2026-06-02', 'duree' => 8, 'budget_min' => 2500, 'budget_max' => 4200, 'style_voyage' => 'Culture', 'centres_interet' => 'Ville, Gastronomie', 'niveau_fatigue' => 2],
    ['user_id' => 2, 'destination' => 'Milan', 'date_depart' => '2026-06-10', 'duree' => 5, 'budget_min' => 1200, 'budget_max' => 2200, 'style_voyage' => 'Luxe', 'centres_interet' => 'Mode, Ville', 'niveau_fatigue' => 2],
    ['user_id' => 2, 'destination' => 'Amman', 'date_depart' => '2026-06-21', 'duree' => 7, 'budget_min' => 1700, 'budget_max' => 2900, 'style_voyage' => 'Aventure', 'centres_interet' => 'Histoire, Nature', 'niveau_fatigue' => 1],
    ['user_id' => 2, 'destination' => 'Tbilissi', 'date_depart' => '2026-07-05', 'duree' => 6, 'budget_min' => 1300, 'budget_max' => 2400, 'style_voyage' => 'Économique', 'centres_interet' => 'Ville, Gastronomie', 'niveau_fatigue' => 2],
    ['user_id' => 2, 'destination' => 'Osaka', 'date_depart' => '2026-07-15', 'duree' => 9, 'budget_min' => 2800, 'budget_max' => 4600, 'style_voyage' => 'Culture', 'centres_interet' => 'Ville, Culture', 'niveau_fatigue' => 3],
    ['user_id' => 3, 'destination' => 'Doha', 'date_depart' => '2026-06-18', 'duree' => 4, 'budget_min' => 900, 'budget_max' => 1800, 'style_voyage' => 'Luxe', 'centres_interet' => 'Ville, Détente', 'niveau_fatigue' => 2],
    ['user_id' => 4, 'destination' => 'Barcelone', 'date_depart' => '2026-06-24', 'duree' => 5, 'budget_min' => 1100, 'budget_max' => 2100, 'style_voyage' => 'Culture', 'centres_interet' => 'Ville, Sport', 'niveau_fatigue' => 2],
];

$stmtCustom = $pdo->prepare('INSERT INTO circuit_personnalise (user_id, destination, date_depart, duree, budget_min, budget_max, style_voyage, centres_interet, niveau_fatigue, etape, statut, meteo_info, plan_b, created_at) VALUES (:user_id, :destination, :date_depart, :duree, :budget_min, :budget_max, :style_voyage, :centres_interet, :niveau_fatigue, 4, :statut, :meteo_info, :plan_b, :created_at)');
foreach ($customs as $idx => $custom) {
    $stmtCustom->execute($custom + [
        'statut' => 'SOUMIS',
        'meteo_info' => '🌤️ ' . $custom['destination'] . ' | 24°C | conditions favorables pour les visites',
        'plan_b' => '✅ Plan B prêt pour ' . $custom['destination'] . ' — alternatives indoor, transferts flexibles et activités ' . mb_strtolower($custom['style_voyage']) . ' en cas d’imprévu météo.',
        'created_at' => (new DateTimeImmutable('2026-03-01'))->modify('+' . $idx . ' days')->format('Y-m-d H:i:s'),
    ]);
}

$ratingUpdate = $pdo->query('SELECT id_circuit, ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total_reviews FROM circuit_review GROUP BY id_circuit');
foreach ($ratingUpdate->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $update = $pdo->prepare('UPDATE circuit SET note_moyenne = :avg_rating, nb_avis = :total_reviews WHERE id_circuit = :id');
    $update->execute([
        'avg_rating' => $row['avg_rating'],
        'total_reviews' => $row['total_reviews'],
        'id' => $row['id_circuit'],
    ]);
}

echo "Database reset completed: {$target}\n";

-- ╔══════════════════════════════════════════════════════════╗
-- ║  FLY & GO — Schéma de base de données MySQL              ║
-- ║  Compatible Symfony 6.4 + Doctrine ORM                   ║
-- ╚══════════════════════════════════════════════════════════╝

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `flyandgo` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `flyandgo`;

-- ── USERS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `user` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `email`       VARCHAR(180) NOT NULL UNIQUE,
  `roles`       JSON NOT NULL,
  `password`    VARCHAR(255) NOT NULL,
  `nom`         VARCHAR(100) NOT NULL,
  `prenom`      VARCHAR(100) NOT NULL,
  `telephone`   VARCHAR(20) DEFAULT NULL,
  `avatar`      VARCHAR(255) DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── HEBERGEMENTS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `hebergement` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `nom`            VARCHAR(255) NOT NULL,
  `ville`          VARCHAR(100) NOT NULL,
  `type`           VARCHAR(100) NOT NULL,
  `prix_par_nuit`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image`          VARCHAR(255) DEFAULT NULL,
  `description`    TEXT DEFAULT NULL,
  `adresse`        VARCHAR(255) DEFAULT NULL,
  `capacite`       INT DEFAULT NULL,
  `disponible`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── RESERVATIONS HEBERGEMENT ───────────────────────────────
CREATE TABLE IF NOT EXISTS `reservation` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `hebergement_id`   INT NOT NULL,
  `date_debut`       DATE NOT NULL,
  `date_fin`         DATE NOT NULL,
  `montant_total`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `statut`           VARCHAR(30) NOT NULL DEFAULT 'EN_ATTENTE',
  `nom_client`       VARCHAR(150) NOT NULL,
  `email_client`     VARCHAR(180) NOT NULL,
  `telephone_client` VARCHAR(20) DEFAULT NULL,
  `nombre_personnes` INT NOT NULL DEFAULT 1,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`hebergement_id`) REFERENCES `hebergement`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── AVIS HEBERGEMENT ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `avis` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `hebergement_id` INT NOT NULL,
  `note`           INT NOT NULL DEFAULT 5,
  `commentaire`    TEXT NOT NULL,
  `auteur`         VARCHAR(100) NOT NULL,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`hebergement_id`) REFERENCES `hebergement`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── CIRCUITS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `circuit` (
  `id`                 INT AUTO_INCREMENT PRIMARY KEY,
  `titre`              VARCHAR(255) NOT NULL,
  `description`        TEXT DEFAULT NULL,
  `duree`              VARCHAR(100) DEFAULT NULL,
  `prix`               DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image`              VARCHAR(255) DEFAULT NULL,
  `difficulte`         VARCHAR(50) DEFAULT NULL,
  `places_disponibles` INT DEFAULT NULL,
  `depart`             VARCHAR(100) DEFAULT NULL,
  `destination`        VARCHAR(100) DEFAULT NULL,
  `actif`              TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── RESERVATIONS CIRCUIT ───────────────────────────────────
CREATE TABLE IF NOT EXISTS `reservation_circuit` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `circuit_id`       INT NOT NULL,
  `nom_client`       VARCHAR(150) NOT NULL,
  `email_client`     VARCHAR(180) NOT NULL,
  `telephone`        VARCHAR(20) DEFAULT NULL,
  `date_reservation` DATE NOT NULL,
  `nb_personnes`     INT NOT NULL DEFAULT 1,
  `montant_total`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `statut`           VARCHAR(30) NOT NULL DEFAULT 'EN_ATTENTE',
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`circuit_id`) REFERENCES `circuit`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── ACTIVITIES ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `activity` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `title`      VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `duration`   VARCHAR(50) DEFAULT NULL,
  `date`       DATE DEFAULT NULL,
  `capacity`   INT NOT NULL DEFAULT 10,
  `image`      VARCHAR(255) DEFAULT NULL,
  `lieu`       VARCHAR(100) DEFAULT NULL,
  `actif`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── BOOKINGS ACTIVITY ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `booking` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `activity_id`   INT NOT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `email`         VARCHAR(180) NOT NULL,
  `client_phone`  VARCHAR(20) DEFAULT NULL,
  `persons`       INT NOT NULL DEFAULT 1,
  `booking_date`  DATE NOT NULL,
  `total_price`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status`        VARCHAR(30) NOT NULL DEFAULT 'PENDING',
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`activity_id`) REFERENCES `activity`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── REVIEWS ACTIVITY ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `review` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `activity_id` INT NOT NULL,
  `author`      VARCHAR(100) NOT NULL,
  `rating`      INT NOT NULL DEFAULT 5,
  `comment`     TEXT NOT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`activity_id`) REFERENCES `activity`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── FORUM POSTS ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `forum_post` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `title`      VARCHAR(255) NOT NULL,
  `content`    TEXT NOT NULL,
  `author`     VARCHAR(100) NOT NULL,
  `categorie`  VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status`     VARCHAR(20) NOT NULL DEFAULT 'APPROVED',
  `vues`       INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── FORUM COMMENTS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `forum_comment` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `post_id`    INT NOT NULL,
  `parent_id`  INT DEFAULT NULL,
  `author`     VARCHAR(100) NOT NULL,
  `content`    TEXT NOT NULL,
  `score`      INT NOT NULL DEFAULT 0,
  `likes`      INT NOT NULL DEFAULT 0,
  `dislikes`   INT NOT NULL DEFAULT 0,
  `is_pinned`  TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `forum_post`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_id`) REFERENCES `forum_comment`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ── DONNÉES DE DÉMONSTRATION ───────────────────────────────
INSERT INTO `user` (`email`, `roles`, `password`, `nom`, `prenom`, `is_active`) VALUES
('admin@flyandgo.tn', '["ROLE_ADMIN"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 'Admin', 1),
('user@flyandgo.tn',  '["ROLE_USER"]',  '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dupont', 'Jean', 1);
-- Mot de passe par défaut: password

INSERT INTO `hebergement` (`nom`, `ville`, `type`, `prix_par_nuit`, `description`, `capacite`, `disponible`) VALUES
('Hôtel Sidi Bou Said Palace', 'Sidi Bou Said', 'Hôtel', 280.000, 'Un hôtel de luxe avec vue panoramique sur la Méditerranée. Architecture traditionnelle tunisienne.', 4, 1),
('Villa Djerba Sunset', 'Djerba', 'Villa', 450.000, 'Magnifique villa en bord de mer avec piscine privée et jardin fleuri.', 8, 1),
('Riad El Medina', 'Tunis', 'Riad', 190.000, 'Riad authentique au cœur de la médina de Tunis. Décor traditionnel et confort moderne.', 2, 1),
('Resort Hammamet Plage', 'Hammamet', 'Resort', 320.000, 'Resort 5 étoiles directement sur la plage. Accès illimité au spa et aux activités nautiques.', 6, 1),
('Appartement Carthage View', 'Carthage', 'Appartement', 150.000, 'Bel appartement moderne avec terrasse et vue sur les ruines de Carthage.', 4, 1),
('Maison d hôtes Tozeur', 'Tozeur', 'Maison d\'hôtes', 120.000, 'Maison d hôtes traditionnelle aux portes du désert. Idéale pour découvrir le Sahara.', 3, 1);

INSERT INTO `circuit` (`titre`, `description`, `duree`, `prix`, `difficulte`, `places_disponibles`, `depart`, `destination`) VALUES
('Circuit Sahara Magique', 'Découvrez les dunes dorées du Sahara lors de ce circuit exceptionnel. Nuits en camp bédouin, balade en dromadaire et coucher de soleil sur les dunes.', '5 jours / 4 nuits', 850.000, 'Facile', 15, 'Tunis', 'Douz'),
('Tour du Cap Bon', 'Explorez les vignobles, les plages et les villages pittoresques du Cap Bon. Un voyage entre mer, histoire et gastronomie.', '3 jours / 2 nuits', 350.000, 'Facile', 20, 'Tunis', 'Nabeul'),
('Circuit Montagnes Tunisiennes', 'Randonnée à travers les magnifiques montagnes de la Kroumirie et les forêts de chêne-liège.', '4 jours / 3 nuits', 520.000, 'Modéré', 12, 'Tunis', 'Aïn Draham'),
('Aventure Désert Extrême', 'Pour les amateurs de sensations fortes : dunes géantes, oasis secrètes et nuit sous les étoiles du Sahara.', '7 jours / 6 nuits', 1200.000, 'Difficile', 8, 'Tunis', 'Ksar Ghilane');

INSERT INTO `activity` (`title`, `description`, `price`, `duration`, `capacity`, `lieu`) VALUES
('Randonnée Djebel Zaghouan', 'Ascension du point culminant du Nord de la Tunisie. Vue panoramique exceptionnelle sur la plaine de Zaghouan.', 45.000, '6 heures', 15, 'Zaghouan'),
('Plongée Sous-Marine Tabarka', 'Découvrez les fonds marins cristallins de Tabarka. Tous niveaux acceptés. Équipement fourni.', 85.000, '3 heures', 8, 'Tabarka'),
('Tour à cheval Tozeur', 'Balade équestre dans les oasis et aux abords des dunes. Un moment magique dans un décor de carte postale.', 60.000, '2 heures', 10, 'Tozeur'),
('Atelier Poterie Nabeul', 'Apprenez l art de la poterie avec des artisans locaux. Créez votre propre pièce à emporter en souvenir.', 35.000, '2 heures', 12, 'Nabeul'),
('Surf et Kitesurf Djerba', 'Cours de surf et kitesurf pour tous niveaux sur les plages paradisiaques de Djerba.', 95.000, '4 heures', 6, 'Djerba'),
('Visite Carthage Antique', 'Visite guidée des ruines de Carthage avec un archéologue passionné. Histoire et patrimoine.', 25.000, '3 heures', 25, 'Carthage');

INSERT INTO `forum_post` (`title`, `content`, `author`, `categorie`, `status`) VALUES
('Les meilleurs hébergements à Djerba ?', 'Je planifie un voyage à Djerba en famille pour 2 semaines. Quelqu un peut me recommander des hébergements proches de la mer avec piscine ? Budget moyen.', 'Ahmed B.', 'Hébergement', 'APPROVED'),
('Circuit Sahara : vos expériences ?', 'J ai prévu de faire le circuit Sahara en octobre. Est-ce la bonne saison ? Des conseils sur ce qu il faut emporter ?', 'Sarah M.', 'Circuit', 'APPROVED'),
('Activités pour enfants en Tunisie', 'Nous voyageons avec des enfants de 5 et 8 ans. Quelles sont les meilleures activités adaptées aux familles ?', 'Pierre L.', 'Activité', 'APPROVED'),
('Conseils visa et entrée en Tunisie', 'Bonjour, je viens de France. Y a-t-il des démarches particulières pour entrer en Tunisie ? Combien de temps est-on autorisé à rester ?', 'Marie T.', 'Conseil', 'APPROVED');

INSERT INTO `forum_comment` (`post_id`, `author`, `content`) VALUES
(1, 'Karim R.', 'Bonjour ! Je recommande vivement le Resort Hammamet Plage. Excellent rapport qualité-prix et les enfants adorent !'),
(1, 'Leila S.', 'Villa Djerba Sunset est parfaite pour une famille. La piscine privée est un vrai plus !'),
(2, 'Mohamed A.', 'Octobre est parfait pour le Sahara ! La chaleur est supportable et les nuits sont magnifiques.');

INSERT INTO `avis` (`hebergement_id`, `note`, `commentaire`, `auteur`) VALUES
(1, 5, 'Magnifique hôtel avec une vue imprenable sur la mer ! Service impeccable et personnel très accueillant.', 'Sophie M.'),
(1, 4, 'Très bon séjour, chambre propre et confortable. Petit-déjeuner excellent. Je recommande.', 'Thomas B.'),
(2, 5, 'La villa est absolument parfaite. Piscine privée, vue mer, calme total. Un séjour de rêve !', 'Famille Dupont'),
(3, 5, 'Le riad est magnifique, décoration authentique. On se sent vraiment dans la Tunisie traditionnelle.', 'Julie L.'),
(4, 4, 'Resort très bien équipé. Accès plage direct et spa superbe. Un peu cher mais ça vaut le coup.', 'Marc D.');

INSERT INTO `review` (`activity_id`, `rating`, `comment`, `author`) VALUES
(1, 5, 'Randonnée magnifique ! Guide très compétent et vue au sommet à couper le souffle.', 'Antoine P.'),
(2, 5, 'Plongée inoubliable ! Les fonds marins de Tabarka sont exceptionnels. Moniteur très patient.', 'Claire V.'),
(3, 4, 'Belle balade à cheval dans les oasis. Dépaysement total et paysages magnifiques.', 'Nour B.');

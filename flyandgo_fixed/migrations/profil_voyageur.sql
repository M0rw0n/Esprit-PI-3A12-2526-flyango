-- =============================================
-- PROFIL_VOYAGEUR TABLE
-- =============================================
CREATE TABLE IF NOT EXISTS `profil_voyageur` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`         INT NOT NULL,
  `destination_preferee` VARCHAR(255) NOT NULL,
  `type_voyage`     VARCHAR(50) NOT NULL,
  `budget`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Index pour les requêtes fréquentes
CREATE INDEX idx_profil_type ON `profil_voyageur`(`type_voyage`);
CREATE INDEX idx_profil_budget ON `profil_voyageur`(`budget`);
CREATE INDEX idx_profil_destination ON `profil_voyageur`(`destination_preferee`(100));

-- =============================================
-- DONNÉES DE DÉMONSTRATION
-- =============================================
INSERT INTO `profil_voyageur` (`user_id`, `destination_preferee`, `type_voyage`, `budget`) VALUES
(1, 'Djerba', 'Family', 2500.00),
(2, 'Paris', 'Cultural', 1800.00);

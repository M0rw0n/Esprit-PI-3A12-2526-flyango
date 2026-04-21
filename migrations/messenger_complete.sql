-- =====================================================
-- MESSENGER TABLES - SQL COMPLET
-- Exécuter dans phpMyAdmin ou console MySQL
-- =====================================================

-- 1. Table conversations
CREATE TABLE IF NOT EXISTS `conversation` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(50) NOT NULL DEFAULT 'private',
  `name` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `created_by_id` INT DEFAULT NULL,
  KEY `idx_type` (`type`),
  KEY `idx_updated` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table participants
CREATE TABLE IF NOT EXISTS `conversation_participant` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `conversation_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `unread_count` INT NOT NULL DEFAULT 0,
  `last_read_at` DATETIME DEFAULT NULL,
  `joined_at` DATETIME NOT NULL,
  KEY `idx_conv` (`conversation_id`),
  KEY `idx_user` (`user_id`),
  UNIQUE KEY `unique_conv_user` (`conversation_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table messages
CREATE TABLE IF NOT EXISTS `message` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `conversation_id` INT NOT NULL,
  `sender_id` INT NOT NULL,
  `content` LONGTEXT NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'sent',
  `created_at` DATETIME NOT NULL,
  `read_at` DATETIME DEFAULT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  KEY `idx_conv` (`conversation_id`),
  KEY `idx_sender` (`sender_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clés étrangères
ALTER TABLE `conversation` 
  ADD CONSTRAINT `fk_conv_creator` FOREIGN KEY (`created_by_id`) REFERENCES `user`(`id`) ON DELETE SET NULL;

ALTER TABLE `conversation_participant` 
  ADD CONSTRAINT `fk_part_conv` FOREIGN KEY (`conversation_id`) REFERENCES `conversation`(`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_part_user` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE;

ALTER TABLE `message` 
  ADD CONSTRAINT `fk_msg_conv` FOREIGN KEY (`conversation_id`) REFERENCES `conversation`(`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `user`(`id`) ON DELETE CASCADE;

-- =====================================================
-- TEST: Créer une conversation test
-- (ID utilisateurs à adapter selon ta base)
-- =====================================================

-- Exemple: Conversation entre user ID 1 et 2
INSERT INTO `conversation` (`type`, `created_at`, `updated_at`, `created_by_id`) 
VALUES ('private', NOW(), NOW(), 1);

SET @conv_id = LAST_INSERT_ID();

INSERT INTO `conversation_participant` (`conversation_id`, `user_id`, `joined_at`, `unread_count`) 
VALUES 
(@conv_id, 1, NOW(), 0),
(@conv_id, 2, NOW(), 0);

-- Tester message
INSERT INTO `message` (`conversation_id`, `sender_id`, `content`, `status`, `created_at`)
VALUES (@conv_id, 1, 'Bonjour! Test de message', 'sent', NOW());

-- Mettre à jour unread pour le destinataire
UPDATE `conversation_participant` SET `unread_count` = `unread_count` + 1 
WHERE `conversation_id` = @conv_id AND `user_id` = 2;

-- Mettre à jour updated_at
UPDATE `conversation` SET `updated_at` = NOW() WHERE `id` = @conv_id;
-- Fly&Go Messenger + Friends SQL Setup
-- Run this file to create all necessary tables

-- ============================================
-- 1. MESSENGER TABLES (if not exist)
-- ============================================

CREATE TABLE IF NOT EXISTS `conversation` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(20) NOT NULL DEFAULT 'private',
    `name` VARCHAR(255) NULL,
    `image` VARCHAR(500) NULL,
    `created_by_id` INT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `conversation_participant` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `joined_at` DATETIME NOT NULL,
    `unread_count` INT DEFAULT 0,
    FOREIGN KEY (`conversation_id`) REFERENCES `conversation`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `message` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `sender_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `status` VARCHAR(20) DEFAULT 'sent',
    `created_at` DATETIME NOT NULL,
    `read_at` DATETIME NULL,
    `image` VARCHAR(500) NULL,
    `forum_post_id` INT NULL,
    FOREIGN KEY (`conversation_id`) REFERENCES `conversation`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sender_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `call` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NULL,
    `caller_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `type` VARCHAR(20) DEFAULT 'audio',
    `status` VARCHAR(20) DEFAULT 'missed',
    `started_at` DATETIME NULL,
    `ended_at` DATETIME NULL,
    `duration` INT DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    FOREIGN KEY (`conversation_id`) REFERENCES `conversation`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`caller_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. FRIEND REQUEST TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS `friend_request` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `status` VARCHAR(20) DEFAULT 'pending',
    `created_at` DATETIME NOT NULL,
    `responded_at` DATETIME NULL,
    FOREIGN KEY (`sender_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
    INDEX `idx_friend_sender` (`sender_id`),
    INDEX `idx_friend_receiver` (`receiver_id`),
    INDEX `idx_friend_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. ADD COLUMNS (if not exist)
-- ============================================

-- Add forum_post_id to message if not exists
SET @column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'message' 
    AND COLUMN_NAME = 'forum_post_id'
);
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE message ADD COLUMN forum_post_id INT NULL', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 4. CLEAR CACHE
-- ============================================
-- Run: php bin/console cache:clear

SELECT 'Fly&Go Messenger Setup Complete!' AS status;

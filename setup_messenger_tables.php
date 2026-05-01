<?php
/**
 * Fly&Go Messenger Setup Script
 * Run: php setup_messenger_tables.php
 */

require_once __DIR__ . '/config/bootstrap.php';

use Doctrine\DBAL\Connection;

$conn = $entityManager->getConnection();

echo "🚀 Fly&Go Messenger Setup\n";
echo "=========================\n\n";

$sqlStatements = [
    // Conversation table
    "CREATE TABLE IF NOT EXISTS `conversation` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `type` VARCHAR(20) NOT NULL DEFAULT 'private',
        `name` VARCHAR(255) NULL,
        `image` VARCHAR(500) NULL,
        `created_by_id` INT NULL,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    // Conversation Participant table
    "CREATE TABLE IF NOT EXISTS `conversation_participant` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `conversation_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `joined_at` DATETIME NOT NULL,
        `unread_count` INT DEFAULT 0,
        FOREIGN KEY (`conversation_id`) REFERENCES `conversation`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    // Message table
    "CREATE TABLE IF NOT EXISTS `message` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    // App Call table
    "CREATE TABLE IF NOT EXISTS `call` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    // Friend Request table
    "CREATE TABLE IF NOT EXISTS `friend_request` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `sender_id` INT NOT NULL,
        `receiver_id` INT NOT NULL,
        `status` VARCHAR(20) DEFAULT 'pending',
        `created_at` DATETIME NOT NULL,
        `responded_at` DATETIME NULL,
        FOREIGN KEY (`sender_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`receiver_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];

foreach ($sqlStatements as $sql) {
    try {
        $conn->executeStatement($sql);
        echo "✅ Table créée avec succès\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "✅ Table existe déjà\n";
        } else {
            echo "⚠️ Erreur: " . substr($e->getMessage(), 0, 80) . "\n";
        }
    }
}

// Add forum_post_id column to message if not exists
try {
    $conn->executeStatement("ALTER TABLE message ADD COLUMN forum_post_id INT NULL");
    echo "✅ Colonne forum_post_id ajoutée\n";
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "✅ Colonne forum_post_id existe déjà\n";
    } else {
        echo "⚠️ Colonne: " . substr($e->getMessage(), 0, 80) . "\n";
    }
}

echo "\n🎉 Setup terminé !\n";
echo "\nAPI Routes disponibles:\n";
echo "- POST /api/messages/start/{userId} - Démarrer conversation\n";
echo "- GET /api/messages/conversations - Liste conversations\n";
echo "- POST /api/share/to-conversation - Partager post\n";
echo "- GET /api/share/get-conversations - Conversations pour partage\n";
echo "- POST /api/friend/request/{userId} - Demande d'ami\n";
echo "- POST /api/friend/accept/{requestId} - Accepter ami\n";

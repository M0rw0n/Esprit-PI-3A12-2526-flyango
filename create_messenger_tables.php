<?php
require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$connectionParams = [
    'dbname' => 'pidev3a29',
    'user' => 'root',
    'password' => '',
    'host' => '127.0.0.1',
    'driver' => 'pdo_mysql',
    'serverVersion' => '8.0',
];

try {
    $conn = DriverManager::getConnection($connectionParams);
    
    // Create conversation table
    $sql = "CREATE TABLE IF NOT EXISTS `conversation` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `type` VARCHAR(50) NOT NULL DEFAULT 'private',
        `name` VARCHAR(255) NULL,
        `image` VARCHAR(500) NULL,
        `created_by_id` INT NULL,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->executeStatement($sql);
    echo "Table 'conversation' created or already exists.\n";
    
    // Create conversation_participant table
    $sql = "CREATE TABLE IF NOT EXISTS `conversation_participant` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `conversation_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `unread_count` INT DEFAULT 0,
        `last_read_at` DATETIME NULL,
        `joined_at` DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->executeStatement($sql);
    echo "Table 'conversation_participant' created or already exists.\n";
    
    // Create message table
    $sql = "CREATE TABLE IF NOT EXISTS `message` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `conversation_id` INT NOT NULL,
        `sender_id` INT NOT NULL,
        `content` TEXT NOT NULL,
        `status` VARCHAR(20) DEFAULT 'sent',
        `created_at` DATETIME NOT NULL,
        `read_at` DATETIME NULL,
        `image` VARCHAR(500) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->executeStatement($sql);
    echo "Table 'message' created or already exists.\n";
    
    // Create indexes
    try {
        $conn->executeStatement("CREATE INDEX idx_conv_user ON conversation_participant (conversation_id, user_id)");
        echo "Index idx_conv_user created.\n";
    } catch (\Exception $e) {
        echo "Index idx_conv_user already exists or error: " . $e->getMessage() . "\n";
    }
    
    try {
        $conn->executeStatement("CREATE INDEX idx_msg_conv ON message (conversation_id)");
        echo "Index idx_msg_conv created.\n";
    } catch (\Exception $e) {
        echo "Index idx_msg_conv already exists or error: " . $e->getMessage() . "\n";
    }
    
    // Create call table
    $sql = "CREATE TABLE IF NOT EXISTS `app_call` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `caller_id` INT NOT NULL,
        `receiver_id` INT NOT NULL,
        `conversation_id` INT NULL,
        `status` VARCHAR(20) NOT NULL DEFAULT 'missed',
        `type` VARCHAR(20) NOT NULL DEFAULT 'audio',
        `created_at` DATETIME NOT NULL,
        `duration` INT NULL,
        `started_at` DATETIME NULL,
        `ended_at` DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->executeStatement($sql);
    echo "Table 'app_call' created or already exists.\n";
    
    // Create indexes for call table
    try {
        $conn->executeStatement("CREATE INDEX idx_call_caller ON app_call (caller_id)");
        echo "Index idx_call_caller created.\n";
    } catch (\Exception $e) {
        echo "Index idx_call_caller already exists.\n";
    }
    
    try {
        $conn->executeStatement("CREATE INDEX idx_call_receiver ON app_call (receiver_id)");
        echo "Index idx_call_receiver created.\n";
    } catch (\Exception $e) {
        echo "Index idx_call_receiver already exists.\n";
    }
    
    try {
        $conn->executeStatement("CREATE INDEX idx_call_conversation ON app_call (conversation_id)");
        echo "Index idx_call_conversation created.\n";
    } catch (\Exception $e) {
        echo "Index idx_call_conversation already exists.\n";
    }
    
    echo "\nAll messaging tables created successfully!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

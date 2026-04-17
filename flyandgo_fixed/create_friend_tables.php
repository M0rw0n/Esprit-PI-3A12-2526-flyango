<?php

$friendly = require_once __DIR__ . '/../config/bootstrap.php';

use Doctrine\ORM\Tools\SchemaTool;
use App\Entity\FriendRequest;

$em = $entityManager;

$tool = new SchemaTool($em);
$classes = [$em->getClassMetadata(FriendRequest::class)];

try {
    $tool->createSchema($classes);
    echo "Table 'friend_request' created successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    $conn = $em->getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS friend_request (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        created_at DATETIME NOT NULL,
        responded_at DATETIME NULL,
        INDEX idx_friend_sender (sender_id),
        INDEX idx_friend_receiver (receiver_id),
        INDEX idx_friend_status (status),
        FOREIGN KEY (sender_id) REFERENCES `user`(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES `user`(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $conn->executeStatement($sql);
        echo "Table 'friend_request' created via raw SQL!\n";
    } catch (\Exception $e2) {
        echo "Raw SQL also failed: " . $e2->getMessage() . "\n";
    }
}

$conn = $em->getConnection();
try {
    $conn->executeStatement("ALTER TABLE message ADD COLUMN IF NOT EXISTS forum_post_id INT NULL");
    echo "Column 'forum_post_id' added to 'message' table!\n";
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column 'forum_post_id' already exists in 'message' table.\n";
    } else {
        echo "Note: " . $e->getMessage() . "\n";
    }
}

echo "\nFriend Request system ready!\n";
echo "\nAPI Routes:\n";
echo "- POST /api/friend/request/{userId} - Send friend request\n";
echo "- POST /api/friend/accept/{requestId} - Accept request\n";
echo "- POST /api/friend/reject/{requestId} - Reject request\n";
echo "- POST /api/friend/cancel/{requestId} - Cancel sent request\n";
echo "- GET /api/friend/received - Get received requests\n";
echo "- GET /api/friend/sent - Get sent requests\n";
echo "- GET /api/friend/list - Get friends list\n";
echo "- GET /api/friend/status/{userId} - Get friendship status\n";
echo "- GET /api/friend/pending/count - Get pending requests count\n";

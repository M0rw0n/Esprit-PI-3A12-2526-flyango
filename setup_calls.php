<?php
$host = '127.0.0.1';
$db   = 'pidev3a29';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE TABLE IF NOT EXISTS pending_calls (
        id INT AUTO_INCREMENT PRIMARY KEY,
        from_user_id INT NOT NULL,
        to_user_id INT NOT NULL,
        call_type VARCHAR(20) NOT NULL DEFAULT 'audio',
        sdp TEXT,
        status VARCHAR(20) NOT NULL DEFAULT 'calling',
        created_at DATETIME NOT NULL,
        INDEX idx_to_user (to_user_id),
        INDEX idx_status (status)
    )";
    
    $pdo->exec($sql);
    echo "✅ Table 'pending_calls' creee avec succes!\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
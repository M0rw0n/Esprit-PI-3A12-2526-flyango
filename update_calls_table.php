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
    
    // Add offer, answer, ice_candidate columns
    $pdo->exec("ALTER TABLE pending_calls ADD COLUMN offer TEXT");
    $pdo->exec("ALTER TABLE pending_calls ADD COLUMN answer TEXT");
    $pdo->exec("ALTER TABLE pending_calls ADD COLUMN ice_candidate TEXT");
    $pdo->exec("ALTER TABLE pending_calls MODIFY status VARCHAR(20) DEFAULT 'calling'");
    
    echo "✅ Table updated with offer, answer, ice_candidate columns!\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
-- Tables pour la messagerie (Messenger-like)
-- Exécuter ces requêtes dans votre base de données MySQL

-- 1. Table des conversations
CREATE TABLE IF NOT EXISTS conversation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL DEFAULT 'private',
    name VARCHAR(255) DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    created_by_id INT DEFAULT NULL,
    INDEX idx_type (type),
    FOREIGN KEY (created_by_id) REFERENCES `user`(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table des participants aux conversations
CREATE TABLE IF NOT EXISTS conversation_participant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    user_id INT NOT NULL,
    unread_count INT NOT NULL DEFAULT 0,
    last_read_at DATETIME DEFAULT NULL,
    joined_at DATETIME NOT NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_user (user_id),
    UNIQUE KEY unique_conv_user (conversation_id, user_id),
    FOREIGN KEY (conversation_id) REFERENCES conversation(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES `user`(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table des messages
CREATE TABLE IF NOT EXISTS message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    content LONGTEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'sent',
    created_at DATETIME NOT NULL,
    read_at DATETIME DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender (sender_id),
    INDEX idx_status (status),
    FOREIGN KEY (conversation_id) REFERENCES conversation(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES `user`(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exemple: Créer une conversation privée entre 2 utilisateurs
INSERT INTO conversation (type, created_at, updated_at, created_by_id) 
VALUES ('private', NOW(), NOW(), 1);

SET @conv_id = LAST_INSERT_ID();

INSERT INTO conversation_participant (conversation_id, user_id, joined_at) 
VALUES 
(@conv_id, 1, NOW()),
(@conv_id, 2, NOW());

-- Exemple: Ajouter un message
INSERT INTO message (conversation_id, sender_id, content, status, created_at)
VALUES (@conv_id, 1, 'Bonjour! Comment allez-vous?', 'sent', NOW());
CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT NOT NULL,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    keywords VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO faq (question, answer, keywords) VALUES 
('Comment réserver ?', 'Pour effectuer une réservation, rendez-vous sur la page du service souhaité (hébergement, circuit, transport ou activité). Cliquez sur le bouton ''Réserver'' et suivez les étapes.', 'reserver, réservation, commande, acheter'),
('Comment annuler ?', 'Pour annuler une réservation, allez dans ''Mon espace'' > ''Mes réservations''. Cliquez sur la réservation concernée puis sur ''Annuler''.', 'annuler, annulation, remboursement, supprimer'),
('Quels sont les modes de paiement ?', 'Nous acceptons les paiements par carte bancaire (Visa, Mastercard), virement bancaire et PayPal.', 'paiement, payer, carte, virement, paypal'),
('Où trouver ma facture ?', 'Après paiement, vous pouvez télécharger vos factures depuis ''Mon espace'' > ''Mes réservations''.', 'facture, reçu, invoice, télécharger');

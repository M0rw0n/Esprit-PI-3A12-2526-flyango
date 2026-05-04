<?php

namespace App\Service;

class NLPService
{
    private array $intents = [];
    private array $stopWords = [];
    private array $wordWeights = [];
    
    public function __construct()
    {
        $this->initStopWords();
        $this->initIntents();
        $this->initWordWeights();
    }

    private function initStopWords(): void
    {
        $this->stopWords = [
            'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux',
            'et', 'ou', 'mais', 'donc', 'car', 'ni', 'que', 'qui',
            'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles',
            'mon', 'ma', 'mes', 'ton', 'ta', 'tes', 'son', 'sa', 'ses',
            'ce', 'cet', 'cette', 'ces', 'quel', 'quelle', 'quels', 'quelles',
            'me', 'te', 'se', 'lui', 'leur', 'moi', 'toi', 'soi',
            'être', 'avoir', 'faire', 'pouvoir', 'vouloir', 'devoir',
            'oui', 'non', 'bien', 'mal', 'plus', 'moins', 'aussi',
            'dans', 'sur', 'sous', 'avec', 'sans', 'pour', 'par', 'en',
            'est', 'sont', 'été', 'fait', 'était', 'étaient'
        ];
    }

    private function initIntents(): void
    {
        $this->intents = [
            'hotel_reservation' => [
                'name' => 'Réservation hôtel',
                'keywords' => [
                    'réserver', 'réservation', 'reserver', 'booking', 'chambre', 
                    'hôtel', 'hotel', 'hébergements', 'hebergement', 'proposition',
                    'propositions', 'recommander', 'recommendation', 'quel hotel',
                    'trouver hotel', 'chercher hotel', 'disponible', 'luxe', 'pas cher',
                    'économique', 'pas cher', 'milieu', 'premium', '推荐', '预定'
                ],
                'required_words' => ['hotel', 'réserver', 'réservation', 'reserver', 'booking', 'chambre', 'proposition', 'hébergements', 'hebergement', 'proposer', 'recommander'],
                'response' => "Pour une réservation d'hôtel à Paris, plusieurs options s'offrent à vous:

🏨 **Nos recommandations par quartier:**
• 🗼 **Champs-Élysées** - Pour le prestige et la vue sur la Tour Eiffel
• 🎨 **Le Marais** - Pour le côté branché et culturel  
• 🏛️ **La Défense** - Pour les voyageurs d'affaires
• 🌙 **Montmartre** - Pour l'authenticité parisienne

💰 **Budget:**
• Économique: Formule 1, Ibis Budget
• Milieu: Novotel, Mercure, Hilton
• Premium: Le Meurice, Ritz, Four Seasons

📱 **Comment réserver:**
1. Allez sur la page Hébergements
2. Utilisez les filtres pour Paris
3. Cliquez sur Réserver

💡 Vous pouvez aussi nous contacter directement pour des recommandations personnalisées!"
            ],

            'flight_booking' => [
                'name' => 'Réservation vol',
                'keywords' => ['vol', 'vols', 'avion', 'billet', 'billets', 'voler', 'départ', 'arrivée', 'compagnie', 'transport'],
                'required_words' => ['vol', 'avion', 'billet'],
                'response' => "Pour réserver un vol, voici comment procéder:

✈️ **Options disponibles:**
• Vols réguliers vers toutes destinations
• Comparaison des compagnies
• Meilleurs tarifs garantis

🔍 **Comment réserver:**
1. Allez dans la page Transport
2. Entrez votre ville de départ et destination
3. Choisissez vos dates
4. Comparez les prix
5. Réservez en un clic

💡 Conseil: Réservez tôt pour les meilleurs prix!"
            ],

            'circuit_booking' => [
                'name' => 'Réservation circuit',
                'keywords' => ['circuit', 'roadtrip', 'parcours', 'générer', 'generer', 'créer', 'creer', 'personnalisé', 'sur mesure', 'ia', 'ai', 'itinerary', 'itineraire', 'voyage organisé'],
                'required_words' => ['circuit', 'roadtrip', 'générer', 'generer', 'créer', 'personnalisé', 'sur mesure', 'ia', 'ai'],
<<<<<<< HEAD
                'response' => "Découvrez nos circuits exceptionnels ! 🌍

🗺️ **Options disponibles :**
• Circuits culturels et historiques
• Aventures dans le désert
• Escapades balnéaires (Djerba, Hammamet...)
• Circuits personnalisés avec notre IA (si activée)

🔍 **Comment voir nos circuits :**
1. Allez dans la section [Circuits](/circuits)
2. Utilisez les filtres (destination, budget, durée)
3. Sélectionnez votre circuit idéal
4. Cliquez sur 'Réserver'

💡 Vous pouvez aussi créer votre propre itinéraire dans votre espace personnel !"
=======
                'response' => "Pour un circuit personnalisé, utilisez notre assistant IA!

🗺️ **Options:**
• Circuits prédéfinis variés
• Création de circuit sur mesure
• IA qui génère votre itinéraire

🤖 **Comment créer un circuit IA:**
1. Allez dans Circuits
2. Cliquez sur 'Créer avec l\'IA'
3. Décrivez vos préférences
4. L'IA génère votre circuit!

💡 Notre IA prend en compte: budget, durée, style de voyage, centres d'intérêt.
Essayez-le, c'est gratuit et rapide!"
>>>>>>> testsisi
            ],

            'paris_travel' => [
                'name' => 'Voyage Paris',
                'keywords' => ['paris', 'france', 'paris conseils', 'paris tips', 'visiter paris', 'voyage paris', 'aller a paris', 'je veux aller', 'conseils paris', 'guide paris'],
                'required_words' => ['paris'],
                'response' => "Pour un voyage à Paris 🗼:

🏨 **Hébergements:**
Le Marais, Montmartre, Champs-Élysées, Quartier Latin

🎫 **Monuments:**
Tour Eiffel, Louvre, Notre-Dame, Champs-Élysées, Montmartre (Sacré-Cœur)

🚇 **Transport:**
Metro (le plus pratique!), Bus RATP, Vélib' (vélo)

🍽️ **Spécialités:**
Croissants, crêpes, bistrots, fromages et vins

💡 **Conseils:**
• Paris Museum Pass pour les musées
• Évitez juillet-aout
• Réservez les musées à l'avance
• Achetez un carnet de metro

Pour réserver, utilisez notre page Hébergements!"
            ],

            'contact' => [
                'name' => 'Contact',
                'keywords' => ['contact', 'contacter', 'email', 'téléphone', 'telephone', 'appeler', 'joindre', 'whatsapp', 'support', 'aide', 'numero', 'adresse', 'localisation', 'probleme', 'question'],
                'required_words' => ['contact', 'contacter', 'téléphone', 'telephone', 'appeler', 'joindre', 'support', 'aide'],
                'response' => "Contactez-nous facilement:

📧 **Email:** contact@flyandgo.tn
📞 **Téléphone:** +216 12 345 678
💬 **WhatsApp:** +216 12 345 678

🕐 **Horaire:** Lun-Ven: 9h-18h

💡 Pour une réponse rapide, donnez-nous le maximum de détails sur votre demande!"
            ],

            'general_booking' => [
                'name' => 'Réservation générale',
                'keywords' => ['réservation', 'reserver', 'book', 'booking', 'commande', 'acheter', 'payer', 'comment reserver', 'etapes', 'processus'],
                'required_words' => ['réservation', 'reserver', 'book', 'booking'],
                'response' => "Pour effectuer une réservation sur Fly&Go:

📋 **Étapes:**
1. Choisissez votre service (hébergement, circuit, transport, activité)
2. Sélectionnez vos dates et options
3. Cliquez sur 'Réserver'
4. Remplissez vos informations
5. Effectuez le paiement
6. Recevez votre confirmation par email

💡 **Services disponibles:**
• 🏨 Hébergements
• 🗺️ Circuits
• ✈️ Transport
• 🎯 Activités

Visitez les pages correspondantes pour commencer votre réservation!"
            ],

            'cancel' => [
                'name' => 'Annulation',
                'keywords' => ['annuler', 'annulation', 'cancel', 'supprimer', 'suppression', 'remboursement', 'rembourser', 'annule', 'annuler reservation'],
                'required_words' => ['annuler', 'annulation', 'cancel', 'remboursement', 'rembourser'],
                'response' => "Pour annuler une réservation:

❌ **Procédure:**
1. Allez dans 'Mon espace'
2. Cliquez sur 'Mes réservations'
3. Sélectionnez la réservation à annuler
4. Cliquez sur 'Annuler'

⚠️ **Attention:**
• Des frais d'annulation peuvent s'appliquer
• Selon le type de réservation

💰 **Remboursement:**
• Sous 5-10 jours ouvrés

Contactez-nous: contact@flyandgo.tn"
            ],

            'payment' => [
                'name' => 'Paiement',
                'keywords' => ['paiement', 'payer', 'carte', 'credit', 'virement', 'paypal', 'prix', 'tarif', 'moyen', 'secures', 'sécurisé'],
                'required_words' => ['paiement', 'payer', 'carte', 'virement', 'paypal'],
                'response' => "Moyens de paiement disponibles:

💳 **Carte bancaire:**
Visa, Mastercard, autres cartes

🏦 **Virement bancaire:**
IBAN fourni lors de la réservation

🅿️ **PayPal:**
Option disponible

🔒 **Sécurité:**
Paiement sécurisé, Chiffrement SSL

Les prix sont affichés en euros (€)."
            ],

            'profile' => [
                'name' => 'Profil voyageur',
                'keywords' => ['profil', 'voyageur', 'préférences', 'preferences', 'personnaliser', 'style', 'budget', 'durée', 'goûts', 'gouts', 'modifier', 'complet'],
                'required_words' => ['profil', 'voyageur', 'préférences', 'preferences'],
                'response' => "Optimisez votre expérience avec votre profil voyageur!

👤 **Pourquoi compléter votre profil:**
• Recommandations personnalisées
• Circuits IA adaptés à vos goûts
• Offres spéciales

📝 **Comment faire:**
1. Allez dans 'Mon espace'
2. Cliquez sur 'Mon profil voyageur'
3. Remplissez vos préférences

💡 Un profil complet aide notre IA à vous proposer des circuits parfaitement adaptés!"
            ],

            'activity' => [
                'name' => 'Activité',
                'keywords' => ['activité', 'activites', 'excursion', 'visite', 'guide', 'randonnée', 'rando', 'decouverte', 'experience', 'que faire', 'quoi faire'],
                'required_words' => ['activité', 'activites', 'excursion', 'visite'],
                'response' => "Découvrez nos activités et excursions:

🎯 **Types d'activités:**
• Visites culturelles et historiques
• Excursions et roadtrips
• Activités adventure
• Détente et bien-être
• Expériences locales

🔍 **Comment trouver:**
Allez sur la page Activités pour voir toutes les options!"
            ],

            'greeting' => [
                'name' => 'Salutation',
                'keywords' => ['bonjour', 'salut', 'hello', 'hi', 'coucou', 'hey', 'bonsoir', 'bienvenue'],
                'required_words' => ['bonjour', 'salut', 'hello', 'hi', 'coucou', 'hey', 'bonsoir'],
                'response' => "Bonjour! 👋 Je suis l'assistant Fly&Go, votre assistant de voyage!

Je peux vous aider avec:
🏨 Réservations d'hébergement
✈️ Billets d'avion
🗺️ Circuits et voyages
🎯 Activités et excursions
💰 Comparateur de prix
❓ Questions générales

Comment puis-je vous aider aujourd'hui?"
            ],

            'thanks' => [
                'name' => 'Remerciement',
                'keywords' => ['merci', 'thanks', 'thank', 'remercier', 'bravo', 'super', 'genial', 'parfait', 'nickel', 'impeccable', 'génial', 'magnifique', 'excellent'],
                'required_words' => ['merci', 'thanks', 'bravo', 'super', 'genial', 'parfait'],
                'response' => "De rien! 😊 Je suis ravi de pouvoir vous aider!

N'hésitez pas si vous avez d'autres questions.
Bon voyage avec Fly&Go! ✈️🌍"
            ],

            'goodbye' => [
                'name' => 'Au revoir',
                'keywords' => ['au revoir', 'bye', 'adieu', 'ciao', 'see you', 'goodbye', 'à bientôt', 'aurevoir', 'salut'],
                'required_words' => ['au revoir', 'bye', 'adieu', 'ciao', 'salut'],
                'response' => "Au plaisir de vous revoir! 👋

Bonne continuation et meilleurs voyages avec Fly&Go! ✈️🌍🌴"
            ]
        ];
    }

    private function initWordWeights(): void
    {
        $this->wordWeights = [
            'réservation' => 10, 'réserver' => 10, 'reserver' => 10, 'booking' => 10,
            'hôtel' => 8, 'hotel' => 8, 'chambre' => 8, 'hébergements' => 8,
            'vol' => 8, 'avion' => 8, 'billet' => 8,
            'circuit' => 8, 'voyage' => 6, 'roadtrip' => 8,
            'paris' => 7, 'tunisie' => 7, 'maroc' => 7,
            'excursion' => 7, 'visite' => 5,
            'annuler' => 8, 'remboursement' => 8,
            'paiement' => 7, 'payer' => 7,
            'profil' => 6, 'voyageur' => 6,
            'contact' => 7, 'téléphone' => 7,
            'proposition' => 5, 'conseils' => 5, 'recommander' => 5,
            'proposer' => 3, 'trouver' => 3, 'chercher' => 3,
            'je' => 1, 'veux' => 2, 'voudrais' => 2, 'donner' => 2
        ];
    }

<<<<<<< HEAD
    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y'
        ];
        $text = strtr($text, $accents);
        $text = preg_replace('/[^a-z0-9 ]/i', ' ', $text);
        return preg_replace('/\s+/', ' ', $text);
    }

    public function classifyIntent(string $message): array
    {
        $normalizedMessage = $this->normalizeText($message);
        $words = explode(' ', $normalizedMessage);
        
        $scores = [];
        foreach ($this->intents as $intent => $data) {
            $scores[$intent] = 0;
            
            // Check direct keyword match in normalized message
            foreach ($data['keywords'] as $keyword) {
                $normalizedKeyword = $this->normalizeText($keyword);
                if (mb_strpos($normalizedMessage, $normalizedKeyword) !== false) {
                    $weight = $this->wordWeights[$normalizedKeyword] ?? 5;
                    $scores[$intent] += $weight;
                }
            }
            
            // Check required words (normalized)
            if (isset($data['required_words'])) {
                $hasRequired = false;
                foreach ($data['required_words'] as $req) {
                    if (mb_strpos($normalizedMessage, $this->normalizeText($req)) !== false) {
                        $hasRequired = true;
                        break;
                    }
                }
                if (!$hasRequired && count($data['required_words']) > 0) {
                    $scores[$intent] = 0;
                }
            }
        }
        
        arsort($scores);
        $bestIntent = array_key_first($scores);
        $confidence = $scores[$bestIntent] ?? 0;
        
        return [
            'intent' => $confidence > 0 ? $bestIntent : null,
            'confidence' => $confidence
        ];
    }

    public function getResponse(string $message): ?string
=======
    public function classifyIntent(string $message): array
    {
        $message = mb_strtolower(trim($message));
        
        $scores = [];
        
        foreach ($this->intents as $intentId => $intent) {
            $score = $this->calculateScore($message, $intent);
            if ($score > 0) {
                $scores[$intentId] = $score;
            }
        }
        
        if (empty($scores)) {
            return ['intent' => null, 'confidence' => 0];
        }
        
        arsort($scores);
        $topIntent = array_key_first($scores);
        
        return [
            'intent' => $topIntent,
            'confidence' => $scores[$topIntent],
            'intent_name' => $this->intents[$topIntent]['name'] ?? ''
        ];
    }

    private function calculateScore(string $message, array $intent): float
    {
        $score = 0;
        
        if (isset($intent['required_words'])) {
            $requiredFound = 0;
            foreach ($intent['required_words'] as $word) {
                if (mb_strpos($message, $word) !== false) {
                    $requiredFound++;
                }
            }
            if ($requiredFound > 0) {
                $score += $requiredFound * 5;
            } else {
                return 0;
            }
        }
        
        if (isset($intent['keywords'])) {
            foreach ($intent['keywords'] as $keyword) {
                if (mb_strpos($message, $keyword) !== false) {
                    $weight = $this->wordWeights[$keyword] ?? 2;
                    $score += $weight;
                    
                    if (mb_strlen($keyword) > 5) {
                        $score += 2;
                    }
                }
            }
        }
        
        return $score;
    }

    public function getResponse(string $message): string
>>>>>>> testsisi
    {
        $classification = $this->classifyIntent($message);
        
        if (!$classification['intent'] || $classification['confidence'] < 3) {
            return null;
        }
        
        return $this->intents[$classification['intent']]['response'] ?? null;
    }

    public function getIntentInfo(string $intentId): ?array
    {
        return $this->intents[$intentId] ?? null;
    }

    public function getAllIntents(): array
    {
        return array_map(function($intent, $id) {
            return [
                'id' => $id,
                'name' => $intent['name'],
                'keywords_count' => count($intent['keywords'] ?? [])
            ];
        }, $this->intents, array_keys($this->intents));
    }
}
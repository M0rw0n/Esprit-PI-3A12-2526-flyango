<?php

namespace App\Service;

<<<<<<< HEAD
use App\Repository\FAQRepository;
use App\Repository\CircuitRepository;
use App\Service\HuggingFaceService;
use App\Service\LocalEmbeddingService;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

=======
>>>>>>> testsisi
class ChatbotService
{
    private ?OpenAIService $openAIService;
    private ?NLPService $nlpService;
<<<<<<< HEAD
    private ?FAQRepository $faqRepository;
    private ?CircuitRepository $circuitRepository;
    private ?HuggingFaceService $hfService;
    private LocalEmbeddingService $localEmbeddingService;
    private ?CacheInterface $cache;
    private array $faq = [];
    private string $fallbackMessage = "Désolé, je ne trouve pas de réponse précise à votre demande. 🧐\n\nVoici ce que vous pouvez faire :\n• Reformulez votre question avec des mots-clés simples (ex: 'hôtel', 'circuit', 'vol', 'Djerba')\n• Créez un circuit personnalisé : /circuits/personnalise\n• Consultez nos circuits : /circuits\n• Contactez notre support via le forum\n\nJe reste à votre disposition !";

    public function __construct(
        ?OpenAIService $openAIService = null,
        ?NLPService $nlpService = null,
        ?FAQRepository $faqRepository = null,
        ?CircuitRepository $circuitRepository = null,
        ?HuggingFaceService $hfService = null,
        ?CacheInterface $cache = null
=======
    private array $faq = [];
    private string $fallbackMessage = "Je n'ai pas trouvé de réponse précise à votre question. Vous pouvez nous contacter directement via le forum ou le support. Nous serions ravis de vous aider!";

    public function __construct(
        ?OpenAIService $openAIService = null,
        ?NLPService $nlpService = null
>>>>>>> testsisi
    )
    {
        $this->openAIService = $openAIService;
        $this->nlpService = $nlpService;
<<<<<<< HEAD
        $this->faqRepository = $faqRepository;
        $this->circuitRepository = $circuitRepository;
        $this->hfService = $hfService;
        $this->localEmbeddingService = new LocalEmbeddingService();
        $this->cache = $cache;
=======
>>>>>>> testsisi
        $this->initFaq();
    }

    private function initFaq(): void
    {
        $this->faq = [
            'booking' => [
                'keywords' => ['booking', 'reserver', 'reservation', 'reserve', 'commande', 'book', 'acheter', 'achete', 'payer', 'paye', 'achats', 'achat'],
                'response' => "Pour effectuer une réservation, rendez-vous sur la page du service souhaité (hébergement, circuit, transport ou activité). Cliquez sur le bouton 'Réserver' et suivez les étapes. Vous recevrez un email de confirmation après paiement."
            ],
            'cancel' => [
                'keywords' => ['annuler', 'annulation', 'cancel', 'supprimer', 'suppression', 'remboursement', 'rembourser', 'remise'],
                'response' => "Pour annuler une réservation, allez dans 'Mon espace' > 'Mes réservations'. Cliquez sur la réservation concernée puis sur 'Annuler'. Attention : des frais d'annulation peuvent s'appliquer selon les conditions."
            ],
            'flight' => [
                'keywords' => ['vol', 'vols', 'avion', 'voler', 'flight', 'plane', 'depart', 'arrivee', 'bagage', 'bagages', 'valise', 'billet', 'billets'],
                'response' => "Pour les transports en avion, consultez notre page Transport. Nous comparons les compagnies aériennes pour vous trouver les meilleurs tarifs. N'oubliez pas de vérifier les conditions de bagages."
            ],
            'hotel' => [
                'keywords' => ['hotel', 'hebergement', 'logement', 'chambre', 'sejour', 'hostel', 'resort', 'maison', 'hote', 'villa', 'appartement', 'studio'],
                'response' => "Découvrez nos hébergements disponibles sur la page Hébergements. Nous proposons des hôtels, maisons d'hôtes, villas et resorts. Utilisez les filtres pour trouver selon votre budget et vos préférences."
            ],
            'circuit' => [
                'keywords' => ['circuit', 'circuits', 'roadtrip', 'voyage', 'organise', 'parcours', 'itinerary', 'itineraire', 'tours', 'trek', 'randonnee'],
                'response' => "Explorez nos circuits sur la page Circuits. Vous pouvez aussi créer votre propre circuit personnalisé avec notre assistant IA via 'Circuit personnalisé' dans le menu!"
            ],
            'activity' => [
                'keywords' => ['activite', 'activites', 'excursion', 'excursions', 'visite', 'visites', 'guide', 'tour', 'experience', 'decouverte', ' Randonnée', 'rando'],
                'response' => "Découvrez les activités disponibles sur la page Activités. Excursions, visites guidées, expériences locales... Il y en a pour tous les goûts!"
            ],
            'payment' => [
                'keywords' => ['paiement', 'payer', 'carte', 'credit', 'virement', 'paypal', 'prix', 'tarif', 'cout', 'coute', 'banque', 'versement'],
                'response' => "Nous acceptons les paiements par carte bancaire (Visa, Mastercard), virement bancaire et PayPal. Les prix sont indiqués en euros. Un paiement sécurisé est effectué lors de la réservation."
            ],
            'account' => [
                'keywords' => ['compte', 'profil', 'identifiant', 'mot de passe', 'password', 'inscription', 'inscrire', 'register', 'connexion', 'login', 'compte'],
                'response' => "Créez un compte via le bouton 'Inscription' en haut à droite. Vous pourrez ensuite gérer vos réservations, favoris, historique et profil voyageur depuis 'Mon espace'."
            ],
            'favorites' => [
                'keywords' => ['favori', 'favorite', 'favoris', 'heart', 'coeur', 'saved', 'sauvegarder', 'bookmark', 'liked', 'aime', 'love'],
                'response' => "Pour sauvegarder vos favoris, cliquez sur l'icône ❤️ sur les hébergements, circuits, activités ou transports. Accédez à tous vos favoris via 'Favoris' dans le menu utilisateur."
            ],
            'history' => [
                'keywords' => ['historique', 'history', 'recherche', 'search', 'parcours', 'past', 'dernier', 'anciens', 'passes'],
                'response' => "Votre historique de navigation et réservations est disponible dans 'Mon espace' > 'Historique'. Vous pouvez filtrer par type: réservations, favoris ou recherches."
            ],
            'contact' => [
                'keywords' => ['contact', 'email', 'telephone', 'telephoner', 'appeler', 'whatsapp', 'support', 'aide', 'help', 'numero', 'appeler', 'joindre'],
                'response' => "Contactez-nous: Email: contact@flyandgo.tn | Téléphone: +216 12 345 678 | Nous répondons généralement sous 24h. Vous pouvez aussi poster sur le forum!"
            ],
            'ai_circuit' => [
                'keywords' => ['ia', 'ai', 'generer', 'generer', 'creer', 'personnalise', 'sur mesure', 'custom', 'assistant', 'robot', 'intelligent', 'smart'],
                'response' => "Utilisez notre assistant IA pour créer un circuit personnalisé! Cliquez sur 'Circuit personnalisé' dans le menu ou allez dans Circuits > Créer avec l'IA. Décrivez vos préférences et l'IA génère un itinéraire adapté."
            ],
            'profile' => [
                'keywords' => ['profil', 'voyageur', 'preference', 'preferences', 'type', 'style', 'budget', 'duree', 'personnalite', 'gout', 'gouts'],
                'response' => "Complétez votre profil voyageur dans 'Mon espace' > 'Mon profil voyageur'. Cela aide nos recommandations et l'IA à vous proposer des circuits adaptés à vos préférences!"
            ],
            'forum' => [
                'keywords' => ['forum', 'discussion', 'discuter', 'question', 'reponse', 'avis', 'comment', 'post', 'commentaire', 'communaute', 'communauté'],
                'response' => "Rejoignez notre forum pour poser des questions, partager vos expériences et obtenir des conseils d'autres voyageurs. Accédez via le menu 'Forum'."
            ],
            
            'reviews' => [
                'keywords' => ['avis', 'review', 'note', 'rating', 'etoile', 'etoiles', 'star', 'stars', 'feedback', 'commentaire', 'opinion', 'noter'],
                'response' => "Laissez un avis après votre séjour! Allez dans 'Mon espace' > 'Mes avis'. Vos retours aident la communauté et les futurs voyageurs à faire leur choix."
            ],
            'delivery' => [
                'keywords' => ['livraison', 'delivery', 'confirmation', 'recu', 'facture', 'invoice', 'pdf', 'telecharger', 'download', 'reçu', 'confirmer'],
                'response' => "Après paiement, vous recevez un email de confirmation avec votre reçu. Vous pouvez aussi télécharger vos factures depuis 'Mon espace' > 'Mes réservations'."
            ],
            'language' => [
                'keywords' => ['langue', 'language', 'francais', 'français', 'english', 'anglais', 'arabe', 'arabic', 'traduction', 'traduit', 'traduire'],
                'response' => "Le site est actuellement disponible en français. Nous travaillons pour ajouter d'autres langues. Pour toute question, n'hésitez pas à nous contacter!"
            ],
            'destinations' => [
                'keywords' => ['destination', 'destinations', 'aller', 'voyage', 'trip', 'vacances', 'holidays', 'tunisie', 'maroc', 'egypte', 'egypte', 'espagne', 'italie', 'turquie', 'grece', 'algerie', 'londres', 'berlin', 'amsterdam', 'barcelone', 'madrid'],
                'response' => "Découvrez nos destinations populaires: Tunisie (Djerba, Hammamet, Tunis), Maroc (Marrakech, Fès), Égypte (Le Caire, Louxor), Europe (Londres, Barcelonne, Madrid) et plus encore! Explorez les sections correspondantes du site."
            ],
            'search_destination' => [
                'keywords' => ['rechercher', 'chercher', 'trouver', 'voir', 'afficher', 'lista', 'list', 'cherche', 'besoin'],
                'response' => '__SEARCH__'
            ],
            'filter_by_country' => [
                'keywords' => ['pays', 'pays', 'ville', 'region', 'zone', 'endroit', 'lieu'],
                'response' => '__FILTER__'
            ],
            'paris_advice' => [
                'keywords' => ['paris', 'france', 'paris conseils', 'paris tips', 'visiter paris', 'voyage paris', 'paris voyage', 'je veux aller a paris', 'aller a paris', 'conseils paris', 'guide paris'],
                'response' => "Pour un voyage à Paris 🗼:\n\n🏨 Hébergements: Le Marais, Montmartre ou靠近卢浮宫\n\n🎫 Monuments: Tour Eiffel, Louvre, Notre-Dame, Champs-Élysées\n\n🚇 Transport: Metro (c'est le plus pratique!), bus, Vélib'\n\n🍽️ Cuisine: Croissants, crêpes, bistrots parisiens\n\n💡 Conseils: Achetez le Paris Museum Pass, évitez les saisons touristiqueuses (juillet-aout), réservez les musées à l'avance!\n\nPour réserver un séjour à Paris, contactez-nous!"
            ],
            'paris_hotel' => [
                'keywords' => ['paris hotel', 'hotel paris', 'réservation paris', 'reserver paris', 'hôtel paris', 'reserve hotel paris', 'réserver hotel paris', 'booking paris', 'sejour paris', 'hébergements paris', 'hebergement paris', 'proposition hotel', 'propositions hotel', 'hotel recommandé', 'hotel recommandation', 'quel hotel', 'trouver hotel paris', 'chercher hotel'],
                'response' => "Pour une réservation d'hôtel à Paris, plusieurs options s'offrent à vous:\n\n🏨 **Nos recommandations par quartier:**\n• 🗼 **Champs-Élysées** - Pour le prestige et la vue sur la Tour Eiffel\n• 🎨 **Le Marais** - Pour le côté branché et culturel\n• 🏛️ **La Défense** - Pour les business travelers\n• 🌙 **Montmartre** - Pour l'authenticité parisienne\n\n💰 **Budget:**\n• Économique: Formule 1, Ibis Budget\n• Milieu: Novotel, Mercure, Hilton\n• Premium: Le Meurice, Ritz, Four Seasons\n\n📱 **Comment réserver:**\n1. Allez sur la page Hébergements\n2. Utilisez les filtres pour Paris\n3. Cliquez sur Réserver\n\n💡 Vous pouvez aussi nous contacter directement pour des recommandations personnalisées!"
            ],
            'refund_policy' => [
                'keywords' => ['remboursement', 'rembourser', 'promo', 'promotion', 'reduction', 'reduire', 'code', 'coupon', 'offre', 'special', 'remise', 'gratuit', 'gratuit'],
                'response' => "Les conditions de remboursement varient selon le type de réservation. Consultez les CGU lors de la réservation. Pour les promotions, utilisez les codes promo lors du paiement."
            ],
            'greeting' => [
                'keywords' => ['bonjour', 'salut', 'hello', 'hi', 'coucou', 'hey', 'bonsoir', 'morning', 'good morning', 'good evening'],
                'response' => "Bonjour! Je suis ravi de vous aider. Comment puis-je vous accompagner aujourd'hui? Vous pouvez me poser des questions sur les réservations, hébergements, circuits, transports, activités et plus encore!"
            ],
            'thanks' => [
                'keywords' => ['merci', 'thanks', 'thank you', 'remercier', 'appreciate', 'bravo', 'super', 'genial', 'parfait', ' Nickel', 'impeccable'],
                'response' => "De rien! Je suis là pour vous aider. N'hésitez pas si vous avez d'autres questions. Bonne exploration sur Fly&Go! 🌟"
            ],
            'goodbye' => [
                'keywords' => ['au revoir', 'bye', 'adieu', 'salut', 'a plus', 'a+', 'ciao', 'see you', 'goodbye'],
                'response' => "Au plaisir de vous revoir! Bonne continuation avec Fly&Go et vos prochains voyages! ✈️🌍"
            ],
            'voyage_paris' => [
                'keywords' => ['paris', 'aller a paris', 'voyage paris', 'vol paris', 'billet paris', 'date depart', 'periode'],
                'response' => "Je peux vous aider à organiser votre voyage : vols, hôtels et activités. Quelle est votre date de départ ?"
            ],
            'prix_billet' => [
                'keywords' => ['prix', 'coute', 'cher', 'combien', 'tarif', 'cout'],
                'response' => "Les prix varient selon la période. Voulez-vous que je compare les meilleurs tarifs pour vous ?"
            ],
            'periode_paris' => [
                'keywords' => ['periode', 'quand', 'moment', 'printemps', 'automne', 'ete', 'hiver', 'meilleur moment'],
                'response' => "Le printemps et l'automne sont idéals pour éviter la foule et profiter du climat."
            ],
            'visa' => [
                'keywords' => ['visa', 'passeport', 'document', 'nationalite', 'carte'],
                'response' => "Cela dépend de votre nationality. Voulez-vous que je vérifie pour vous ?"
            ],
            'duree_paris' => [
                'keywords' => ['duree', 'jours', 'combien temps', 'jours suffisante'],
                'response' => "3 à 5 jours suffisent pour voir les incontournables."
            ],
            'hotel_paris' => [
                'keywords' => ['hotel paris', 'hebergement paris', 'logement paris', 'chambre'],
                'response' => "Quel est votre budget et vos dates ? Je peux vous proposer plusieurs options."
            ],
            'hotel_pas_cher' => [
                'keywords' => ['pas cher', 'economique', 'budget', 'bar'],
                'response' => "Je peux vous proposer des options économiques bien notées."
            ],
            'hotel_eiffel' => [
                'keywords' => ['eiffel', 'tour eiffel', 'proche', 'champ elysees', 'montmartre'],
                'response' => "Plusieurs hôtels sont disponibles à proximité. Voulez-vous voir les meilleurs ?"
            ],
            'airbnb_ou_hotel' => [
                'keywords' => ['airbnb', 'location', 'appartement'],
                'response' => "Les hôtels offrent plus de services, Airbnb est souvent plus flexible."
            ],
            'hotel_kdej' => [
                'keywords' => ['petit|dejeuner', 'pdej', 'kdej', 'buffet'],
                'response' => "Oui, je peux filtrer uniquement les hôtels avec petit déjeuner."
            ],
            'circuit_personnalise' => [
                'keywords' => ['circuit personnalise', 'creer', 'generer', 'itinerary', 'planning'],
                'response' => "Je peux vous générer un itinéraire selon vos préférences (culture, luxe, budget…)."
            ],
            'circuit_5_jours' => [
                'keywords' => ['5 jours', 'une semaine', 'jourscircuit'],
                'response' => "Parfait ! Je vous prépare un planning détaillé jour par jour."
            ],
            'circuit_economique' => [
                'keywords' => ['economique', 'pas cher', 'budget'],
                'response' => "Je peux créer un circuit avec activités gratuites et transport économique."
            ],
            'circuit_luxe' => [
                'keywords' => ['luxe', 'premium', '5 etoiles', 'gastronomique'],
                'response' => "Je propose hôtels 5★, restaurants gastronomiques et visites VIP."
            ],
            'circuit_famille' => [
                'keywords' => ['famille', 'enfant', 'enfants'],
                'response' => "Je vais inclure des activités adaptées aux enfants."
            ],
            'activites_paris' => [
                'keywords' => ['que faire', 'activite', 'visiter'],
                'response' => "Visiter la Tour Eiffel, le Louvre, Montmartre et faire une croisière sur la Seine."
            ],
            'activites_gratuites' => [
                'keywords' => ['gratuit', 'gratuitement', 'gratuite', 'gratuits'],
                'response' => "Oui, comme les parcs, musées gratuits certains jours."
            ],
            'activites_romantiques' => [
                'keywords' => ['romantique', 'couple', 'amour'],
                'response' => "Dîner crucero, coucher de soleil à Montmartre."
            ],
            'activites_insolites' => [
                'keywords' => ['insolite', 'secret', 'cache', 'inhabituel'],
                'response' => "Escape games, catacombes, visites secrètes."
            ],
            'activites_enfants' => [
                'keywords' => ['enfant', 'enfants', 'famille'],
                'response' => "Disneyland Paris est un incontournable."
            ],
            'budget_paris' => [
                'keywords' => ['budget', 'prix', 'cout', 'combien'],
                'response' => "Environ 80 à 200€/jour selon votre style."
            ],
            'economiser' => [
                'keywords' => ['economiser', 'economie', 'econo', 'pas cher'],
                'response' => "Réservez à l'avance et utilisez les transports publics."
            ],
            'transport_pas_cher' => [
                'keywords' => ['transport', 'metro', 'bus', 'rer'],
                'response' => "Le métro est le plus économique."
            ],
            'restaurants_pas_chers' => [
                'keywords' => ['restaurant', 'manger', 'repas', 'bouffe'],
                'response' => "Je peux vous proposer des adresses locales."
            ],
            'vol_pas_cher' => [
                'keywords' => ['pas cher', 'meilleur prix', 'discount', 'reduction'],
                'response' => "Donnez-moi votre ville de départ et vos dates."
            ],
            'vol_direct' => [
                'keywords' => ['direct', 'escale', 'vol direct'],
                'response' => "Les vols directs sont plus rapides mais parfois plus chers."
            ],
            'moment_reserver' => [
                'keywords' => ['reserver', 'quand', 'moment'],
                'response' => "1 à 2 mois à l'avance."
            ],
            'compagnies' => [
                'keywords' => ['compagnie', 'compagnies', 'aerienne'],
                'response' => "Je peux vous proposer les meilleures options."
            ],
            'bagages' => [
                'keywords' => ['bagage', 'valise', 'kg', 'poids'],
                'response' => "Cela dépend du billet choisi."
            ],
            'paris_sur' => [
                'keywords' => ['sur', 'safe', 'danger', 'criminel'],
                'response' => "Oui, mais faites attention aux pickpockets."
            ],
            'langue' => [
                'keywords' => ['langue', 'langage', 'parler', 'anglait'],
                'response' => "Le français, mais beaucoup parlent anglais."
            ],
            'monnaie' => [
                'keywords' => ['monnaie', 'euro', 'argent', 'devise'],
                'response' => "Euro (€)."
            ],
            'internet' => [
                'keywords' => ['internet', 'wifi', 'forfait', 'data'],
                'response' => "WiFi disponible dans la plupart des lieux."
            ],
            'transport_aeroport' => [
                'keywords' => ['aeroport', 'airport', 'cdg', 'ory'],
                'response' => "RER, taxi ou navette."
            ],
            'voyage_complet' => [
                'keywords' => ['complet', 'integral', 'planifie'],
                'response' => "Je vais créer un itinéraire avec vols, hôtel et activités."
            ],
            'voyage_solo' => [
                'keywords' => ['solo', 'seul', 'seule'],
                'response' => "Je propose un circuit sécurisé et optimisé."
            ],
            'voyage_couple' => [
                'keywords' => ['couple', 'romantique', 'amoureux'],
                'response' => "Je crée un circuit romantique."
            ],
            'recommander_gouts' => [
                'keywords' => ['recommander', 'gout', 'preference'],
                'response' => "Oui, je personnalise selon vos préférences."
            ],
            'itineraire_intelligent' => [
                'keywords' => ['intelligent', 'optimise', 'ia'],
                'response' => "Optimisé par IA selon temps et distance."
            ],
            'suggestions_auto' => [
                'keywords' => ['suggestion', 'automatique', 'proposer'],
                'response' => "Oui, basées sur votre profil."
            ],
            'analyse_prix' => [
                'keywords' => ['analyse', 'offre', 'promotion'],
                'response' => "Je détecte les meilleures offres."
            ],
            'valise' => [
                'keywords' => ['valise', 'bagage', 'emporter'],
                'response' => "Vêtements adaptés à la saison + documents."
            ],
            'assurance' => [
                'keywords' => ['assurance', 'assurance voyage'],
                'response' => "Fortement recommandée."
            ],
            'carte_bancaire' => [
                'keywords' => ['carte', 'banque', 'frais'],
                'response' => "Vérifiez les frais à l'étranger."
            ],
            'adaptateur' => [
                'keywords' => ['adaptateur', 'electricite', 'prise'],
                'response' => "Type européen."
            ],
            'tunisie' => [
                'keywords' => ['tunisie', 'tunis', 'djerba', 'hammamet', 'sousse', 'carthage'],
                'response' => "La Tunisie offre des destinations magnifiqueS! Djerba pour les plages, Hammamet pour le Golf, Tunis pour la culture."
            ],
            'maroc' => [
                'keywords' => ['maroc', 'marrakech', 'fes', 'casablanca', 'essaouira'],
                'response' => "Le Maroc est une destination fascinante! Marrakech pour l'exotisme, Fès pour l'histoire, Essaouira pour l'océan."
            ],
            'egypte' => [
                'keywords' => ['egypte', 'le caire', 'louxor', 'aswan', 'pyramide'],
                'response' => "L'Égypte avec ses pyramides et temples! Le Caire pour les pyramides, Louxor pour la Vallée des Rois."
            ],
            'espagne' => [
                'keywords' => ['espagne', 'barcelone', 'madrid', 'valencia', 'sevilla', 'ibiza'],
                'response' => "L'Espagne多变! Barcelona pour Gaudi, Madrid pour la vie nocturne, Ibiza pour les plages."
            ],
            'italie' => [
                'keywords' => ['italie', 'rome', 'venise', 'florence', 'milan', 'naple'],
                'response' => "L'Italie est un musée à ciel ouvert! Rome pour l'histoire, Venise pour le romantisme, Florence pour l'art."
            ],
            'grece' => [
                'keywords' => ['grece', 'athene', 'santorin', 'mykonos', 'crete'],
                'response' => "La Grèce avec ses îles magnifiques! Athènes pour l'histoire, Santorin pour les couchers de soleil."
            ],
            'angleterre' => [
                'keywords' => ['angleterre', 'londres', 'manchester', 'uk', 'britannique'],
                'response' => "L'Angleterre offre une richesse culturelle unique! Londres pour tout voir."
            ],
            'allemagne' => [
                'keywords' => ['allemagne', 'berlin', 'munich', 'francfort', 'hambourg'],
                'response' => "L'Allemagne combine histoire et modernité! Berlin pour la culture, Munich pour la fête."
            ],
            'voyage_thematique' => [
                'keywords' => ['thematique', 'theme', 'sport', 'golf', 'wellness'],
                'response' => "Je peux organiser des voyages thématiques: Golf, Wellness, Gastronomie, Aventure!"
            ],
            'voyage_nature' => [
                'keywords' => ['nature', 'randonnée', 'marche', 'montagne', 'foret'],
                'response' => "Parfait pour la nature! Je propose des circuits在大山中."
            ],
            'voyage_plage' => [
                'keywords' => ['plage', 'mer', 'ocean', 'banian', 'cote'],
                'response' => "Je connais les plus belles plages!"
            ],
            'voyage_gastronomie' => [
                'keywords' => ['gastronomie', 'restaurant', 'gourmet', 'cuisine', 'vin'],
                'response' => "Parfait pour les gourmets! Je réserve les meilleures tables."
            ],
            'voyage_romantique' => [
                'keywords' => ['lune de miel', 'voyage de noces', 'amoureux'],
                'response' => "Je crée des expériences romantiques inoubliables!"
            ],
            'voyage_sportif' => [
                'keywords' => ['sport', 'football', 'tennis', 'golf', 'ski'],
                'response' => "Je peux organiser votre voyage sportif!"
            ],
            'voyage_sur_mesure' => [
                'keywords' => ['sur mesure', 'personnalise', 'custom'],
                'response' => "Je crée des voyages entièrement personnalisés!"
            ],
            'guide_local' => [
                'keywords' => ['guide', 'guide local', 'accompagnateur'],
                'response' => "Je peux vous fournir un guide local francophone."
            ],
            'transfert_aeroport' => [
                'keywords' => ['transfert', 'navette', 'privat'],
                'response' => "Je organise les transferts aéroport."

            ],
            'location_voiture' => [
                'keywords' => ['voiture', 'location', 'louage', 'conduite'],
                'response' => "Je peux comparer les locations de voiture."
            ],
            'excursion' => [
                'keywords' => ['excursion', 'journee', 'tour', 'visite guidee'],
                'response' => "Je propose des excursions d'une journée."
            ],
            'croisiere' => [
                'keywords' => ['croisiere', 'bateau', 'seine', 'fleuv'],
                'response' => "Les croisières sont magiques! Laverte sur la Seine."
            ],
            'billet_coupe' => [
                'keywords' => ['coupe', 'fast', 'rapide', 'urgent'],
                'response' => "Je peux vérifier les billets disponibles."
            ],
            'billet_groupe' => [
                'keywords' => ['groupe', 'plusieurs', 'collectif'],
                'response' => "Des tarifs groupes sont disponibles!"
            ],
            'reservation_groupe' => [
                'keywords' => ['reservation groupe', 'ensemble', 'commande collective'],
                'response' => "Je gère les réservations de groupe."
            ],
            'tarif_famille' => [
                'keywords' => ['tarif famille', 'enfant gratuit', 'bebe'],
                'response' => "Des tarifs familiaux sont applicables."
            ],
            'animal' => [
                'keywords' => ['animal', 'chat', 'chien', 'pet'],
                'response' => "Certains hébergements acceptent les animaux."
            ],
            'handicap' => [
                'keywords' => ['handicap', 'pmr', 'accessibilite', 'mobilite reduite'],
                'response' => "Je trouve des hébergements accessibles."
            ],
            'alimentation' => [
                'keywords' => ['vegan', 'vegetarien', 'halal', 'casher', 'sans gluten'],
                'response' => "Je m'adapte à toutes les alimentations."
            ],
            'plainte' => [
                'keywords' => ['plainte', 'reclamation', 'probleme', 'insatisfaction'],
                'response' => "Contacteznotre service client. Je vous mets en lien."
            ],
            'avis' => [
                'keywords' => ['avis', 'note', 'opinion', 'recommander'],
                'response' => "Les avis sont importants! Voulez-vous voir les avis clients?"
            ],
            'programme_fidelite' => [
                'keywords' => ['fidelite', 'points', 'loyalty', 'carte'],
                'response' => "Gagnez des points à chaque réservation!"
            ],
            'carte_cadeau' => [
                'keywords' => ['cadeau', 'bon', 'cheque', 'offrir'],
                'response' => "Les cartes-cadeaux sont disponibles!"
            ],
            'pub_voyage' => [
                'keywords' => ['newsletter', 'alerte', 'promo', 'offre special'],
                'response' => "Abonnez-vous à notre newsletter!"
            ],
            'contact_whatsapp' => [
                'keywords' => ['whatsapp', 'wtsp', 'telephone'],
                'response' => "Contactez-nous sur WhatsApp!"
            ],
            'horaires' => [
                'keywords' => ['horaire', 'heure', 'ouverture', 'disponible'],
                'response' => "Nous sommes disponibles 24h/24!"
            ],
            'delai_confirmation' => [
                'keywords' => ['delai', 'temps', 'confirm', 'quand'],
                'response' => "La confirmation prend quelques minutes."
            ],
            'modification_reservation' => [
                'keywords' => ['modifier', 'changer', 'date', 'horaire'],
                'response' => "Oui, modifiez depuis votre espace client."
            ],
            'annulation_gratuite' => [
                'keywords' => ['annuler gratuit', 'sans frais', 'remboursable'],
                'response' => "Selon les conditions, l'annulation peut être gratuite."
            ],
            'prix_minimum' => [
                'keywords' => ['minimum', 'pas cher', 'moins cher'],
                'response' => "Le prix minimum start à partir de..."
            ],
            'promo_encours' => [
                'keywords' => ['promo', 'reduction', 'soldes'],
                'response' => "Vérifiez nos promotions en cours!"
            ],
            'code_promo' => [
                'keywords' => ['code', 'coupon', 'reduire'],
                'response' => "Entrez le code promo lors du paiement."
            ],
            'meilleur_prix' => [
                'keywords' => ['garantie', 'moins cher', 'meilleur prix'],
                'response' => "Nous proposons le meilleur rapport qualité-prix."
            ],
            'paiement_securise' => [
                'keywords' => ['securise', 'crypt', 'ssl', 'paiement secur'],
                'response' => "Le paiement est 100% sécurisé."
            ],
            'cb_accept' => [
                'keywords' => ['carte visa', 'mastercard', 'amex', 'cb'],
                'response' => "Nous acceptons toutes les cartes."
            ],
            'virement' => [
                'keywords' => ['virement', 'vir', 'banque'],
                'response' => "Le virement est accepté."
            ],
            'paiement_plusieurs_foi' => [
                'keywords' => ['plusieurs fois', 'mensuel', 'fractionn'],
                'response' => "Le paiement fractionné est disponible."
            ],
            'facture' => [
                'keywords' => ['facture', 'recu', 'invoice', 'telecharger'],
                'response' => "Téléchargez votre facture dans votre espace."
            ],
            'confirmation_email' => [
                'keywords' => ['email confirm', 'recu email', 'confirmation'],
<<<<<<< HEAD
                'response' => "Vous allez recevoir un e-mail de confirmation contenant tous les détails de votre réservation."
=======
                'response' => "Vous recevoir un email de confirmation."
>>>>>>> testsisi
            ]
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

    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0; $normA = 0; $normB = 0;
        $count = count($a);
        for ($i = 0; $i < $count; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] ** 2;
            $normB += $b[$i] ** 2;
        }
        if ($normA == 0 || $normB == 0) return 0;
        return $dot / (sqrt($normA) * sqrt($normB));
    }

    public function processMessage(string $message, array $conversationHistory = [], ?array $userContext = null): array
    {
        $startTotal = microtime(true);
        $normalizedMessage = $this->normalizeText($message);
        
        $result = [
            'response' => '',
            'action' => null,
            'filters' => [],
            'faq_id' => null,
            'performance' => []
        ];

        if (empty($normalizedMessage)) {
=======
    public function processMessage(string $message, array $conversationHistory = []): array
    {
        $message = mb_strtolower(trim($message));

        $result = [
            'response' => '',
            'action' => null,
            'filters' => []
        ];

        if (empty($message)) {
>>>>>>> testsisi
            $result['response'] = "Bonjour! Je suis votre assistant Fly&Go. Comment puis-je vous aider aujourd'hui?";
            return $result;
        }

<<<<<<< HEAD
        // 1. Check Database FAQ (Hybrid Search: Semantic + Keywords)
        if ($this->faqRepository) {
            $startSearch = microtime(true);
            $dbFaqs = $this->cache ? $this->cache->get('chatbot_db_faqs', function (ItemInterface $item) {
                $item->expiresAfter(3600);
                return $this->faqRepository->findAll();
            }) : $this->faqRepository->findAll();
            
            $bestDbMatch = null;
            $maxFinalScore = 0;
            
            // Generate or get user embedding from cache (OpenAI or HuggingFace or Local fallback)
            $userEmbedding = null;
            if ($this->openAIService && $this->openAIService->isEnabled()) {
                $startEmbed = microtime(true);
                $userEmbedding = $this->openAIService->generateEmbedding($message);
                $result['performance']['embedding_ms'] = round((microtime(true) - $startEmbed) * 1000, 2);
            } else if ($this->hfService && $this->hfService->isEnabled()) {
                $startEmbed = microtime(true);
                $userEmbedding = $this->hfService->generateEmbedding($message);
                $result['performance']['embedding_ms'] = round((microtime(true) - $startEmbed) * 1000, 2);
            } else {
                // Use local embedding service as fallback
                $startEmbed = microtime(true);
                $userEmbedding = $this->localEmbeddingService->generateEmbedding($message);
                $result['performance']['embedding_ms'] = round((microtime(true) - $startEmbed) * 1000, 2);
            }

            foreach ($dbFaqs as $dbFaq) {
                // Keyword Score (0.3 weight)
                $keywordScore = 0;
                $questionNormalized = $this->normalizeText($dbFaq->getQuestion());
                
                // Direct match or high keyword overlap
                if (mb_strpos($normalizedMessage, $questionNormalized) !== false) {
                    $keywordScore = 1.0;
                } else {
                    $faqKeywords = explode(' ', $this->normalizeText($dbFaq->getKeywords() ?: ''));
                    $matches = 0;
                    $foundCount = 0;
                    foreach ($faqKeywords as $kw) {
                        if (mb_strlen($kw) < 3) continue;
                        $foundCount++;
                        if (mb_strpos($normalizedMessage, $kw) !== false) $matches++;
                    }
                    $keywordScore = $foundCount > 0 ? ($matches / $foundCount) : 0;
                }

                // Semantic Score (0.7 weight)
                $semanticScore = 0;
                if ($userEmbedding && $dbFaq->getEmbedding()) {
                    $semanticScore = $this->cosineSimilarity($userEmbedding, $dbFaq->getEmbedding());
                }

                // Adjust scores: semantic is much more reliable for intent
                // but keyword matching is good for specific terms (e.g. "wifi")
                $finalScore = ($semanticScore * 0.75) + ($keywordScore * 0.25);

                // Penalty for FAQs with lots of down feedback
                if ($dbFaq->getFeedbackDown() > 5 && $dbFaq->getFeedbackDown() > $dbFaq->getFeedbackUp()) {
                    $finalScore *= 0.8;
                }

                if ($finalScore > $maxFinalScore) {
                    $maxFinalScore = $finalScore;
                    $bestDbMatch = $dbFaq;
                }
            }
            $result['performance']['search_ms'] = round((microtime(true) - $startSearch) * 1000, 2);

            if ($bestDbMatch && $maxFinalScore >= 0.80) { // Increased threshold for better precision
                $result['response'] = $bestDbMatch->getAnswer();
                $result['faq_id'] = $bestDbMatch->getId();
                $result['performance']['total_ms'] = round((microtime(true) - $startTotal) * 1000, 2);
                return $result;
            }
        }

        // 2. Detect Complex Intent (Itineraries, specific plans, etc.)
        $isComplexRequest = (mb_strlen($message) > 40) || 
                           (preg_match('/(\d+)\s*(jours|nuit|day|jr)/i', $message)) ||
                           (preg_match('/(circuit|programme|planning|itineraire|itinerary|voyage)/i', $message) && ($this->extractDestination($message) || $this->extractCountry($message)));

        if ($isComplexRequest) {
            if ($this->openAIService && $this->openAIService->isEnabled()) {
                $startAI = microtime(true);
                $aiResult = $this->processWithAI($message, $conversationHistory, $result, $userContext);
                $aiResult['performance']['ai_ms'] = round((microtime(true) - $startAI) * 1000, 2);
                $aiResult['performance']['total_ms'] = round((microtime(true) - $startTotal) * 1000, 2);
                return $aiResult;
            }

            // Fallback for complex request when OpenAI is disabled: search database
            $destination = $this->extractDestination($message);
            $country = $this->extractCountry($message);
            $searchLocation = $destination ?: $country;

            if ($searchLocation && $this->circuitRepository) {
                $circuits = $this->circuitRepository->createQueryBuilder('c')
                    ->where('c.destination LIKE :loc OR c.titre LIKE :loc')
                    ->andWhere('c.actif = :actif')
                    ->setParameter('loc', '%' . $searchLocation . '%')
                    ->setParameter('actif', true)
                    ->setMaxResults(3)
                    ->getQuery()
                    ->getResult();

                if (!empty($circuits)) {
                    $response = "Je ne peux pas générer un itinéraire personnalisé en temps réel pour l'instant (IA non configurée), mais j'ai trouvé ces circuits pour **$searchLocation** qui pourraient vous intéresser :\n\n";
                    foreach ($circuits as $c) {
                        $response .= "📍 **" . $c->getTitre() . "** (" . $c->getDuree() . " jours)\n";
                        $response .= "   *Prix: " . $c->getPrix() . "€*\n";
                        if ($c->getDescription()) {
                            $desc = mb_substr($c->getDescription(), 0, 100) . "...";
                            $response .= "   _" . $desc . "_\n\n";
                        }
                    }
                    $response .= "\nVous pouvez les consulter et réserver ici : [Nos Circuits](/circuits)";
                    $result['response'] = $response;
                    return $result;
                }
            }
        }

        // 2. Check NLP Service
=======
>>>>>>> testsisi
        if ($this->nlpService) {
            $nlpResponse = $this->nlpService->getResponse($message);
            if ($nlpResponse) {
                $result['response'] = $nlpResponse;
                return $result;
            }
        }

<<<<<<< HEAD
        // 3. Check Hardcoded FAQ (Fallback/Static)
        $matches = [];
        $words = explode(' ', $message);
=======
        $matches = [];
>>>>>>> testsisi
        
        foreach ($this->faq as $category => $data) {
            foreach ($data['keywords'] as $keyword) {
                $keywordLower = mb_strtolower($keyword);
<<<<<<< HEAD
                
                if (mb_strpos($message, $keywordLower) !== false) {
                    $score = mb_strlen($keyword) * 2;
                    // Lower score for very generic keywords to avoid shadowing specific intents
                    if (in_array($keywordLower, ['voyage', 'circuit', 'hotel', 'vol', 'prix'])) {
                        $score = $score / 2;
                    }
                    $matches[] = ['score' => $score, 'response' => $data['response'], 'category' => $category];
                    continue;
                }

                if (mb_strlen($keywordLower) > 4) {
                    foreach ($words as $word) {
                        if (mb_strlen($word) < 4) continue;
                        $dist = levenshtein($word, $keywordLower);
                        if ($dist <= 1) {
                            $matches[] = ['score' => mb_strlen($keywordLower), 'response' => $data['response'], 'category' => $category];
                        }
                    }
=======
                if (mb_strpos($message, $keywordLower) !== false) {
                    $score = mb_strlen($keyword);
                    $matches[] = ['score' => $score, 'response' => $data['response'], 'category' => $category];
>>>>>>> testsisi
                }
            }
        }

        if (!empty($matches)) {
<<<<<<< HEAD
            usort($matches, fn($a, $b) => $b['score'] - $a['score']);
=======
            usort($matches, function($a, $b) {
                return $b['score'] - $a['score'];
            });

>>>>>>> testsisi
            $response = $matches[0]['response'] ?? '';

            if ($response === '__SEARCH__' || $response === '__FILTER__') {
                $destination = $this->extractDestination($message);
                $country = $this->extractCountry($message);
                $lieu = $country ?: $destination;
                $prix = $this->extractPrice($message);
                
                $result['action'] = 'search';
                $result['filters']['query'] = !empty($destination) ? $destination : (!empty($country) ? $country : '');
                $result['filters']['pays'] = $country ?: '';
                $result['filters']['lieu'] = $lieu ?: '';
                $result['filters']['prix_min'] = $prix['min'] ?? 0;
                $result['filters']['prix_max'] = $prix['max'] ?? 0;
                
                $responseText = "Je recherche";
                if (!empty($destination)) $responseText .= " pour $destination";
                if (!empty($country)) $responseText .= " en $country";
                if (!empty($prix)) {
                    if (!empty($prix['max'])) $responseText .= " jusqu'à " . $prix['max'] . "€";
                }
                $responseText .= "...";
                
                $result['response'] = $responseText;
                return $result;
            }

            if (count($matches) >= 2) {
<<<<<<< HEAD
                if (isset($matches[0]['category'], $matches[1]['category'])) {
=======
                if (isset($matches[0]['category']) && isset($matches[1]['category'])) {
>>>>>>> testsisi
                    if (in_array($matches[0]['category'], ['paris_hotel', 'paris_advice']) || 
                        in_array($matches[1]['category'], ['paris_hotel', 'paris_advice'])) {
                        foreach ($matches as $m) {
                            if (in_array($m['category'], ['paris_hotel', 'paris_advice'])) {
                                $result['response'] = $m['response'];
                                return $result;
                            }
                        }
                    }
                }
            }
            
<<<<<<< HEAD
            // If the best match is too generic and we have OpenAI, let's try OpenAI instead
            if ($matches[0]['score'] < 10 && $this->openAIService && $this->openAIService->isEnabled()) {
                return $this->processWithAI($message, $conversationHistory, $result, $userContext);
            }

=======
>>>>>>> testsisi
            $result['response'] = $response;
            return $result;
        }

<<<<<<< HEAD
        // 4. OpenAI Fallback
        if ($this->openAIService && $this->openAIService->isEnabled()) {
            return $this->processWithAI($message, $conversationHistory, $result, $userContext);
        }

        // 5. Default Fallback
        $result['response'] = $this->fallbackMessage;
        return $result;
    }

    private function processWithAI(string $message, array $conversationHistory, array $result, ?array $userContext = null): array
    {
        $historyForAI = [];
        foreach ($conversationHistory as $msg) {
            $historyForAI[] = [
                'role' => $msg['isUser'] ? 'user' : 'assistant',
                'content' => $msg['content']
            ];
        }
        
        $response = $this->openAIService->chat($message, $historyForAI, $userContext);
        
        if ($response['success']) {
            $result['response'] = $response['response'];
            return $result;
        }

=======
        if (mb_strlen($message) < 4) {
            $result['response'] = "Tapez un mot-clé plus long pour une réponse précise (ex: 'réservation', 'hôtel', 'vol', 'circuit', 'contact')";
            return $result;
        }

        if ($this->openAIService && $this->openAIService->isEnabled()) {
            $historyForAI = [];
            foreach ($conversationHistory as $msg) {
                $historyForAI[] = [
                    'role' => $msg['isUser'] ? 'user' : 'assistant',
                    'content' => $msg['content']
                ];
            }
            
            $response = $this->openAIService->chat($message, $historyForAI);
            
            if ($response['success']) {
                $result['response'] = $response['response'];
                return $result;
            }
        }

>>>>>>> testsisi
        $result['response'] = $this->fallbackMessage;
        return $result;
    }

    private function extractDestination(string $message): ?string
    {
        $destinations = [
            'paris' => 'Paris', 'londres' => 'Londres', 'new york' => 'New York', 'new-york' => 'New York',
            'marrakech' => 'Marrakech', 'fes' => 'Fès', 'tunis' => 'Tunis', 'djerba' => 'Djerba',
<<<<<<< HEAD
            'jerba' => 'Djerba', 'houmt souk' => 'Djerba', 'guellala' => 'Djerba', 'midoun' => 'Djerba',
            'hammamet' => 'Hammamet', 'roma' => 'Rome', 'barcelone' => 'Barcelone', 'madrid' => 'Madrid',
            'berlin' => 'Berlin', 'amsterdam' => 'Amsterdam', 'istanbul' => 'Istanbul', 'dubai' => 'Dubaï',
            'sousse' => 'Sousse', 'monastir' => 'Monastir', 'sfax' => 'Sfax', 'bizerte' => 'Bizerte',
            'tozeur' => 'Tozeur', 'nefta' => 'Tozeur', 'kairouan' => 'Kairouan', 'mahdia' => 'Mahdia',
            'tabarka' => 'Tabarka', 'douz' => 'Douz', 'matmata' => 'Matmata', 'tataouine' => 'Tataouine',
            'nabeul' => 'Nabeul', 'kelibia' => 'Kélibia', 'haouaria' => 'Haouaria', 'korba' => 'Korba',
            'zaghouan' => 'Zaghouan', 'beja' => 'Béja', 'jendouba' => 'Jendouba', 'le kef' => 'Le Kef',
            'siliana' => 'Siliana', 'gafsa' => 'Gafsa', 'gabes' => 'Gabès', 'medenine' => 'Médenine',
            'kebili' => 'Kébili', 'tataouine' => 'Tataouine', 'sidi bouzid' => 'Sidi Bouzid', 'kasserine' => 'Kasserine'
=======
            'hammamet' => 'Hammamet', 'roma' => 'Rome', 'barcelone' => 'Barcelone', 'madrid' => 'Madrid',
            'berlin' => 'Berlin', 'amsterdam' => 'Amsterdam', 'istanbul' => 'Istanbul', 'dubai' => 'Dubaï'
>>>>>>> testsisi
        ];

        foreach ($destinations as $key => $name) {
            if (mb_strpos($message, $key) !== false) {
                return $name;
            }
        }

        return null;
    }

    private function extractCountry(string $message): ?string
    {
        $countries = [
            'france' => 'France', 'tunisie' => 'Tunisie', 'maroc' => 'Maroc', 'egypte' => 'Égypte',
            'espagne' => 'Espagne', 'italie' => 'Italie', 'turquie' => 'Turquie', 'grece' => 'Grèce',
            'algerie' => 'Algérie', 'angleterre' => 'Angleterre', 'allemagne' => 'Allemagne',
<<<<<<< HEAD
            'pays-bas' => 'Pays-Bas', 'emirats' => 'Émirats arabes unis', 'belgique' => 'Belgique',
            'suisse' => 'Suisse', 'canada' => 'Canada', 'usa' => 'États-Unis', 'portugal' => 'Portugal'
=======
            'pays-bas' => 'Pays-Bas', 'emirats' => 'Émirats arabes unis'
>>>>>>> testsisi
        ];

        foreach ($countries as $key => $name) {
            if (mb_strpos($message, $key) !== false) {
                return $name;
            }
        }

        return null;
    }

    private function extractPrice(string $message): array
    {
        $prices = [];
        
        if (preg_match_all('/(\d+)\s*(?:euros?|€|eur)/i', $message, $matches)) {
            foreach ($matches[1] as $price) {
                $prices[] = (int)$price;
            }
        }
        
        if (preg_match('/jusqu?\s*a\s*(\d+)/i', $message, $matches)) {
            return ['max' => (int)$matches[1]];
        }
        
        if (preg_match('/entre\s*(\d+)\s*et\s*(\d+)/i', $message, $matches)) {
            return ['min' => (int)$matches[1], 'max' => (int)$matches[2]];
        }
        
        if (!empty($prices)) {
            return ['max' => max($prices)];
        }
        
        return [];
    }

    public function getWelcomeMessage(): string
    {
        return "👋 Bonjour! Je suis votre assistant Fly&Go. Comment puis-je vous aider aujourd'hui?\n\nJe peux vous répondre sur:\n• Réservations & annulations\n• Hébergements & circuits\n• Paiements & facturation\n• Profil voyageur & préférences\n• Activités & transports\n• et bien plus encore!\n\nTapez votre question.";
    }

    public function getQuickSuggestions(): array
    {
        return [
            ['label' => '🔍 Rechercher un vol', 'query' => 'vol'],
            ['label' => '🏨 Réserver un hôtel', 'query' => 'hôtel'],
            ['label' => '🗺️ Créer un circuit', 'query' => 'circuit personnalisé'],
            ['label' => '📋 Mes réservations', 'query' => 'réservation'],
            ['label' => '⭐ Laisser un avis', 'query' => 'avis']
        ];
    }

    public function prepareForFutureAI(): array
    {
        $openAIEnabled = $this->openAIService && $this->openAIService->isEnabled();
        
        return [
            'ready' => true,
            'current_model' => $openAIEnabled ? 'openai-gpt-3.5-turbo' : 'rule-based-faq',
            'openai_integration' => [
                'available' => $openAIEnabled,
                'setup_needed' => !$openAIEnabled,
                'environment_variables' => ['OPENAI_API_KEY'],
                'suggested_model' => 'gpt-4'
            ],
            'message_format' => [
                'user_message' => 'string',
                'bot_response' => 'string',
                'timestamp' => 'datetime',
                'session_id' => 'string (optional)'
            ]
        ];
    }
}
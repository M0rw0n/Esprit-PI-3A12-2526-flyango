<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SemanticSearchService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;
    private array $circuitEmbeddings = [];

    public function __construct(
        ?string $huggingFaceApiKey = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->apiKey = $huggingFaceApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->initCircuitEmbeddings();
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    private function initCircuitEmbeddings(): void
    {
        $this->circuitEmbeddings = [
            'romantique' => [0.85, 0.72, 0.68, 0.91, 0.45],
            'aventure' => [0.12, 0.95, 0.88, 0.34, 0.67],
            'nature' => [0.45, 0.88, 0.92, 0.55, 0.78],
            'detente' => [0.92, 0.34, 0.45, 0.88, 0.23],
            'plage' => [0.78, 0.45, 0.56, 0.92, 0.34],
            'calme' => [0.91, 0.23, 0.34, 0.85, 0.12],
            'luxe' => [0.95, 0.67, 0.45, 0.78, 0.56],
            'spa' => [0.88, 0.56, 0.34, 0.92, 0.45],
            'pas cher' => [0.34, 0.78, 0.45, 0.23, 0.95],
            'mer' => [0.82, 0.45, 0.67, 0.95, 0.34],
            'montagne' => [0.23, 0.92, 0.95, 0.12, 0.78],
            'desert' => [0.15, 0.98, 0.88, 0.08, 0.65],
            'culture' => [0.67, 0.45, 0.78, 0.56, 0.34],
            'famille' => [0.78, 0.56, 0.67, 0.82, 0.45],
            'grup' => [0.45, 0.88, 0.78, 0.34, 0.67],
            'exploration' => [0.12, 0.95, 0.92, 0.15, 0.88],
            'historique' => [0.65, 0.56, 0.82, 0.45, 0.23],
            'sunset' => [0.88, 0.34, 0.56, 0.92, 0.12],
            'vacances' => [0.78, 0.45, 0.56, 0.88, 0.34],
            'randonnée' => [0.23, 0.92, 0.95, 0.12, 0.82],
            'piscine' => [0.85, 0.45, 0.34, 0.88, 0.56],
            'gastronomie' => [0.72, 0.56, 0.67, 0.78, 0.45],
            'chien' => [0.34, 0.78, 0.56, 0.23, 0.88],
            'paris' => [0.45, 0.34, 0.56, 0.78, 0.23],
            'voyage' => [0.78, 0.45, 0.56, 0.88, 0.34],
            'sejour' => [0.72, 0.45, 0.56, 0.85, 0.34],
            'circuit' => [0.65, 0.56, 0.67, 0.72, 0.45],
            'aller' => [0.45, 0.34, 0.45, 0.65, 0.34],
            'destination' => [0.56, 0.45, 0.56, 0.72, 0.45],
            'ville' => [0.45, 0.34, 0.56, 0.78, 0.34],
            'capitale' => [0.56, 0.34, 0.56, 0.72, 0.34],
            'visite' => [0.67, 0.45, 0.67, 0.65, 0.45],
            'decouverte' => [0.34, 0.78, 0.78, 0.45, 0.56],
            'tourisme' => [0.56, 0.45, 0.56, 0.72, 0.45],
            'monument' => [0.67, 0.45, 0.67, 0.56, 0.34],
            'museum' => [0.65, 0.45, 0.65, 0.56, 0.34],
        ];
    }

    public function search(string $query, array $circuits): array
    {
        $queryLower = strtolower($query);
        
        $queryWords = array_filter(
            preg_replace('/[^a-zA-Z]/', ' ', $queryLower),
            fn($w) => strlen(trim($w)) > 1
        );
        
        if (empty($queryWords)) {
            return [];
        }
        
        $stopWords = ['je', 'veux', 'aller', 'une', 'des', 'avec', 'pour', 'mon', 'mes', 'vais', 'a', 'le', 'la', 'les', 'un', 'cet', 'cette', 'du', 'au'];
        $searchWords = array_filter($queryWords, fn($w) => !in_array($w, $stopWords));
        
        $matched = [];
        foreach ($circuits as $circuit) {
            $circuitText = strtolower($this->getCircuitText($circuit));
            
            $score = 0;
            $foundWords = [];
            
            foreach ($searchWords as $word) {
                if (strlen($word) < 2) continue;
                
                if (strpos($circuitText, $word) !== false) {
                    $score += 1;
                    $foundWords[] = $word;
                }
            }
            
            if ($score > 0) {
                $matched[] = [
                    'circuit' => $circuit,
                    'score' => $score,
                    'matchedKeywords' => array_unique($foundWords)
                ];
            }
        }
        
        usort($matched, fn($a, $b) => $b['score'] <=> $a['score']);
        
        return $matched;
    }

    private function getQueryEmbedding(string $query): array
    {
        $keywords = $this->extractKeywords($query);
        $embedding = array_fill(0, 5, 0.0);
        
        $count = 0;
        foreach ($keywords as $keyword) {
            if (isset($this->circuitEmbeddings[$keyword])) {
                foreach ($this->circuitEmbeddings[$keyword] as $i => $val) {
                    $embedding[$i] += $val;
                }
                $count++;
            }
        }
        
        if ($count > 0) {
            $embedding = array_map(fn($v) => $v / $count, $embedding);
        }
        
        return $embedding;
    }

    private function getTextEmbedding(string $text): array
    {
        $keywords = $this->extractKeywords($text);
        $embedding = array_fill(0, 5, 0.0);
        
        $count = 0;
        foreach ($keywords as $keyword) {
            if (isset($this->circuitEmbeddings[$keyword])) {
                foreach ($this->circuitEmbeddings[$keyword] as $i => $val) {
                    $embedding[$i] += $val;
                }
                $count++;
            }
        }
        
        if ($count > 0) {
            $embedding = array_map(fn($v) => $v / $count, $embedding);
        }
        
        return $embedding;
    }

    private function extractKeywords(string $text): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\s]/u', ' ', $text);
        
        $keywords = [];
        $words = explode(' ', trim($text));
        
        $searchTerms = [
            'romantique' => ['romantique', 'amour', 'lune de miel', 'couple', 'mariage', 'amoureux'],
            'aventure' => ['aventure', 'aventureux', 'exploration', 'decouverte', 'aventure'],
            'nature' => ['nature', 'naturel', 'vert', 'paysage', 'faune', 'flore', 'forest'],
            'detente' => ['detente', 'detente', 'repos', 'relax', 'calme', 'bien etre'],
            'plage' => ['plage', 'mer', 'ocean', 'cote', 'baignade', 'sand', 'bord de mer'],
            'calme' => ['calme', 'tranquil', 'paisible', 'silencieux', 'peace', 'repos'],
            'luxe' => ['luxe', 'luxe', 'vip', 'chic', 'haut de gamme', 'prestige'],
            'spa' => ['spa', 'bien-etre', 'massage', 'bien-etre', 'relaxation', 'bienetre'],
            'pas cher' => ['pas cher', 'economique', 'abordable', 'budget', 'promotion', 'pascher', 'pas cher'],
            'mer' => ['mer', 'marine', 'bateau', 'nautique', 'cote', 'ocean'],
            'montagne' => ['montagne', 'montagne', 'altitude', 'neige', 'sommet', 'randonnée'],
            'desert' => ['desert', 'sahara', 'dune', 'oasis', 'trips', '沙漠'],
            'culture' => ['culture', 'historique', 'patrimoine', 'monument', 'museum', 'visite'],
            'famille' => ['famille', 'enfant', 'familial', 'kids', 'jeune', 'enfants'],
            'groupe' => ['groupe', 'amis', 'equipe', 'collectif', 'amis'],
            'sunset' => ['sunset', 'coucher soleil', 'soir', 'dusk'],
            'vacances' => ['vacances', 'holidays', 'conge', 'sejour', 'vacance'],
            'randonnée' => ['randonnee', 'marche', 'trek', 'hiking', 'montagne'],
            'piscine' => ['piscine', 'piscine', 'bassin', 'nage', 'baignade'],
            'gastronomie' => ['gastronomie', 'cuisine', 'nourriture', 'restaurant', 'gourmand'],
            'chien' => ['chien', 'animal', 'pet', 'ami', 'canin'],
            'paris' => ['paris', 'paris', 'france', '首都'],
            'voyage' => ['voyage', 'voyage', 'trip', 'trajet'],
            'sejour' => ['sejour', 'sejour', 'stay', 'residenc'],
            'circuit' => ['circuit', 'circuit', 'tour', 'parcours'],
            'aller' => ['aller', 'go', 'going', 'venir', 'aller'],
            'destination' => ['destination', 'destin', 'lieu', 'endroit'],
            'ville' => ['ville', 'city', 'urbain', 'metropole'],
            'capitale' => ['capitale', 'capital', '首都'],
            'visite' => ['visite', 'visit', 'seeing', 'decouverte'],
            'decouverte' => ['decouverte', 'discovery', 'explore', 'trouv'],
            'tourisme' => ['tourisme', 'tourism', 'guide'],
            'monument' => ['monument', 'tower', 'statue', 'buildin'],
            'museum' => ['museum', 'musee', 'exhibition'],
        ];
        
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) < 3) continue;
            
            foreach ($searchTerms as $key => $terms) {
                foreach ($terms as $term) {
                    if (strpos($word, $term) !== false || strpos($term, $word) !== false) {
                        $keywords[] = $key;
                        break 2;
                    }
                }
            }
            
            if (isset($this->circuitEmbeddings[$word])) {
                $keywords[] = $word;
            }
        }
        
        return array_unique($keywords);
    }

    private function getCircuitText($circuit): string
    {
        $parts = [];
        
        if (method_exists($circuit, 'getTitre')) {
            $parts[] = $circuit->getTitre();
        }
        if (method_exists($circuit, 'getDescription')) {
            $parts[] = $circuit->getDescription();
        }
        if (method_exists($circuit, 'getDepart')) {
            $parts[] = $circuit->getDepart();
        }
        if (method_exists($circuit, 'getDestination')) {
            $parts[] = $circuit->getDestination();
        }
        
        return implode(' ', $parts);
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;
        
        for ($i = 0; $i < count($a); $i++) {
            $dotProduct += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }
        
        if ($normA == 0 || $normB == 0) {
            return 0;
        }
        
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    private function findMatchedKeywords(string $query): array
    {
        $query = strtolower($query);
        $matches = [];
        
        $searchTerms = [
            'romantique', 'aventure', 'nature', 'detente', 'plage', 'calme', 
            'luxe', 'spa', 'pas cher', 'mer', 'montagne', 'desert',
            'culture', 'famille', 'groupe', 'vacances', 'randonnée',
            'piscine', 'gastronomie', 'paris', 'voyage', 'sejour', 
            'circuit', 'aller', 'destination', 'ville', 'capitale',
            'visite', 'decouverte', 'tourisme', 'monument', 'museum'
        ];
        
        foreach ($searchTerms as $term) {
            if (strpos($query, $term) !== false) {
                $matches[] = $term;
            }
        }
        
        return $matches;
    }

    public function getSuggestions(): array
    {
        return [
            'Voyages romantiques',
            'Aventure nature',
            'Détente spa',
            'Plage calme',
            'Luxe pas cher',
            'Circuit famille',
            'Randonnée montagne',
            'Culture et histoire',
        ];
    }

    public function getExampleQueries(): array
    {
        return [
            "je veux un voyage romantique pas cher en mer avec plage calme",
            "vacances luxe détente spa",
            "voyage aventure nature",
            "circuit famille avec enfants",
            "randonnée montagne découverte",
            "voyage culturel historique",
            "séjour romantique bord de mer",
            "aventure desert et oasis",
        ];
    }
}
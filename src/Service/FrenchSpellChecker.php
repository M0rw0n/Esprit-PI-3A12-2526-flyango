<?php

namespace App\Service;

class FrenchSpellChecker
{
    private array $corrections = [];
    
    public function __construct()
    {
        $this->initCorrections();
    }
    
    private function initCorrections(): void
    {
        $this->corrections = [
            // Common typos - greetings
            'bnjour' => 'bonjour',
            'bjr' => 'bonjour',
            'bonjr' => 'bonjour',
            'bonj' => 'bonjour',
            'bsoir' => 'bonsoir',
            'bsr' => 'bonsoir',
            'bjour' => 'bonjour',
            
            // Common shortcuts
            'bcp' => 'beaucoup',
            'svp' => 's\'il vous plaît',
            'stp' => 's\'il te plaît',
            'a+' => 'à plus',
            'a plus' => 'à plus',
            'qd' => 'quand',
            'pq' => 'pourquoi',
            'pk' => 'pourquoi',
            'cmt' => 'comment',
            'cmt' => 'comment',
            'jx' => 'jusque',
            'pr' => 'pour',
            'dc' => 'donc',
            'mtn' => 'maintenant',
            'tjrs' => 'toujours',
            'qq' => 'quelque',
            'qqn' => 'quelqu\'un',
            'qqch' => 'quelque chose',
            'tjs' => 'toujours',
            'jsais' => 'je sais',
            'jsui' => 'je suis',
            'jsuis' => 'je suis',
            'g' => 'je',
            't' => 'te',
            'vs' => 'vous',
            'j\'ai' => 'j\'ai',
            'j\'ai' => 'j\'ai',
            'jai' => 'j\'ai',
            
            // Accents missing
            'annee' => 'année',
            'annees' => 'années',
            'etude' => 'étude',
            'etudes' => 'études',
            'hotel' => 'hôtel',
            'hotels' => 'hôtels',
            'reservation' => 'réservation',
            'reservations' => 'réservations',
            'reserver' => 'réserver',
            'remboursement' => 'remboursement',
            'rembourser' => 'rembourser',
            'annulation' => 'annulation',
            'annuler' => 'annuler',
            'modifier' => 'modifier',
            'supprimer' => 'supprimer',
            'contacter' => 'contacter',
            'information' => 'information',
            'informations' => 'informations',
            'adresse' => 'adresse',
            'comment' => 'comment',
            'quand' => 'quand',
            'pourquoi' => 'pourquoi',
            'combien' => 'combien',
            'sejour' => 'séjour',
            'sejours' => 'séjours',
            'voyaje' => 'voyage',
            'voyajes' => 'voyages',
            'bagaje' => 'bagage',
            'bagajes' => 'bagages',
            'vol' => 'vol',
            'vols' => 'vols',
            'aerien' => 'aérien',
            'aeroport' => 'aéroport',
            'gare' => 'gare',
            'tarif' => 'tarif',
            'tarifs' => 'tarifs',
            'gratuit' => 'gratuit',
            'gratos' => 'gratuit',
            
            // Double letters
            'nn' => 'n',
            'tt' => 't',
            'ss' => 's',
            'rr' => 'r',
            'll' => 'l',
            'ee' => 'e',
            'oo' => 'o',
            
            // Punctuation spacing
            ' ,' => ',',
            ' .' => '.',
            ' !' => '!',
            ' ?' => '?',
            '  ' => ' ',
        ];
    }
    
    public function checkAndCorrect(string $text): array
    {
        $original = $text;
        $corrected = $text;
        $changes = [];
        
        // First: local corrections
        $localResult = $this->localCheckAndCorrect($text);
        $corrected = $localResult['corrected'];
        $changes = array_merge($changes, $localResult['changes']);
        
        // Second: API check (optional - using spellchecker API)
        $apiResult = $this->apiCheckAndCorrect($corrected);
        if (!empty($apiResult)) {
            foreach ($apiResult as $apiChange) {
                if (!in_array($apiChange, array_column($changes, 'from'))) {
                    $changes[] = $apiChange;
                }
            }
            $corrected = $apiResult['corrected'] ?? $corrected;
        }
        
        $hasChanges = !empty($changes) || $original !== $corrected;
        
        return [
            'original' => $original,
            'corrected' => $corrected,
            'changes' => $changes,
            'hasChanges' => $hasChanges
        ];
    }
    
    private function localCheckAndCorrect(string $text): array
    {
        $corrected = $text;
        $changes = [];
        
        // Remove extra spaces
        while (strpos($corrected, '  ') !== false) {
            $corrected = str_replace('  ', ' ', $corrected);
        }
        
        // Trim
        $corrected = trim($corrected);
        
        // Fix common typos - case insensitive
        foreach ($this->corrections as $wrong => $correct) {
            if (strcasecmp($wrong, $correct) !== 0) {
                // Replace whole word only for short forms
                if (strlen($wrong) <= 4) {
                    $pattern = '/\b' . preg_quote($wrong, '/') . '\b/iu';
                } else {
                    $pattern = '/' . preg_quote($wrong, '/') . '/iu';
                }
                
                if (mb_stripos($corrected, $wrong) !== false) {
                    $newText = preg_replace($pattern, $correct, $corrected);
                    if ($newText !== $corrected) {
                        $changes[] = ['from' => $wrong, 'to' => $correct];
                        $corrected = $newText;
                    }
                }
            }
        }
        
        // Fix double letters (keep one)
        $doubleLetters = ['nn', 'tt', 'ss', 'rr', 'll', 'oo', 'ee', 'aa', 'ii', 'uu', 'éé' => 'é', 'èè' => 'è'];
        foreach ($doubleLetters as $wrong => $correct) {
            if (is_string($wrong)) {
                $pattern = '/' . $wrong . '/i';
                $corrected = preg_replace($pattern, $correct, $corrected);
            }
        }
        
        // Capitalize first letter of sentence
        $corrected = $this->capitalizeFirstLetter($corrected);
        
        // Fix spaces before punctuation
        $corrected = preg_replace('/\s+([,!?;:])/', '$1', $corrected);
        
        // Fix spaces after punctuation  
        $corrected = preg_replace('/([.!?;:])([^ ])/', '$1 $2', $corrected);
        
        // Clean up multiple spaces again
        while (strpos($corrected, '  ') !== false) {
            $corrected = str_replace('  ', ' ', $corrected);
        }
        
        $corrected = trim($corrected);
        
        return [
            'corrected' => $corrected,
            'changes' => $changes
        ];
    }
    
    private function capitalizeFirstLetter(string $text): string
    {
        // Split by sentence
        $sentences = preg_split('/([.!?]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        
        foreach ($sentences as $sentence) {
            if (preg_match('/[.!?]/', $sentence)) {
                $result .= $sentence;
            } else {
                $result .= mb_strtoupper(mb_substr($sentence, 0, 1), 'UTF-8') . mb_substr($sentence, 1);
            }
        }
        
        return $result;
    }
    
    public function analyze(string $text): array
    {
        $words = preg_split('/\s+/', $text);
        $wordCount = count($words);
        $charCount = mb_strlen($text, 'UTF-8');
        
        return [
            'wordCount' => $wordCount,
            'charCount' => $charCount,
            'readingTime' => max(1, ceil($wordCount / 200 * 60)), // seconds
            'sentences' => preg_split('/[.!?]+/', $text),
        ];
    }
    
    private function apiCheckAndCorrect(string $text): array
    {
        $changes = [];
        
        // Option 1: LibreTranslate (free self-hosted or public instance)
        try {
            $url = 'https://libretranslate.com/detect';
            $response = @file_get_contents($url, false, stream_context_create([
                'http' => ['method' => 'POST', 'timeout' => 2]
            ]));
        } catch (\Exception $e) {}
        
        // Option 2: Use FrenchDic - https://github.com/eric-therond/dicos
        // This is a local dictionary approach, no API needed
        
        // Option 3: LanguageTool API (free public instance - rate limited)
        try {
            $url = 'https://api.languagetool.org/v2/check';
            $data = ['text' => $text, 'language' => 'fr'];
            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n",
                    'content' => json_encode($data),
                    'timeout' => 3
                ]
            ];
            $response = @file_get_contents($url, false, stream_context_create($options));
            if ($response) {
                $result = json_decode($response, true);
                if (!empty($result['matches'])) {
                    foreach ($result['matches'] as $match) {
                        if (!empty($match['replacements'])) {
                            $replacement = $match['replacements'][0]['value'] ?? '';
                            if ($replacement && $replacement !== $text) {
                                $changes[] = [
                                    'from' => $match['context']['text'] ?? $text,
                                    'to' => $replacement
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Silent fail
        }
        
        return [
            'corrected' => $text,
            'changes' => $changes
        ];
    }
}
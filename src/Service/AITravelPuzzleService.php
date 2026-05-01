<?php

namespace App\Service;

class AITravelPuzzleService
{
    private array $destinations = [
        ['id' => 'france', 'country' => 'France', 'capital' => 'Paris', 'emoji' => '🇫🇷', 'landmark' => 'Tour Eiffel', 'fun_fact' => 'La Tour Eiffel mesure 330m de hauteur et a été construite en 2 ans!', 'image' => 'https://images.unsplash.com/photo-1511739001486-6bfe10ce65f4?w=800'],
        ['id' => 'italy', 'country' => 'Italie', 'capital' => 'Rome', 'emoji' => '🇮🇹', 'landmark' => 'Colisée', 'fun_fact' => 'Le Colisée pouvait accueillir 50 000 spectateurs!', 'image' => 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800'],
        ['id' => 'japan', 'country' => 'Japon', 'capital' => 'Tokyo', 'emoji' => '🇯🇵', 'landmark' => 'Mont Fuji', 'fun_fact' => 'Le Mont Fuji est un volcan actif qui n\'a pas erupte depuis 1707!', 'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800'],
        ['id' => 'egypt', 'country' => 'Egypte', 'capital' => 'Le Caire', 'emoji' => '🇪🇬', 'landmark' => 'Pyramides de Gizeh', 'fun_fact' => 'Les pyramides sont les seules des 7 merveilles qui subsistent!', 'image' => 'https://images.unsplash.com/photo-1572252009286-268acec5ca0a?w=800'],
        ['id' => 'usa', 'country' => 'Etats-Unis', 'capital' => 'Washington', 'emoji' => '🇺🇸', 'landmark' => 'Statue de la Liberte', 'fun_fact' => 'La statue mesure 93m avec le piédestal!', 'image' => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800'],
        ['id' => 'uk', 'country' => 'Royaume-Uni', 'capital' => 'Londres', 'emoji' => '🇬🇧', 'landmark' => 'Big Ben', 'fun_fact' => 'Big Ben est en réalité la grosse cloche de 13 tonnes!', 'image' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800'],
        ['id' => 'spain', 'country' => 'Espagne', 'capital' => 'Madrid', 'emoji' => '🇪🇸', 'landmark' => 'Sagrada Familia', 'fun_fact' => 'La construction a commencé en 1882 et n\'est toujours pas terminée!', 'image' => 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=800'],
        ['id' => 'morocco', 'country' => 'Maroc', 'capital' => 'Rabat', 'emoji' => '🇲🇦', 'landmark' => 'Djemaa el-Fna', 'fun_fact' => 'Cette place est inscrite au patrimoine UNESCO depuis 2001!', 'image' => 'https://images.unsplash.com/photo-1597212720158-24b2bfc23547?w=800'],
        ['id' => 'turkey', 'country' => 'Turquie', 'capital' => 'Ankara', 'emoji' => '🇹🇷', 'landmark' => 'Sainte-Sophie', 'fun_fact' => 'Ce bâtiment a été successivement église, mosquée et musée!', 'image' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=800'],
        ['id' => 'brazil', 'country' => 'Brésil', 'capital' => 'Brasilia', 'emoji' => '🇧🇷', 'landmark' => 'Christ Redempteur', 'fun_fact' => 'La statue mesure 30m et pèse 635 tonnes!', 'image' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=800'],
        ['id' => 'australia', 'country' => 'Australie', 'capital' => 'Canberra', 'emoji' => '🇦🇺', 'landmark' => 'Opera de Sydney', 'fun_fact' => 'Le toit est composé de plus d\'un million de tuiles!', 'image' => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800'],
        ['id' => 'india', 'country' => 'Inde', 'capital' => 'New Delhi', 'emoji' => '🇮🇳', 'landmark' => 'Taj Mahal', 'fun_fact' => 'Il a nécessité 20 000 travailleurs pendant 22 ans!', 'image' => 'https://images.unsplash.com/photo-1564507592333-c60657eea523?w=800'],
        ['id' => 'thailand', 'country' => 'Thailande', 'capital' => 'Bangkok', 'emoji' => '🇹🇭', 'landmark' => 'Grand Palais', 'fun_fact' => 'Le Palais royal est habité par le roi depuis 1782!', 'image' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=800'],
        ['id' => 'greece', 'country' => 'Grèce', 'capital' => 'Athènes', 'emoji' => '🇬🇷', 'landmark' => 'Parthénon', 'fun_fact' => 'Ce temple était dédié à la déesse Athéna!', 'image' => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800'],
        ['id' => 'china', 'country' => 'Chine', 'capital' => 'Pékin', 'emoji' => '🇨🇳', 'landmark' => 'Grande Muraille', 'fun_fact' => 'La muraille mesure 21 196 km de long!', 'image' => 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?w=800'],
    ];

    public function getRandomDestination(): array
    {
        return $this->destinations[array_rand($this->destinations)];
    }

    public function getDestinationById(string $id): ?array
    {
        foreach ($this->destinations as $dest) {
            if ($dest['id'] === $id) return $dest;
        }
        return null;
    }

    public function getDailyChallenge(): array
    {
        $dayOfYear = (int)date('z');
        return $this->destinations[$dayOfYear % count($this->destinations)];
    }

    public function getAllDestinations(): array
    {
        return $this->destinations;
    }

    public function getGridSize(string $difficulty): array
    {
        return match($difficulty) {
            'easy' => ['rows' => 4, 'cols' => 4, 'total' => 16],
            'medium' => ['rows' => 6, 'cols' => 6, 'total' => 36],
            'hard' => ['rows' => 8, 'cols' => 8, 'total' => 64],
            default => ['rows' => 4, 'cols' => 4, 'total' => 16]
        };
    }

    public function getTimeLimit(string $difficulty): int
    {
        return match($difficulty) {
            'easy' => 300,
            'medium' => 480,
            'hard' => 600,
            default => 300
        };
    }

    public function getWorldTourSequence(): array
    {
        $sequence = ['france', 'brazil', 'thailand'];
        return array_map(fn($id) => $this->getDestinationById($id), $sequence);
    }
}
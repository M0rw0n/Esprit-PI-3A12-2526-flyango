<?php

namespace App\DataFixtures\Passport;

use App\Entity\Passport\Puzzle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PuzzleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $puzzles = [
            [
                'title' => 'La Tour Eiffel',
                'cityName' => 'Paris',
                'countryName' => 'France',
                'clue' => 'Cette ville est traversée par la Seine et est appelée la Ville Lumière',
                'difficulty' => 'easy',
                'orderIndex' => 1,
                'image' => 'paris.jpg'
            ],
            [
                'title' => 'Le Colisée',
                'cityName' => 'Rome',
                'countryName' => 'Italie',
                'clue' => 'Cette ancienne capitale de l\'Empire romain possède un amphithéâtre antique emblématique',
                'difficulty' => 'medium',
                'orderIndex' => 2,
                'image' => 'rome.jpg'
            ],
            [
                'title' => 'Mont Fuji',
                'cityName' => 'Tokyo',
                'countryName' => 'Japon',
                'clue' => 'Cette capitale high-tech abrite une montagne sacrée et un quartier branché nommé Shibuya',
                'difficulty' => 'hard',
                'orderIndex' => 3,
                'image' => 'tokyo.jpg'
            ],
            [
                'title' => 'Statue de la Liberté',
                'cityName' => 'New York',
                'countryName' => 'États-Unis',
                'clue' => 'Cette métropole américaine abrite une statue emblématique offerte par la France',
                'difficulty' => 'medium',
                'orderIndex' => 4,
                'image' => 'newyork.jpg'
            ],
            [
                'title' => 'Pyramides de Gizeh',
                'cityName' => 'Le Caire',
                'countryName' => 'Égypte',
                'clue' => 'Cette ville ancienne borde le Nil et est proche des fameuses pyramides',
                'difficulty' => 'medium',
                'orderIndex' => 5,
                'image' => 'cairo.jpg'
            ],
            [
                'title' => 'Mosquée Koutoubia',
                'cityName' => 'Marrakech',
                'countryName' => 'Maroc',
                'clue' => 'Cette ville rouge possède une mosquée au minaret emblématique',
                'difficulty' => 'easy',
                'orderIndex' => 6,
                'image' => 'marrakech.jpg'
            ],
            [
                'title' => 'Opéra de Sydney',
                'cityName' => 'Sydney',
                'countryName' => 'Australie',
                'clue' => 'Cette ville australienne possède un opéra aux voiles blanches emblématique',
                'difficulty' => 'hard',
                'orderIndex' => 7,
                'image' => 'sydney.jpg'
            ],
            [
                'title' => 'Christ le Rédempteur',
                'cityName' => 'Rio de Janeiro',
                'countryName' => 'Brésil',
                'clue' => 'Cette ville festive domine une statue géante sur une montagne face à la plage',
                'difficulty' => 'medium',
                'orderIndex' => 8,
                'image' => 'rio.jpg'
            ],
            [
                'title' => 'Sainte-Sophie',
                'cityName' => 'Istanbul',
                'countryName' => 'Turquie',
                'clue' => 'Cette ville它是连接两大洲的城市，拥有一个伟大的穹顶教堂',
                'difficulty' => 'hard',
                'orderIndex' => 9,
                'image' => 'istanbul.jpg'
            ],
        ];

        foreach ($puzzles as $data) {
            $puzzle = new Puzzle();
            $puzzle->setTitle($data['title']);
            $puzzle->setCityName($data['cityName']);
            $puzzle->setCountryName($data['countryName']);
            $puzzle->setClue($data['clue']);
            $puzzle->setDifficulty($data['difficulty']);
            $puzzle->setOrderIndex($data['orderIndex']);
            $puzzle->setImageFilename($data['image']);
            $manager->persist($puzzle);
        }

        $manager->flush();
    }
}
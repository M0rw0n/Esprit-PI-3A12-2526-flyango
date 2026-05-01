<?php

namespace App\Command;

use App\Entity\Circuit;
use App\Entity\Hebergement;
use App\Entity\Activity;
use App\Entity\TransportOffer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-test-data',
    description: 'Add test data for circuits, hebergements, activities, and transports'
)]
class SeedTestDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Seeding Test Data');

        // Seed Circuits
        $io->section('Creating Circuits');
        $circuits = [
            ['titre' => 'Circuit Djerba', 'destination' => 'Djerba', 'type' => 'Détente', 'prix' => 450, 'duree' => 5, 'description' => 'Un circuit relaxant à Djerba'],
            ['titre' => 'Circuit Sahara', 'destination' => 'Tozeur', 'type' => 'Aventure', 'prix' => 680, 'duree' => 7, 'description' => 'Découvrez le Sahara tunisien'],
            ['titre' => 'Circuit Culturel Tunis', 'destination' => 'Tunis', 'type' => 'Culturel', 'prix' => 320, 'duree' => 3, 'description' => 'Circuit culturel dans la capitale'],
            ['titre' => 'Circuit Nord', 'destination' => 'Bizerte', 'type' => 'Détente', 'prix' => 380, 'duree' => 4, 'description' => 'Découvrez le nord de la Tunisie'],
            ['titre' => 'Circuit Sud', 'destination' => 'Gabès', 'type' => 'Aventure', 'prix' => 550, 'duree' => 6, 'description' => 'Exploration du sud tunisien'],
        ];

        $creator = $this->userRepository->findOneBy([]) ?? (new User())->setEmail('seed@test.com')->setPrenom('Seed')->setNom('User')->setRoles(['ROLE_USER'])->setPassword('dummy');

        foreach ($circuits as $data) {
            $circuit = new Circuit($creator);
            $circuit->setTitre($data['titre'])
                ->setDestination($data['destination'])
                ->setType($data['type'])
                ->setPrix($data['prix'])
                ->setDuree($data['duree'])
                ->setDescription($data['description'])
                ->setActif(true)
                ->setSourceType('admin');
            $this->em->persist($circuit);
            $io->text("✅ Circuit: {$data['titre']}");
        }

        // Seed Hébergements
        $io->section('Creating Hébergements');
        $hebergements = [
            ['nom' => 'Hotel Djerba Palace', 'ville' => 'Djerba', 'type' => 'Hôtel', 'prix' => 250, 'description' => 'Hôtel 5 étoiles'],
            ['nom' => 'Dar Djerba', 'ville' => 'Djerba', 'type' => 'Maison d\'hôte', 'prix' => 120, 'description' => 'Charme traditionnel'],
            ['nom' => 'Résidence Sousse', 'ville' => 'Sousse', 'type' => 'Appartement', 'prix' => 180, 'description' => 'Appartements modernes'],
            ['nom' => 'Hotel Tunis', 'ville' => 'Tunis', 'type' => 'Hôtel', 'prix' => 320, 'description' => 'Hôtel centre-ville'],
            ['nom' => 'Dar Sousse', 'ville' => 'Sousse', 'type' => 'Maison d\'hôte', 'prix' => 95, 'description' => 'Bonne ambiance'],
        ];

        foreach ($hebergements as $data) {
            $heb = new Hebergement();
            $heb->setNom($data['nom'])
                ->setVille($data['ville'])
                ->setType($data['type'])
                ->setPrixParNuit($data['prix'])
                ->setDescription($data['description'])
                ->setDisponible(true);
            $this->em->persist($heb);
            $io->text("✅ Hébergement: {$data['nom']}");
        }

        // Seed Activities
        $io->section('Creating Activities');
        $activities = [
            ['title' => 'Excursion Djerba', 'lieu' => 'Djerba', 'category' => 'Excursion', 'price' => 80],
            ['title' => 'Quad Sahara', 'lieu' => 'Tozeur', 'category' => 'Aventure', 'price' => 150],
            ['title' => 'Visite Médina Tunis', 'lieu' => 'Tunis', 'category' => 'Culture', 'price' => 45],
            ['title' => 'Plongée Djerba', 'lieu' => 'Djerba', 'category' => 'Sport', 'price' => 120],
            ['title' => 'Camel Ride', 'lieu' => 'Nefta', 'category' => 'Aventure', 'price' => 60],
        ];

        foreach ($activities as $data) {
            $act = new Activity();
            $act->setTitle($data['title'])
                ->setLieu($data['lieu'])
                ->setCategory($data['category'])
                ->setPrice($data['price'])
                ->setActif(true);
            $this->em->persist($act);
            $io->text("✅ Activité: {$data['title']}");
        }

        // Seed Transports
        $io->section('Creating Transports');
        $transports = [
            ['depart' => 'Tunis', 'destination' => 'Djerba', 'type' => 'Avion', 'prix' => 180, 'duree' => '1h'],
            ['depart' => 'Sousse', 'destination' => 'Tozeur', 'type' => 'Bus', 'prix' => 45, 'duree' => '6h'],
            ['depart' => 'Tunis', 'destination' => 'Sfax', 'type' => 'Train', 'prix' => 25, 'duree' => '3h'],
            ['depart' => 'Djerba', 'destination' => 'Tunis', 'type' => 'Avion', 'prix' => 190, 'duree' => '1h'],
            ['depart' => 'Bizerte', 'destination' => 'Sousse', 'type' => 'Bus', 'prix' => 35, 'duree' => '4h'],
        ];

        foreach ($transports as $data) {
            $transport = new TransportOffer();
            $transport->setDepartureCity($data['depart'])
                ->setArrivalCity($data['destination'])
                ->setTransportType($data['type'])
                ->setPrice($data['prix'])
                ->setDuration($data['duree'])
                ->setIsActive(true);
            $this->em->persist($transport);
            $io->text("✅ Transport: {$data['depart']} → {$data['destination']}");
        }

        $this->em->flush();

        $io->success('Test data seeded successfully!');
        $io->table(
            ['Type', 'Nombre'],
            [
                ['Circuits', count($circuits)],
                ['Hébergements', count($hebergements)],
                ['Activités', count($activities)],
                ['Transports', count($transports)],
            ]
        );

        return Command::SUCCESS;
    }
}

<?php

namespace App\Command;

use App\Entity\Circuit;
use App\Entity\Hebergement;
use App\Entity\Activity;
use App\Entity\TransportOffer;
use App\Entity\User;
use App\Entity\FAQ;
use App\Entity\ForumPost;
use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-full-data',
    description: 'Add comprehensive test data (100+ rows)'
)]
class SeedFullDataCommand extends Command
{
    private int $created = 0;

    public function __construct(
        private EntityManagerInterface $em,
        private ?UserPasswordHasherInterface $passwordHasher = null
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('🌱 Seeding Full Test Data (100+ rows)');

        $this->createUsers($io);
        $this->createCircuits($io, 25);
        $this->createHebergements($io, 30);
        $this->createActivities($io, 30);
        $this->createTransports($io, 20);
        $this->createFAQs($io, 15);
        $this->createForumPosts($io, 20);

        $this->em->flush();

        $io->success("✅ Successfully created {$this->created} new records!");

        return Command::SUCCESS;
    }

    private function createUsers(SymfonyStyle $io): void
    {
        $io->section('Creating Users');
        $users = [
            ['nom' => 'Ben Ali', 'prenom' => 'Ahmed', 'email' => 'ahmed.benali@email.tn', 'role' => 'ROLE_USER'],
            ['nom' => 'Messaoudi', 'prenom' => 'Fatma', 'email' => 'fatma.messaoudi@email.tn', 'role' => 'ROLE_USER'],
            ['nom' => ' Trabelsi', 'prenom' => 'Mohamed', 'email' => 'mohamed.trabelsi@email.tn', 'role' => 'ROLE_USER'],
            ['nom' => 'Boukhris', 'prenom' => 'Sana', 'email' => 'sana.boukhris@email.tn', 'role' => 'ROLE_USER'],
            ['nom' => 'Hamdi', 'prenom' => 'Youssef', 'email' => 'youssef.hamdi@email.tn', 'role' => 'ROLE_USER'],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setEmail($data['email'])
                ->setRole($data['role'])
                ->setActif(true)
                ->setPassword(password_hash('password123', PASSWORD_BCRYPT));
            $this->em->persist($user);
            $this->created++;
        }
        $io->text("✅ Created " . count($users) . " users");
    }

    private function createCircuits(SymfonyStyle $io, int $count): void
    {
        $io->section("Creating $count Circuits");
        
        $types = ['Aventure', 'Détente', 'Culturel', 'Économique', 'Luxe', 'Familial', 'Romantique', 'Sportif'];
        $destinations = ['Djerba', 'Tunis', 'Sousse', 'Hammamet', 'Tozeur', 'Sfax', 'Bizerte', 'Kairouan', 'Monastir', 'Mahdia', 'Gabès', 'Nabeul', 'Tabarka'];
        $difficultes = ['Facile', 'Modéré', 'Difficile', 'Expert'];

        $lieux = ['Djerba Beach Resort', 'Palais des Rais', 'Musée de Bardo', 'Ksar Ouled Soltane', 'Chott el Jerid', 'Cité des Sciences', 'Ribat de Sousse', 'Amphithéâtre d\'El Jem', 'Site archéologique de Carthage', 'Parc National de l\'Ichkeul'];

        for ($i = 1; $i <= $count; $i++) {
            $circuit = new Circuit();
            $type = $types[array_rand($types)];
            $destination = $destinations[array_rand($destinations)];
            $lieu = $lieux[array_rand($lieux)];
            $duree = rand(2, 14);
            $prix = rand(150, 1500);
            
            $circuit->setTitre("Circuit $type - $destination " . chr(64 + $i))
                ->setDestination($destination)
                ->setType($type)
                ->setPrix($prix)
                ->setDuree($duree)
                ->setDescription("Découvrez le circuit $type à $destination. Une expérience inoubliable avec visits de $lieu.")
                ->setDestination($destination)
                ->setDifficulte($difficultes[array_rand($difficultes)])
                ->setActif(rand(0, 10) > 2)
                ->setSourceType('admin')
                ->setPlacesDisponibles(rand(5, 30));

            $this->em->persist($circuit);
            $this->created++;
            
            if ($i % 10 === 0) {
                $io->text("   ... $i circuits created");
            }
        }
        $io->text("✅ Created $count circuits");
    }

    private function createHebergements(SymfonyStyle $io, int $count): void
    {
        $io->section("Creating $count Hébergements");
        
        $types = ['Hôtel', 'Maison d\'hôte', 'Appartement', 'Villa', 'Riad', 'Chalet', 'Auberge', 'Résidence'];
        $villes = ['Djerba', 'Tunis', 'Sousse', 'Hammamet', 'Tozeur', 'Sfax', 'Bizerte', 'Nabeul', 'Monastir', 'Mahdia'];
        $noms = ['Palace', 'Dar', 'Résidence', 'Hotel', 'Maison', 'Villa', 'Sahara', 'Mer', 'Soleil', 'Étoile', 'Palmier', 'Oasis', 'Azur', 'Bleu', 'Blanc'];

        for ($i = 1; $i <= $count; $i++) {
            $heb = new Hebergement();
            $type = $types[array_rand($types)];
            $ville = $villes[array_rand($villes)];
            $nom = $noms[array_rand($noms)];
            $prix = rand(50, 800);
            $chambres = rand(2, 15);
            
            $heb->setNom("$nom $ville $i")
                ->setVille($ville)
                ->setType($type)
                ->setPrixParNuit($prix)
                ->setCapacite($chambres * 2)
                ->setDescription("Superbe $type à $ville. Cadre idéal pour vos vacances.")
                ->setDisponible(rand(0, 10) > 1);

            $this->em->persist($heb);
            $this->created++;
            
            if ($i % 10 === 0) {
                $io->text("   ... $i hébergements created");
            }
        }
        $io->text("✅ Created $count hébergements");
    }

    private function createActivities(SymfonyStyle $io, int $count): void
    {
        $io->section("Creating $count Activities");
        
        $categories = ['Aventure', 'Culture', 'Sport', 'Détente', 'Gastronomie', 'Excursion', 'Visite', 'Divertissement'];
        $lieux = ['Djerba', 'Tunis', 'Sousse', 'Hammamet', 'Tozeur', 'Sfax', 'Bizerte', 'Nabeul', 'Carthage', 'El Jem', 'Kairouan'];
        $activities = ['Excursion', 'Randonnée', 'Plongée', 'Quad', 'Camel Ride', 'Visite Guidée', 'Dégustation', 'Safari', 'Surf', 'Yoga', 'Spa', 'Cours de Cuisine', 'Balade en Bateau', 'Parachutisme', 'Escalade'];

        for ($i = 1; $i <= $count; $i++) {
            $act = new Activity();
            $category = $categories[array_rand($categories)];
            $lieu = $lieux[array_rand($lieux)];
            $activityName = $activities[array_rand($activities)];
            $prix = rand(20, 300);
            $duree = rand(1, 8);
            
            $act->setTitle("$activityName à $lieu - $i")
                ->setLieu($lieu)
                ->setCategory($category)
                ->setPrice($prix)
                ->setDuration("$duree heures")
                ->setDescription("Profitez de cette $activityName inoubliable à $lieu.")
                ->setActif(rand(0, 10) > 1);

            $this->em->persist($act);
            $this->created++;
            
            if ($i % 10 === 0) {
                $io->text("   ... $i activités created");
            }
        }
        $io->text("✅ Created $count activities");
    }

    private function createTransports(SymfonyStyle $io, int $count): void
    {
        $io->section("Creating $count Transports");
        
        $types = ['Avion', 'Bus', 'Train', 'Taxi', 'Vélocation'];
        $villes = ['Tunis', 'Sousse', 'Sfax', 'Djerba', 'Bizerte', 'Sousse', 'Hammamet', 'Tozeur', 'Monastir', 'Nabeul', 'Mahdia', 'Kairouan', 'Gabès'];

        for ($i = 1; $i <= $count; $i++) {
            $transport = new TransportOffer();
            $type = $types[array_rand($types)];
            $depart = $villes[array_rand($villes)];
            $destination = $villes[array_rand($villes)];
            
            $transport->setTransportType($type)
                ->setDepartureCity($depart)
                ->setArrivalCity($destination)
                ->setPrice(rand(15, 250))
                ->setAvailableSeats(rand(10, 100))
                ->setDuration(rand(1, 12) . 'h')
                ->setCompanyName('Compagnie ' . chr(65 + rand(0, 25)))
                ->setIsActive(rand(0, 10) > 2);

            $this->em->persist($transport);
            $this->created++;
            
            if ($i % 10 === 0) {
                $io->text("   ... $i transports created");
            }
        }
        $io->text("✅ Created $count transports");
    }

    private function createFAQs(SymfonyStyle $io, int $count): void
    {
        $io->section("Creating $count FAQs");
        
        $faqs = [
            ['q' => 'Comment réserver un voyage?', 'a' => 'Vous pouvez réserver en ligne via notre plateforme ou nous contacter directement.'],
            ['q' => 'Quels sont les moyens de paiement?', 'a' => 'Nous acceptons les cartes bancaires, PayPal et les virements bancaires.'],
            ['q' => 'Comment annuler une réservation?', 'a' => 'Vous pouvez annuler depuis votre espace client jusqu\'à 48h avant.'],
            ['q' => 'Y a-t-il des réductions pour les groupes?', 'a' => 'Oui, nous offrons des réductions pour les groupes de plus de 10 personnes.'],
            ['q' => 'Les prix sont-ils TTC?', 'a' => 'Tous nos prix sont indiqués en TTC, sans frais cachés.'],
            ['q' => 'Proposez-vous des assurances voyage?', 'a' => 'Oui, nous proposons des assurances annulation et assistance.'],
            ['q' => 'Puis-je modifier ma réservation?', 'a' => 'Oui, vous pouvez modifier jusqu\'à 72h avant le départ.'],
            ['q' => 'Avez-vous des guides parlant français?', 'a' => 'Oui, tous nos guides parlent français et anglais.'],
            ['q' => 'Les transferts sont-ils inclus?', 'a' => 'Selon le circuit, les transferts peuvent être inclus. Vérifiez les détails.'],
            ['q' => 'Puis-je payer en plusieurs fois?', 'a' => 'Oui, nous proposons le paiement en 3x sans frais.'],
            ['q' => 'Les repas sont-ils inclus?', 'a' => 'Cela dépend du circuit. Consultez les détails de chaque offre.'],
            ['q' => 'Y a-t-il un âge minimum pour participer?', 'a' => 'La plupart de nos circuits sont accessibles à partir de 4 ans.'],
            ['q' => 'Comment obtenir mon billet?', 'a' => 'Vous recevrez vos billets par email 48h avant le départ.'],
            ['q' => 'Puis-je créer un circuit sur mesure?', 'a' => 'Oui, nous proposons un service de création de circuits personnalisés.'],
            ['q' => 'Y a-t-il du WiFi dans les hôtels?', 'a' => 'La plupart de nos hôtels partenaires offrent le WiFi gratuit.'],
        ];

        $faqIndex = 0;
        for ($i = 1; $i <= $count; $i++) {
            $faq = new FAQ();
            $data = $faqs[$faqIndex % count($faqs)];
            $faq->setQuestion($data['q'] . ($faqIndex >= count($faqs) ? " #$faqIndex" : ''))
                ->setAnswer($data['a'])
                ->setKeywords('réservation, voyage, tourism, tunisie');

            $this->em->persist($faq);
            $this->created++;
            $faqIndex++;
        }
        $io->text("✅ Created $count FAQs");
    }

    private function createForumPosts(SymfonyStyle $io, int $count): void
    {
        $io->section("Creating $count Forum Posts");
        
        $titles = [
            'Meilleur circuit pour familles',
            'Conseils pour voyager à Djerba',
            'Où manger à Tunis?',
            'Expérience de plongée à Sousse',
            'Circuits économiques en Tunisie',
            'Voyage romantique à Hammamet',
            'Safari dans le Sahara',
            'Visiter Kairouan',
            'Transport depuis l\'aéroport',
            'Meilleurs hôtels à Sfax',
        ];

        $authors = ['Ali', 'Fatma', 'Mohamed', 'Sana', 'Youssef', 'Nadia', 'Karim', 'Amina', 'Sami', 'Rania'];

        for ($i = 1; $i <= $count; $i++) {
            $post = new ForumPost();
            $title = $titles[array_rand($titles)];
            
            $post->setTitle($title . ($i > 10 ? " - $i" : ''))
                ->setAuthor($authors[array_rand($authors)])
                ->setContent("Bonjour à tous! Je partage mon expérience concernant $title. N'hésitez pas à me poser des questions!")
                ->setStatus(['PENDING', 'APPROVED', 'APPROVED'][rand(0, 2)]);

            $this->em->persist($post);
            $this->created++;
        }
        $io->text("✅ Created $count forum posts");
    }
}

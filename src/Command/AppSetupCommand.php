<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Activity;
use App\Entity\Avis;
use App\Entity\Booking;
use App\Entity\Circuit;
use App\Entity\CircuitAvis;
use App\Entity\ForumComment;
use App\Entity\ForumPost;
use App\Entity\Hebergement;
use App\Entity\Reservation;
use App\Entity\ReservationCircuit;
use App\Entity\Review;
use App\Entity\User;
use App\Service\CircuitAiPlanner;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:setup-demo', description: 'Rebuilds the database and seeds a complete Fly&Go demo.')]
class AppSetupCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly CircuitAiPlanner $planner,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Fly&Go — initialisation de la base de démonstration');

        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        if ($metadata === []) {
            $io->error('Aucune métadonnée Doctrine trouvée.');
            return Command::FAILURE;
        }

        $tool = new SchemaTool($this->em);
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);

        $users = $this->seedUsers();
        $hebergements = $this->seedHebergements();
        $activities = $this->seedActivities();
        $circuits = $this->seedCircuits($users);
        $this->seedReservationsAndAvis($users, $hebergements, $activities, $circuits);
        $this->seedForum();

        $this->em->flush();

        $io->success([
            'Base recréée avec succès.',
            'Compte admin : admin@flyandgo.tn / Admin123!',
            'Compte client : client@flyandgo.tn / Client123!',
        ]);

        return Command::SUCCESS;
    }

    /** @return array<string, User> */
    private function seedUsers(): array
    {
        $rows = [
            ['nom' => 'Admin', 'prenom' => 'FlyGo', 'email' => 'admin@flyandgo.tn', 'password' => 'Admin123!', 'roles' => ['ROLE_ADMIN']],
            ['nom' => 'Ben Ali', 'prenom' => 'Sami', 'email' => 'client@flyandgo.tn', 'password' => 'Client123!', 'roles' => ['ROLE_USER']],
            ['nom' => 'Trabelsi', 'prenom' => 'Nour', 'email' => 'nour@flyandgo.tn', 'password' => 'Client123!', 'roles' => ['ROLE_USER']],
        ];

        $users = [];
        foreach ($rows as $i => $row) {
            $user = new User();
            $user->setNom($row['nom'])
                ->setPrenom($row['prenom'])
                ->setEmail($row['email'])
                ->setTelephone('+216 20 000 00' . $i)
                ->setRoles($row['roles'])
                ->setPassword($this->passwordHasher->hashPassword($user, $row['password']))
                ->setCreatedAt(new \DateTimeImmutable('-' . (20 - $i) . ' days'));
            $this->em->persist($user);
            $users[$row['email']] = $user;
        }

        return $users;
    }

    /** @return Hebergement[] */
    private function seedHebergements(): array
    {
        $rows = [
            ['nom' => 'Dar El Medina', 'ville' => 'Tunis', 'type' => 'Maison d\'hôtes', 'prix' => 260.000, 'capacite' => 2, 'adresse' => 'Médina de Tunis', 'description' => 'Maison d’hôtes premium avec check-in rapide, petit-déjeuner inclus et annulation flexible.'],
            ['nom' => 'Blue Horizon Djerba', 'ville' => 'Djerba', 'type' => 'Resort', 'prix' => 420.000, 'capacite' => 4, 'adresse' => 'Zone touristique Midoun', 'description' => 'Resort bord de mer avec piscine et offres familles.'],
            ['nom' => 'Sidi Bou Escape', 'ville' => 'Sidi Bou Saïd', 'type' => 'Villa', 'prix' => 560.000, 'capacite' => 6, 'adresse' => 'Rue Habib Thameur', 'description' => 'Villa avec vue panoramique et expérience premium.'],
            ['nom' => 'Sahara Camp Signature', 'ville' => 'Douz', 'type' => 'Auberge', 'prix' => 310.000, 'capacite' => 3, 'adresse' => 'Route du Sahara', 'description' => 'Camp saharien avec activités incluses et transfert privé.'],
        ];

        $entities = [];
        foreach ($rows as $index => $row) {
            $entity = (new Hebergement())
                ->setNom($row['nom'])
                ->setVille($row['ville'])
                ->setType($row['type'])
                ->setPrixParNuit($row['prix'])
                ->setCapacite($row['capacite'])
                ->setAdresse($row['adresse'])
                ->setDescription($row['description'])
                ->setDisponible(true)
                ->setCreatedAt(new \DateTimeImmutable('-' . (10 - $index) . ' days'));
            $this->em->persist($entity);
            $entities[] = $entity;
        }

        return $entities;
    }

    /** @return Activity[] */
    private function seedActivities(): array
    {
        $rows = [
            ['title' => 'Excursion Sidi Bou Saïd', 'lieu' => 'Sidi Bou Saïd', 'duration' => '4h', 'price' => 120.000, 'capacity' => 18, 'description' => 'Parcours photo, dégustation locale et réservation instantanée.', 'date' => '+4 days'],
            ['title' => 'Safari 4x4 Sahara', 'lieu' => 'Douz', 'duration' => '1 jour', 'price' => 290.000, 'capacity' => 12, 'description' => 'Expérience immersive avec guide et campement premium.', 'date' => '+9 days'],
            ['title' => 'Food Tour Tunis', 'lieu' => 'Tunis', 'duration' => '3h', 'price' => 95.000, 'capacity' => 10, 'description' => 'Découverte street food avec guide francophone.', 'date' => '+5 days'],
        ];

        $entities = [];
        foreach ($rows as $index => $row) {
            $entity = (new Activity())
                ->setTitle($row['title'])
                ->setLieu($row['lieu'])
                ->setDuration($row['duration'])
                ->setPrice($row['price'])
                ->setCapacity($row['capacity'])
                ->setDescription($row['description'])
                ->setDate(new \DateTimeImmutable($row['date']))
                ->setActif(true)
                ->setCreatedAt(new \DateTimeImmutable('-' . (7 - $index) . ' days'));
            $this->em->persist($entity);
            $entities[] = $entity;
        }

        return $entities;
    }

    /** @return Circuit[] */
    private function seedCircuits(array $users): array
    {
        $rows = [
            ['titre' => 'Grand Tour du Nord', 'destination' => 'Bizerte', 'depart' => 'Tunis', 'duree' => '4 jours', 'prix' => 890.000, 'difficulte' => 'Facile', 'places' => 18, 'description' => 'Circuit culturel premium avec étapes optimisées.'],
            ['titre' => 'Sahara Signature', 'destination' => 'Douz', 'depart' => 'Sfax', 'duree' => '3 jours', 'prix' => 760.000, 'difficulte' => 'Modéré', 'places' => 12, 'description' => 'Expérience désert avec guide, sunset camp et transferts inclus.'],
            ['titre' => 'Cap Bon Escapade', 'destination' => 'Hammamet', 'depart' => 'Tunis', 'duree' => '2 jours', 'prix' => 520.000, 'difficulte' => 'Facile', 'places' => 20, 'description' => 'Circuit court idéal week-end avec réservation rapide.'],
        ];

        $entities = [];
        $adminUser = $users['admin@flyandgo.tn'];
        foreach ($rows as $index => $row) {
            $entity = (new Circuit($adminUser))
                ->setTitre($row['titre'])
                ->setDestination($row['destination'])
                ->setDepart($row['depart'])
                ->setDuree($row['duree'])
                ->setPrix($row['prix'])
                ->setDifficulte($row['difficulte'])
                ->setPlacesDisponibles($row['places'])
                ->setDescription($row['description'])
                ->setActif(true)
                ->setSourceType('admin')
                ->setCreatedAt(new \DateTimeImmutable('-' . (8 - $index) . ' days'));
            $this->em->persist($entity);
            $entities[] = $entity;
        }

        $custom1 = $this->planner->generate([
            'destination' => 'Tokyo',
            'depart' => 'Tunis',
            'style' => 'Découverte',
            'budget' => 'Premium',
            'participants' => 2,
            'jours' => 7,
            'date_depart' => date('Y-m-d', strtotime('+30 days')),
            'date_retour' => date('Y-m-d', strtotime('+37 days')),
        ]);
        $circuit1 = (new Circuit($users['client@flyandgo.tn']))
            ->setTitre($custom1['titre'])
            ->setDescription($custom1['description'])
            ->setDuree($custom1['duree'])
            ->setPrix((float) $custom1['prix'])
            ->setDifficulte($custom1['difficulte'])
            ->setDepart($custom1['depart'])
            ->setDestination($custom1['destination'])
            ->setPlacesDisponibles((int) $custom1['places'])
            ->setActif(true)
            ->setIsCustom(true)
            ->setIsAiGenerated(true)
            ->setSourceType('custom')
            ->setGeneratedContext($custom1['generated_context'])
            ->setCreatedAt(new \DateTimeImmutable('-2 days'));
        $this->em->persist($circuit1);
        $entities[] = $circuit1;

        $custom2 = $this->planner->generate([
            'destination' => 'Marrakech',
            'depart' => 'Tunis',
            'style' => 'Romantique',
            'budget' => 'Moyen',
            'participants' => 2,
            'jours' => 4,
            'date_depart' => date('Y-m-d', strtotime('+18 days')),
            'date_retour' => date('Y-m-d', strtotime('+22 days')),
        ]);
        $circuit2 = (new Circuit($users['nour@flyandgo.tn']))
            ->setTitre($custom2['titre'])
            ->setDescription($custom2['description'])
            ->setDuree($custom2['duree'])
            ->setPrix((float) $custom2['prix'])
            ->setDifficulte($custom2['difficulte'])
            ->setDepart($custom2['depart'])
            ->setDestination($custom2['destination'])
            ->setPlacesDisponibles((int) $custom2['places'])
            ->setActif(true)
            ->setIsCustom(true)
            ->setIsAiGenerated(true)
            ->setSourceType('custom')
            ->setGeneratedContext($custom2['generated_context'])
            ->setCreatedAt(new \DateTimeImmutable('-1 day'));
        $this->em->persist($circuit2);
        $entities[] = $circuit2;

        return $entities;
    }

    private function seedReservationsAndAvis(array $users, array $hebergements, array $activities, array $circuits): void
    {
        $r1 = (new Reservation())
            ->setHebergement($hebergements[0])
            ->setUser($users['client@flyandgo.tn'])
            ->setNomClient($users['client@flyandgo.tn']->getFullName())
            ->setEmailClient($users['client@flyandgo.tn']->getEmail())
            ->setTelephoneClient($users['client@flyandgo.tn']->getTelephone())
            ->setDateDebut(new \DateTimeImmutable('+8 days'))
            ->setDateFin(new \DateTimeImmutable('+10 days'))
            ->setNombrePersonnes(2)
            ->setMontantTotal(520)
            ->setStatut('CONFIRMEE');
        $this->em->persist($r1);

        $r2 = (new Reservation())
            ->setHebergement($hebergements[1])
            ->setUser($users['nour@flyandgo.tn'])
            ->setNomClient($users['nour@flyandgo.tn']->getFullName())
            ->setEmailClient($users['nour@flyandgo.tn']->getEmail())
            ->setTelephoneClient($users['nour@flyandgo.tn']->getTelephone())
            ->setDateDebut(new \DateTimeImmutable('+14 days'))
            ->setDateFin(new \DateTimeImmutable('+16 days'))
            ->setNombrePersonnes(3)
            ->setMontantTotal(840)
            ->setStatut('EN_ATTENTE');
        $this->em->persist($r2);

        $a1 = (new Avis())
            ->setHebergement($hebergements[0])
            ->setUser($users['client@flyandgo.tn'])
            ->setAuteur($users['client@flyandgo.tn']->getFullName())
            ->setNote(5)
            ->setCommentaire('Accueil excellent et réservation très fluide.');
        $this->em->persist($a1);

        $a2 = (new Avis())
            ->setHebergement($hebergements[1])
            ->setUser($users['nour@flyandgo.tn'])
            ->setAuteur($users['nour@flyandgo.tn']->getFullName())
            ->setNote(4)
            ->setCommentaire('Très bon resort, idéal pour un séjour famille.');
        $this->em->persist($a2);

        $booking = (new Booking())
            ->setActivity($activities[0])
            ->setUser($users['client@flyandgo.tn'])
            ->setCustomerName($users['client@flyandgo.tn']->getFullName())
            ->setEmail($users['client@flyandgo.tn']->getEmail())
            ->setClientPhone($users['client@flyandgo.tn']->getTelephone())
            ->setPersons(2)
            ->setBookingDate(new \DateTimeImmutable('+4 days'))
            ->setTotalPrice(240)
            ->setStatus('CONFIRMED');
        $this->em->persist($booking);

        $review = (new Review())
            ->setActivity($activities[0])
            ->setUser($users['client@flyandgo.tn'])
            ->setAuthor($users['client@flyandgo.tn']->getFullName())
            ->setRating(5)
            ->setComment('Organisation parfaite et expérience premium.');
        $this->em->persist($review);

        $rc = (new ReservationCircuit())
            ->setCircuit($circuits[0])
            ->setUser($users['client@flyandgo.tn'])
            ->setNomClient($users['client@flyandgo.tn']->getFullName())
            ->setEmailClient($users['client@flyandgo.tn']->getEmail())
            ->setTelephone($users['client@flyandgo.tn']->getTelephone())
            ->setDateReservation(new \DateTimeImmutable('+6 days'))
            ->setNbPersonnes(2)
            ->setMontantTotal($circuits[0]->getPrix() * 2)
            ->setStatut('CONFIRMEE');
        $this->em->persist($rc);

        $cr = (new CircuitAvis())
            ->setCircuit($circuits[0])
            ->setUser($users['client@flyandgo.tn'])
            ->setAuthor($users['client@flyandgo.tn']->getFullName())
            ->setRating(5)
            ->setComment('Circuit bien organisé, programme très clair.');
        $this->em->persist($cr);

        $customReservation = (new ReservationCircuit())
            ->setCircuit($circuits[3])
            ->setUser($users['client@flyandgo.tn'])
            ->setNomClient($users['client@flyandgo.tn']->getFullName())
            ->setEmailClient($users['client@flyandgo.tn']->getEmail())
            ->setTelephone($users['client@flyandgo.tn']->getTelephone())
            ->setDateReservation(new \DateTimeImmutable('+30 days'))
            ->setNbPersonnes(2)
            ->setMontantTotal($circuits[3]->getPrix() * 2)
            ->setStatut('EN_ATTENTE');
        $this->em->persist($customReservation);

        $customReview = (new CircuitAvis())
            ->setCircuit($circuits[3])
            ->setUser($users['client@flyandgo.tn'])
            ->setAuthor($users['client@flyandgo.tn']->getFullName())
            ->setRating(4)
            ->setComment('Le circuit IA m’a donné une bonne base de voyage.');
        $this->em->persist($customReview);
    }

    private function seedForum(): void
    {
        $post = (new ForumPost())
            ->setTitle('Circuit IA : vos retours ?')
            ->setContent('Avez-vous testé le nouveau module de circuit personnalisé ?')
            ->setAuthor('Fly&Go Team')
            ->setCategorie('Nouveautés')
            ->setStatus('APPROVED');
        $this->em->persist($post);

        $comment = (new ForumComment())
            ->setPost($post)
            ->setAuthor('Sami')
            ->setContent('Oui, le résultat est clair et bien structuré.');
        $this->em->persist($comment);
    }
}

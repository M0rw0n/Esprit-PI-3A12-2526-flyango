<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Hebergement;
use App\Entity\Circuit;
use App\Entity\Activity;
use App\Entity\ForumPost;
use App\Entity\ForumComment;
use App\Entity\Avis;
use App\Entity\Review;
use App\Entity\Reservation;
use App\Entity\ReservationCircuit;
use App\Entity\Booking;
use App\Entity\CircuitAvis;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@flyandgo.tn')
            ->setRole('ROLE_ADMIN')
            ->setNom('Administrateur')
            ->setPrenom('Admin')
            ->setTelephone('+216 20 000 001')
            ->setActif(true)
            ->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $user1 = new User();
        $user1->setEmail('user@flyandgo.tn')
            ->setRole('ROLE_USER')
            ->setNom('Dupont')
            ->setPrenom('Jean')
            ->setTelephone('+216 20 000 002')
            ->setActif(true)
            ->setPassword($this->hasher->hashPassword($user1, 'password'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('sarah@flyandgo.tn')
            ->setRole('ROLE_USER')
            ->setNom('Mallek')
            ->setPrenom('Sarah')
            ->setTelephone('+216 20 000 003')
            ->setActif(true)
            ->setPassword($this->hasher->hashPassword($user2, 'password'));
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('ahmed@flyandgo.tn')
            ->setRole('ROLE_USER')
            ->setNom('Benali')
            ->setPrenom('Ahmed')
            ->setTelephone('+216 20 000 004')
            ->setActif(true)
            ->setPassword($this->hasher->hashPassword($user3, 'password'));
        $manager->persist($user3);

        $manager->flush();

        $users = [$admin, $user1, $user2, $user3];

        $h1 = new Hebergement();
        $h1->setNom('Hôtel Sidi Bou Said Palace')
            ->setVille('Sidi Bou Said')
            ->setType('Hôtel')
            ->setPrixParNuit(280.00)
            ->setDescription('Un hôtel de luxe avec vue panoramique sur la Méditerranée. Architecture traditionnelle tunisienne.')
            ->setCapacite(4)
            ->setDisponible(true);
        $manager->persist($h1);

        $h2 = new Hebergement();
        $h2->setNom('Villa Djerba Sunset')
            ->setVille('Djerba')
            ->setType('Villa')
            ->setPrixParNuit(450.00)
            ->setDescription('Magnifique villa en bord de mer avec piscine privée et jardin fleuri.')
            ->setCapacite(8)
            ->setDisponible(true);
        $manager->persist($h2);

        $h3 = new Hebergement();
        $h3->setNom('Riad El Medina')
            ->setVille('Tunis')
            ->setType('Riad')
            ->setPrixParNuit(190.00)
            ->setDescription('Riad authentique au cœur de la médina de Tunis. Décor traditionnel et confort moderne.')
            ->setCapacite(2)
            ->setDisponible(true);
        $manager->persist($h3);

        $h4 = new Hebergement();
        $h4->setNom('Resort Hammamet Plage')
            ->setVille('Hammamet')
            ->setType('Resort')
            ->setPrixParNuit(320.00)
            ->setDescription('Resort 5 étoiles directement sur la plage. Accès illimité au spa et aux activités nautiques.')
            ->setCapacite(6)
            ->setDisponible(true);
        $manager->persist($h4);

        $h5 = new Hebergement();
        $h5->setNom('Appartement Carthage View')
            ->setVille('Carthage')
            ->setType('Appartement')
            ->setPrixParNuit(150.00)
            ->setDescription('Bel appartement moderne avec terrasse et vue sur les ruines de Carthage.')
            ->setCapacite(4)
            ->setDisponible(true);
        $manager->persist($h5);

        $h6 = new Hebergement();
        $h6->setNom('Maison d\'hôtes Tozeur')
            ->setVille('Tozeur')
            ->setType('Maison d\'hôtes')
            ->setPrixParNuit(120.00)
            ->setDescription('Maison d\'hôtes traditionnelle aux portes du désert. Idéale pour découvrir le Sahara.')
            ->setCapacite(3)
            ->setDisponible(true);
        $manager->persist($h6);

        $manager->flush();
        $hebergements = [$h1, $h2, $h3, $h4, $h5, $h6];

        $c1 = new Circuit();
        $c1->setTitre('Circuit Sahara Magique')
            ->setDescription('Découvrez les dunes dorées du Sahara lors de ce circuit exceptionnel. Nuits en camp bédouin, balade en dromadaire et coucher de soleil sur les dunes.')
            ->setDuree(5)
            ->setPrix(850.00)
            ->setDifficulte('Facile')
            ->setPlacesDisponibles(15)
            ->setDepart('Tunis')
            ->setDestination('Douz')
            ->setActif(true)
            ->setSourceType('admin');
        $manager->persist($c1);

        $c2 = new Circuit();
        $c2->setTitre('Tour du Cap Bon')
            ->setDescription('Explorez les vignobles, les plages et les villages pittoresques du Cap Bon. Un voyage entre mer, histoire et gastronomie.')
            ->setDuree(3)
            ->setPrix(350.00)
            ->setDifficulte('Facile')
            ->setPlacesDisponibles(20)
            ->setDepart('Tunis')
            ->setDestination('Nabeul')
            ->setActif(true)
            ->setSourceType('admin');
        $manager->persist($c2);

        $c3 = new Circuit();
        $c3->setTitre('Circuit Montagnes Tunisiennes')
            ->setDescription('Randonnée à travers les montagnes de la Kroumirie et les forêts de chêne-liège.')
            ->setDuree(4)
            ->setPrix(520.00)
            ->setDifficulte('Modéré')
            ->setPlacesDisponibles(12)
            ->setDepart('Tunis')
            ->setDestination('Aïn Draham')
            ->setActif(true)
            ->setSourceType('admin');
        $manager->persist($c3);

        $c4 = new Circuit();
        $c4->setTitre('Aventure Désert Extrême')
            ->setDescription('Pour les amateurs de sensations fortes : dunes géantes, oasis secrètes et nuit sous les étoiles du Sahara.')
            ->setDuree(7)
            ->setPrix(1200.00)
            ->setDifficulte('Difficile')
            ->setPlacesDisponibles(8)
            ->setDepart('Tunis')
            ->setDestination('Ksar Ghilane')
            ->setActif(true)
            ->setSourceType('admin');
        $manager->persist($c4);

        $manager->flush();
        $circuits = [$c1, $c2, $c3, $c4];

        $a1 = new Activity();
        $a1->setTitle('Randonnée Djebel Zaghouan')
            ->setDescription('Ascension du point culminant du Nord de la Tunisie. Vue panoramique exceptionnelle.')
            ->setPrice(45.00)
            ->setDuration('6 heures')
            ->setCapacity(15)
            ->setLieu('Zaghouan')
            ->setActif(true);
        $manager->persist($a1);

        $a2 = new Activity();
        $a2->setTitle('Plongée Sous-Marine Tabarka')
            ->setDescription('Découvrez les fonds marins cristallins de Tabarka. Tous niveaux acceptés. Équipement fourni.')
            ->setPrice(85.00)
            ->setDuration('3 heures')
            ->setCapacity(8)
            ->setLieu('Tabarka')
            ->setActif(true);
        $manager->persist($a2);

        $a3 = new Activity();
        $a3->setTitle('Tour à cheval Tozeur')
            ->setDescription('Balade équestre dans les oasis et aux abords des dunes. Un moment magique dans un décor de carte postale.')
            ->setPrice(60.00)
            ->setDuration('2 heures')
            ->setCapacity(10)
            ->setLieu('Tozeur')
            ->setActif(true);
        $manager->persist($a3);

        $a4 = new Activity();
        $a4->setTitle('Atelier Poterie Nabeul')
            ->setDescription('Apprenez l\'art de la poterie avec des artisans locaux. Créez votre propre pièce à emporter en souvenir.')
            ->setPrice(35.00)
            ->setDuration('2 heures')
            ->setCapacity(12)
            ->setLieu('Nabeul')
            ->setActif(true);
        $manager->persist($a4);

        $a5 = new Activity();
        $a5->setTitle('Surf et Kitesurf Djerba')
            ->setDescription('Cours de surf et kitesurf pour tous niveaux sur les plages paradisiaques de Djerba.')
            ->setPrice(95.00)
            ->setDuration('4 heures')
            ->setCapacity(6)
            ->setLieu('Djerba')
            ->setActif(true);
        $manager->persist($a5);

        $a6 = new Activity();
        $a6->setTitle('Visite Carthage Antique')
            ->setDescription('Visite guidée des ruines de Carthage avec un archéologue passionné. Histoire et patrimoine.')
            ->setPrice(25.00)
            ->setDuration('3 heures')
            ->setCapacity(25)
            ->setLieu('Carthage')
            ->setActif(true);
        $manager->persist($a6);

        $manager->flush();
        $activities = [$a1, $a2, $a3, $a4, $a5, $a6];

        $p1 = new ForumPost();
        $p1->setTitle('Les meilleurs hébergements à Djerba ?')
            ->setContent('Je planifie un voyage à Djerba en famille pour 2 semaines. Quelqu\'un peut me recommander des hébergements proches de la mer avec piscine ? Budget moyen.')
            ->setAuthor('Ahmed B.')
            ->setCategorie('Hébergement')
            ->setStatus('APPROVED');
        $manager->persist($p1);

        $p2 = new ForumPost();
        $p2->setTitle('Circuit Sahara : vos expériences ?')
            ->setContent('J\'ai prévu de faire le circuit Sahara en octobre. Est-ce la bonne saison ? Des conseils sur ce qu\'il faut emporter ?')
            ->setAuthor('Sarah M.')
            ->setCategorie('Circuit')
            ->setStatus('APPROVED');
        $manager->persist($p2);

        $p3 = new ForumPost();
        $p3->setTitle('Activités pour enfants en Tunisie')
            ->setContent('Nous voyageons avec des enfants de 5 et 8 ans. Quelles sont les meilleures activités adaptées aux familles ?')
            ->setAuthor('Pierre L.')
            ->setCategorie('Activité')
            ->setStatus('APPROVED');
        $manager->persist($p3);

        $p4 = new ForumPost();
        $p4->setTitle('Conseils visa et entrée en Tunisie')
            ->setContent('Bonjour, je viens de France. Y a-t-il des démarches particulières pour entrer en Tunisie ? Combien de temps est-on autorisé à rester ?')
            ->setAuthor('Marie T.')
            ->setCategorie('Conseil')
            ->setStatus('APPROVED');
        $manager->persist($p4);

        $manager->flush();
        $posts = [$p1, $p2, $p3, $p4];

        $comment1 = new ForumComment();
        $comment1->setPost($p1)
            ->setAuthor('Karim R.')
            ->setContent('Bonjour ! Je recommande vivement le Resort Hammamet Plage. Excellent rapport qualité-prix et les enfants adorent !');
        $manager->persist($comment1);

        $comment2 = new ForumComment();
        $comment2->setPost($p1)
            ->setAuthor('Leila S.')
            ->setContent('Villa Djerba Sunset est parfaite pour une famille. La piscine privée est un vrai plus !');
        $manager->persist($comment2);

        $comment3 = new ForumComment();
        $comment3->setPost($p2)
            ->setAuthor('Mohamed A.')
            ->setContent('Octobre est parfait pour le Sahara ! La chaleur est supportable et les nuits sont magnifiques.');
        $manager->persist($comment3);
        $manager->flush();

        $comment4 = new ForumComment();
        $comment4->setPost($p2)
            ->setParentId($comment3->getId())
            ->setAuthor('Sarah M.')
            ->setContent('Merci beaucoup Mohamed ! Vous avez fait quel circuit exactement ?');
        $manager->persist($comment4);

        $comment5 = new ForumComment();
        $comment5->setPost($p3)
            ->setAuthor('Nadia K.')
            ->setContent('Le Musée du Bardo est super pour les enfants, ils adorent les momies ! Et la plage à Djerba aussi.');
        $manager->persist($comment5);

        $manager->flush();

        $avis1 = new Avis();
        $avis1->setHebergement($h1)
            ->setUser($user1)
            ->setNote(5)
            ->setCommentaire('Magnifique hôtel avec une vue imprenable sur la mer ! Service impeccable et personnel très accueillant.');
        $manager->persist($avis1);

        $avis2 = new Avis();
        $avis2->setHebergement($h1)
            ->setUser($user2)
            ->setNote(4)
            ->setCommentaire('Très bon séjour, chambre propre et confortable. Petit-déjeuner excellent. Je recommande.');
        $manager->persist($avis2);

        $avis3 = new Avis();
        $avis3->setHebergement($h2)
            ->setUser($user1)
            ->setNote(5)
            ->setCommentaire('La villa est absolument parfaite. Piscine privée, vue mer, calme total. Un séjour de rêve !');
        $manager->persist($avis3);

        $avis4 = new Avis();
        $avis4->setHebergement($h3)
            ->setUser($user3)
            ->setNote(5)
            ->setCommentaire('Le riad est magnifique, décoration authentique. On se sent vraiment dans la Tunisie traditionnelle.');
        $manager->persist($avis4);

        $avis5 = new Avis();
        $avis5->setHebergement($h4)
            ->setUser($user2)
            ->setNote(4)
            ->setCommentaire('Resort très bien équipé. Accès plage direct et spa superbe. Un peu cher mais ça vaut le coup.');
        $manager->persist($avis5);

        $avis6 = new Avis();
        $avis6->setHebergement($h5)
            ->setUser($user1)
            ->setNote(4)
            ->setCommentaire('Super appartement, propre et bien équipé. La vue depuis la terrasse est exceptionnelle.');
        $manager->persist($avis6);

        $manager->flush();

        $rev1 = new Review();
        $rev1->setActivity($a1)
            ->setUser($user1)
            ->setAuthor('Antoine P.')
            ->setRating(5)
            ->setComment('Randonnée magnifique ! Guide très compétent et vue au sommet à couper le souffle.');
        $manager->persist($rev1);

        $rev2 = new Review();
        $rev2->setActivity($a2)
            ->setUser($user2)
            ->setAuthor('Claire V.')
            ->setRating(5)
            ->setComment('Plongée inoubliable ! Les fonds marins de Tabarka sont exceptionnels. Moniteur très patient.');
        $manager->persist($rev2);

        $rev3 = new Review();
        $rev3->setActivity($a3)
            ->setUser($user3)
            ->setAuthor('Nour B.')
            ->setRating(4)
            ->setComment('Belle balade à cheval dans les oasis. Dépaysement total et paysages magnifiques.');
        $manager->persist($rev3);

        $rev4 = new Review();
        $rev4->setActivity($a6)
            ->setUser($user1)
            ->setAuthor('Marc D.')
            ->setRating(5)
            ->setComment('Guide passionnant, on apprend plein de choses sur l\'histoire de Carthage.');
        $manager->persist($rev4);

        $manager->flush();

        $ca1 = new CircuitAvis();
        $ca1->setCircuit($c1)
            ->setUser($user1)
            ->setAuthor('Jean D.')
            ->setRating(5)
            ->setComment('Une expérience inoubliable ! Le camp bédouin est magique et les guides sont exceptionnels.');
        $manager->persist($ca1);

        $ca2 = new CircuitAvis();
        $ca2->setCircuit($c2)
            ->setUser($user2)
            ->setAuthor('Lina K.')
            ->setRating(4)
            ->setComment('Très belle région le Cap Bon. Les vignobles sont magnifiques et la cuisine locale est excellente.');
        $manager->persist($ca2);

        $ca3 = new CircuitAvis();
        $ca3->setCircuit($c3)
            ->setUser($user3)
            ->setAuthor('Riadh M.')
            ->setRating(5)
            ->setComment('Randonnée exceptionnelle dans les montagnes. La forêt de chêne-liège est splendide.');
        $manager->persist($ca3);

        $manager->flush();

        $res1 = new Reservation();
        $res1->setHebergement($h1)
            ->setUser($user1)
            ->setNomClient('Jean Dupont')
            ->setEmailClient('jean.dupont@email.com')
            ->setTelephoneClient('+216 20 123 456')
            ->setNombrePersonnes(2)
            ->setDateDebut(new \DateTime('2026-05-10'))
            ->setDateFin(new \DateTime('2026-05-14'))
            ->setNombreNuits(4)
            ->setMontantTotal(1120.00)
            ->setStatut('Confirmée');
        $manager->persist($res1);

        $res2 = new Reservation();
        $res2->setHebergement($h2)
            ->setUser($user2)
            ->setNomClient('Sarah Mallek')
            ->setEmailClient('sarah.m@email.com')
            ->setTelephoneClient('+216 20 234 567')
            ->setNombrePersonnes(4)
            ->setDateDebut(new \DateTime('2026-06-15'))
            ->setDateFin(new \DateTime('2026-06-22'))
            ->setNombreNuits(7)
            ->setMontantTotal(3150.00)
            ->setStatut('Confirmée');
        $manager->persist($res2);

        $res3 = new Reservation();
        $res3->setHebergement($h4)
            ->setNomClient('Pierre Lambert')
            ->setEmailClient('pierre.l@email.com')
            ->setTelephoneClient('+216 20 345 678')
            ->setNombrePersonnes(3)
            ->setDateDebut(new \DateTime('2026-07-01'))
            ->setDateFin(new \DateTime('2026-07-05'))
            ->setNombreNuits(4)
            ->setMontantTotal(1280.00)
            ->setStatut('En attente');
        $manager->persist($res3);

        $manager->flush();

        $rc1 = new ReservationCircuit();
        $rc1->setCircuit($c1)
            ->setUser($user1)
            ->setNomClient('Jean Dupont')
            ->setEmailClient('jean.dupont@email.com')
            ->setTelephone('+216 20 123 456')
            ->setNbPersonnes(2)
            ->setDateReservation(new \DateTime())
            ->setDateDepart(new \DateTime('2026-09-10'))
            ->setMontantTotal(1700.00)
            ->setStatut('CONFIRME');
        $manager->persist($rc1);

        $rc2 = new ReservationCircuit();
        $rc2->setCircuit($c2)
            ->setUser($user2)
            ->setNomClient('Sarah Mallek')
            ->setEmailClient('sarah.m@email.com')
            ->setTelephone('+216 20 234 567')
            ->setNbPersonnes(2)
            ->setDateReservation(new \DateTime())
            ->setDateDepart(new \DateTime('2026-08-20'))
            ->setMontantTotal(700.00)
            ->setStatut('CONFIRME');
        $manager->persist($rc2);

        $rc3 = new ReservationCircuit();
        $rc3->setCircuit($c4)
            ->setNomClient('Ahmed Benali')
            ->setEmailClient('ahmed.b@email.com')
            ->setTelephone('+216 20 345 678')
            ->setNbPersonnes(1)
            ->setDateReservation(new \DateTime())
            ->setDateDepart(new \DateTime('2026-10-05'))
            ->setMontantTotal(1200.00)
            ->setStatut('EN_ATTENTE');
        $manager->persist($rc3);

        $manager->flush();

        $b1 = new Booking();
        $b1->setActivity($a2)
            ->setUser($user1)
            ->setCustomerName('Jean Dupont')
            ->setEmail('jean.dupont@email.com')
            ->setClientPhone('+216 20 123 456')
            ->setPersons(2)
            ->setBookingDate(new \DateTime('2026-06-15'))
            ->setTotalPrice(170.00)
            ->setStatus('CONFIRMED');
        $manager->persist($b1);

        $b2 = new Booking();
        $b2->setActivity($a3)
            ->setUser($user2)
            ->setCustomerName('Sarah Mallek')
            ->setEmail('sarah.m@email.com')
            ->setClientPhone('+216 20 234 567')
            ->setPersons(2)
            ->setBookingDate(new \DateTime('2026-07-20'))
            ->setTotalPrice(120.00)
            ->setStatus('CONFIRMED');
        $manager->persist($b2);

        $b3 = new Booking();
        $b3->setActivity($a5)
            ->setCustomerName('Pierre Lambert')
            ->setEmail('pierre.l@email.com')
            ->setClientPhone('+216 20 345 678')
            ->setPersons(1)
            ->setBookingDate(new \DateTime('2026-08-01'))
            ->setTotalPrice(95.00)
            ->setStatus('PENDING');
        $manager->persist($b3);

        $manager->flush();
    }
}

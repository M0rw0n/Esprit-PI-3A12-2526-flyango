<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Booking;
use App\Entity\Review;
<<<<<<< HEAD
use App\Entity\User;
use App\Repository\ActivityRepository;
=======
use App\Repository\ActivityRepository;
use App\Repository\ReviewRepository;
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

<<<<<<< HEAD
#[Route('/activites')]
class ActivityController extends AbstractController
{
    #[Route('', name: 'activity_index', methods: ['GET'])]
    public function index(Request $request, ActivityRepository $repo): Response
    {
        $q         = $request->query->get('q');
        $lieu      = $request->query->get('lieu');
        $prixMin   = $request->query->get('prix_min') ? (float)$request->query->get('prix_min') : null;
        $prixMax   = $request->query->get('prix_max') ? (float)$request->query->get('prix_max') : null;
        $dateDebut = $request->query->get('date_debut');
        $dateFin   = $request->query->get('date_fin');
        $sort      = $request->query->get('sort');

        $activities = $repo->search($q, $lieu, $prixMin, $prixMax, $dateDebut, $dateFin, $sort);

        if ($request->isXmlHttpRequest()) {
            $data = array_map(fn(Activity $a) => [
                'id'          => $a->getId(),
                'title'       => $a->getTitle(),
                'description' => $a->getDescription(),
                'price'       => $a->getPrice(),
                'duration'    => $a->getDuration(),
                'lieu'        => $a->getLieu(),
                'image'       => $a->getImage(),
            ], $activities);
            return new JsonResponse(['activities' => $data]);
        }

        return $this->render('activity/index.html.twig', [
            'activities' => $activities,
            'q'          => $q,
            'lieu'       => $lieu,
            'prix_min'   => $prixMin,
            'prix_max'   => $prixMax,
            'date_debut' => $dateDebut,
            'date_fin'   => $dateFin,
            'sort'       => $sort,
        ]);
    }

    #[Route('/{id}', name: 'activity_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, ActivityRepository $repo): Response
    {
        $a = $repo->find($id);
        if (!$a) throw $this->createNotFoundException();
        return $this->render('activity/show.html.twig', ['activity' => $a]);
    }

    #[Route('/{id}/reserver', name: 'activity_book', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function book(int $id, Request $request, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $a = $repo->find($id);
        if (!$a) throw $this->createNotFoundException();

        /** @var User $user */
        $user = $this->getUser();
        $persons = (int)$request->request->get('persons', 1);
        $booking = new Booking();
        $booking->setActivity($a)
                ->setUser($user)
                ->setCustomerName($user->getFullName())
                ->setEmail($user->getEmail())
                ->setClientPhone($user->getTelephone())
                ->setPersons($persons)
                ->setTotalPrice($persons * $a->getPrice())
                ->setStatus('PENDING');

        if ($d = $request->request->get('booking_date')) {
            $booking->setBookingDate(new \DateTime($d));
=======
#[Route('/activity')]
class ActivityController extends AbstractController
{
    /* ── LIST ── */
    #[Route('', name: 'activity_index', methods: ['GET'])]
    public function index(ActivityRepository $repo): Response
    {
        return $this->render('activity/index.html.twig', [
            'activities' => $repo->findAllWithPlace(),
        ]);
    }

    /* ── AJAX SEARCH ── */
    #[Route('/search', name: 'activity_search', methods: ['GET'])]
    public function search(Request $request, ActivityRepository $repo): JsonResponse
    {
        $q = trim($request->query->get('q', ''));

        $activities = $q
            ? $repo->searchByTitle($q)
            : $repo->findAllWithPlace();

        $data = array_map(fn(Activity $a) => [
            'id'          => $a->getId(),
            'title'       => $a->getTitle(),
            'description' => $a->getDescription(),
            'price'       => $a->getPrice(),
            'duration'    => $a->getDuration(),
            'capacity'    => $a->getCapacity(),
            'date'        => $a->getDate()?->format('Y-m-d'),
            'image'       => $a->getImage(),
            'place'       => $a->getPlace()?->getName(),
        ], $activities);

        return new JsonResponse(['success' => true, 'activities' => $data]);
    }

    /* ── SHOW ── */
    #[Route('/{id}', name: 'activity_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, ActivityRepository $activityRepo, ReviewRepository $reviewRepo): Response
    {
        $activity = $activityRepo->find($id);
        if (!$activity) throw $this->createNotFoundException('Activité introuvable.');

        $reviews = $reviewRepo->findBy(['activityId' => $id], ['createdAt' => 'DESC']);

        return $this->render('activity/show.html.twig', [
            'activity' => $activity,
            'reviews'  => $reviews,
        ]);
    }

    /* ── NEW ── */
    #[Route('/new', name: 'activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $activity = new Activity();
            $activity->setTitle($request->request->get('title'));
            $activity->setDescription($request->request->get('description'));
            $activity->setPrice((float)$request->request->get('price'));
            $activity->setDuration($request->request->get('duration'));
            $activity->setCapacity((int)$request->request->get('capacity'));

            if ($dateStr = $request->request->get('date')) {
                $activity->setDate(new \DateTime($dateStr));
            }

            // Image upload
            $file = $request->files->get('image');
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/activities', $filename);
                $activity->setImage('uploads/activities/' . $filename);
            }

            $em->persist($activity);
            $em->flush();

            $this->addFlash('success', 'Activité créée avec succès !');
            return $this->redirectToRoute('activity_index');
        }

        return $this->render('activity/new.html.twig');
    }

    /* ── EDIT ── */
    #[Route('/{id}/edit', name: 'activity_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $activity = $repo->find($id);
        if (!$activity) throw $this->createNotFoundException();

        if ($request->isMethod('POST')) {
            $activity->setTitle($request->request->get('title'));
            $activity->setDescription($request->request->get('description'));
            $activity->setPrice((float)$request->request->get('price'));
            $activity->setDuration($request->request->get('duration'));
            $activity->setCapacity((int)$request->request->get('capacity'));

            if ($dateStr = $request->request->get('date')) {
                $activity->setDate(new \DateTime($dateStr));
            }

            $file = $request->files->get('image');
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/activities', $filename);
                $activity->setImage('uploads/activities/' . $filename);
            }

            $em->flush();
            $this->addFlash('success', 'Activité modifiée !');
            return $this->redirectToRoute('activity_show', ['id' => $id]);
        }

        return $this->render('activity/edit.html.twig', ['activity' => $activity]);
    }

    /* ── DELETE ── */
    #[Route('/{id}/delete', name: 'activity_delete', methods: ['POST', 'DELETE'])]
    public function delete(int $id, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $activity = $repo->find($id);
        if ($activity) { $em->remove($activity); $em->flush(); }
        $this->addFlash('success', 'Activité supprimée.');
        return $this->redirectToRoute('activity_index');
    }

    /* ── BOOKING ── */
    #[Route('/{activityId}/book', name: 'booking_create', methods: ['POST'])]
    public function createBooking(int $activityId, Request $request, ActivityRepository $activityRepo, EntityManagerInterface $em): Response
    {
        $activity = $activityRepo->find($activityId);
        if (!$activity) throw $this->createNotFoundException();

        $booking = new Booking();
        $booking->setCustomerName($request->request->get('customer_name'));
        $booking->setEmail($request->request->get('email'));
        $booking->setClientPhone($request->request->get('client_phone'));
        $booking->setActivityId($activityId);
        $booking->setPersons((int)$request->request->get('persons', 1));
        $booking->setTotalPrice((float)$request->request->get('total_price'));
        $booking->setStatus('PENDING');

        if ($dateStr = $request->request->get('booking_date')) {
            $booking->setBookingDate(new \DateTime($dateStr));
        } else {
            $booking->setBookingDate(new \DateTime());
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
        }

        $em->persist($booking);
        $em->flush();

<<<<<<< HEAD
        $this->addFlash('success', '🎉 Réservation activité ajoutée à votre espace.');
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/{id}/avis', name: 'activity_review', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addReview(int $id, Request $request, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $a = $repo->find($id);
        if (!$a) throw $this->createNotFoundException();

        /** @var User $user */
        $user = $this->getUser();
        $review = $em->getRepository(Review::class)->findOneBy(['activity' => $a, 'user' => $user]) ?? new Review();

        $review->setActivity($a)
               ->setUser($user)
               ->setAuthor($user->getFullName())
               ->setRating((int)$request->request->get('rating', 5))
               ->setComment((string)$request->request->get('comment', ''));

        if ($review->getId() === null) {
            $em->persist($review);
        }
        $em->flush();

        $this->addFlash('success', '⭐ Votre avis activité a bien été enregistré.');
        return $this->redirectToRoute('user_avis');
=======
        $this->addFlash('success', '🎉 Réservation confirmée ! Nous vous contacterons bientôt.');
        return $this->redirectToRoute('activity_show', ['id' => $activityId]);
    }

    /* ── ADD REVIEW ── */
    #[Route('/{activityId}/review', name: 'review_add', methods: ['POST'])]
    public function addReview(int $activityId, Request $request, EntityManagerInterface $em): Response
    {
        $review = new Review();
        $review->setActivityId($activityId);
        $review->setAuthor($request->request->get('author', 'Anonyme'));
        $review->setRating((int)$request->request->get('rating', 5));
        $review->setComment($request->request->get('comment'));
        $review->setCreatedAt(new \DateTime());

        $em->persist($review);
        $em->flush();

        $this->addFlash('success', '⭐ Merci pour votre avis !');
        return $this->redirectToRoute('activity_show', ['id' => $activityId]);
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
    }
}

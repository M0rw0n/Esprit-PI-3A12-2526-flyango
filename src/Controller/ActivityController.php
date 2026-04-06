<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Booking;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        }

        $em->persist($booking);
        $em->flush();

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
    }
}

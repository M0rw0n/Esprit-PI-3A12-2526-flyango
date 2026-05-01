<?php

namespace App\Controller;

use App\Entity\CircuitAvis;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\BookingRepository;
use App\Repository\ReservationCircuitRepository;
use App\Repository\ReservationRepository;
use App\Repository\CircuitRepository;
use App\Repository\ReviewRepository;
use App\Repository\CircuitAvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mon-espace')]
class UserSpaceController extends AbstractController
{
    #[Route('', name: 'user_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('user/dashboard.html.twig');
    }

    #[Route('/reservations', name: 'user_reservations', methods: ['GET'])]
    public function reservations(
        Request $request,
        PaginatorInterface $paginator,
        ReservationRepository $resHebRepo,
        BookingRepository $bookingRepo,
        ReservationCircuitRepository $resCircRepo,
    ): Response {
        $user = $this->getUser();

        $hebergements = $paginator->paginate(
            $resHebRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_h', 1),
            5,
            ['pageParameterName' => 'page_h']
        );

        $transports = $paginator->paginate(
            $bookingRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_t', 1),
            5,
            ['pageParameterName' => 'page_t']
        );

        $circuits = $paginator->paginate(
            $resCircRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_c', 1),
            5,
            ['pageParameterName' => 'page_c']
        );

        return $this->render('user/reservations.html.twig', [
            'hebergements' => $hebergements,
            'transports' => $transports,
            'circuits' => $circuits,
        ]);
    }

    #[Route('/avis', name: 'user_avis', methods: ['GET'])]
    public function avis(
        AvisRepository $avisRepo,
        ReviewRepository $reviewRepo,
        CircuitAvisRepository $circuitAvisRepo,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $user = $this->getUser();

        $hebergements = $paginator->paginate(
            $avisRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_h', 1),
            5,
            ['pageParameterName' => 'page_h']
        );

        $activites = $paginator->paginate(
            $reviewRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_a', 1),
            5,
            ['pageParameterName' => 'page_a']
        );

        $circuits = $paginator->paginate(
            $circuitAvisRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_c', 1),
            5,
            ['pageParameterName' => 'page_c']
        );

        return $this->render('user/avis.html.twig', [
            'hebergements' => $hebergements,
            'activites' => $activites,
            'circuits' => $circuits,
        ]);
    }

    #[Route('/profil', name: 'user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $fullName = $request->request->get('fullName');
            $parts = explode(' ', trim($fullName), 2);
            if (count($parts) === 2) {
                $user->setPrenom($parts[0]);
                $user->setNom($parts[1]);
            } else {
                $user->setPrenom($fullName);
            }
            $user->setTelephone($request->request->get('telephone'));
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/profile.html.twig');
    }

    #[Route('/avis/hebergement/{id}/delete', name: 'user_hebergement_avis_delete', methods: ['POST'])]
    public function deleteHebergementAvis(int $id, ReviewRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $avis = $repo->find($id);
        if ($avis && $avis->getUser() === $this->getUser()) {
            $em->remove($avis);
            $em->flush();
            $this->addFlash('success', 'Avis supprimé.');
        }
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/avis/activity/{id}/delete', name: 'user_activity_avis_delete', methods: ['POST'])]
    public function deleteActivityAvis(int $id, ReviewRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $avis = $repo->find($id);
        if ($avis && $avis->getUser() === $this->getUser()) {
            $em->remove($avis);
            $em->flush();
            $this->addFlash('success', 'Avis supprimé.');
        }
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/avis/circuit/{id}/delete', name: 'user_circuit_avis_delete', methods: ['POST'])]
    public function deleteCircuitAvis(int $id, CircuitAvisRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $avis = $repo->find($id);
        if ($avis && $avis->getUser() === $this->getUser()) {
            $em->remove($avis);
            $em->flush();
            $this->addFlash('success', 'Avis supprimé.');
        }
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/circuit/{id}/delete', name: 'user_circuit_delete', methods: ['POST'])]
    public function deleteCircuit(int $id, CircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $circuit = $repo->find($id);
        if ($circuit && $circuit->getCreator() === $this->getUser()) {
            $em->remove($circuit);
            $em->flush();
            $this->addFlash('success', 'Circuit supprimé.');
        }
        return $this->redirectToRoute('user_dashboard');
    }

    #[Route('/reservation/hebergement/{id}/cancel', name: 'user_hebergement_cancel', methods: ['POST'])]
    public function cancelHebergement(int $id, ReservationRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $res = $repo->find($id);
        if ($res && $res->getUser() === $this->getUser()) {
            $em->remove($res);
            $em->flush();
            $this->addFlash('success', 'Réservation annulée.');
        }
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/reservation/activity/{id}/cancel', name: 'user_activity_cancel', methods: ['POST'])]
    public function cancelActivity(int $id, BookingRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $res = $repo->find($id);
        if ($res && $res->getUser() === $this->getUser()) {
            $em->remove($res);
            $em->flush();
            $this->addFlash('success', 'Réservation annulée.');
        }
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/reservation/circuit/{id}/cancel', name: 'user_circuit_cancel', methods: ['POST'])]
    public function cancelCircuit(int $id, ReservationCircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $res = $repo->find($id);
        if ($res && $res->getUser() === $this->getUser()) {
            $em->remove($res);
            $em->flush();
            $this->addFlash('success', 'Réservation annulée.');
        }
        return $this->redirectToRoute('user_reservations');
    }
}

<?php

namespace App\Controller;

use App\Dto\CircuitSearchInput;
use App\Dto\ComparatorInput;
use App\Dto\CustomCircuitRequest;
use App\Dto\ReservationFilterInput;
use App\Dto\ReservationRequest;
use App\Dto\ReviewRequest;
use App\Service\CircuitModuleService;
use App\Service\DemoUserContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CircuitModuleController extends AbstractController
{
    public function __construct(
        private readonly CircuitModuleService $service,
        private readonly DemoUserContext $demoUserContext,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function home(Request $request): Response
    {
        return $this->portal($request);
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function portal(Request $request): Response
    {
        $userId = $this->demoUserContext->getUserId($request);
        $homeData = $this->service->getUserHomepageData($userId);
        $adminDashboard = $this->service->getAdminDashboard();

        return $this->render('portal/index.html.twig', [
            'userId' => $userId,
            'homeData' => $homeData,
            'adminDashboard' => $adminDashboard,
        ]);
    }

    #[Route('/espace-user', name: 'user_home', methods: ['GET'])]
    public function userHome(Request $request): Response
    {
        $userId = $this->demoUserContext->getUserId($request);

        return $this->render('user/home.html.twig', [
            'userId' => $userId,
            'homeData' => $this->service->getUserHomepageData($userId),
            'types' => $this->service->getTypes(),
        ]);
    }

    #[Route('/circuits', name: 'circuit_index', methods: ['GET'])]
    public function circuits(Request $request): Response
    {
        $input = CircuitSearchInput::fromRequest($request);
        $circuits = $this->service->searchCircuits($input);

        return $this->render('circuit_module/circuits/index.html.twig', [
            'filters' => $input,
            'types' => $this->service->getTypes(),
            'circuits' => $circuits,
            'userId' => $this->demoUserContext->getUserId($request),
        ]);
    }

    #[Route('/circuits/api/search', name: 'circuit_search_api', methods: ['GET'])]
    public function circuitSearch(Request $request): JsonResponse
    {
        $input = CircuitSearchInput::fromRequest($request);
        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            return $this->json(['message' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $circuits = $this->service->searchCircuits($input);
        $html = $this->renderView('circuit_module/circuits/_cards.html.twig', [
            'circuits' => $circuits,
            'userId' => $this->demoUserContext->getUserId($request),
        ]);

        return $this->json([
            'html' => $html,
            'count' => count($circuits),
        ]);
    }

    #[Route('/circuits/{id}', name: 'circuit_show', methods: ['GET'])]
    public function show(Request $request, int $id): Response
    {
        $circuit = $this->service->getCircuitById($id);
        if (!$circuit) {
            throw $this->createNotFoundException('Circuit introuvable.');
        }

        return $this->render('circuit_module/circuits/show.html.twig', [
            'circuit' => $circuit,
            'pricing' => $circuit['pricing'],
            'reservationDefaults' => ['date_depart' => $circuit['start_date'] ?? '', 'nb_travelers' => 2],
            'userId' => $this->demoUserContext->getUserId($request),
        ]);
    }

    #[Route('/circuits/{id}/reserve', name: 'circuit_reserve', methods: ['POST'])]
    public function reserve(Request $request, int $id): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('reserve_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('circuit_show', ['id' => $id, 'user' => $this->demoUserContext->getUserId($request)]);
        }

        $dto = ReservationRequest::fromRequest($request);
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $this->addFlash('error', trim((string) $errors));

            return $this->redirectToRoute('circuit_show', ['id' => $id, 'user' => $this->demoUserContext->getUserId($request)]);
        }

        $reservationId = $this->service->createReservation($id, $dto, $this->demoUserContext->getUserId($request));
        $this->addFlash('success', sprintf('Réservation #%d confirmée avec succès.', $reservationId));

        return $this->redirectToRoute('reservation_index', ['user' => $this->demoUserContext->getUserId($request)]);
    }

    #[Route('/circuits-sur-mesure', name: 'custom_circuit_index', methods: ['GET', 'POST'])]
    public function custom(Request $request): Response
    {
        $values = [
            'destination' => '',
            'dateDepart' => '',
            'dateRetour' => '',
            'duree' => 7,
            'budgetMin' => 500,
            'budgetMax' => 3000,
            'styleVoyage' => 'Aventure',
            'niveauFatigue' => 2,
            'centresInteret' => [],
        ];
        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('custom_circuit', (string) $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');

                return $this->redirectToRoute('custom_circuit_index', ['user' => $this->demoUserContext->getUserId($request)]);
            }

            $dto = CustomCircuitRequest::fromRequest($request);
            $values = $dto->toArray();
            $violations = $this->validator->validate($dto);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
            } else {
                $this->service->saveCustomCircuit($dto, $this->demoUserContext->getUserId($request));
                $this->addFlash('success', 'Votre circuit sur mesure a été enregistré dans la base actuelle.');

                return $this->redirectToRoute('custom_circuit_index', ['user' => $this->demoUserContext->getUserId($request)]);
            }
        }

        return $this->render('circuit_module/custom/index.html.twig', [
            'values' => $values,
            'errors' => $errors,
            'userId' => $this->demoUserContext->getUserId($request),
        ]);
    }

    #[Route('/mes-reservations', name: 'reservation_index', methods: ['GET'])]
    public function reservations(Request $request): Response
    {
        $userId = $this->demoUserContext->getUserId($request);
        $filters = ReservationFilterInput::fromRequest($request);

        return $this->render('circuit_module/reservations/index.html.twig', [
            'filters' => $filters,
            'reservations' => $this->service->getReservations($userId, $filters),
            'stats' => $this->service->getReservationStats($userId),
            'userId' => $userId,
        ]);
    }

    #[Route('/mes-reservations/api/search', name: 'reservation_search_api', methods: ['GET'])]
    public function reservationsSearch(Request $request): JsonResponse
    {
        $userId = $this->demoUserContext->getUserId($request);
        $filters = ReservationFilterInput::fromRequest($request);
        $reservations = $this->service->getReservations($userId, $filters);

        return $this->json([
            'html' => $this->renderView('circuit_module/reservations/_list.html.twig', ['reservations' => $reservations, 'userId' => $userId]),
            'count' => count($reservations),
        ]);
    }

    #[Route('/mes-reservations/{id}/annuler', name: 'reservation_cancel', methods: ['POST'])]
    public function cancelReservation(Request $request, int $id): RedirectResponse
    {
        $userId = $this->demoUserContext->getUserId($request);
        if ($this->isCsrfTokenValid('cancel_reservation_' . $id, (string) $request->request->get('_token'))) {
            $this->service->cancelReservation($id, $userId);
            $this->addFlash('success', 'La réservation a été annulée.');
        } else {
            $this->addFlash('error', 'Action non autorisée.');
        }

        return $this->redirectToRoute('reservation_index', ['user' => $userId]);
    }

    #[Route('/mes-avis', name: 'review_index', methods: ['GET'])]
    public function reviews(Request $request): Response
    {
        $userId = $this->demoUserContext->getUserId($request);

        return $this->render('circuit_module/reviews/index.html.twig', [
            'summary' => $this->service->getReviewSummary($userId),
            'reviews' => $this->service->getUserReviews($userId),
            'circuits' => $this->service->getFeaturedCircuitsForReview(),
            'userId' => $userId,
        ]);
    }

    #[Route('/circuits/{id}/avis', name: 'review_store', methods: ['POST'])]
    public function storeReview(Request $request, int $id): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('review_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('circuit_show', ['id' => $id, 'user' => $this->demoUserContext->getUserId($request)]);
        }

        $dto = ReviewRequest::fromRequest($request);
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $this->addFlash('error', trim((string) $errors));

            return $this->redirectToRoute('circuit_show', ['id' => $id, 'user' => $this->demoUserContext->getUserId($request)]);
        }

        $this->service->createReview($id, $dto, $this->demoUserContext->getUserId($request));
        $this->addFlash('success', 'Votre avis a été enregistré.');

        return $this->redirectToRoute('circuit_show', ['id' => $id, 'user' => $this->demoUserContext->getUserId($request)]);
    }

    #[Route('/comparateur-prix', name: 'comparator_index', methods: ['GET'])]
    public function comparator(Request $request): Response
    {
        return $this->render('circuit_module/comparator/index.html.twig', [
            'userId' => $this->demoUserContext->getUserId($request),
        ]);
    }

    #[Route('/comparateur-prix/api/search', name: 'comparator_search_api', methods: ['GET'])]
    public function comparatorSearch(Request $request): JsonResponse
    {
        $input = ComparatorInput::fromRequest($request);
        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            return $this->json(['message' => trim((string) $errors)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json($this->service->comparePrices($input));
    }

    #[Route('/ratings', name: 'rating_index', methods: ['GET'])]
    public function ratings(Request $request): Response
    {
        return $this->render('circuit_module/rating/index.html.twig', [
            'dashboard' => $this->service->getRatingDashboard(),
            'userId' => $this->demoUserContext->getUserId($request),
        ]);
    }
}

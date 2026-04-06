<?php

namespace App\Controller;

use App\Dto\AdminCircuitData;
use App\Dto\AdminCircuitFilterInput;
use App\Dto\AdminReservationData;
use App\Dto\AdminReservationFilterInput;
use App\Dto\AdminReviewData;
use App\Dto\AdminReviewFilterInput;
use App\Service\CircuitModuleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin')]
final class AdminModuleController extends AbstractController
{
    public function __construct(
        private readonly CircuitModuleService $service,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    #[Route('/dashboard', name: 'admin_dashboard_alias', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'dashboard' => $this->service->getAdminDashboard(),
        ]);
    }

    #[Route('/circuits', name: 'admin_circuits', methods: ['GET'])]
    public function circuits(Request $request): Response
    {
        $filters = AdminCircuitFilterInput::fromRequest($request);
        $editId = $request->query->getInt('edit');
        $editCircuit = $editId > 0 ? $this->service->getAdminCircuitById($editId) : null;
        $formValues = $editCircuit ? AdminCircuitData::fromArray($editCircuit)->toArray() : (new AdminCircuitData())->toArray();

        return $this->render('admin/circuits/index.html.twig', [
            'filters' => $filters,
            'types' => $this->service->getTypes(),
            'stats' => $this->service->getAdminCircuitStats(),
            'circuits' => $this->service->getAdminCircuits($filters),
            'editCircuit' => $editCircuit,
            'formValues' => $formValues,
            'formErrors' => [],
        ]);
    }

    #[Route('/circuits/api/search', name: 'admin_circuits_search_api', methods: ['GET'])]
    public function circuitsSearch(Request $request): JsonResponse
    {
        $filters = AdminCircuitFilterInput::fromRequest($request);
        $circuits = $this->service->getAdminCircuits($filters);

        return $this->json([
            'html' => $this->renderView('admin/circuits/_list.html.twig', ['circuits' => $circuits]),
            'count' => count($circuits),
        ]);
    }

    #[Route('/circuits/create', name: 'admin_circuit_create', methods: ['POST'])]
    public function createCircuit(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_circuit_create', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Session expirée. Veuillez réessayer.');

            return $this->redirectToRoute('admin_circuits');
        }

        $dto = AdminCircuitData::fromRequest($request);
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            $filters = AdminCircuitFilterInput::fromRequest($request);

            return $this->render('admin/circuits/index.html.twig', [
                'filters' => $filters,
                'types' => $this->service->getTypes(),
                'stats' => $this->service->getAdminCircuitStats(),
                'circuits' => $this->service->getAdminCircuits($filters),
                'editCircuit' => null,
                'formValues' => $dto->toArray(),
                'formErrors' => $this->formatViolations($violations),
            ], new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $this->service->createAdminCircuit($dto);
        $this->addFlash('success', 'Nouveau pack ajouté avec succès.');

        return $this->redirectToRoute('admin_circuits');
    }

    #[Route('/circuits/{id}/update', name: 'admin_circuit_update', methods: ['POST'])]
    public function updateCircuit(Request $request, int $id): Response
    {
        if (!$this->isCsrfTokenValid('admin_circuit_update_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Session expirée. Veuillez réessayer.');

            return $this->redirectToRoute('admin_circuits', ['edit' => $id]);
        }

        $dto = AdminCircuitData::fromRequest($request);
        $violations = $this->validator->validate($dto);
        $editCircuit = $this->service->getAdminCircuitById($id);
        if (!$editCircuit) {
            throw $this->createNotFoundException('Pack introuvable.');
        }

        if (count($violations) > 0) {
            $filters = AdminCircuitFilterInput::fromRequest($request);

            return $this->render('admin/circuits/index.html.twig', [
                'filters' => $filters,
                'types' => $this->service->getTypes(),
                'stats' => $this->service->getAdminCircuitStats(),
                'circuits' => $this->service->getAdminCircuits($filters),
                'editCircuit' => $editCircuit,
                'formValues' => $dto->toArray(),
                'formErrors' => $this->formatViolations($violations),
            ], new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $this->service->updateAdminCircuit($id, $dto);
        $this->addFlash('success', 'Pack mis à jour avec succès.');

        return $this->redirectToRoute('admin_circuits', ['edit' => $id]);
    }

    #[Route('/circuits/{id}/delete', name: 'admin_circuit_delete', methods: ['POST'])]
    public function deleteCircuit(Request $request, int $id): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('admin_circuit_delete_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Suppression refusée : jeton invalide.');

            return $this->redirectToRoute('admin_circuits');
        }

        $result = $this->service->deleteAdminCircuit($id);
        $this->addFlash($result['deleted'] ? 'success' : 'error', $result['message']);

        return $this->redirectToRoute('admin_circuits');
    }

    #[Route('/reservations', name: 'admin_reservations', methods: ['GET'])]
    public function reservations(Request $request): Response
    {
        $filters = AdminReservationFilterInput::fromRequest($request);
        $editId = $request->query->getInt('edit');
        $editReservation = $editId > 0 ? $this->service->getAdminReservationById($editId) : null;
        $formValues = $editReservation ? AdminReservationData::fromArray($editReservation)->toArray() : (new AdminReservationData())->toArray();

        return $this->renderReservationsPage($filters, $editReservation, $formValues, []);
    }

    #[Route('/reservations/api/search', name: 'admin_reservations_search_api', methods: ['GET'])]
    public function reservationsSearch(Request $request): JsonResponse
    {
        $filters = AdminReservationFilterInput::fromRequest($request);
        $reservations = $this->service->getAdminReservations($filters);

        return $this->json([
            'html' => $this->renderView('admin/reservations/_list.html.twig', ['reservations' => $reservations]),
            'count' => count($reservations),
        ]);
    }

    #[Route('/reservations/create', name: 'admin_reservation_create', methods: ['POST'])]
    public function createReservation(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_reservation_create', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Session expirée. Veuillez réessayer.');

            return $this->redirectToRoute('admin_reservations');
        }

        $dto = AdminReservationData::fromRequest($request);
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->renderReservationsPage(
                AdminReservationFilterInput::fromRequest($request),
                null,
                $dto->toArray(),
                $this->formatViolations($violations),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $this->service->createAdminReservation($dto);
        } catch (\RuntimeException $exception) {
            return $this->renderReservationsPage(
                AdminReservationFilterInput::fromRequest($request),
                null,
                $dto->toArray(),
                [$exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->addFlash('success', 'Réservation ajoutée avec succès.');

        return $this->redirectToRoute('admin_reservations');
    }

    #[Route('/reservations/{id}/update', name: 'admin_reservation_update', methods: ['POST'])]
    public function updateReservation(Request $request, int $id): Response
    {
        if (!$this->isCsrfTokenValid('admin_reservation_update_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Session expirée. Veuillez réessayer.');

            return $this->redirectToRoute('admin_reservations', ['edit' => $id]);
        }

        $dto = AdminReservationData::fromRequest($request);
        $violations = $this->validator->validate($dto);
        $editReservation = $this->service->getAdminReservationById($id);
        if (!$editReservation) {
            throw $this->createNotFoundException('Réservation introuvable.');
        }

        if (count($violations) > 0) {
            return $this->renderReservationsPage(
                AdminReservationFilterInput::fromRequest($request),
                $editReservation,
                $dto->toArray(),
                $this->formatViolations($violations),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $this->service->updateAdminReservation($id, $dto);
        } catch (\RuntimeException $exception) {
            return $this->renderReservationsPage(
                AdminReservationFilterInput::fromRequest($request),
                $editReservation,
                $dto->toArray(),
                [$exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->addFlash('success', 'Réservation mise à jour avec succès.');

        return $this->redirectToRoute('admin_reservations', ['edit' => $id]);
    }

    #[Route('/reservations/{id}/delete', name: 'admin_reservation_delete', methods: ['POST'])]
    public function deleteReservation(Request $request, int $id): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('admin_reservation_delete_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Suppression refusée : jeton invalide.');

            return $this->redirectToRoute('admin_reservations');
        }

        $this->service->deleteAdminReservation($id);
        $this->addFlash('success', 'Réservation supprimée avec succès.');

        return $this->redirectToRoute('admin_reservations');
    }

    #[Route('/avis', name: 'admin_reviews', methods: ['GET'])]
    public function reviews(Request $request): Response
    {
        $filters = AdminReviewFilterInput::fromRequest($request);
        $editId = $request->query->getInt('edit');
        $editReview = $editId > 0 ? $this->service->getAdminReviewById($editId) : null;
        $formValues = $editReview ? AdminReviewData::fromArray($editReview)->toArray() : (new AdminReviewData())->toArray();

        return $this->renderReviewsPage($filters, $editReview, $formValues, []);
    }

    #[Route('/avis/api/search', name: 'admin_reviews_search_api', methods: ['GET'])]
    public function reviewsSearch(Request $request): JsonResponse
    {
        $filters = AdminReviewFilterInput::fromRequest($request);
        $reviews = $this->service->getAdminReviews($filters);

        return $this->json([
            'html' => $this->renderView('admin/reviews/_list.html.twig', ['reviews' => $reviews]),
            'count' => count($reviews),
        ]);
    }

    #[Route('/avis/create', name: 'admin_review_create', methods: ['POST'])]
    public function createReview(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_review_create', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Session expirée. Veuillez réessayer.');

            return $this->redirectToRoute('admin_reviews');
        }

        $dto = AdminReviewData::fromRequest($request);
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->renderReviewsPage(
                AdminReviewFilterInput::fromRequest($request),
                null,
                $dto->toArray(),
                $this->formatViolations($violations),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $this->service->createAdminReview($dto);
        } catch (\RuntimeException $exception) {
            return $this->renderReviewsPage(
                AdminReviewFilterInput::fromRequest($request),
                null,
                $dto->toArray(),
                [$exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->addFlash('success', 'Avis ajouté avec succès.');

        return $this->redirectToRoute('admin_reviews');
    }

    #[Route('/avis/{id}/update', name: 'admin_review_update', methods: ['POST'])]
    public function updateReview(Request $request, int $id): Response
    {
        if (!$this->isCsrfTokenValid('admin_review_update_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Session expirée. Veuillez réessayer.');

            return $this->redirectToRoute('admin_reviews', ['edit' => $id]);
        }

        $dto = AdminReviewData::fromRequest($request);
        $violations = $this->validator->validate($dto);
        $editReview = $this->service->getAdminReviewById($id);
        if (!$editReview) {
            throw $this->createNotFoundException('Avis introuvable.');
        }

        if (count($violations) > 0) {
            return $this->renderReviewsPage(
                AdminReviewFilterInput::fromRequest($request),
                $editReview,
                $dto->toArray(),
                $this->formatViolations($violations),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $this->service->updateAdminReview($id, $dto);
        } catch (\RuntimeException $exception) {
            return $this->renderReviewsPage(
                AdminReviewFilterInput::fromRequest($request),
                $editReview,
                $dto->toArray(),
                [$exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->addFlash('success', 'Avis mis à jour avec succès.');

        return $this->redirectToRoute('admin_reviews', ['edit' => $id]);
    }

    #[Route('/avis/{id}/delete', name: 'admin_review_delete', methods: ['POST'])]
    public function deleteReview(Request $request, int $id): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('admin_review_delete_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Suppression refusée : jeton invalide.');

            return $this->redirectToRoute('admin_reviews');
        }

        $this->service->deleteAdminReview($id);
        $this->addFlash('success', 'Avis supprimé avec succès.');

        return $this->redirectToRoute('admin_reviews');
    }

    private function formatViolations(ConstraintViolationListInterface $violations): array
    {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getMessage();
        }

        return array_values(array_unique($messages));
    }

    private function renderReservationsPage(
        AdminReservationFilterInput $filters,
        ?array $editReservation,
        array $formValues,
        array $formErrors,
        int $statusCode = Response::HTTP_OK,
    ): Response {
        return $this->render('admin/reservations/index.html.twig', [
            'filters' => $filters,
            'stats' => $this->service->getAdminReservationStats(),
            'reservations' => $this->service->getAdminReservations($filters),
            'editReservation' => $editReservation,
            'formValues' => $formValues,
            'formErrors' => $formErrors,
            'circuits' => $this->service->getAdminCircuitOptions(),
            'users' => $this->service->getAdminUsers(),
        ], new Response('', $statusCode));
    }

    private function renderReviewsPage(
        AdminReviewFilterInput $filters,
        ?array $editReview,
        array $formValues,
        array $formErrors,
        int $statusCode = Response::HTTP_OK,
    ): Response {
        return $this->render('admin/reviews/index.html.twig', [
            'filters' => $filters,
            'summary' => $this->service->getAdminReviewSummary(),
            'reviews' => $this->service->getAdminReviews($filters),
            'editReview' => $editReview,
            'formValues' => $formValues,
            'formErrors' => $formErrors,
            'circuits' => $this->service->getAdminCircuitOptions(),
            'users' => $this->service->getAdminUsers(),
        ], new Response('', $statusCode));
    }
}

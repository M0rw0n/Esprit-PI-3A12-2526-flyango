<?php

namespace App\Controller\Admin;

use App\Entity\ProfilVoyageur;
use App\Entity\User;
use App\Form\ProfilVoyageurType;
use App\Form\UserType;
use App\Repository\ProfilVoyageurRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private ProfilVoyageurRepository $profilVoyageurRepository,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        $stats = $this->userService->getStats();

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'currentTab' => 'dashboard',
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(Request $request): Response
    {
        $search = $request->query->get('search');
        $role   = $request->query->get('role');
        $status = $request->query->get('status');
        $sort   = $request->query->get('sort', 'date_desc');

        $filters = array_filter([
            'search' => $search,
            'role'   => $role,
            'status' => $status,
            'sort'   => $sort,
        ]);

        $users = $this->userRepository->findByFilters(
            search: $search,
            role:   $role,
            status: $status,
            sort:   $sort
        );

        return $this->render('admin/dashboard.html.twig', [
            'users'      => $users,
            'filters'    => $filters,
            'stats'      => $this->userService->getStats(),
            'currentTab' => 'users',
        ]);
    }

    #[Route('/users/create', name: 'app_admin_user_create', methods: ['GET', 'POST'])]
    public function createUser(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('motDePasse')->getData();
            $this->userService->createUser($user, $plainPassword, sendWelcomeEmail: true);

            // Create traveler profile if user is VOYAGEUR
            if ($user->getRole() === 'VOYAGEUR') {
                $profil = new ProfilVoyageur();
                $profil->setUser($user);
                $profil->setDestinationPreferee('À déterminer');
                $profil->setTypeVoyage('Adventure');
                $profil->setBudget('1000');
                $this->entityManager->persist($profil);
                $this->entityManager->flush();
            }

            $this->addFlash('success', 'Utilisateur créé avec succès.');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/_user_modal.html.twig', [
            'form' => $form,
            'mode' => 'create',
        ]);
    }

    #[Route('/users/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function editUser(int $id, Request $request): Response
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->updateUser($user);
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/_user_modal.html.twig', [
            'form' => $form,
            'mode' => 'edit',
            'user' => $user,
        ]);
    }

    #[Route('/users/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(int $id, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_users');
        }

        $user = $this->userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $this->userService->deleteUser($user);
        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/{id}/toggle-active', name: 'app_admin_user_toggle_active', methods: ['POST'])]
    public function toggleActive(int $id): Response
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $this->userService->toggleActive($user);
        $message = $user->isActif() ? 'Utilisateur activé' : 'Utilisateur désactivé';
        $this->addFlash('success', $message);

        return $this->redirectToRoute('app_admin_users');
    }

    /**
     * AJAX endpoint — returns JSON list of users for dynamic search/filter.
     * Supports: q (search), role, status, sort
     */
    #[Route('/users/search', name: 'app_admin_users_search', methods: ['GET'])]
    public function searchUsers(Request $request): JsonResponse
    {
        $search = $request->query->get('q');
        $role   = $request->query->get('role');
        $status = $request->query->get('status');
        $sort   = $request->query->get('sort', 'date_desc');

        $users = $this->userRepository->findByFilters(
            search: $search,
            role:   $role,
            status: $status,
            sort:   $sort
        );

        $data = array_map(function (User $user) {
            return [
                'id'          => $user->getId(),
                'nom_complet' => $user->getNomComplet(),
                'email'       => $user->getEmail(),
                'phone'       => $user->getPhone(),
                'role'        => $user->getRole(),
                'actif'       => $user->isActif(),
                'created_at'  => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
                // CSRF token so the JS-rendered delete form works
                'csrf_delete' => $this->container->get('security.csrf.token_manager')
                    ->getToken('delete' . $user->getId())
                    ->getValue(),
            ];
        }, $users);

        return new JsonResponse($data);
    }

    #[Route('/profiles', name: 'app_admin_profiles')]
    public function profiles(Request $request): Response
    {
        $typeVoyage  = $request->query->get('type_voyage');
        $destination = $request->query->get('destination');
        $budgetMin   = $request->query->get('budget_min');
        $budgetMax   = $request->query->get('budget_max');

        $profils = $this->profilVoyageurRepository->findByFilters(
            typeVoyage:  $typeVoyage,
            destination: $destination,
            budgetMin:   $budgetMin   ? (float) $budgetMin   : null,
            budgetMax:   $budgetMax   ? (float) $budgetMax   : null
        );

        $statsByType = $this->profilVoyageurRepository->getStatsByType();

        return $this->render('admin/dashboard.html.twig', [
            'profils'     => $profils,
            'statsByType' => $statsByType,
            'stats'       => $this->userService->getStats(),
            'currentTab'  => 'profiles',
        ]);
    }

    #[Route('/profiles/{userId}/edit', name: 'app_admin_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(int $userId, Request $request): Response
    {
        $user = $this->userRepository->find($userId);

        if (!$user || !$user->getProfilVoyageur()) {
            throw $this->createNotFoundException('Profil non trouvé');
        }

        $profil = $user->getProfilVoyageur();
        $form   = $this->createForm(ProfilVoyageurType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($profil);
            $this->entityManager->flush();
            $this->addFlash('success', 'Profil modifié avec succès.');
            return $this->redirectToRoute('app_admin_profiles');
        }

        return $this->render('admin/_profile_modal.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/profiles/{userId}/delete', name: 'app_admin_profile_delete', methods: ['POST'])]
    public function deleteProfile(int $userId): Response
    {
        $user = $this->userRepository->find($userId);

        if (!$user || !$user->getProfilVoyageur()) {
            throw $this->createNotFoundException('Profil non trouvé');
        }

        $profil = $user->getProfilVoyageur();
        $this->entityManager->remove($profil);
        $this->entityManager->flush();

        $this->addFlash('success', 'Profil supprimé avec succès.');
        return $this->redirectToRoute('app_admin_profiles');
    }
}

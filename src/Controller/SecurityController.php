<?php

namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\RegistrationFormType;
use App\Repository\PasswordResetTokenRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private EmailService $emailService,
        private UserRepository $userRepository,
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_admin_dashboard');
            }
            return $this->redirectToRoute('app_profil');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profil');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if email already exists
            $existingUser = $this->userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }

            // Set default values
            $user->setRole('VOYAGEUR');
            $user->setActif(true);
            $user->setAuthProvider('LOCAL');

            // Create profile for traveler
            $plainPassword = $form->get('motDePasse')->getData();
            $this->userService->createUser($user, $plainPassword);

            $this->addFlash('success', 'Inscription réussie! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profil');
        }

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                // For security, don't reveal if email exists
                $this->addFlash('info', 'Si cet email existe, vous recevrez un lien de réinitialisation.');
                return $this->redirectToRoute('app_login');
            }

            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $resetToken = new PasswordResetToken();
            $resetToken->setUser($user);
            $resetToken->setToken($token);
            $resetToken->setExpirationDate(new \DateTimeImmutable('+1 day'));

            $this->entityManager->persist($resetToken);
            $this->entityManager->flush();

            // Send email
            $resetLink = $this->generateUrl('app_reset_password', ['token' => $token], 0);
            $this->emailService->sendPasswordResetEmail($user, $resetLink);

            $this->addFlash('success', 'Un lien de réinitialisation a été envoyé à votre email.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(string $token, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profil');
        }

        $resetToken = $this->passwordResetTokenRepository->findValidToken($token);

        if (!$resetToken) {
            $this->addFlash('error', 'Ce lien de réinitialisation est invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }

            // Update password
            $user = $resetToken->getUser();
            $this->userService->updateUserPassword($user, $newPassword);

            // Mark token as used
            $resetToken->setUsed(true);
            $this->entityManager->persist($resetToken);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé. Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/reset_password.html.twig', [
            'token' => $token,
        ]);
    }
}

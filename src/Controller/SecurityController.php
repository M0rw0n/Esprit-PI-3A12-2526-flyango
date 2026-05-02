<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private function redirectAuthenticatedUser(): Response
    {
        return $this->redirectToRoute($this->isGranted('ROLE_ADMIN') ? 'admin_dashboard' : 'user_dashboard');
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectAuthenticatedUser();
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'google_client_id' => '455416813258-6jue7nupumfub7jtsguc6pg89s9mdh9r.apps.googleusercontent.com',
            'facebook_app_id' => '947651210933536',
            'api_auth_google' => $this->generateUrl('api_auth_google'),
            'api_auth_facebook' => $this->generateUrl('api_auth_facebook'),
        ]);
    }

    #[Route('/post-login', name: 'app_after_login')]
    public function afterLogin(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectAuthenticatedUser();
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectAuthenticatedUser();
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $nom    = trim($request->request->get('nom', ''));
            $prenom = trim($request->request->get('prenom', ''));
            $email  = trim($request->request->get('email', ''));
            $pass   = $request->request->get('password', '');
            $pass2  = $request->request->get('password_confirm', '');
            $tel    = trim($request->request->get('telephone', ''));

            if (!$nom) $errors[] = 'Le nom est requis.';
            if (!$prenom) $errors[] = 'Le prénom est requis.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
            if (strlen($pass) < 6) $errors[] = 'Mot de passe minimum 6 caractères.';
            if ($pass !== $pass2) $errors[] = 'Les mots de passe ne correspondent pas.';

            if (!$errors) {
                $existing = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existing) {
                    $errors[] = 'Cet email est déjà utilisé.';
                } else {
                    $user = new User();
                    $user->setNom($nom)
                         ->setPrenom($prenom)
                         ->setEmail($email)
                         ->setTelephone($tel ?: null)
                         ->setPassword($hasher->hashPassword($user, $pass))
                         ->setRoles(['ROLE_USER']);

                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', '✅ Compte créé ! Connectez-vous maintenant.');
                    return $this->redirectToRoute('app_login');
                }
            }
        }

        return $this->render('security/register.html.twig', ['errors' => $errors]);
    }

    #[Route('/api/auth/google', name: 'api_auth_google', methods: ['POST'])]
    public function apiAuthGoogle(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $email = $data['email'] ?? null;
        $googleId = $data['google_id'] ?? null;
        $name = $data['name'] ?? 'Utilisateur';
        $avatar = $data['avatar'] ?? null;
        
        if (!$email || !$googleId) {
            return $this->json(['success' => false, 'error' => 'Données invalides']);
        }
        
        $user = $em->getRepository(User::class)->findOneBy(['googleId' => $googleId]);
        
        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            
            if ($user) {
                $user->setGoogleId($googleId);
            } else {
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($hasher->hashPassword($user, bin2hex(random_bytes(16))));
                $user->setGoogleId($googleId);
                $user->setRoles(['ROLE_USER']);
                
                $parts = explode(' ', $name, 2);
                $user->setPrenom($parts[0]);
                $user->setNom($parts[1] ?? '');
            }
        }
        
        if ($avatar && !$user->getAvatar()) {
            $user->setAvatar($avatar);
        }
        
        $em->persist($user);
        $em->flush();
        
        return $this->json(['success' => true, 'redirect' => $this->generateUrl('user_dashboard')]);
    }

    #[Route('/api/auth/facebook', name: 'api_auth_facebook', methods: ['POST'])]
    public function apiAuthFacebook(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $email = $data['email'] ?? null;
        $facebookId = $data['facebook_id'] ?? null;
        $name = $data['name'] ?? 'Utilisateur';
        $avatar = $data['avatar'] ?? null;
        
        if (!$email || !$facebookId) {
            return $this->json(['success' => false, 'error' => 'Données invalides']);
        }
        
        $user = $em->getRepository(User::class)->findOneBy(['facebookId' => $facebookId]);
        
        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            
            if ($user) {
                $user->setFacebookId($facebookId);
            } else {
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($hasher->hashPassword($user, bin2hex(random_bytes(16))));
                $user->setFacebookId($facebookId);
                $user->setRoles(['ROLE_USER']);
                
                $parts = explode(' ', $name, 2);
                $user->setPrenom($parts[0]);
                $user->setNom($parts[1] ?? '');
            }
        }
        
        if ($avatar && !$user->getAvatar()) {
            $user->setAvatar($avatar);
        }
        
        $em->persist($user);
        $em->flush();
        
        return $this->json(['success' => true, 'redirect' => $this->generateUrl('user_dashboard')]);
    }
}

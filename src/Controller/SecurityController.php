<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}

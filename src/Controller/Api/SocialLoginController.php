<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/api')]
class SocialLoginController extends AbstractController
{
    #[Route('/auth/facebook', name: 'api_facebook_login', methods: ['POST'])]
    public function facebookLogin(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $facebookToken = $data['facebook_token'] ?? null;
        $facebookId = $data['facebook_id'] ?? null;
        $email = $data['email'] ?? null;
        $name = $data['name'] ?? '';
        $avatar = $data['avatar'] ?? null;

        if (!$facebookToken || !$facebookId) {
            return $this->json(['success' => false, 'error' => 'Token Facebook requis'], 400);
        }

        if (!$email) {
            return $this->json(['success' => false, 'error' => 'Email requis'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['facebookId' => $facebookId]);
        
        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user && $user->getFacebookId()) {
                return $this->json(['success' => false, 'error' => 'Cet email est déjà utilisé avec un autre provider'], 400);
            }
        }

        if (!$user) {
            $parts = explode(' ', $name, 2);
            $prenom = $parts[0] ?? '';
            $nom = $parts[1] ?? '';

            $user = new User();
            $user->setNom($nom)
                 ->setPrenom($prenom)
                 ->setEmail($email)
                 ->setAvatar($avatar)
                 ->setFacebookId($facebookId)
                 ->setPassword($hasher->hashPassword($user, bin2hex(random_bytes(16))))
                 ->setRoles(['ROLE_USER']);

            $em->persist($user);
        } else {
            $user->setFacebookId($facebookId);
            if ($avatar && !$user->getAvatar()) {
                $user->setAvatar($avatar);
            }
        }

        $em->flush();

        return $this->json([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'avatar' => $user->getAvatar(),
                'role' => $user->getRole(),
            ]
        ]);
    }

    #[Route('/auth/google', name: 'api_google_login', methods: ['POST'])]
    public function googleLogin(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $googleToken = $data['google_token'] ?? null;
        $googleId = $data['google_id'] ?? null;
        $email = $data['email'] ?? null;
        $name = $data['name'] ?? '';
        $avatar = $data['avatar'] ?? null;

        if (!$googleToken || !$googleId) {
            return $this->json(['success' => false, 'error' => 'Token Google requis'], 400);
        }

        if (!$email) {
            return $this->json(['success' => false, 'error' => 'Email requis'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['googleId' => $googleId]);
        
        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user && $user->getGoogleId()) {
                return $this->json(['success' => false, 'error' => 'Cet email est déjà utilisé avec un autre provider'], 400);
            }
        }

        if (!$user) {
            $parts = explode(' ', $name, 2);
            $prenom = $parts[0] ?? '';
            $nom = $parts[1] ?? '';

            $user = new User();
            $user->setNom($nom)
                 ->setPrenom($prenom)
                 ->setEmail($email)
                 ->setAvatar($avatar)
                 ->setGoogleId($googleId)
                 ->setPassword($hasher->hashPassword($user, bin2hex(random_bytes(16))))
                 ->setRoles(['ROLE_USER']);

            $em->persist($user);
        } else {
            $user->setGoogleId($googleId);
            if ($avatar && !$user->getAvatar()) {
                $user->setAvatar($avatar);
            }
        }

        $em->flush();

        return $this->json([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'avatar' => $user->getAvatar(),
                'role' => $user->getRole(),
            ]
        ]);
    }
}
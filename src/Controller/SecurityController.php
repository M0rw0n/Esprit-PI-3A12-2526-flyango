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
<<<<<<< HEAD
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SecurityController extends AbstractController
{
    private const TEST_RECAPTCHA_KEY = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';

=======

class SecurityController extends AbstractController
{
>>>>>>> testsisi
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
<<<<<<< HEAD
            'facebook_app_id' => '951675844123224',
            'recaptcha_site_key' => $this->getParameter('recaptcha_site_key'),
=======
            'facebook_app_id' => '947651210933536',
>>>>>>> testsisi
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
<<<<<<< HEAD
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em, HttpClientInterface $httpClient): Response
=======
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
>>>>>>> testsisi
    {
        if ($this->getUser()) {
            return $this->redirectAuthenticatedUser();
        }

        $errors = [];

        if ($request->isMethod('POST')) {
<<<<<<< HEAD
            $nom = trim($request->request->get('nom', ''));
            $prenom = trim($request->request->get('prenom', ''));
            $email = trim($request->request->get('email', ''));
            $pass = $request->request->get('password', '');
            $pass2 = $request->request->get('password_confirm', '');
            $tel = trim($request->request->get('telephone', ''));
            $recaptchaResponse = $request->request->get('g-recaptcha-response');

            if (!$nom) {
                $errors[] = 'Le nom est requis.';
            }
            if (!$prenom) {
                $errors[] = 'Le prénom est requis.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email invalide.';
            }
            if (strlen($pass) < 6) {
                $errors[] = 'Mot de passe minimum 6 caractères.';
            }
            if ($pass !== $pass2) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }
            if (!$this->verifyRecaptcha($httpClient, $recaptchaResponse)) {
                $errors[] = 'La vérification CAPTCHA a échoué.';
            }
=======
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
>>>>>>> testsisi

            if (!$errors) {
                $existing = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existing) {
                    $errors[] = 'Cet email est déjà utilisé.';
                } else {
                    $user = new User();
                    $user->setNom($nom)
<<<<<<< HEAD
                        ->setPrenom($prenom)
                        ->setEmail($email)
                        ->setTelephone($tel ?: null)
                        ->setPassword($hasher->hashPassword($user, $pass))
                        ->setRoles(['ROLE_USER']);
=======
                         ->setPrenom($prenom)
                         ->setEmail($email)
                         ->setTelephone($tel ?: null)
                         ->setPassword($hasher->hashPassword($user, $pass))
                         ->setRoles(['ROLE_USER']);
>>>>>>> testsisi

                    $em->persist($user);
                    $em->flush();

<<<<<<< HEAD
                    return $this->redirectToRoute('app_after_login');
=======
                    $this->addFlash('success', '✅ Compte créé ! Connectez-vous maintenant.');
                    return $this->redirectToRoute('app_login');
>>>>>>> testsisi
                }
            }
        }

<<<<<<< HEAD
        return $this->render('security/register.html.twig', [
            'errors' => $errors,
            'recaptcha_site_key' => $this->getParameter('recaptcha_site_key'),
        ]);
    }

    private function verifyRecaptcha(HttpClientInterface $httpClient, ?string $response): bool
    {
        if (empty($response)) {
            return false;
        }

        $secretKey = (string) $this->getParameter('recaptcha_secret_key');
        if ($secretKey === self::TEST_RECAPTCHA_KEY) {
            return true;
        }

        try {
            $responseData = $httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => $secretKey,
                    'response' => $response,
                ],
            ]);

            $content = $responseData->getContent(false);
            $data = json_decode($content, true);

            return (bool) ($data['success'] ?? false);
        } catch (\Exception $e) {
            return false;
        }
    }

    #[Route('/oauth2callback', name: 'oauth2_callback')]
    public function oauth2Callback(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, TokenStorageInterface $tokenStorage): Response
    {
        $code = $request->query->get('code');
        $state = $request->query->get('state');
        
        if (!$code || $state !== 'google_login') {
            return $this->redirectToRoute('app_login');
        }
        
        $clientId = '455416813258-6jue7nupumfub7jtsguc6pg89s9mdh9r.apps.googleusercontent.com';
        $clientSecret = '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';
        
        $host = $request->getHttpHost();
        if (strpos($host, 'loca.lt') !== false || strpos($host, 'ngrok') !== false) {
            $redirectUri = 'https://chatty-dots-bet.loca.lt/oauth2callback';
        } else {
            $redirectUri = $request->getScheme() . '://' . $host . '/oauth2callback';
        }
        
        $tokenResponse = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query([
                    'code' => $code,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUri,
                    'grant_type' => 'authorization_code'
                ])
            ]
        ]));
        
        $tokenData = json_decode($tokenResponse, true);
        $accessToken = $tokenData['access_token'] ?? null;
        
        if (!$accessToken) {
            $error = $tokenData['error'] ?? $tokenData['error_description'] ?? 'Erreur Google OAuth';
            if (strpos($error, 'redirect_uri') !== false || strpos($error, 'mismatch') !== false) {
                $this->addFlash('error', 'Google: Autoriser le redirect_uri dans Google Cloud Console → OAuth → Authorized redirect URIs: https://chatty-dots-bet.loca.lt/oauth2callback');
            } else {
                $this->addFlash('error', 'Erreur Google: ' . $error);
            }
            return $this->redirectToRoute('app_login');
        }
        
        $userInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo?alt=json', false, stream_context_create([
            'http' => ['header' => 'Authorization: Bearer ' . $accessToken]
        ])), true);
        
        if (!$userInfo) {
            $this->addFlash('error', 'Erreur获取utilisateur');
            return $this->redirectToRoute('app_login');
        }
        
        $user = $em->getRepository(User::class)->findOneBy(['googleId' => $userInfo['id']]);
        
        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $userInfo['email']]);
            
            if ($user) {
                $user->setGoogleId($userInfo['id']);
            } else {
                $user = new User();
                $user->setEmail($userInfo['email']);
                $user->setPassword($hasher->hashPassword($user, bin2hex(random_bytes(16))));
                $user->setGoogleId($userInfo['id']);
                $user->setRoles(['ROLE_USER']);
                $user->setPrenom($userInfo['given_name'] ?? '');
                $user->setNom($userInfo['family_name'] ?? '');
            }
        }
        
        if (!$user->isActif()) {
            $this->addFlash('error', 'Votre compte a été désactivé.');
            return $this->redirectToRoute('app_login');
        }
        
        $em->persist($user);
        $em->flush();
        
        $token = new UsernamePasswordToken($user, 'google', $user->getRoles());
        $tokenStorage->setToken($token);
        
        return $this->redirectToRoute('user_dashboard');
    }

    #[Route('/facebook/callback', name: 'facebook_callback')]
    public function facebookCallback(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, TokenStorageInterface $tokenStorage): Response
    {
        $code = $request->query->get('code');
        $state = $request->query->get('state');
        
        if (!$code || $state !== 'fb_login') {
            $this->addFlash('error', 'Code Facebook invalide');
            return $this->redirectToRoute('app_login');
        }
        
        $appId = '951675844123224';
        $appSecret = 'abec5cffa7622b4f1bd7adb507c25693';
        
        $host = $request->getHttpHost();
        if (strpos($host, 'loca.lt') !== false || strpos($host, 'ngrok') !== false) {
            $redirectUri = 'https://chatty-dots-bet.loca.lt/facebook/callback';
        } else {
            $redirectUri = $request->getScheme() . '://' . $host . '/facebook/callback';
        }
        
        $tokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token?' .
            'client_id=' . $appId .
            '&redirect_uri=' . urlencode($redirectUri) .
            '&client_secret=' . $appSecret .
            '&code=' . $code;
        
        $tokenResponse = json_decode(file_get_contents($tokenUrl), true);
        $accessToken = $tokenResponse['access_token'] ?? null;
        
        if (!$accessToken) {
            $errorMsg = $tokenResponse['error']['message'] ?? 'Erreur Facebook OAuth';
            if (strpos($errorMsg, 'redirect_uri') !== false) {
                $this->addFlash('error', 'Facebook: Ajouter redirect_uri dans developers.facebook.com → App Settings → Advanced → Security → Valid OAuth Redirect URIs: https://chatty-dots-bet.loca.lt/facebook/callback');
            } else {
                $this->addFlash('error', 'Erreur Facebook: ' . $errorMsg);
            }
            return $this->redirectToRoute('app_login');
        }
        
        $userInfo = json_decode(file_get_contents('https://graph.facebook.com/me?fields=id,name,email,picture&access_token=' . $accessToken), true);
        
        if (!$userInfo || !isset($userInfo['id'])) {
            $this->addFlash('error', 'Erreur获取utilisateur Facebook');
            return $this->redirectToRoute('app_login');
        }
        
        $user = $em->getRepository(User::class)->findOneBy(['facebookId' => $userInfo['id']]);
        
        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $userInfo['email'] ?? '']);
            
            if ($user) {
                $user->setFacebookId($userInfo['id']);
            } else {
                $user = new User();
                $email = $userInfo['email'] ?? $userInfo['id'] . '@facebook.local';
                $user->setEmail($email);
                $user->setPassword($hasher->hashPassword($user, bin2hex(random_bytes(16))));
                $user->setFacebookId($userInfo['id']);
                $user->setRoles(['ROLE_USER']);
                
                $parts = explode(' ', $userInfo['name'] ?? 'Utilisateur', 2);
                $user->setPrenom($parts[0]);
                $user->setNom($parts[1] ?? '');
            }
        }
        
        if (!$user->isActif()) {
            $this->addFlash('error', 'Votre compte a été désactivé.');
            return $this->redirectToRoute('app_login');
        }
        
        $em->persist($user);
        $em->flush();
        
        $token = new UsernamePasswordToken($user, 'facebook', $user->getRoles());
        $tokenStorage->setToken($token);
        
        return $this->redirectToRoute('user_dashboard');
    }

    #[Route('/api/auth/google-signin', name: 'api_auth_google', methods: ['POST'])]
    public function apiAuthGoogle(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

=======
        return $this->render('security/register.html.twig', ['errors' => $errors]);
    }

    #[Route('/api/auth/google', name: 'api_auth_google', methods: ['POST'])]
    public function apiAuthGoogle(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
>>>>>>> testsisi
        $email = $data['email'] ?? null;
        $googleId = $data['google_id'] ?? null;
        $name = $data['name'] ?? 'Utilisateur';
        $avatar = $data['avatar'] ?? null;
<<<<<<< HEAD

        if (!$email || !$googleId) {
            return $this->json(['success' => false, 'error' => 'Données invalides']);
        }

        $user = $em->getRepository(User::class)->findOneBy(['googleId' => $googleId]);

        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

=======
        
        if (!$email || !$googleId) {
            return $this->json(['success' => false, 'error' => 'Données invalides']);
        }
        
        $user = $em->getRepository(User::class)->findOneBy(['googleId' => $googleId]);
        
        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            
>>>>>>> testsisi
            if ($user) {
                $user->setGoogleId($googleId);
            } else {
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($hasher->hashPassword($user, bin2hex(random_bytes(16))));
                $user->setGoogleId($googleId);
                $user->setRoles(['ROLE_USER']);
<<<<<<< HEAD

=======
                
>>>>>>> testsisi
                $parts = explode(' ', $name, 2);
                $user->setPrenom($parts[0]);
                $user->setNom($parts[1] ?? '');
            }
        }
<<<<<<< HEAD

        if (!$user->isActif()) {
            return $this->json(['success' => false, 'error' => 'Votre compte a été désactivé. Veuillez contacter le support.']);
        }

        if ($avatar && !$user->getAvatar()) {
            $user->setAvatar($avatar);
        }

        $em->persist($user);
        $em->flush();

        $token = new UsernamePasswordToken($user, 'google', $user->getRoles());
        $tokenStorage->setToken($token);

=======
        
        if ($avatar && !$user->getAvatar()) {
            $user->setAvatar($avatar);
        }
        
        $em->persist($user);
        $em->flush();
        
>>>>>>> testsisi
        return $this->json(['success' => true, 'redirect' => $this->generateUrl('user_dashboard')]);
    }

    #[Route('/api/auth/facebook', name: 'api_auth_facebook', methods: ['POST'])]
<<<<<<< HEAD
    public function apiAuthFacebook(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

=======
    public function apiAuthFacebook(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
>>>>>>> testsisi
        $email = $data['email'] ?? null;
        $facebookId = $data['facebook_id'] ?? null;
        $name = $data['name'] ?? 'Utilisateur';
        $avatar = $data['avatar'] ?? null;
<<<<<<< HEAD

        if (!$email || !$facebookId) {
            return $this->json(['success' => false, 'error' => 'Données invalides']);
        }

        $user = $em->getRepository(User::class)->findOneBy(['facebookId' => $facebookId]);

        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

=======
        
        if (!$email || !$facebookId) {
            return $this->json(['success' => false, 'error' => 'Données invalides']);
        }
        
        $user = $em->getRepository(User::class)->findOneBy(['facebookId' => $facebookId]);
        
        if (!$user) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            
>>>>>>> testsisi
            if ($user) {
                $user->setFacebookId($facebookId);
            } else {
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($hasher->hashPassword($user, bin2hex(random_bytes(16))));
                $user->setFacebookId($facebookId);
                $user->setRoles(['ROLE_USER']);
<<<<<<< HEAD

=======
                
>>>>>>> testsisi
                $parts = explode(' ', $name, 2);
                $user->setPrenom($parts[0]);
                $user->setNom($parts[1] ?? '');
            }
        }
<<<<<<< HEAD

        if (!$user->isActif()) {
            return $this->json(['success' => false, 'error' => 'Votre compte a été désactivé. Veuillez contacter le support.']);
        }

        if ($avatar && !$user->getAvatar()) {
            $user->setAvatar($avatar);
        }

        $em->persist($user);
        $em->flush();

        $token = new UsernamePasswordToken($user, 'facebook', $user->getRoles());
        $tokenStorage->setToken($token);

        return $this->json(['success' => true, 'redirect' => $this->generateUrl('user_dashboard')]);
    }
}
=======
        
        if ($avatar && !$user->getAvatar()) {
            $user->setAvatar($avatar);
        }
        
        $em->persist($user);
        $em->flush();
        
        return $this->json(['success' => true, 'redirect' => $this->generateUrl('user_dashboard')]);
    }
}
>>>>>>> testsisi

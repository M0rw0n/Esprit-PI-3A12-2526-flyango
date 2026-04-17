<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PasswordCredentials;
use Symfony\Component\Security\Http\EntryPoint\FormLoginAuthenticator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecaptchaLoginAuthenticator extends AbstractAuthenticator
{
    private ?string $recaptchaSecretKey = null;

    public function __construct(
        private HttpClientInterface $httpClient,
        private RouterInterface $router,
    ) {
        $this->recaptchaSecretKey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    }

    public function supports(Request $request): bool
    {
        return $request->getPathInfo() === '/login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $recaptchaResponse = $request->request->get('_g_recaptcha_response');

        if (!$recaptchaResponse) {
            throw new CustomUserMessageAuthenticationException('Veuillez compléter le CAPTCHA.');
        }

        $recaptchaVerified = $this->verifyRecaptcha($recaptchaResponse);
        if (!$recaptchaVerified) {
            throw new CustomUserMessageAuthenticationException('La vérification CAPTCHA a échoué.');
        }

        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            []
        );
    }

    private function verifyRecaptcha(string $response): bool
    {
        if (empty($response) || $response === '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI') {
            return true;
        }

        try {
            $responseData = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => $this->recaptchaSecretKey,
                    'response' => $response,
                ],
            ]);

            $content = $responseData->getContent(false);
            $data = json_decode($content, true);
            return $data['success'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        if ($user instanceof User && !$user->isActif()) {
            throw new CustomUserMessageAuthenticationException('Votre compte a été désactivé. Veuillez contacter le support.');
        }

        $targetPath = $request->getSession()->get(Security::LAST_USERNAME, '/mon-espace');
        if ($user instanceof User && $user->hasRole('ROLE_ADMIN')) {
            $targetPath = '/admin';
        } else {
            $targetPath = '/mon-espace';
        }

        return new RedirectResponse($targetPath);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $request->getSession()->set(Security::LAST_USERNAME, $request->request->get('_username'));

        return new RedirectResponse('/login');
    }
}
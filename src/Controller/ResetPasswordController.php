<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));
            
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $user->setResetToken($token);
                    $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
                    $em->flush();
                    
                    $emailMessage = (new Email())
                        ->from(getenv('MAILER_FROM') ?: 'noreply@flyandgo.tn')
                        ->to($user->getEmail())
                        ->subject('Réinitialisation de votre mot de passe - Fly&Go')
                        ->html($this->renderView('reset_password/email.html.twig', [
                            'user' => $user,
                            'token' => $token,
                        ]));
                    
                    try {
                        $mailer->send($emailMessage);
                    } catch (\Exception $e) {
                        $this->addFlash('warning', 'Email non envoyé (serveur non configuré). Utilisez ce token: ' . $token);
                    }
                }
                
                $this->addFlash('info', 'Si un compte existe avec cet email, vous recevez un lien de réinitialisation.');
                return $this->redirectToRoute('app_forgot_password_check_email');
            }
            
            $this->addFlash('error', 'Veuillez entrer une adresse email valide.');
        }
        
        return $this->render('reset_password/request.html.twig');
    }

    #[Route('/check-email', name: 'app_forgot_password_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('reset_password/check_email.html.twig');
    }

    #[Route('/reset/{token}', name: 'app_forgot_password_reset')]
    public function reset(Request $request, string $token, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['resetToken' => $token]);
        
        if (!$user || !$user->getResetTokenExpiresAt() || $user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'Lien de réinitialisation invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password_request');
        }
        
        if ($request->isMethod('POST')) {
            $password = $request->request->get('password', '');
            $confirm = $request->request->get('confirm_password', '');
            
            if (strlen($password) < 6) {
                $this->addFlash('error', 'Le mot de passe doit contenir au moins 6 caractères.');
                return $this->render('reset_password/reset.html.twig', ['token' => $token]);
            }
            
            if ($password !== $confirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('reset_password/reset.html.twig', ['token' => $token]);
            }
            
            $user->setPassword($hasher->hashPassword($user, $password));
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);
            $em->flush();
            
            $this->addFlash('success', 'Mot de passe réinitialisé! Connectez-vous avec votre nouveau mot de passe.');
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('reset_password/reset.html.twig', ['token' => $token]);
    }
}
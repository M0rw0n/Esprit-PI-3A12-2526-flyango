<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($_ENV['MAILER_FROM_ADDRESS'] ?? 'noreply@flyandgo.com', 'Fly & Go'))
            ->to(new Address($user->getEmail(), $user->getNomComplet()))
            ->subject('Bienvenue chez Fly & Go!')
            ->htmlTemplate('emails/welcome.html.twig')
            ->context([
                'user' => $user,
            ]);

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Log error but don't fail
            error_log('Email sending failed: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(User $user, string $resetLink): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($_ENV['MAILER_FROM_ADDRESS'] ?? 'noreply@flyandgo.com', 'Fly & Go'))
            ->to(new Address($user->getEmail(), $user->getNomComplet()))
            ->subject('Réinitialisez votre mot de passe')
            ->htmlTemplate('emails/reset_password.html.twig')
            ->context([
                'user' => $user,
                'resetLink' => $resetLink,
            ]);

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Log error but don't fail
            error_log('Email sending failed: ' . $e->getMessage());
        }
    }
}

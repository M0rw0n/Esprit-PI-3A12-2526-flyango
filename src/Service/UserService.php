<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private EmailService $emailService,
    ) {}

    /**
     * Create a new user with password hashing and welcome email
     */
    public function createUser(User $user, string $plainPassword, bool $sendWelcomeEmail = true): void
    {
        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setMotDePasse($hashedPassword);

        // Persist to database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Send welcome email
        if ($sendWelcomeEmail) {
            $this->emailService->sendWelcomeEmail($user);
        }
    }

    /**
     * Update user password
     */
    public function updateUserPassword(User $user, string $plainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setMotDePasse($hashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Update user information
     */
    public function updateUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(User $user): void
    {
        $user->setActif(!$user->isActif());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(): array
    {
        return $this->userRepository->getStats();
    }
}

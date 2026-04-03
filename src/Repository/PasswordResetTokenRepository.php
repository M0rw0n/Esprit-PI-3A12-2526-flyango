<?php

namespace App\Repository;

use App\Entity\PasswordResetToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordResetToken>
 */
class PasswordResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    public function findValidToken(string $token): ?PasswordResetToken
    {
        $tokenEntity = $this->findOneBy(['token' => $token]);

        if (!$tokenEntity || $tokenEntity->isUsed() || $tokenEntity->isExpired()) {
            return null;
        }

        return $tokenEntity;
    }

    public function cleanExpiredTokens(): int
    {
        return $this->createQueryBuilder('t')
            ->delete()
            ->where('t.expirationDate < :now')
            ->orWhere('t.used = :used')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('used', true)
            ->getQuery()
            ->execute();
    }
}

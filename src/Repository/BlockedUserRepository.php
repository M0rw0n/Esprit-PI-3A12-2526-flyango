<?php

namespace App\Repository;

use App\Entity\BlockedUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlockedUser>
 */
class BlockedUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockedUser::class);
    }

    public function isBlocked(User $user, User $target): bool
    {
        return $this->findOneBy([
            'user' => $user,
            'blockedUser' => $target
        ]) !== null;
    }

    public function findBlockedUsers(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }
}

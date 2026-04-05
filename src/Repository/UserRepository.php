<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find users with dynamic filters
     */
    public function findByFilters(
        ?string $search = null,
        ?string $role = null,
        ?string $status = null,
        ?\DateTime $dateFrom = null,
        ?\DateTime $dateTo = null,
        ?string $sort = null
    ): array {
        $qb = $this->createQueryBuilder('u');

        if ($search) {
            $qb->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
                ->setParameter('search', "%{$search}%");
        }

        if ($role && $role !== 'all') {
            $qb->andWhere('u.role = :role')
                ->setParameter('role', strtoupper($role));
        }

        if ($status === 'active') {
            $qb->andWhere('u.actif = :actif')
                ->setParameter('actif', true);
        } elseif ($status === 'inactive') {
            $qb->andWhere('u.actif = :actif')
                ->setParameter('actif', false);
        }

        if ($dateFrom) {
            $qb->andWhere('u.createdAt >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom);
        }

        if ($dateTo) {
            $qb->andWhere('u.createdAt <= :dateTo')
                ->setParameter('dateTo', $dateTo);
        }

        // Sorting
        match ($sort) {
            'name_asc' => $qb->orderBy('u.nom', 'ASC'),
            'name_desc' => $qb->orderBy('u.nom', 'DESC'),
            'date_asc' => $qb->orderBy('u.createdAt', 'ASC'),
            'date_desc' => $qb->orderBy('u.createdAt', 'DESC'),
            'role_asc' => $qb->orderBy('u.role', 'ASC'),
            default => $qb->orderBy('u.createdAt', 'DESC'),
        };

        return $qb->getQuery()->getResult();
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(): array
    {
        $allUsers = $this->count([]);
        $activeUsers = $this->count(['actif' => true]);
        $voyageurs = $this->count(['role' => 'VOYAGEUR']);
        $admins = $this->count(['role' => 'ADMIN']);

        // New this month
        $firstDayOfMonth = new \DateTime('first day of this month');
        $newThisMonth = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt >= :dateFrom')
            ->setParameter('dateFrom', $firstDayOfMonth)
            ->getQuery()
            ->getSingleScalarResult();

        $activePercent = $allUsers > 0 ? round(($activeUsers / $allUsers) * 100, 1) : 0;

        return [
            'totalUsers' => $allUsers,
            'activeUsers' => $activeUsers,
            'voyageurs' => $voyageurs,
            'admins' => $admins,
            'newThisMonth' => (int)$newThisMonth,
            'activePercent' => $activePercent,
        ];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $hashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setMotDePasse($hashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @see UserProviderInterface
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->findOneBy(['email' => $identifier]);
        
        if (!$user) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $identifier));
        }

        return $user;
    }

    /**
     * @see UserProviderInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $refreshedUser = $this->find($user->getId());
        
        if (!$refreshedUser) {
            throw new UserNotFoundException(sprintf('User with id "%s" not found.', $user->getId()));
        }

        return $refreshedUser;
    }

    /**
     * @see UserProviderInterface
     */
    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }
}

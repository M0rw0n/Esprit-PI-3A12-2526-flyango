<?php
namespace App\Repository;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, User::class); }
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {
        if (!$user instanceof User) throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    
    public function searchUsers(string $query, ?int $excludeUserId = null, int $limit = 20): array
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.actif = :active')
            ->andWhere('(LOWER(u.nom) LIKE :q OR LOWER(u.prenom) LIKE :q OR LOWER(CONCAT(u.prenom, \' \', u.nom)) LIKE :q OR LOWER(u.email) LIKE :q)')
            ->setParameter('active', true)
            ->setParameter('q', '%' . strtolower($query) . '%')
            ->setMaxResults($limit)
            ->orderBy('u.nom', 'ASC');
        
        if ($excludeUserId !== null) {
            $qb->andWhere('u.id != :excludeId')
               ->setParameter('excludeId', $excludeUserId);
        }
        
        return $qb->getQuery()->getResult();
    }
}

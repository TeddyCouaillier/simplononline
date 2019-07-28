<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Get all users who get the specifi role
     * @param string $val
     * @return User[]
     */
    public function findAllByUserRole($val)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.userRoles','ru')
            ->andWhere('ru.title = :val')
            ->setParameter('val', $val)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all users in the current promo
     * @return User[]
     */
    public function findCurrentPromoType()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.promotion', 'p')
            ->andWhere('p.current = true')
        ;
    }

    /**
     * Find all users in the current promo
     * @return User[]
     */
    public function findAllByCurrentPromo()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.promotion', 'p')
            ->andWhere('p.current = true')
            ->getQuery()
            ->getResult()
        ;
    }
}

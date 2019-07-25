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

    // /**
    //  * @return User[] Returns an array of User objects
    //  */

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


//SELECT u.* FROM user as u LEFT JOIN role_user as ru ON u.id = ru.user_id LEFT JOIN role as r ON r.id = ru.role_id WHERE r.title = "ROLE_MEDIATEUR"
//SELECT u.* FROM user as u,role as r,role_user as ru WHERE u.id = ru.user_id AND r.id = ru.role_id AND r.title = "ROLE_MEDIATEUR"

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

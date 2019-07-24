<?php

namespace App\Repository;

use App\Entity\UserSkills;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserSkills|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSkills|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSkills[]    findAll()
 * @method UserSkills[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSkillsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserSkills::class);
    }

    // /**
    //  * @return UserSkills[] Returns an array of UserSkills objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserSkills
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

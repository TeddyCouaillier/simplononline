<?php

namespace App\Repository;

use App\Entity\Codeblock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Codeblock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Codeblock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Codeblock[]    findAll()
 * @method Codeblock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeblockRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Codeblock::class);
    }

    // /**
    //  * @return Codeblock[] Returns an array of Codeblock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Codeblock
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

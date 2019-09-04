<?php

namespace App\Repository;

use App\Entity\Promotion;
use App\Entity\Correction;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Correction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Correction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Correction[]    findAll()
 * @method Correction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorrectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Correction::class);
    }

    /**
     * Find all projects corrections with an user in the specific promo
     * @param Promotion $promo
     * @return Correction[]
     */
    public function findAllCorrectionByPromo(Promotion $promo)
    {
        return $this->createQueryBuilder('c')
            ->join('c.project','p')
            ->join('p.users', 'u')
            ->andWhere('u.promotion = :promo')
            ->setParameter('promo', $promo)
            ->getQuery()
            ->getResult()
        ;
    }
}

<?php

namespace App\Repository;

use App\Entity\Help;
use App\Entity\Promotion;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Help|null find($id, $lockMode = null, $lockVersion = null)
 * @method Help|null findOneBy(array $criteria, array $orderBy = null)
 * @method Help[]    findAll()
 * @method Help[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Help::class);
    }

    /**
     * Find all helps post by an user in the specific promo
     * @param Promotion $promo
     * @return Help[]
     */
    public function findAllHelpByPromo(Promotion $promo)
    {
        return $this->createQueryBuilder('p')
            ->join('p.publisher','u')
            ->andWhere('u.promotion = :promo')
            ->setParameter('promo', $promo)
            ->getQuery()
            ->getResult()
        ;
    }
}

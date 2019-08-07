<?php

namespace App\Repository;

use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    public function findAllOtherCurrent($promo)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT p FROM App\Entity\Promotion p
                WHERE p.current = true
                AND p != :promo
            ')
            ->setParameter('promo', $promo);
        ;

        return $query->getResult();
    }
}

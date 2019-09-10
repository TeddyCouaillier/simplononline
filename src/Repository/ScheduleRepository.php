<?php

namespace App\Repository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    public function findAllNow()
    {
        $time = new \DateTime();
        $query = $this->getEntityManager()->createQuery('
            SELECT s FROM App\Entity\Schedule s
            WHERE s.beginAt between CURRENT_DATE() and :end  ORDER BY s.beginAt
        ')
        ->setParameter('end', $time->setTime(23,59));

        return $query->getResult();
    }

    public function findAllFutures()
    {
        $time = new \DateTime('+1 days');
        $query = $this->getEntityManager()->createQuery('
            SELECT s FROM App\Entity\Schedule s
            WHERE s.beginAt >= :tomorrow  ORDER BY s.beginAt
        ')
        ->setParameter('tomorrow', $time->setTime(00,00));

        return $query->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Deadline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Deadline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deadline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deadline[]    findAll()
 * @method Deadline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeadlineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Deadline::class);
    }
}
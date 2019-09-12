<?php

namespace App\Repository;

use App\Entity\UserDeadline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserDeadline|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDeadline|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDeadline[]    findAll()
 * @method UserDeadline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDeadlineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserDeadline::class);
    }
}
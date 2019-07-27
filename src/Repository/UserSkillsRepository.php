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
}

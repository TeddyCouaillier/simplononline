<?php

namespace App\Repository;

use App\Entity\Files;
use App\Entity\User;
use App\Entity\UserFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFiles[]    findAll()
 * @method UserFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFilesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserFiles::class);
    }
}

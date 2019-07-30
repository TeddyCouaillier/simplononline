<?php

namespace App\Repository;

use App\Entity\TrainingCourse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TrainingCourse|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingCourse|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingCourse[]    findAll()
 * @method TrainingCourse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingCourseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TrainingCourse::class);
    }
}

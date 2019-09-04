<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Promotion;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Get all users who get the specifi role
     * @param string $val
     * @return User[]
     */
    public function findAllByUserRole($val)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.userRoles','ru')
            ->andWhere('ru.title = :val')
            ->setParameter('val', $val)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all users in the current promo
     * @return User[]
     */
    public function findCurrentPromoType()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.promotion', 'p')
            ->andWhere('p.current = true')
        ;
    }

    /**
     * Find all users in the current promo
     * @return User[]
     */
    public function findAllByCurrentPromo()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.promotion', 'p')
            ->andWhere('p.current = true')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all moderators for the current promotion
     * @return User[]
     */
    public function findAllModeratorByCurrentPromo()
    {
        return $this->createQueryBuilder('u')
            ->join('u.promotionmod', 'pm')
            ->andWhere('pm.current = true')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all users in a specific project
     * @param Project $project
     * @return User[]
     */
    public function findAllByProject($project)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.projects', 'p')
            ->andWhere('p = :project')
            ->setParameter('project', $project)
        ;
    }

    /**
     * Find all users with a role
     * @return User[]
     */
    public function findAllUserByRole()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.userRoles', 'ur')
            ->andWhere('ur is not null')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all users with a role
     */
    public function getAllUserByRole()
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.userRoles', 'ur')
            ->andWhere('ur is not null')
        ;
    }

    /**
     * Get all users with a role in the current promo
     */
    public function getAllUserRoleByCurrentPromo()
    {
        return $this->createQueryBuilder('u')
            ->join('u.userRoles', 'ur')
            ->join('u.promotionmod', 'p')
            ->andWhere('ur is not null')
            ->andWhere('p.current = true')
        ;
    }

    /**
     * Find all task's users
     * @param Task $task
     * @return User[]
     */
    public function findAllByTask(Task $task)
    {
        return $this->createQueryBuilder('u')
            ->join('u.tasks', 't')
            ->andWhere('t = :task')
            ->setParameter('task',$task)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get the weather average in the specific promo
     * @param Promotion $promo
     * @return double
     */
    public function findWeatherAvgByPromo(Promotion $promo)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT AVG(u.weather)
                FROM App\Entity\User u
                WHERE u.promotion = :promo
            ')
            ->setParameter('promo',$promo)
        ;

        return $query->getSingleScalarResult();
    }
}

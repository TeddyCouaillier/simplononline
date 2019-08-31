<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Language;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Get all tasks by the type given
     * @param Project $project
     * @param integer $type
     * @param integer $offset
     * @return array
     */
    public function findAllTasksByType(Project $project, int $type, int $offset = 0)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT DISTINCT t
                FROM App\Entity\Project p
                JOIN App\Entity\Task t
                WHERE t.project = :project
                AND t.type = :type
                ORDER BY t.createdAt DESC
            ')
            ->setFirstResult($offset)
            ->setMaxResults(5)
            ->setParameters([
                'project' =>$project,
                'type'    =>$type
            ])
        ;

        return $query->getResult();
    }

    /**
     * Get all projects by a specific language
     * @param Language $language
     * @return Project[]
     */
    public function findAllByLanguage(Language $language)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.languages','l')
            ->andWhere('l = :language')
            ->setParameter('language', $language)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find a specific user's projects
     * @param User $user
     * @return Project[]
     */
    public function findAllByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->join('p.users','u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find a specific user's projects for pagination
     * @param User    $user
     * @param integer $limit
     * @param integer $offset
     * @return Project[]
     */
    public function findAllByUserLimit(User $user, int $limit, int $offset)
    {
        return $this->createQueryBuilder('p')
            ->join('p.users','u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}

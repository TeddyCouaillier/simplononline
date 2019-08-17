<?php

namespace App\Repository;

use App\Entity\Language;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
}

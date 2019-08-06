<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Get all subtasks total in a project
     * @param Project $project
     * @return integer
     */
    public function getTotalSubtask(Project $project){
        $query = $this->getEntityManager()->createQuery('
            SELECT count(s)
            FROM App\Entity\Subtask s, App\Entity\Task t
            WHERE t.project = :project
            AND t.id = s.task
        ')
        ->setParameter('project',$project->getId());
        return intval($query->getSingleScalarResult());
    }

    /**
     * Get all subtasks done total in a project
     * @param Project $project
     * @return integer
     */
    public function getTotalSubtaskDone(Project $project){
        $query = $this->getEntityManager()->createQuery('
            SELECT count(s)
            FROM App\Entity\Subtask s, App\Entity\Task t
            WHERE t.project = :project
            AND t.id = s.task
            AND s.done = true
        ')
        ->setParameter('project',$project->getId());
        return intval($query->getSingleScalarResult());
    }

    /**
     * Get all subtasks total by type in a project
     * @param Project $project
     * @param integer $type
     * @return integer
     */
    public function getTotalSubtaskByType(Project $project, int $type)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT COUNT(s) FROM App\Entity\Subtask s, App\Entity\Task t
            WHERE s.task = t
            AND t.project = :project
            AND t.type = :type
        ')
        ->setParameters([
            'project' => $project,
            'type' => $type
        ]);
        return intval($query->getSingleScalarResult());
    }

    /**
     * Get all subtasks done total by type in a project
     * @param Project $project
     * @param integer $type
     * @return integer
     */
    public function getTotalSubtaskDoneByType(Project $project, int $type)
    {
        $query = $this->getEntityManager()->createQuery('
        SELECT COUNT(s) FROM App\Entity\Subtask s, App\Entity\Task t
        WHERE s.task = t
        AND t.project = :project
        AND t.type = :type
        AND s.done = true
        ')
        ->setParameters(['project' => $project,'type' => $type]);

        return intval($query->getSingleScalarResult());
    }
}

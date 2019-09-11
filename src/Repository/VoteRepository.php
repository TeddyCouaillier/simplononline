<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Vote;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function findByLike(Game $game)
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->andWhere('v.likeType = true')
            ->andWhere('v.game = :game')
            ->setParameter('game', $game)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findByDislike(Game $game)
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->andWhere('v.likeType = false')
            ->andWhere('v.game = :game')
            ->setParameter('game', $game)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
<?php

namespace App\Repository;

use App\Entity\Week;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Week>
 */
class WeekRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Week::class);
    }

    /**
     * The most recently created week, or null when none exists.
     */
    public function findLatest(): ?Week
    {
        return $this->findOneBy([], ['id' => 'DESC']);
    }
}

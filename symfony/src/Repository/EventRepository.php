<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findByDate(string $date): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.date LIKE :date')
            ->setParameter('date', '%' . $date . '%')
            ->getQuery()
            ->getResult();
    }
}

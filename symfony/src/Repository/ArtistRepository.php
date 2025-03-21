<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    public function findByName(string $name): ?array
    {

        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :name')
            ->setParameter('name','%'. $name. '%')
            ->getQuery()
            ->getResult();
    }

}

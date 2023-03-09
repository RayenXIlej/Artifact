<?php

namespace App\Repository;

use App\Entity\PetSitter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PetSitter|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetSitter|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetSitter[]    findAll()
 * @method PetSitter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetSitterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetSitter::class);
    }

   
}

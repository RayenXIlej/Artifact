<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Offre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offre[]    findAll()
 * @method Offre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }
    public function SortByDescription(){
        return $this->createQueryBuilder('e')
            ->orderBy('e.Description','ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    
    public function SortByPrix()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.Prix','ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    
    
    public function SortByDateDebut()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.DateDebut','ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function SortByDateFin()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.DateFin','ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    
    
    
    
    
    
    
    
    public function findByDescription( $Description)
    {
        return $this-> createQueryBuilder('e')
            ->andWhere('e.Description LIKE :Description')
            ->setParameter('Description','%' .$Description. '%')
            ->getQuery()
            ->execute();
    }
    public function findByPrix( $Prix)
    {
        return $this-> createQueryBuilder('e')
            ->andWhere('e.Prix LIKE :Prix')
            ->setParameter('Prix','%' .$Prix. '%')
            ->getQuery()
            ->execute();
    }
    public function findByDateDebut( $DateDebut)
    {
        return $this-> createQueryBuilder('e')
            ->andWhere('e.DateDebut LIKE :DateDebut')
            ->setParameter('DateDebut','%' .$DateDebut. '%')
            ->getQuery()
            ->execute();
    }
   
}




   
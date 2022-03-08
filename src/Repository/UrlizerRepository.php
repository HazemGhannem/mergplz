<?php

namespace App\Repository;

use App\Entity\Urlizer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Urlizer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Urlizer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Urlizer[]    findAll()
 * @method Urlizer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlizerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Urlizer::class);
    }

    // /**
    //  * @return Urlizer[] Returns an array of Urlizer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Urlizer
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

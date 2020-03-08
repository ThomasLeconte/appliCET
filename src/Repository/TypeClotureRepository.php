<?php

namespace App\Repository;

use App\Entity\TypeCloture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TypeCloture|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeCloture|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeCloture[]    findAll()
 * @method TypeCloture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeClotureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeCloture::class);
    }

    // /**
    //  * @return TypeCloture[] Returns an array of TypeCloture objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeCloture
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

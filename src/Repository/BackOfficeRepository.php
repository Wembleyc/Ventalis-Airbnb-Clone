<?php

namespace App\Repository;

use App\Entity\BackOffice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BackOffice>
 *
 * @method BackOffice|null find($id, $lockMode = null, $lockVersion = null)
 * @method BackOffice|null findOneBy(array $criteria, array $orderBy = null)
 * @method BackOffice[]    findAll()
 * @method BackOffice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackOfficeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackOffice::class);
    }

//    /**
//     * @return BackOffice[] Returns an array of BackOffice objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BackOffice
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

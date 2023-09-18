<?php

namespace App\Repository;

use App\Entity\Logements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Logements>
 *
 * @method Logements|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logements|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logements[]    findAll()
 * @method Logements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logements::class);
    }

//    /**
//     * @return Logements[] Returns an array of Logements objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Logements
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Collection>
 *
 * @method Collection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection[]    findAll()
 * @method Collection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collection::class);
    }

    public function findWhereTargetNotReached()
    {
        return $this->createQueryBuilder('coll')
            ->select('coll')
            ->leftJoin('coll.contributors', 'contr')
            ->groupBy('coll')
            ->having('COALESCE(SUM(contr.amount), 0) < coll.target_amount')
            ->getQuery()
            ->getResult();
    }

    public function findWhereRemainingAmountLessThanOrEqual(float $amount)
    {
        return $this->createQueryBuilder('coll')
            ->select('coll', 'coll.target_amount - COALESCE(SUM(contr.amount), 0) as remainingAmount')
            ->leftJoin('coll.contributors', 'contr')
            ->groupBy('coll')
            ->having('remainingAmount <= :amount')
            ->setParameter('amount', $amount)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Collection[] Returns an array of Collection objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Collection
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\ProduitVendu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitVendu>
 *
 * @method ProduitVendu|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitVendu|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitVendu[]    findAll()
 * @method ProduitVendu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitVenduRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitVendu::class);
    }

//    /**
//     * @return ProduitVendu[] Returns an array of ProduitVendu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProduitVendu
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

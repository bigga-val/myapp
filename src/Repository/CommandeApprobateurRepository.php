<?php

namespace App\Repository;

use App\Entity\CommandeApprobateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommandeApprobateur>
 *
 * @method CommandeApprobateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeApprobateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeApprobateur[]    findAll()
 * @method CommandeApprobateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeApprobateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeApprobateur::class);
    }

//    /**
//     * @return CommandeApprobateur[] Returns an array of CommandeApprobateur objects
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

//    public function findOneBySomeField($value): ?CommandeApprobateur
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

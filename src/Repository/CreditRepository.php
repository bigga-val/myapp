<?php

namespace App\Repository;

use App\Entity\Credit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Credit>
 *
 * @method Credit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Credit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Credit[]    findAll()
 * @method Credit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Credit::class);
    }

//    /**
//     * @return Credit[] Returns an array of Credit objects
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

//    public function findOneBySomeField($value): ?Credit
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function sortiepardate($dateDebut, $dateFin): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT SUM(c.montant) montant         
                    FROM App\Entity\Credit c
                     where c.dateCredit between :dateDebut and :dateFin
                    --GROUP BY v.venteDate 
            '
        );
        //$query->setParameter('mydate', $mydate);
        $query->setParameter('dateDebut', $dateDebut);
        $query->setParameter('dateFin', $dateFin);
        return $query->getResult();
    }
}

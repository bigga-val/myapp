<?php

namespace App\Repository;

use App\Entity\PaieEmploye;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaieEmploye>
 *
 * @method PaieEmploye|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaieEmploye|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaieEmploye[]    findAll()
 * @method PaieEmploye[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaieEmployeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaieEmploye::class);
    }

//    /**
//     * @return PaieEmploye[] Returns an array of PaieEmploye objects
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

//    public function findOneBySomeField($value): ?PaieEmploye
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function totalParPeriode(?\DateTimeInterface $debut, ?\DateTimeInterface $fin): float
    {
        $qb = $this->createQueryBuilder('pe')->select('SUM(pe.total)');

        if ($debut) $qb->andWhere('pe.createdAt >= :debut')->setParameter('debut', $debut->format('Y-m-d') . ' 00:00:00');
        if ($fin)   $qb->andWhere('pe.createdAt <= :fin')->setParameter('fin',   $fin->format('Y-m-d')   . ' 23:59:59');

        return (float) ($qb->getQuery()->getSingleScalarResult() ?? 0);
    }

    /**
     * Masse salariale mois par mois pour une année civile donnée.
     * Retourne [1 => total_jan, ..., 12 => total_dec].
     */
    public function masseSalarialeParMois(int $annee): array
    {
        $results = $this->createQueryBuilder('pe')
            ->select('p.MonthPay AS mois, SUM(pe.total) AS total')
            ->join('pe.Paie', 'p')
            ->where('p.YearPay = :annee')
            ->setParameter('annee', $annee)
            ->groupBy('p.MonthPay')
            ->getQuery()
            ->getResult();

        $data = array_fill(1, 12, 0.0);
        foreach ($results as $row) {
            $data[(int) $row['mois']] = (float) $row['total'];
        }
        return $data;
    }

    public function masseSalarialeMois(int $annee, int $mois): float
    {
        $result = $this->createQueryBuilder('pe')
            ->select('SUM(pe.total)')
            ->join('pe.Paie', 'p')
            ->where('p.MonthPay = :mois AND p.YearPay = :annee')
            ->setParameter('mois', $mois)
            ->setParameter('annee', $annee)
            ->getQuery()->getSingleScalarResult();
        return (float) ($result ?? 0);
    }

    /** @deprecated Utiliser masseSalarialeMois() */
    public function masseSalarialeMoisCourant(): float
    {
        return $this->masseSalarialeMois((int) date('Y'), (int) date('n'));
    }
}

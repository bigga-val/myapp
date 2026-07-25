<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Bulletin;
use App\Entity\Classe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bulletin>
 *
 * @method Bulletin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bulletin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bulletin[]    findAll()
 * @method Bulletin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BulletinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bulletin::class);
    }

    public function findByClasseAndPeriode(Classe $classe, int $periode, AnneeAcademique $annee): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.classe = :classe')
            ->andWhere('b.periode = :periode')
            ->andWhere('b.anneeAcademique = :annee')
            ->setParameter('classe', $classe)
            ->setParameter('periode', $periode)
            ->setParameter('annee', $annee)
            ->join('b.eleve', 'e')
            ->orderBy('b.rangClasse', 'ASC')
            ->getQuery()->getResult();
    }
}

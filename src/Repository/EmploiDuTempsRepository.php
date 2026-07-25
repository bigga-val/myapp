<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Employe;
use App\Entity\EmploiDuTemps;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmploiDuTemps>
 *
 * @method EmploiDuTemps|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmploiDuTemps|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmploiDuTemps[]    findAll()
 * @method EmploiDuTemps[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmploiDuTempsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmploiDuTemps::class);
    }

    public function findByClasseGroupedByDay(Classe $classe, AnneeAcademique $annee): array
    {
        $slots = $this->createQueryBuilder('e')
            ->where('e.classe = :classe')
            ->andWhere('e.anneeAcademique = :annee')
            ->andWhere('e.actif = true')
            ->setParameter('classe', $classe)
            ->setParameter('annee', $annee)
            ->orderBy('e.jourSemaine', 'ASC')
            ->addOrderBy('e.heureDebut', 'ASC')
            ->getQuery()->getResult();

        $grouped = [];
        foreach ($slots as $slot) {
            $grouped[$slot->getJourSemaine()][] = $slot;
        }
        return $grouped;
    }

    public function hasConflitEnseignant(Employe $enseignant, int $jour, \DateTimeInterface $debut, \DateTimeInterface $fin, AnneeAcademique $annee, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.enseignant = :ens')
            ->andWhere('e.jourSemaine = :jour')
            ->andWhere('e.anneeAcademique = :annee')
            ->andWhere('e.actif = true')
            ->andWhere('e.heureDebut < :fin AND e.heureFin > :debut')
            ->setParameter('ens', $enseignant)
            ->setParameter('jour', $jour)
            ->setParameter('annee', $annee)
            ->setParameter('debut', $debut->format('H:i:s'))
            ->setParameter('fin', $fin->format('H:i:s'));
        if ($excludeId) {
            $qb->andWhere('e.id != :excludeId')->setParameter('excludeId', $excludeId);
        }
        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}

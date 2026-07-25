<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Presence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presence>
 *
 * @method Presence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presence[]    findAll()
 * @method Presence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    public function findByClasseAndDate(Classe $classe, \DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.eleve', 'e')
            ->join('App\Entity\Inscription', 'i', 'WITH', 'i.eleve = e AND i.classe = :classe')
            ->where('p.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('classe', $classe)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()->getResult();
    }

    public function findByEleveAndPeriode(Eleve $eleve, \DateTimeInterface $debut, \DateTimeInterface $fin): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.eleve = :eleve')
            ->andWhere('p.date BETWEEN :debut AND :fin')
            ->setParameter('eleve', $eleve)
            ->setParameter('debut', $debut->format('Y-m-d'))
            ->setParameter('fin', $fin->format('Y-m-d'))
            ->orderBy('p.date', 'DESC')
            ->getQuery()->getResult();
    }

    public function countAbsencesByEleve(Eleve $eleve, AnneeAcademique $annee): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.eleve = :eleve')
            ->andWhere("p.statut IN ('absent', 'retard')")
            ->andWhere('p.date BETWEEN :debut AND :fin')
            ->setParameter('eleve', $eleve)
            ->setParameter('debut', $annee->getDateDebut()->format('Y-m-d'))
            ->setParameter('fin', $annee->getDateFin()->format('Y-m-d'))
            ->getQuery()->getSingleScalarResult();
    }
}

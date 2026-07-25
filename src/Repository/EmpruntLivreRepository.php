<?php

namespace App\Repository;

use App\Entity\Eleve;
use App\Entity\EmpruntLivre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmpruntLivre>
 */
class EmpruntLivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmpruntLivre::class);
    }

    /**
     * Emprunts en cours ou en retard, triés par date de retour prévue ASC.
     */
    public function findEnCours(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.statut IN (:statuts)')
            ->setParameter('statuts', ['en_cours', 'en_retard'])
            ->orderBy('e.dateRetourPrevue', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tous les emprunts d'un élève, triés par date d'emprunt DESC.
     */
    public function findByEleve(Eleve $eleve): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.emprunteur = :eleve')
            ->setParameter('eleve', $eleve)
            ->orderBy('e.dateEmprunt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Nombre d'emprunts actifs (statut en_cours ou en_retard).
     */
    public function countEnCours(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.statut IN (:statuts)')
            ->setParameter('statuts', ['en_cours', 'en_retard'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Emprunts où dateRetourPrevue < TODAY et statut != 'rendu'.
     */
    public function findEnRetard(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.dateRetourPrevue < :today')
            ->andWhere('e.statut != :rendu')
            ->setParameter('today', new \DateTime('today'))
            ->setParameter('rendu', 'rendu')
            ->orderBy('e.dateRetourPrevue', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

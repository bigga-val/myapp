<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Eleve;
use App\Entity\PaiementFrais;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaiementFrais>
 *
 * @method PaiementFrais|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaiementFrais|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaiementFrais[]    findAll()
 * @method PaiementFrais[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementFraisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaiementFrais::class);
    }

    /**
     * Tous les paiements d'un élève, triés par date décroissante.
     */
    public function findByEleve(Eleve $eleve): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.eleve = :eleve')
            ->setParameter('eleve', $eleve)
            ->orderBy('p.datePaiement', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Paiements d'un mois donné (par datePaiement).
     */
    public function findByMois(int $annee, int $mois): array
    {
        $debut = new \DateTime(sprintf('%d-%02d-01', $annee, $mois));
        $fin   = (clone $debut)->modify('last day of this month');

        return $this->createQueryBuilder('p')
            ->where('p.datePaiement BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('p.datePaiement', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Somme des montants payés pour un mois donné.
     */
    public function sumPercusMois(int $annee, int $mois): float
    {
        $debut = new \DateTime(sprintf('%d-%02d-01', $annee, $mois));
        $fin   = (clone $debut)->modify('last day of this month');

        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.montantPaye)')
            ->where('p.datePaiement BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Montants perçus mois par mois pour une année civile donnée.
     * Retourne [1 => total_jan, 2 => total_fev, ..., 12 => total_dec].
     */
    public function percusParMois(int $annee): array
    {
        $data = array_fill(1, 12, 0.0);
        for ($m = 1; $m <= 12; $m++) {
            $data[$m] = $this->sumPercusMois($annee, $m);
        }
        return $data;
    }

    /**
     * Total des frais perçus pour toute une année civile.
     */
    public function totalPercusAnnee(int $annee): float
    {
        $debut = new \DateTime("$annee-01-01");
        $fin   = new \DateTime("$annee-12-31");

        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.montantPaye)')
            ->where('p.datePaiement BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Recouvrement par frais : chaque FraisScolaire actif de l'année avec son nombre de payants.
     * Frais liés à une classe + frais globaux (classe IS NULL) retournés séparément puis fusionnés.
     * Retourne [{fraisId, fraisLibelle, type, montant, classeId|null, classe|null, nbPayants}].
     */
    public function recouvrementParFrais(AnneeAcademique $annee): array
    {
        // Frais liés à au moins une classe
        $avecClasse = $this->getEntityManager()->createQuery(
            'SELECT f.id AS fraisId, f.libelle AS fraisLibelle, t.libelle AS type, f.montant AS montant,
                    c.id AS classeId, c.nom AS classe,
                    COUNT(DISTINCT p.eleve) AS nbPayants
             FROM App\Entity\FraisScolaire f
             LEFT JOIN f.type t
             JOIN f.classes c
             LEFT JOIN App\Entity\PaiementFrais p WITH p.fraisScolaire = f
             WHERE f.anneeAcademique = :annee AND f.actif = true
             GROUP BY f.id, c.id
             ORDER BY c.nom ASC, t.libelle ASC'
        )->setParameter('annee', $annee)->getResult();

        // Frais globaux (sans classe)
        $global = $this->getEntityManager()->createQuery(
            'SELECT f.id AS fraisId, f.libelle AS fraisLibelle, t.libelle AS type, f.montant AS montant,
                    COUNT(DISTINCT p.eleve) AS nbPayants
             FROM App\Entity\FraisScolaire f
             LEFT JOIN f.type t
             LEFT JOIN App\Entity\PaiementFrais p WITH p.fraisScolaire = f
             WHERE f.anneeAcademique = :annee AND f.actif = true AND f.classes IS EMPTY
             GROUP BY f.id
             ORDER BY t.libelle ASC'
        )->setParameter('annee', $annee)->getResult();

        foreach ($global as &$row) {
            $row['classeId'] = null;
            $row['classe']   = null;
        }

        return array_merge($avecClasse, $global);
    }

    /**
     * Liste des élèves ayant payé un frais donné.
     * Retourne [{id, nom, prenom, postnom, matricule, totalPaye, dernierPaiement, modePaiement}].
     */
    public function elevesAyantPayeFrais(\App\Entity\FraisScolaire $frais): array
    {
        return $this->createQueryBuilder('p')
            ->select(
                'e.id, e.nom, e.prenom, e.postnom, e.matricule,
                 SUM(p.montantPaye) AS totalPaye,
                 MAX(p.datePaiement) AS dernierPaiement,
                 p.modePaiement AS modePaiement'
            )
            ->join('p.eleve', 'e')
            ->where('p.fraisScolaire = :frais')
            ->setParameter('frais', $frais)
            ->groupBy('e.id, p.modePaiement')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Nombre de frais scolaires actifs de l'année sans paiement correspondant.
     * Approximation : nb frais actifs - nb frais distincts déjà payés.
     */
    public function countEnAttente(AnneeAcademique $annee): int
    {
        $nbFraisTotal = (int) $this->getEntityManager()
            ->createQuery(
                'SELECT COUNT(f.id) FROM App\Entity\FraisScolaire f
                 WHERE f.anneeAcademique = :annee AND f.actif = true'
            )
            ->setParameter('annee', $annee)
            ->getSingleScalarResult();

        $nbFraisPaies = (int) $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT p.fraisScolaire)')
            ->join('p.fraisScolaire', 'f')
            ->where('f.anneeAcademique = :annee')
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getSingleScalarResult();

        return max(0, $nbFraisTotal - $nbFraisPaies);
    }
}

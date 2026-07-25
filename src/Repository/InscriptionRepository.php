<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\FraisScolaire;
use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscription>
 *
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    public function findByClasse(Classe $classe): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.classe = :classe')
            ->setParameter('classe', $classe)
            ->join('i.eleve', 'e')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()->getResult();
    }

    public function countByAnnee(AnneeAcademique $annee): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.anneeAcademique = :annee')
            ->setParameter('annee', $annee)
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * Tous les élèves éligibles à un frais donné (ceux qui devraient payer).
     * Si le frais est lié à une classe → inscriptions dans cette classe.
     * Si le frais est global → toutes les inscriptions de l'année.
     * Retourne [{id, nom, prenom, postnom, matricule}].
     */
    /**
     * Élèves inscrits dans une classe donnée pour une année. Retourne [{id, nom, prenom, postnom, matricule}].
     */
    public function elevesParClasse(\App\Entity\Classe $classe, AnneeAcademique $annee): array
    {
        return $this->createQueryBuilder('i')
            ->select('e.id, e.nom, e.prenom, e.postnom, e.matricule')
            ->join('i.eleve', 'e')
            ->where('i.classe = :classe AND i.anneeAcademique = :annee')
            ->setParameter('classe', $classe)
            ->setParameter('annee', $annee)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function elevesEligiblesPourFrais(FraisScolaire $frais, AnneeAcademique $annee): array
    {
        $qb = $this->createQueryBuilder('i')
            ->select('e.id, e.nom, e.prenom, e.postnom, e.matricule')
            ->join('i.eleve', 'e')
            ->where('i.anneeAcademique = :annee')
            ->setParameter('annee', $annee)
            ->orderBy('e.nom', 'ASC');

        if (!$frais->getClasses()->isEmpty()) {
            // Frais limité à certaines classes → uniquement les inscrits dans ces classes
            $qb->andWhere('i.classe IN (:classes)')
               ->setParameter('classes', $frais->getClasses());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Liste des élèves d'une classe avec leur statut de paiement (total payé, nb paiements).
     * Retourne [{id, nom, prenom, postnom, matricule, totalPaye, nbPaiements}].
     */
    public function elevesAvecStatutPaiements(\App\Entity\Classe $classe, AnneeAcademique $annee): array
    {
        return $this->getEntityManager()->createQuery(
            'SELECT e.id, e.nom, e.prenom, e.postnom, e.matricule,
                    COALESCE(SUM(p.montantPaye), 0) AS totalPaye,
                    COUNT(p.id) AS nbPaiements
             FROM App\Entity\Inscription i
             JOIN i.eleve e
             LEFT JOIN App\Entity\PaiementFrais p WITH p.eleve = e
             WHERE i.classe = :classe AND i.anneeAcademique = :annee
             GROUP BY e.id
             ORDER BY totalPaye DESC, e.nom ASC'
        )
        ->setParameter('classe', $classe)
        ->setParameter('annee', $annee)
        ->getResult();
    }

    /**
     * Nombre d'élèves inscrits par classe pour une année académique.
     * Retourne ['NomClasse' => nbEleves, ...].
     */
    public function effectifParClasse(AnneeAcademique $annee): array
    {
        $rows = $this->createQueryBuilder('i')
            ->select('c.nom AS classe, COUNT(i.id) AS nbEleves')
            ->join('i.classe', 'c')
            ->where('i.anneeAcademique = :annee')
            ->setParameter('annee', $annee)
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($rows as $row) {
            $data[$row['classe']] = (int) $row['nbEleves'];
        }
        return $data;
    }

    public function findByEleve(Eleve $eleve): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.eleve = :eleve')
            ->setParameter('eleve', $eleve)
            ->join('i.anneeAcademique', 'a')
            ->orderBy('a.libelle', 'DESC')
            ->getQuery()->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\FraisScolaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FraisScolaire>
 *
 * @method FraisScolaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method FraisScolaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method FraisScolaire[]    findAll()
 * @method FraisScolaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FraisScolaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FraisScolaire::class);
    }

    /**
     * Retourne les frais actifs pour une année académique donnée.
     */
    public function findByAnnee(AnneeAcademique $annee): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.anneeAcademique = :annee')
            ->andWhere('f.actif = true')
            ->setParameter('annee', $annee)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les frais actifs pour une classe et une année académique données.
     * Si $classe est null, retourne les frais applicables à toutes les classes.
     */
    public function findByClasseAndAnnee(?Classe $classe, AnneeAcademique $annee): array
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.anneeAcademique = :annee')
            ->andWhere('f.actif = true')
            ->setParameter('annee', $annee)
            ->orderBy('f.createdAt', 'DESC');

        if ($classe === null) {
            $qb->andWhere('f.classes IS EMPTY');
        } else {
            // frais global (aucune classe) OU frais incluant cette classe
            $qb->andWhere('f.classes IS EMPTY OR :classe MEMBER OF f.classes')
               ->setParameter('classe', $classe);
        }

        return $qb->getQuery()->getResult();
    }
}

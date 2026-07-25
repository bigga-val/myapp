<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Examen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Examen>
 *
 * @method Examen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Examen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Examen[]    findAll()
 * @method Examen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Examen::class);
    }

    public function findByAnneeAndClasse(AnneeAcademique $annee, ?Classe $classe = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.anneeAcademique = :annee')
            ->setParameter('annee', $annee)
            ->orderBy('e.periode', 'ASC')
            ->addOrderBy('e.dateDebut', 'DESC');
        if ($classe) {
            $qb->andWhere('e.classe = :classe OR e.classe IS NULL')
               ->setParameter('classe', $classe);
        }
        return $qb->getQuery()->getResult();
    }
}

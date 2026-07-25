<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function findByClasseAndPeriode(Classe $classe, int $periode, AnneeAcademique $annee): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.classe = :classe')
            ->andWhere('n.periode = :periode')
            ->andWhere('n.anneeAcademique = :annee')
            ->setParameter('classe', $classe)
            ->setParameter('periode', $periode)
            ->setParameter('annee', $annee)
            ->join('n.eleve', 'e')
            ->join('n.matiere', 'm')
            ->orderBy('e.nom', 'ASC')
            ->addOrderBy('m.nom', 'ASC')
            ->getQuery()->getResult();
    }

    public function findByEleveAndPeriode(Eleve $eleve, int $periode, AnneeAcademique $annee): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.eleve = :eleve')
            ->andWhere('n.periode = :periode')
            ->andWhere('n.anneeAcademique = :annee')
            ->setParameter('eleve', $eleve)
            ->setParameter('periode', $periode)
            ->setParameter('annee', $annee)
            ->join('n.matiere', 'm')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()->getResult();
    }
}

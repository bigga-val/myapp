<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Enum\Niveau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Classe>
 *
 * @method Classe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classe[]    findAll()
 * @method Classe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classe::class);
    }

    public function findByNiveau(Niveau $niveau, AnneeAcademique $annee): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.niveau = :niveau')
            ->andWhere('c.anneeAcademique = :annee')
            ->setParameter('niveau', $niveau)
            ->setParameter('annee', $annee)
            ->orderBy('c.nom', 'ASC')
            ->getQuery()->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\AnneeAcademique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnneeAcademique>
 *
 * @method AnneeAcademique|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnneeAcademique|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnneeAcademique[]    findAll()
 * @method AnneeAcademique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnneeAcademiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnneeAcademique::class);
    }

    public function findCurrent(): ?AnneeAcademique
    {
        return $this->findOneBy(['isCurrent' => true]);
    }
}

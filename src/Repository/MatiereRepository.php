<?php
namespace App\Repository;

use App\Entity\Matiere;
use App\Enum\Niveau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matiere>
 */
class MatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matiere::class);
    }

    public function findByNiveau(Niveau $niveau): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.niveau = :niveau')
            ->setParameter('niveau', $niveau)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()->getResult();
    }
}

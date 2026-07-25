<?php

namespace App\Repository;

use App\Entity\Eleve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Eleve>
 *
 * @method Eleve|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eleve|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eleve[]    findAll()
 * @method Eleve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EleveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eleve::class);
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()->getSingleScalarResult();
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.nom LIKE :q OR e.prenom LIKE :q OR e.matricule LIKE :q OR e.postnom LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()->getResult();
    }
}

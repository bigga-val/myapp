<?php

namespace App\Repository;

use App\Entity\TypeFrais;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeFrais>
 */
class TypeFraisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeFrais::class);
    }

    /** @return TypeFrais[] */
    public function findActifs(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.actif = true')
            ->orderBy('t.ordre', 'ASC')
            ->addOrderBy('t.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCode(string $code): ?TypeFrais
    {
        return $this->findOneBy(['code' => $code]);
    }
}

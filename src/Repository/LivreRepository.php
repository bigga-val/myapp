<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    /**
     * Recherche dans le titre ou l'auteur (LIKE %q%).
     */
    public function search(string $q): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.titre LIKE :q OR l.auteur LIKE :q')
            ->setParameter('q', '%' . $q . '%')
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les livres actifs triés par titre.
     */
    public function findActifs(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Nombre d'exemplaires disponibles = nombreExemplaires - nb emprunts en_cours pour ce livre.
     */
    public function countDisponibles(Livre $livre): int
    {
        $empruntsEnCours = $this->getEntityManager()
            ->createQuery(
                'SELECT COUNT(e.id) FROM App\Entity\EmpruntLivre e
                 WHERE e.livre = :livre AND e.statut IN (:statuts)'
            )
            ->setParameter('livre', $livre)
            ->setParameter('statuts', ['en_cours', 'en_retard'])
            ->getSingleScalarResult();

        return max(0, $livre->getNombreExemplaires() - (int) $empruntsEnCours);
    }
}

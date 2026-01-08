<?php

namespace App\Repository;

use App\Entity\CommandeReception;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommandeReception>
 *
 * @method CommandeReception|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeReception|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeReception[]    findAll()
 * @method CommandeReception[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeReceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeReception::class);
    }

//    /**
//     * @return CommandeReception[] Returns an array of CommandeReception objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CommandeReception
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    public function HistoriqueReceptionParCommande($commandeID): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT cr.id, c.CommandeNumber, p.designation, cr.QuantiteRecue, u.username, cr.ReceptionDate
                    from App\Entity\CommandeReception cr, App\Entity\CommandeProduit cp, App\Entity\User u,
                        App\Entity\Commande c, App\Entity\Produits p
                     where cr.CommandeProduit = cp.id
                     and cp.Produit = p.id
                     and cr.ReceivedBy = u.id
                     and cp.Commande = c.id
                        and (c.id=:commandeID or :commandeID is null)
            '
        );
        $query->setParameter('commandeID', $commandeID);
        return $query->getResult();
    }

    public function CommandeQuantite($commandeID): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT 
    cp.id,
    p.designation,
    cp.Quantite AS QuantiteCommandee,
    COALESCE(SUM(cr2.QuantiteRecue), 0) AS QuantiteRecue,
    (p.prix + (p.prix * COALESCE(cat.Pourcentage, 40) / 100)) AS prixUnitaire,
    (cp.Quantite * (p.prix + (p.prix * COALESCE(cat.Pourcentage, 40) / 100))) AS prixTotal
FROM App\Entity\CommandeProduit cp
JOIN cp.Produit p
JOIN cp.Commande c
LEFT JOIN p.Categorie cat
LEFT JOIN App\Entity\CommandeReception cr2 WITH cr2.CommandeProduit = cp.id
WHERE (:commandeID IS NULL OR c.id = :commandeID)
GROUP BY cp.id

            '
        );
        $query->setParameter('commandeID', $commandeID);
        return $query->getResult();
    }

    public function CommandeQuantite2($commandeID): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT cp.id, p.designation,cp.Quantite QuantiteCommandee, 
            (select sum(recept.QuantiteRecue)
                from App\Entity\CommandeReception recept
                where recept.CommandeProduit = cp.id
            )
            
            QuantiteRecue, 
                (p.prix+(p.prix *  coalesce(cat.Pourcentage, 40)/100)) prixUnitaire, 
                (cp.Quantite * (p.prix+(p.prix *  coalesce(cat.Pourcentage, 40)/100))) prixTotal
                    from App\Entity\CommandeReception cr, App\Entity\CommandeProduit cp, App\Entity\User u,
                        App\Entity\Commande c, App\Entity\Produits p, App\Entity\CategorieProduit cat
                     where cr.CommandeProduit = cp.id
                     and cp.Produit = p.id
                     and cr.ReceivedBy = u.id
                     and cp.Commande = c.id
                     and (p.Categorie = cat.id or p.Categorie is null)
                        and (c.id=:commandeID or :commandeID is null)
                     Group By cp.id--, p.id, p.designation, p.prix
            '
        );
        $query->setParameter('commandeID', $commandeID);
        return $query->getResult();
    }
}

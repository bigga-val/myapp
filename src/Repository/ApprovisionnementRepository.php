<?php

namespace App\Repository;

use App\Entity\Approvisionnement;
use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Approvisionnement>
 *
 * @method Approvisionnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Approvisionnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Approvisionnement[]    findAll()
 * @method Approvisionnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovisionnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Approvisionnement::class);
    }

    public function stockProduit(): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            '    
            SELECT p.id produitID, p.code, concat(p.designation, \' - \', UPPER(p.code)) as designation , p.prix, p.preemption, p.fabricant,
                  SUM(e.qty) AS totalEntree, p.minimum,
                  (
                    SELECT 
                      SUM(s.qty) AS quantite_sortie
                    FROM App\Entity\Produits pr, App\Entity\ProduitVendu s, App\Entity\Vente v
                     where s.produit = pr.id
                          and v.id = s.vente
                          and v.statusVente = \'paid\'
                          and pr.id = produitID
                    GROUP BY pr.id 
                    ) as totalSortie,
                    (
                    SELECT 
                      SUM(s2.qty) AS quantite_reserve
                    FROM App\Entity\Produits pr2, App\Entity\ProduitVendu s2, App\Entity\Vente v2
                     where s2.produit = pr2.id
                          and v2.id = s2.vente
                          and v2.statusVente = \'progress\'
                          and pr2.id = produitID
                    GROUP BY pr2.id 
                    ) as totalReserve,
                    SUM(e.cout) cout
            FROM App\Entity\Produits p, App\Entity\Approvisionnement e
            where e.produit = p.id
            GROUP BY p.id
            '
        );
        return $query->getResult();

    }

    public function stockProduitByID($prodID): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            '    
            SELECT p.id produitID, p.code, concat(p.designation, \' - \', UPPER(p.code)) as designation ,
             p.prix, p.uniteMesure, p.minimum, p.maximum, p.preemption, p.fabricant,
                  SUM(e.qty) AS totalEntree,
                  (
                    SELECT 
                      SUM(s.qty) AS quantite_sortie
                    FROM App\Entity\Produits pr, App\Entity\ProduitVendu s, App\Entity\Vente v
                     where s.produit = pr.id
                          and v.id = s.vente
                          and v.statusVente = \'paid\'
                          and pr.id = :prodID
                    GROUP BY pr.id 
                    ) as totalSortie,
                    (
                    SELECT 
                      SUM(s2.qty) AS quantite_reserve
                    FROM App\Entity\Produits pr2, App\Entity\ProduitVendu s2, App\Entity\Vente v2
                     where s2.produit = pr2.id
                          and v2.id = s2.vente
                          and v2.statusVente = \'progress\'
                          and pr2.id = :prodID
                    GROUP BY pr2.id 
                    ) as totalReserve
            FROM App\Entity\Produits p, App\Entity\Approvisionnement e
            where e.produit = p.id
            and p.id = :prodID
            GROUP BY p.id
            '
        );
        $query->setParameter('prodID', $prodID);

        return $query->getResult();

    }


    public function stockProduitCommandeByID($prodID): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            '    
            SELECT p.id produitID, p.code, concat(p.designation, \' - \', UPPER(p.code)) as designation ,
             p.prix, p.uniteMesure, p.minimum, p.maximum, p.preemption, p.fabricant,
                  SUM(e.qty) AS totalEntree,
                  (
                    SELECT 
                      SUM(s.qty) AS quantite_sortie
                    FROM App\Entity\Produits pr, App\Entity\ProduitVendu s, App\Entity\Vente v
                     where s.produit = pr.id
                          and v.id = s.vente
                          and v.statusVente = \'paid\'
                          and pr.id = produitID
                    GROUP BY pr.id 
                    ) as totalSortie,
                    (
                    SELECT 
                      SUM(s2.qty) AS quantite_reserve
                    FROM App\Entity\Produits pr2, App\Entity\ProduitVendu s2, App\Entity\Vente v2
                     where s2.produit = pr2.id
                          and v2.id = s2.vente
                          and v2.statusVente = \'progress\'
                          and pr2.id = produitID
                    GROUP BY pr2.id 
                    ) as totalReserve,
                    (p.prix+(p.prix *  coalesce(cp.Pourcentage, 40)/100)) prixTotal
            FROM App\Entity\Produits p, App\Entity\Approvisionnement e,
            App\Entity\CategorieProduit cp
            where e.produit = p.id
            and p.Categorie = cp.id
            and p.id = :prodID
            GROUP BY p.id
            '
        );
        $query->setParameter('prodID', $prodID);

        return $query->getResult();

    }


    public function findApprosGrouped(): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            '
            select SUM(a.qty) as totalentre, p.id, p.designation, p.fabricant
                from App\Entity\Approvisionnement a, App\Entity\Produits p
                
                where a.produit = p.id
                group by p.id   
            '
        );
        return $query->getResult();
    }

    public function approparDate($mydate): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT SUM(a.qty * p.prix) montant, SUM(a.qty * p.prix) / a.taux convert         
                    FROM App\Entity\Produits p, App\Entity\Approvisionnement a
                     where a.produit = p.id
                        and a.approDate = :mydate
                    GROUP BY a.approDate 
            '
        );
        //$query->setParameter('mydate', $mydate);
        $query->setParameter('mydate', $mydate->format('Y-m-d'));
        return $query->getResult();

    }


//    /**
//     * @return Approvisionnement[] Returns an array of Approvisionnement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Approvisionnement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

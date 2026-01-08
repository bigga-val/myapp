<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
Use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Vente>
 *
 * @method Vente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vente[]    findAll()
 * @method Vente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }

    public function venteparDate($mydate): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT SUM(s.qty * s.prixUnitaire) montant         
                    FROM App\Entity\Produits pr, App\Entity\ProduitVendu s, App\Entity\Vente v
                     where s.produit = pr.id
                          and v.id = s.vente
                          and v.statusVente = :status
                          and v.venteDate = :mydate
                    GROUP BY v.venteDate 
            '
        );
        $query->setParameter('status', 'paid');
        $query->setParameter('mydate', $mydate->format('Y-m-d'));
        return $query->getResult();
    }



    public function venteparIntervale($dateDebut, $dateFin): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT SUM(s.qty * s.prixUnitaire) montant         
                    FROM App\Entity\Produits pr, App\Entity\ProduitVendu s, App\Entity\Vente v
                     where s.produit = pr.id
                          and v.id = s.vente
                          and v.statusVente = :status
                          and v.venteDate between :dateDebut and :dateFin
                    GROUP BY v.venteDate 
            '
        );
        //$query->setParameter('mydate', $mydate);
        $query->setParameter('status', 'paid');
        $query->setParameter('dateDebut', $dateDebut);
        $query->setParameter('dateFin', $dateFin);
        return $query->getResult();
    }

    public function venteparIntervale2($dateDebut, $dateFin): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT SUM(s.qty * s.prixUnitaire) montant         
                    FROM App\Entity\Produits pr, App\Entity\ProduitVendu s, App\Entity\Vente v
                     where s.produit = pr.id
                          and v.id = s.vente
                          and v.statusVente = :status
                          and v.venteDate between :dateDebut and :dateFin
                    --GROUP BY v.venteDate 
            '
        );
        //$query->setParameter('mydate', $mydate);
        $query->setParameter('dateDebut', $dateDebut);
        $query->setParameter('dateFin', $dateFin);
        $query->setParameter('status', 'paid');

        return $query->getResult();
    }

    //*
    /*
     * Fonction permettant d'afficher les ventes ou factures avec la somme des produits vendus.
     */
    public function venteTotalGrouped(): array{
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT SUM(s.qty * s.prixUnitaire) montant, SUM(s.qty * s.prixUnitaire) / s.taux convert, 
            s.taux, v.id, v.createdAt, v.statusVente, v.createdBy, v.numeroVente, s.id ligneID
            , (select t.designation from App\Entity\Table t where t.id = v.TableServie) as table
                    FROM App\Entity\Produits pr, App\Entity\ProduitVendu s, App\Entity\Vente v
                     where s.produit = pr.id
                          and v.id = s.vente
                          
                    GROUP BY v.id 
            '
        );
        //$query->setParameter('mydate', $mydate);
        return $query->getResult();
    }

    public function venteAnnuelle(){
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            '
                select substring(pv.createdAt, 6, 2) mois, SUM(pv.qty * pv.prixUnitaire) montant
                from App\Entity\Produits p, App\Entity\ProduitVendu pv, App\Entity\Ventev v
                where p.id = pv.produit
                and pv.vente = v.id
                and v.statusVente = :status
                group by mois
            '
        );
        //$query->setParameter('mydate', $mydate);
        $query->setParameter('status', 'paid');
        return $query->getResult();
    }
    public function ApproAnnuel(){
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            '
                select substring(pv.createdAt, 6, 2) mois, SUM(pv.qty * p.prix) montant
                from App\Entity\Produits p, App\Entity\Approvisionnement pv
                where p.id = pv.produit
                group by mois
            '
        );
        //$query->setParameter('mydate', $mydate);
        return $query->getResult();
    }


//    /**
//     * @return Vente[] Returns an array of Vente objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Vente
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

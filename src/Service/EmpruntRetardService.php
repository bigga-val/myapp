<?php

namespace App\Service;

use App\Repository\EmpruntLivreRepository;
use Doctrine\ORM\EntityManagerInterface;

class EmpruntRetardService
{
    /**
     * Met à jour le statut des emprunts non rendus dont la date de retour prévue est dépassée.
     * Retourne le nombre d'emprunts mis à jour.
     */
    public function mettreAJourRetards(EntityManagerInterface $em, EmpruntLivreRepository $repo): int
    {
        $empruntsEnRetard = $repo->findEnRetard();
        $count = 0;

        foreach ($empruntsEnRetard as $emprunt) {
            if ($emprunt->getStatut() !== 'en_retard') {
                $emprunt->setStatut('en_retard');
                $count++;
            }
        }

        if ($count > 0) {
            $em->flush();
        }

        return $count;
    }
}

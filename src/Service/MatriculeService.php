<?php

namespace App\Service;

class MatriculeService
{
    /**
     * Génère un matricule élève : ELV-2025-00042
     */
    public function generateEleveMatricule(int $sequence): string
    {
        return sprintf('ELV-%s-%05d', date('Y'), $sequence);
    }

    /**
     * Génère un numéro de reçu : REC-2025-00042
     */
    public function generateNumeroRecu(int $sequence): string
    {
        return sprintf('REC-%s-%05d', date('Y'), $sequence);
    }
}

<?php

namespace App\Services;

use App\Models\CompteModel;
use RuntimeException;

class CompteService
{
    protected CompteModel $compteModel;

    public function __construct()
    {
        $this->compteModel = new CompteModel();
    }

    public function debiter(array $compte, float $montant): array
    {
        if ($compte['statut'] !== 'ACTIF') {
            throw new RuntimeException("Le compte {$compte['numero_compte']} n'est pas actif.");
        }

        if ($compte['solde'] < $montant) {
            throw new RuntimeException("Solde insuffisant sur le compte {$compte['numero_compte']}.");
        }

        $nouveauSolde = $compte['solde'] - $montant;
        $this->compteModel->update($compte['id'], ['solde' => $nouveauSolde]);

        return array_merge($compte, ['solde' => $nouveauSolde]);
    }

    public function crediter(array $compte, float $montant): array
    {
        if ($compte['statut'] !== 'ACTIF') {
            throw new RuntimeException("Le compte {$compte['numero_compte']} n'est pas actif.");
        }

        $nouveauSolde = $compte['solde'] + $montant;

        if ((float) $compte['plafond'] > 0 && $nouveauSolde > (float) $compte['plafond']) {
            throw new RuntimeException("Le plafond du compte {$compte['numero_compte']} serait dépassé.");
        }

        $this->compteModel->update($compte['id'], ['solde' => $nouveauSolde]);

        return array_merge($compte, ['solde' => $nouveauSolde]);
    }
}

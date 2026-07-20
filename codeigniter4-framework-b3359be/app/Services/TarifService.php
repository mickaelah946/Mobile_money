<?php

namespace App\Services;

use App\Models\GrilleTarifaireModel;

class TarifService
{
    protected GrilleTarifaireModel $grilleModel;

    public function __construct()
    {
        $this->grilleModel = new GrilleTarifaireModel();
    }

    public function calculerFrais(int $typeTransactionId, float $montant): float
    {
        $tarif = $this->grilleModel->findApplicable($typeTransactionId, $montant);

        if ($tarif === null) {
            return 0.0;
        }

        $fraisFixe        = (float) $tarif['frais_fixe'];
        $fraisPourcentage = (float) $tarif['frais_pourcentage'];

        return round($fraisFixe + ($montant * $fraisPourcentage / 100), 2);
    }
}

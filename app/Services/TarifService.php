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

    public function appliquePromotionTransfertInterne (float $frais):float

    { $actif= (int) $this -> parametreModel->getValeur('PROMOTION_TRANSFERT_INTERNE_ACTIF',0);
        if ($actif !== 1)
            return $frais;
    

    $reduction = (float) $this->parametreModel->getValeur('PROMOTION_TRANSFERT_INTERNE_REDUCTION_POURCENTGE',0);
    return round ($frais * (1 - $reduction /100), 2);
    
    }

}

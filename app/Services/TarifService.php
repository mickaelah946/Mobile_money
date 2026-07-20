<?php

namespace App\Services;

use App\Models\GrilleTarifaireModel;
use App\Models\ParametreSystemeModel;

class TarifService
{
    protected GrilleTarifaireModel $grilleModel;
    protected ParametreSystemeModel $parametreModel;

    public function __construct()
    {
        $this->grilleModel    = new GrilleTarifaireModel();
        $this->parametreModel = new ParametreSystemeModel();
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

    /**
     * Commission additionnelle (V2) appliquee uniquement sur les transferts
     * vers un autre operateur, en plus du tarif de transfert normal.
     */
    public function calculerCommissionInterOperateur(float $montant): float
    {
        $pourcentage = (float) $this->parametreModel->getValeur('COMMISSION_INTEROPERATEUR_POURCENTAGE', 0);

        return round($montant * $pourcentage / 100, 2);
    }
}

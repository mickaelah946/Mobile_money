<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\CompteModel;
use App\Models\MouvementModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $compteModel = new CompteModel();
        $compte      = $compteModel->findByClientId((int) $this->session->get('clientId'));

        $mouvements = $compte
            ? (new MouvementModel())->historiqueCompte($compte['id'], 5)
            : [];

        return view('client/dashboard/index', [
            'compte'     => $compte,
            'mouvements' => $mouvements,
        ]);
    }
}

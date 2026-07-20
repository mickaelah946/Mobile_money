<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ClientModel;

class ClientAuthController extends BaseController
{
    public function showLogin()
    {
        if ($this->session->get('clientLoggedIn')) {
            return redirect()->to('/client/dashboard');
        }

        return view('client/auth/login');
    }

    public function login()
    {
        $telephone = trim((string) $this->request->getPost('telephone'));

        if ($telephone === '') {
            return redirect()->back()->withInput()->with('error', 'Veuillez saisir votre numéro de téléphone.');
        }

        $client = (new ClientModel())->where('telephone', $telephone)->first();

        if (! $client) {
            return redirect()->back()->withInput()->with('error', "Aucun client ne correspond à ce numéro.");
        }

        if ($client['statut'] !== 'ACTIF') {
            return redirect()->back()->withInput()->with('error', 'Votre compte est suspendu ou bloqué. Contactez votre agence.');
        }

        $this->session->set([
            'clientLoggedIn'  => true,
            'clientId'        => $client['id'],
            'clientNom'       => $client['nom'],
            'clientPrenom'    => $client['prenom'],
            'clientTelephone' => $client['telephone'],
        ]);

        return redirect()->to('/client/dashboard');
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/client/login');
    }
}

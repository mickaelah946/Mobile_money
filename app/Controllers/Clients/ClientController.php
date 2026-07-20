<?php

namespace App\Controllers\Clients;

use App\Controllers\BaseController;
use App\Libraries\ReferenceGenerator;
use App\Models\ClientModel;
use App\Models\CompteModel;
use App\Models\ParametreSystemeModel;
use App\Models\PrefixeOperateurModel;
use App\Services\AuditService;

class ClientController extends BaseController
{
    protected ClientModel $clientModel;
    protected CompteModel $compteModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->compteModel = new CompteModel();
    }

    public function index()
    {
        $clients = $this->clientModel->orderBy('date_creation', 'DESC')->paginate(20);

        return view('clients/index', [
            'clients' => $clients,
            'pager'   => $this->clientModel->pager,
        ]);
    }

    public function create()
    {
        return view('clients/create', ['client' => null]);
    }

    public function store()
    {
        $rules = [
            'nom'       => 'required|min_length[2]',
            'prenom'    => 'required|min_length[2]',
            'telephone' => 'required|min_length[8]|is_unique[clients.telephone]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (! (new PrefixeOperateurModel())->estInterne($this->request->getPost('telephone'))) {
            return redirect()->back()->withInput()->with('error', "Ce numéro n'appartient pas à notre opérateur : seuls les numéros de notre réseau peuvent être enregistrés comme clients.");
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $tempRef = 'TMP-' . time() . '-' . random_int(100, 999);

        $clientId = $this->clientModel->insert([
            'numero_client'  => $tempRef,
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'telephone'      => $this->request->getPost('telephone'),
            'type_piece'     => $this->request->getPost('type_piece') ?: null,
            'numero_piece'   => $this->request->getPost('numero_piece') ?: null,
            'date_naissance' => $this->request->getPost('date_naissance') ?: null,
            'adresse'        => $this->request->getPost('adresse'),
            'statut'         => 'ACTIF',
            'cree_par'       => $this->session->get('utilisateurId'),
        ], true);

        $this->clientModel->update($clientId, [
            'numero_client' => ReferenceGenerator::client($clientId),
        ]);

        $plafond = (new ParametreSystemeModel())->getValeur('PLAFOND_COMPTE_CLIENT', 0);

        $compteId = $this->compteModel->insert([
            'numero_compte' => 'TMP-' . time() . '-' . random_int(100, 999),
            'type_compte'   => 'CLIENT',
            'client_id'     => $clientId,
            'solde'         => 0,
            'plafond'       => $plafond,
            'statut'        => 'ACTIF',
        ], true);

        $this->compteModel->update($compteId, [
            'numero_compte' => ReferenceGenerator::compte($compteId),
        ]);

        $db->transComplete();

        (new AuditService())->log('CREATION_CLIENT', 'clients', $clientId);

        return redirect()->to('/clients')->with('success', 'Client créé avec succès.');
    }

    public function edit(int $id)
    {
        $client = $this->clientModel->find($id);

        if (! $client) {
            return redirect()->to('/clients')->with('error', 'Client introuvable.');
        }

        return view('clients/edit', ['client' => $client]);
    }

    public function update(int $id)
    {
        $client = $this->clientModel->find($id);

        if (! $client) {
            return redirect()->to('/clients')->with('error', 'Client introuvable.');
        }

        $rules = [
            'nom'       => 'required|min_length[2]',
            'prenom'    => 'required|min_length[2]',
            'telephone' => "required|min_length[8]|is_unique[clients.telephone,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (! (new PrefixeOperateurModel())->estInterne($this->request->getPost('telephone'))) {
            return redirect()->back()->withInput()->with('error', "Ce numéro n'appartient pas à notre opérateur : seuls les numéros de notre réseau peuvent être enregistrés comme clients.");
        }

        $this->clientModel->update($id, [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'telephone'      => $this->request->getPost('telephone'),
            'type_piece'     => $this->request->getPost('type_piece') ?: null,
            'numero_piece'   => $this->request->getPost('numero_piece') ?: null,
            'date_naissance' => $this->request->getPost('date_naissance') ?: null,
            'adresse'        => $this->request->getPost('adresse'),
        ]);

        (new AuditService())->log('MODIFICATION_CLIENT', 'clients', $id);

        return redirect()->to('/clients/' . $id)->with('success', 'Client mis à jour.');
    }

    public function show(int $id)
    {
        $client = $this->clientModel->find($id);

        if (! $client) {
            return redirect()->to('/clients')->with('error', 'Client introuvable.');
        }

        $compte = $this->compteModel->findByClientId($id);

        return view('clients/show', ['client' => $client, 'compte' => $compte]);
    }

    public function changerStatut(int $id)
    {
        $client = $this->clientModel->find($id);

        if (! $client) {
            return redirect()->to('/clients')->with('error', 'Client introuvable.');
        }

        $nouveauStatut = $this->request->getPost('statut');

        if (! in_array($nouveauStatut, ['ACTIF', 'SUSPENDU', 'BLOQUE'], true)) {
            return redirect()->back()->with('error', 'Statut invalide.');
        }

        $this->clientModel->update($id, ['statut' => $nouveauStatut]);

        $compte = $this->compteModel->findByClientId($id);
        if ($compte) {
            $this->compteModel->update($compte['id'], ['statut' => $nouveauStatut]);
        }

        (new AuditService())->log('CHANGEMENT_STATUT_CLIENT', 'clients', $id, "Nouveau statut : {$nouveauStatut}");

        return redirect()->to('/clients/' . $id)->with('success', 'Statut mis à jour.');
    }
}

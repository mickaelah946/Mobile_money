<?php

namespace App\Controllers\Agents;

use App\Controllers\BaseController;
use App\Libraries\ReferenceGenerator;
use App\Models\AgentModel;
use App\Models\CompteModel;
use App\Services\AuditService;

class AgentController extends BaseController
{
    protected AgentModel $agentModel;
    protected CompteModel $compteModel;

    public function __construct()
    {
        $this->agentModel = new AgentModel();
        $this->compteModel = new CompteModel();
    }

    public function index()
    {
        $agents = $this->agentModel->orderBy('date_creation', 'DESC')->paginate(20);

        return view('agents/index', [
            'agents' => $agents,
            'pager'  => $this->agentModel->pager,
        ]);
    }

    public function create()
    {
        return view('agents/create');
    }

    public function store()
    {
        $rules = [
            'nom'       => 'required|min_length[2]',
            'prenom'    => 'required|min_length[2]',
            'telephone' => 'required|min_length[8]|is_unique[agents.telephone]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $agentId = $this->agentModel->insert([
            'code_agent' => 'TMP-' . time() . '-' . random_int(100, 999),
            'nom'        => $this->request->getPost('nom'),
            'prenom'     => $this->request->getPost('prenom'),
            'telephone'  => $this->request->getPost('telephone'),
            'zone'       => $this->request->getPost('zone'),
            'adresse'    => $this->request->getPost('adresse'),
            'statut'     => 'ACTIF',
            'cree_par'   => $this->session->get('utilisateurId'),
        ], true);

        $this->agentModel->update($agentId, [
            'code_agent' => ReferenceGenerator::agent($agentId),
        ]);

        $compteId = $this->compteModel->insert([
            'numero_compte' => 'TMP-' . time() . '-' . random_int(100, 999),
            'type_compte'   => 'AGENT',
            'agent_id'      => $agentId,
            'solde'         => 0,
            'plafond'       => 0,
            'statut'        => 'ACTIF',
        ], true);

        $this->compteModel->update($compteId, [
            'numero_compte' => ReferenceGenerator::compte($compteId),
        ]);

        $db->transComplete();

        (new AuditService())->log('CREATION_AGENT', 'agents', $agentId);

        return redirect()->to('/agents')->with('success', 'Agent créé avec succès.');
    }

    public function edit(int $id)
    {
        $agent = $this->agentModel->find($id);

        if (! $agent) {
            return redirect()->to('/agents')->with('error', 'Agent introuvable.');
        }

        return view('agents/edit', ['agent' => $agent]);
    }

    public function update(int $id)
    {
        $agent = $this->agentModel->find($id);

        if (! $agent) {
            return redirect()->to('/agents')->with('error', 'Agent introuvable.');
        }

        $rules = [
            'nom'       => 'required|min_length[2]',
            'prenom'    => 'required|min_length[2]',
            'telephone' => "required|min_length[8]|is_unique[agents.telephone,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->agentModel->update($id, [
            'nom'       => $this->request->getPost('nom'),
            'prenom'    => $this->request->getPost('prenom'),
            'telephone' => $this->request->getPost('telephone'),
            'zone'      => $this->request->getPost('zone'),
            'adresse'   => $this->request->getPost('adresse'),
        ]);

        (new AuditService())->log('MODIFICATION_AGENT', 'agents', $id);

        return redirect()->to('/agents/' . $id)->with('success', 'Agent mis à jour.');
    }

    public function show(int $id)
    {
        $agent = $this->agentModel->find($id);

        if (! $agent) {
            return redirect()->to('/agents')->with('error', 'Agent introuvable.');
        }

        $compte = $this->compteModel->findByAgentId($id);

        return view('agents/show', ['agent' => $agent, 'compte' => $compte]);
    }

    public function changerStatut(int $id)
    {
        $agent = $this->agentModel->find($id);

        if (! $agent) {
            return redirect()->to('/agents')->with('error', 'Agent introuvable.');
        }

        $nouveauStatut = $this->request->getPost('statut');

        if (! in_array($nouveauStatut, ['ACTIF', 'SUSPENDU', 'BLOQUE'], true)) {
            return redirect()->back()->with('error', 'Statut invalide.');
        }

        $this->agentModel->update($id, ['statut' => $nouveauStatut]);

        $compte = $this->compteModel->findByAgentId($id);
        if ($compte) {
            $this->compteModel->update($compte['id'], ['statut' => $nouveauStatut]);
        }

        (new AuditService())->log('CHANGEMENT_STATUT_AGENT', 'agents', $id, "Nouveau statut : {$nouveauStatut}");

        return redirect()->to('/agents/' . $id)->with('success', 'Statut mis à jour.');
    }
}

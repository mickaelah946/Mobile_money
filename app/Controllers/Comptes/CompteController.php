<?php

namespace App\Controllers\Comptes;

use App\Controllers\BaseController;
use App\Models\CompteModel;
use App\Models\MouvementModel;
use App\Services\AuditService;

class CompteController extends BaseController
{
    protected CompteModel $compteModel;
    protected MouvementModel $mouvementModel;

    public function __construct()
    {
        $this->compteModel   = new CompteModel();
        $this->mouvementModel = new MouvementModel();
    }

    public function index()
    {
        $comptes = $this->compteModel
            ->select('comptes.*, clients.nom as client_nom, clients.prenom as client_prenom, agents.nom as agent_nom, agents.prenom as agent_prenom')
            ->join('clients', 'clients.id = comptes.client_id', 'left')
            ->join('agents', 'agents.id = comptes.agent_id', 'left')
            ->orderBy('comptes.date_creation', 'DESC')
            ->paginate(20);

        return view('comptes/index', [
            'comptes' => $comptes,
            'pager'   => $this->compteModel->pager,
        ]);
    }

    public function show(int $id)
    {
        $compte = $this->compteModel->find($id);

        if (! $compte) {
            return redirect()->to('/comptes')->with('error', 'Compte introuvable.');
        }

        $mouvements = $this->mouvementModel->historiqueCompte($id, 30);

        return view('comptes/show', ['compte' => $compte, 'mouvements' => $mouvements]);
    }

    public function changerStatut(int $id)
    {
        $compte = $this->compteModel->find($id);

        if (! $compte) {
            return redirect()->to('/comptes')->with('error', 'Compte introuvable.');
        }

        $nouveauStatut = $this->request->getPost('statut');

        if (! in_array($nouveauStatut, ['ACTIF', 'SUSPENDU', 'BLOQUE'], true)) {
            return redirect()->back()->with('error', 'Statut invalide.');
        }

        $this->compteModel->update($id, ['statut' => $nouveauStatut]);

        (new AuditService())->log('CHANGEMENT_STATUT_COMPTE', 'comptes', $id, "Nouveau statut : {$nouveauStatut}");

        return redirect()->to('/comptes/' . $id)->with('success', 'Statut du compte mis à jour.');
    }
}

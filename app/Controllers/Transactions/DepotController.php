<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\AgentModel;
use App\Models\ClientModel;
use App\Services\NotificationService;
use App\Services\TransactionService;
use Throwable;

class DepotController extends BaseController
{
    public function index()
    {
        return view('transactions/depot', [
            'clients' => (new ClientModel())->where('statut', 'ACTIF')->orderBy('nom')->findAll(),
            'agents'  => (new AgentModel())->where('statut', 'ACTIF')->orderBy('nom')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'client_id' => 'required|is_natural_no_zero',
            'montant'   => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $client = (new ClientModel())->find((int) $this->request->getPost('client_id'));
        $compte = $client ? (new \App\Models\CompteModel())->findByClientId($client['id']) : null;

        if (! $compte) {
            return redirect()->back()->withInput()->with('error', 'Le client sélectionné n\'a pas de compte associé.');
        }

        $agentId = $this->request->getPost('agent_id') ?: null;

        try {
            $transaction = (new TransactionService())->executerDepot(
                $compte['id'],
                (float) $this->request->getPost('montant'),
                $agentId,
                $this->session->get('utilisateurId'),
                $this->request->getPost('description')
            );

            (new NotificationService())->envoyer([
                'client_id'      => $client['id'],
                'transaction_id' => $transaction['id'],
                'message'        => 'Depot de ' . formatMontant((float) $transaction['montant']) . ' effectue sur votre compte ' . $compte['numero_compte'] . '.',
            ]);
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/transactions/' . $transaction['id'])->with('success', 'Dépôt effectué avec succès.');
    }
}

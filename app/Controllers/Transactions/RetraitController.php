<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\AgentModel;
use App\Models\ClientModel;
use App\Models\CompteModel;
use App\Services\NotificationService;
use App\Services\TransactionService;
use Throwable;

class RetraitController extends BaseController
{
    public function index()
    {
        return view('transactions/retrait', [
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
        $compte = $client ? (new CompteModel())->findByClientId($client['id']) : null;

        if (! $compte) {
            return redirect()->back()->withInput()->with('error', 'Le client sélectionné n\'a pas de compte associé.');
        }

        $agentId = $this->request->getPost('agent_id') ?: null;

        try {
            $transaction = (new TransactionService())->executerRetrait(
                $compte['id'],
                (float) $this->request->getPost('montant'),
                $agentId,
                $this->session->get('utilisateurId'),
                $this->request->getPost('description')
            );

            (new NotificationService())->envoyer([
                'client_id'      => $client['id'],
                'transaction_id' => $transaction['id'],
                'message'        => 'Retrait de ' . formatMontant((float) $transaction['montant']) . ' effectue sur votre compte ' . $compte['numero_compte'] . '.',
            ]);
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/transactions/' . $transaction['id'])->with('success', 'Retrait effectué avec succès.');
    }
}

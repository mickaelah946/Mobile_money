<?php

namespace App\Controllers\Agents;

use App\Controllers\BaseController;
use App\Models\AgentModel;
use App\Models\CompteModel;
use App\Services\NotificationService;
use App\Services\TransactionService;
use Throwable;

class RechargeAgentController extends BaseController
{
    public function show(int $id)
    {
        $agent = (new AgentModel())->find($id);

        if (! $agent) {
            return redirect()->to('/agents')->with('error', 'Agent introuvable.');
        }

        $compte = (new CompteModel())->findByAgentId($id);

        return view('agents/recharge', ['agent' => $agent, 'compte' => $compte]);
    }

    public function store(int $id)
    {
        $rules = ['montant' => 'required|decimal|greater_than[0]'];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $agent  = (new AgentModel())->find($id);
        $compte = $agent ? (new CompteModel())->findByAgentId($id) : null;

        if (! $compte) {
            return redirect()->to('/agents')->with('error', 'Agent ou compte introuvable.');
        }

        try {
            $transaction = (new TransactionService())->executerRechargeAgent(
                $compte['id'],
                (float) $this->request->getPost('montant'),
                $this->session->get('utilisateurId'),
                $this->request->getPost('description')
            );

            (new NotificationService())->envoyer([
                'agent_id'       => $agent['id'],
                'transaction_id' => $transaction['id'],
                'message'        => 'Recharge de ' . formatMontant((float) $transaction['montant']) . ' effectuee sur votre compte flotte ' . $compte['numero_compte'] . '.',
            ]);
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/agents/' . $id)->with('success', 'Flotte rechargée avec succès.');
    }
}

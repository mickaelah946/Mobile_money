<?php

namespace App\Controllers\Rapports;

use App\Controllers\BaseController;
use App\Models\AgentModel;
use App\Models\CompteModel;
use App\Models\ParametreSystemeModel;
use App\Models\TransactionModel;

class RapportController extends BaseController
{
    public function index()
    {
        $agentModel   = new AgentModel();
        $compteModel  = new CompteModel();
        $transactionModel = new TransactionModel();
        $parametreModel = new ParametreSystemeModel();

        $seuilAlerte = (float) $parametreModel->getValeur('SEUIL_ALERTE_FLOTTE_AGENT', 0);

        $comptesAgents = $compteModel
            ->select('comptes.*, agents.nom as agent_nom, agents.prenom as agent_prenom, agents.code_agent')
            ->join('agents', 'agents.id = comptes.agent_id')
            ->where('comptes.type_compte', 'AGENT')
            ->findAll();
        $flotteTotale  = array_sum(array_column($comptesAgents, 'solde'));
        $agentsAlerte  = array_filter($comptesAgents, static fn ($c) => (float) $c['solde'] < $seuilAlerte);

        $repartition = $transactionModel
            ->select('types_transaction.libelle as type_libelle, COUNT(transactions.id) as nombre, SUM(transactions.montant) as volume, SUM(transactions.frais) as frais')
            ->join('types_transaction', 'types_transaction.id = transactions.type_transaction_id')
            ->where('transactions.statut', 'REUSSIE')
            ->groupBy('types_transaction.libelle')
            ->findAll();

        $compteSysteme = $compteModel->findSystemAccount();

        return view('rapports/index', [
            'nbAgentsActifs' => $agentModel->where('statut', 'ACTIF')->countAllResults(),
            'nbAgentsTotal'  => $agentModel->countAllResults(),
            'flotteTotale'   => $flotteTotale,
            'agentsAlerte'   => $agentsAlerte,
            'repartition'    => $repartition,
            'fraisCollectes' => $compteSysteme['solde'] ?? 0,
        ]);
    }
}

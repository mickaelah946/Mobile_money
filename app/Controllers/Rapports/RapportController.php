<?php

namespace App\Controllers\Rapports;

use App\Controllers\BaseController;
use App\Models\AgentModel;
use App\Models\CompteModel;
use App\Models\ParametreSystemeModel;
use App\Models\PrefixeOperateurModel;
use App\Models\TransactionModel;
use App\Models\TypeTransactionModel;

class RapportController extends BaseController
{
    public function index()
    {
        $agentModel        = new AgentModel();
        $compteModel        = new CompteModel();
        $transactionModel   = new TransactionModel();
        $parametreModel      = new ParametreSystemeModel();
        $typeModel             = new TypeTransactionModel();
        $prefixeModel           = new PrefixeOperateurModel();

        $seuilAlerte = (float) $parametreModel->getValeur('SEUIL_ALERTE_FLOTTE_AGENT', 0);

        $comptesAgents = $compteModel
            ->select('comptes.*, agents.nom as agent_nom, agents.prenom as agent_prenom, agents.code_agent')
            ->join('agents', 'agents.id = comptes.agent_id')
            ->where('comptes.type_compte', 'AGENT')
            ->findAll();
        $flotteTotale  = array_sum(array_column($comptesAgents, 'solde'));
        $agentsAlerte  = array_filter($comptesAgents, static fn ($c) => (float) $c['solde'] < $seuilAlerte);

        $repartition = $transactionModel
            ->select('types_transaction.code as type_code, types_transaction.libelle as type_libelle, COUNT(transactions.id) as nombre, SUM(transactions.montant) as volume, SUM(transactions.frais) as frais')
            ->join('types_transaction', 'types_transaction.id = transactions.type_transaction_id')
            ->where('transactions.statut', 'REUSSIE')
            ->groupBy('types_transaction.code')
            ->orderBy('types_transaction.libelle')
            ->findAll();

        $compteSysteme = $compteModel->findSystemAccount();

        // --- V2 : gains via frais separes "notre reseau" / "autres operateurs" ---
        $fraisAutresOperateurs = 0.0;
        $fraisOperateur        = 0.0;
        foreach ($repartition as $r) {
            if ($r['type_code'] === 'TRANSFERT_EXTERNE') {
                $fraisAutresOperateurs += (float) $r['frais'];
            } else {
                $fraisOperateur += (float) $r['frais'];
            }
        }

        // --- V2 : situation des montants a envoyer (regler) a chaque operateur ---
        $typeExterne = $typeModel->findByCode('TRANSFERT_EXTERNE');

        $transfertsExternes = $typeExterne
            ? $transactionModel
                ->where('statut', 'REUSSIE')
                ->where('type_transaction_id', $typeExterne['id'])
                ->findAll()
            : [];

        $montantsParOperateur = [];
        foreach ($transfertsExternes as $t) {
            $numero    = $t['numero_destination_externe'] ?? '';
            $operateur = $prefixeModel->trouverOperateurParNumero($numero);
            $nom       = $operateur['operateur_nom'] ?? ('Opérateur inconnu (préfixe ' . substr($numero, 0, 3) . ')');

            if (! isset($montantsParOperateur[$nom])) {
                $montantsParOperateur[$nom] = ['nombre' => 0, 'montant' => 0.0];
            }
            $montantsParOperateur[$nom]['nombre']++;
            $montantsParOperateur[$nom]['montant'] += (float) $t['montant'];
        }
        ksort($montantsParOperateur);

        return view('rapports/index', [
            'nbAgentsActifs'         => $agentModel->where('statut', 'ACTIF')->countAllResults(),
            'nbAgentsTotal'          => $agentModel->countAllResults(),
            'flotteTotale'           => $flotteTotale,
            'agentsAlerte'           => $agentsAlerte,
            'repartition'            => $repartition,
            'fraisCollectes'         => $compteSysteme['solde'] ?? 0,
            'fraisOperateur'         => $fraisOperateur,
            'fraisAutresOperateurs'  => $fraisAutresOperateurs,
            'montantsParOperateur'   => $montantsParOperateur,
        ]);
    }
}

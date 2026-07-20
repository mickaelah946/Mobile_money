<?php

namespace App\Services;

use App\Libraries\ReferenceGenerator;
use App\Models\CompteModel;
use App\Models\MouvementModel;
use App\Models\TransactionModel;
use App\Models\TypeTransactionModel;
use RuntimeException;
use Throwable;

class TransactionService
{
    protected CompteModel $compteModel;
    protected TransactionModel $transactionModel;
    protected MouvementModel $mouvementModel;
    protected TypeTransactionModel $typeModel;
    protected CompteService $compteService;
    protected TarifService $tarifService;

    public function __construct()
    {
        $this->compteModel      = new CompteModel();
        $this->transactionModel = new TransactionModel();
        $this->mouvementModel   = new MouvementModel();
        $this->typeModel        = new TypeTransactionModel();
        $this->compteService    = new CompteService();
        $this->tarifService     = new TarifService();
    }

    public function executerDepot(int $compteDestinationId, float $montant, ?int $agentId = null, ?int $utilisateurId = null, ?string $description = null): array
    {
        return $this->executer('DEPOT', null, $compteDestinationId, $montant, $agentId, $utilisateurId, $description);
    }

    public function executerRetrait(int $compteSourceId, float $montant, ?int $agentId = null, ?int $utilisateurId = null, ?string $description = null): array
    {
        return $this->executer('RETRAIT', $compteSourceId, null, $montant, $agentId, $utilisateurId, $description);
    }

    public function executerTransfert(int $compteSourceId, int $compteDestinationId, float $montant, ?int $utilisateurId = null, ?string $description = null): array
    {
        return $this->executer('TRANSFERT', $compteSourceId, $compteDestinationId, $montant, null, $utilisateurId, $description);
    }

    public function executerRechargeAgent(int $compteAgentId, float $montant, ?int $utilisateurId = null, ?string $description = null): array
    {
        // Apport de trésorerie externe (cash injecté par l'opérateur) : pas de
        // compte source dans le système, comme pour un dépôt.
        return $this->executer('RECHARGE_AGENT', null, $compteAgentId, $montant, null, $utilisateurId, $description);
    }

    protected function executer(
        string $codeType,
        ?int $compteSourceId,
        ?int $compteDestinationId,
        float $montant,
        ?int $agentId,
        ?int $utilisateurId,
        ?string $description
    ): array {
        $type = $this->typeModel->findByCode($codeType);

        if ($type === null) {
            throw new RuntimeException("Type de transaction inconnu : {$codeType}");
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $frais        = $this->tarifService->calculerFrais((int) $type['id'], $montant);
        $montantTotal = $montant + $frais;

        $transactionId = $this->transactionModel->insert([
            'reference'                 => ReferenceGenerator::transaction(),
            'type_transaction_id'       => $type['id'],
            'compte_source_id'          => $compteSourceId,
            'compte_destination_id'     => $compteDestinationId,
            'montant'                   => $montant,
            'frais'                     => $frais,
            'montant_total'             => $montantTotal,
            'statut'                    => 'EN_ATTENTE',
            'initiateur_utilisateur_id' => $utilisateurId,
            'initiateur_agent_id'       => $agentId,
            'description'               => $description,
            'date_transaction'          => date('Y-m-d H:i:s'),
        ], true);

        try {
            $compteSource      = $compteSourceId ? $this->compteModel->find($compteSourceId) : null;
            $compteDestination = $compteDestinationId ? $this->compteModel->find($compteDestinationId) : null;

            if ($compteSourceId && ! $compteSource) {
                throw new RuntimeException('Compte source introuvable.');
            }
            if ($compteDestinationId && ! $compteDestination) {
                throw new RuntimeException('Compte destination introuvable.');
            }

            // Débit du compte source (montant + frais éventuels)
            if ($compteSource) {
                $avant       = (float) $compteSource['solde'];
                $compteSource = $this->compteService->debiter($compteSource, $montantTotal);
                $this->enregistrerMouvement($transactionId, $compteSource['id'], 'DEBIT', $montantTotal, $avant, (float) $compteSource['solde']);
            }

            // Crédit du compte destination (montant net, hors frais)
            if ($compteDestination) {
                $avant            = (float) $compteDestination['solde'];
                $compteDestination = $this->compteService->crediter($compteDestination, $montant);
                $this->enregistrerMouvement($transactionId, $compteDestination['id'], 'CREDIT', $montant, $avant, (float) $compteDestination['solde']);
            }

            // Les frais collectés alimentent le compte SYSTEME de l'opérateur
            if ($frais > 0) {
                $compteSysteme = $this->compteModel->findSystemAccount();
                if ($compteSysteme) {
                    $avant         = (float) $compteSysteme['solde'];
                    $compteSysteme = $this->compteService->crediter($compteSysteme, $frais);
                    $this->enregistrerMouvement($transactionId, $compteSysteme['id'], 'CREDIT', $frais, $avant, (float) $compteSysteme['solde']);
                }
            }

            $this->transactionModel->update($transactionId, [
                'statut'          => 'REUSSIE',
                'date_traitement' => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();
        } catch (Throwable $e) {
            $db->transRollback();

            $this->transactionModel->update($transactionId, [
                'statut'          => 'ECHOUEE',
                'motif_echec'     => $e->getMessage(),
                'date_traitement' => date('Y-m-d H:i:s'),
            ]);

            throw $e;
        }

        return $this->transactionModel->find($transactionId);
    }

    protected function enregistrerMouvement(int $transactionId, int $compteId, string $sens, float $montant, float $soldeAvant, float $soldeApres): void
    {
        $this->mouvementModel->insert([
            'transaction_id' => $transactionId,
            'compte_id'      => $compteId,
            'sens'           => $sens,
            'montant'        => $montant,
            'solde_avant'    => $soldeAvant,
            'solde_apres'    => $soldeApres,
        ]);
    }
}

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

    /**
     * Transfert entre deux clients internes (memes reseau).
     *
     * @param bool $fraisRetraitInclus V2 : si vrai, l'emetteur paie a l'avance
     *             le frais de retrait que le beneficiaire aurait du payer, qui
     *             est directement credite sur son compte en plus du montant
     *             envoye (il pourra donc retirer la totalite sans frais).
     */
    public function executerTransfert(
        int $compteSourceId,
        int $compteDestinationId,
        float $montant,
        ?int $utilisateurId = null,
        ?string $description = null,
        bool $fraisRetraitInclus = false
    ): array {
        $bonusDestination = 0.0;

        if ($fraisRetraitInclus) {
            $typeRetrait = $this->typeModel->findByCode('RETRAIT');
            if ($typeRetrait !== null) {
                $bonusDestination = $this->tarifService->calculerFrais((int) $typeRetrait['id'], $montant);
            }
        }

        return $this->executer('TRANSFERT', $compteSourceId, $compteDestinationId, $montant, null, $utilisateurId, $description, $bonusDestination);
    }

    /**
     * Transfert vers un numero n'appartenant pas a notre reseau (V2).
     * Il n'y a pas de compte destination dans notre systeme : le montant net
     * "sort" de notre systeme et represente une dette a regler avec l'autre
     * operateur (cf. rapport "montants a envoyer a chaque operateur").
     * Aucune option "frais de retrait inclus" ici : on ne controle pas les
     * frais de retrait de l'autre operateur.
     */
    public function executerTransfertExterne(
        int $compteSourceId,
        string $numeroDestinataire,
        float $montant,
        ?int $utilisateurId = null,
        ?string $description = null
    ): array {
        // Le tarif de base est celui d'un transfert normal (meme grille que
        // TRANSFERT), auquel s'ajoute la commission inter-operateur.
        $typeTransfert   = $this->typeModel->findByCode('TRANSFERT');
        $fraisTransfert  = $typeTransfert ? $this->tarifService->calculerFrais((int) $typeTransfert['id'], $montant) : 0.0;
        $commission      = $this->tarifService->calculerCommissionInterOperateur($montant);
        $fraisSupplementaire = $fraisTransfert + $commission;

        return $this->executer('TRANSFERT_EXTERNE', $compteSourceId, null, $montant, null, $utilisateurId, $description, 0.0, $fraisSupplementaire, $numeroDestinataire);
    }

    public function executerRechargeAgent(int $compteAgentId, float $montant, ?int $utilisateurId = null, ?string $description = null): array
    {
        // Apport de trésorerie externe (cash injecté par l'opérateur) : pas de
        // compte source dans le système, comme pour un dépôt.
        return $this->executer('RECHARGE_AGENT', null, $compteAgentId, $montant, null, $utilisateurId, $description);
    }

    /**
     * @param float $bonusDestination Montant credite au destinataire en plus
     *              du montant envoye (frais de retrait inclus), finance par
     *              un debit supplementaire equivalent sur la source.
     * @param float $fraisSupplementaire Frais additionnel (ex: commission
     *              inter-operateur) credite integralement au compte SYSTEME,
     *              en plus du frais standard de la grille tarifaire.
     */
    protected function executer(
        string $codeType,
        ?int $compteSourceId,
        ?int $compteDestinationId,
        float $montant,
        ?int $agentId,
        ?int $utilisateurId,
        ?string $description,
        float $bonusDestination = 0.0,
        float $fraisSupplementaire = 0.0,
        ?string $numeroDestinationExterne = null
    ): array {
        $type = $this->typeModel->findByCode($codeType);

        if ($type === null) {
            throw new RuntimeException("Type de transaction inconnu : {$codeType}");
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $fraisBase    = $this->tarifService->calculerFrais((int) $type['id'], $montant);
        $frais        = $fraisBase + $fraisSupplementaire;
        $montantTotal = $montant + $frais + $bonusDestination;

        $transactionId = $this->transactionModel->insert([
            'reference'                  => ReferenceGenerator::transaction(),
            'type_transaction_id'        => $type['id'],
            'compte_source_id'           => $compteSourceId,
            'compte_destination_id'      => $compteDestinationId,
            'montant'                    => $montant,
            'frais'                      => $frais,
            'montant_total'              => $montantTotal,
            'statut'                     => 'EN_ATTENTE',
            'initiateur_utilisateur_id'  => $utilisateurId,
            'initiateur_agent_id'        => $agentId,
            'description'                => $description,
            'numero_destination_externe' => $numeroDestinationExterne,
            'date_transaction'           => date('Y-m-d H:i:s'),
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

            // Débit du compte source (montant + frais + bonus destinataire éventuel)
            if ($compteSource) {
                $avant       = (float) $compteSource['solde'];
                $compteSource = $this->compteService->debiter($compteSource, $montantTotal);
                $this->enregistrerMouvement($transactionId, $compteSource['id'], 'DEBIT', $montantTotal, $avant, (float) $compteSource['solde']);
            }

            // Crédit du compte destination (montant net + bonus frais de retrait inclus éventuel)
            if ($compteDestination) {
                $montantCredit    = $montant + $bonusDestination;
                $avant            = (float) $compteDestination['solde'];
                $compteDestination = $this->compteService->crediter($compteDestination, $montantCredit);
                $this->enregistrerMouvement($transactionId, $compteDestination['id'], 'CREDIT', $montantCredit, $avant, (float) $compteDestination['solde']);
            }

            // Les frais collectés (standard + supplémentaire) alimentent le compte SYSTEME
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

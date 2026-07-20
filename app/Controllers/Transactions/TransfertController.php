<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\CompteModel;
use App\Models\PrefixeOperateurModel;
use App\Services\NotificationService;
use App\Services\TransactionService;
use Throwable;

class TransfertController extends BaseController
{
    public function index()
    {
        return view('transactions/transfert', [
            'clients' => (new ClientModel())->where('statut', 'ACTIF')->orderBy('nom')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'client_source_id'    => 'required|is_natural_no_zero',
            'telephone_destinataire' => 'required|min_length[8]',
            'montant'                 => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clientModel = new ClientModel();
        $compteModel = new CompteModel();

        $clientSource = $clientModel->find((int) $this->request->getPost('client_source_id'));
        $compteSource = $clientSource ? $compteModel->findByClientId($clientSource['id']) : null;

        if (! $compteSource) {
            return redirect()->back()->withInput()->with('error', 'Client émetteur ou compte introuvable.');
        }

        $telephoneDestinataire = trim($this->request->getPost('telephone_destinataire'));
        $montant                = (float) $this->request->getPost('montant');
        $description             = $this->request->getPost('description');
        $fraisRetraitInclus       = (bool) $this->request->getPost('frais_retrait_inclus');

        $clientDestination = $clientModel->where('telephone', $telephoneDestinataire)->first();

        try {
            if ($clientDestination) {
                // --- Transfert interne (client de notre reseau) ---
                if ((int) $clientDestination['id'] === (int) $clientSource['id']) {
                    return redirect()->back()->withInput()->with('error', "L'émetteur et le bénéficiaire doivent être différents.");
                }

                $compteDestination = $compteModel->findByClientId($clientDestination['id']);
                if (! $compteDestination) {
                    return redirect()->back()->withInput()->with('error', 'Le bénéficiaire n\'a pas de compte associé.');
                }

                $transaction = (new TransactionService())->executerTransfert(
                    $compteSource['id'],
                    $compteDestination['id'],
                    $montant,
                    $this->session->get('utilisateurId'),
                    $description,
                    $fraisRetraitInclus
                );

                $notificationService = new NotificationService();
                $notificationService->envoyer([
                    'client_id'      => $clientSource['id'],
                    'transaction_id' => $transaction['id'],
                    'message'        => 'Transfert de ' . formatMontant($montant) . ' envoye vers ' . $compteDestination['numero_compte'] . '.',
                ]);
                $notificationService->envoyer([
                    'client_id'      => $clientDestination['id'],
                    'transaction_id' => $transaction['id'],
                    'message'        => 'Transfert recu de ' . formatMontant($montant) . ($fraisRetraitInclus ? ' (frais de retrait deja inclus)' : '') . ' depuis ' . $compteSource['numero_compte'] . '.',
                ]);
            } else {
                // --- Transfert externe (vers un autre operateur) ---
                $operateur = (new PrefixeOperateurModel())->trouverOperateurParNumero($telephoneDestinataire);

                if ($operateur === null) {
                    return redirect()->back()->withInput()->with('error', "Numéro inconnu : aucun client de notre réseau ni préfixe d'un autre opérateur ne correspond.");
                }

                $transaction = (new TransactionService())->executerTransfertExterne(
                    $compteSource['id'],
                    $telephoneDestinataire,
                    $montant,
                    $this->session->get('utilisateurId'),
                    $description
                );

                (new NotificationService())->envoyer([
                    'client_id'      => $clientSource['id'],
                    'transaction_id' => $transaction['id'],
                    'message'        => 'Transfert de ' . formatMontant($montant) . ' envoye vers ' . $telephoneDestinataire . ' (' . $operateur['operateur_nom'] . ').',
                ]);
            }
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/transactions/' . $transaction['id'])->with('success', 'Transfert effectué avec succès.');
    }
}

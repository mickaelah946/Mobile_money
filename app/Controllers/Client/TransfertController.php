<?php

namespace App\Controllers\Client;

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
        $compte = (new CompteModel())->findByClientId((int) $this->session->get('clientId'));

        return view('client/transactions/transfert', ['compte' => $compte]);
    }

    public function store()
    {
        $rules = [
            'telephone_destinataire' => 'required|min_length[8]',
            'montant'                => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clientModel = new ClientModel();
        $compteModel = new CompteModel();

        $clientId     = (int) $this->session->get('clientId');
        $clientSource = $clientModel->find($clientId);
        $compteSource = $clientSource ? $compteModel->findByClientId($clientId) : null;

        if (! $compteSource) {
            return redirect()->back()->withInput()->with('error', 'Votre compte est introuvable. Contactez votre agence.');
        }

        $telephoneDestinataire = trim($this->request->getPost('telephone_destinataire'));
        $montant                = (float) $this->request->getPost('montant');
        $description             = $this->request->getPost('description');
        $fraisRetraitInclus       = (bool) $this->request->getPost('frais_retrait_inclus');

        if ($telephoneDestinataire === $clientSource['telephone']) {
            return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
        }

        $prefixeModel = new PrefixeOperateurModel();
        $estInterne   = $prefixeModel->estInterne($telephoneDestinataire);

        try {
            if ($estInterne) {
                $clientDestination = $clientModel->where('telephone', $telephoneDestinataire)->first();

                if (! $clientDestination) {
                    return redirect()->back()->withInput()->with('error', "Ce numéro appartient à notre opérateur mais ne correspond à aucun client enregistré.");
                }

                $compteDestination = $compteModel->findByClientId($clientDestination['id']);
                if (! $compteDestination) {
                    return redirect()->back()->withInput()->with('error', 'Le bénéficiaire n\'a pas de compte associé.');
                }

                $transaction = (new TransactionService())->executerTransfert(
                    $compteSource['id'],
                    $compteDestination['id'],
                    $montant,
                    null,
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
                $operateur    = $prefixeModel->trouverOperateurParNumero($telephoneDestinataire);
                $nomOperateur = $operateur['operateur_nom'] ?? ('opérateur externe (préfixe ' . substr($telephoneDestinataire, 0, 3) . ')');

                $transaction = (new TransactionService())->executerTransfertExterne(
                    $compteSource['id'],
                    $telephoneDestinataire,
                    $montant,
                    null,
                    $description
                );

                (new NotificationService())->envoyer([
                    'client_id'      => $clientSource['id'],
                    'transaction_id' => $transaction['id'],
                    'message'        => 'Transfert de ' . formatMontant($montant) . ' envoye vers ' . $telephoneDestinataire . ' (' . $nomOperateur . ').',
                ]);
            }
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/client/dashboard')->with('success', 'Transfert effectué avec succès.');
    }
}

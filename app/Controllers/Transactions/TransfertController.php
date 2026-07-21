<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\CompteModel;
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
            'client_source_id'      => 'required|is_natural_no_zero',
            'client_destination_id' => 'required|is_natural_no_zero|differs[client_source_id]',
            'montant'                => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $compteModel = new CompteModel();
        $clientSource      = (new ClientModel())->find((int) $this->request->getPost('client_source_id'));
        $clientDestination = (new ClientModel())->find((int) $this->request->getPost('client_destination_id'));
        $compteSource      = $clientSource ? $compteModel->findByClientId($clientSource['id']) : null;
        $compteDestination = $clientDestination ? $compteModel->findByClientId($clientDestination['id']) : null;

        if (! $compteSource || ! $compteDestination) {
            return redirect()->back()->withInput()->with('error', 'Compte source ou destination introuvable.');
        }

        try {
            $transaction = (new TransactionService())->executerTransfert(
                $compteSource['id'],
                $compteDestination['id'],
                (float) $this->request->getPost('montant'),
                $this->session->get('utilisateurId'),
                $this->request->getPost('description')
            );

            $notificationService = new NotificationService();
            $notificationService->envoyer([
                'client_id'      => $clientSource['id'],
                'transaction_id' => $transaction['id'],
                'message'        => 'Transfert de ' . formatMontant((float) $transaction['montant']) . ' envoye vers ' . $compteDestination['numero_compte'] . '.',
            ]);
            $notificationService->envoyer([
                'client_id'      => $clientDestination['id'],
                'transaction_id' => $transaction['id'],
                'message'        => 'Transfert de ' . formatMontant((float) $transaction['montant']) . ' recu depuis ' . $compteSource['numero_compte'] . '.',
            ]);
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    
        return redirect()->to('/transactions/' . $transaction['id'])->with('success', 'Transfert effectué avec succès.');
    }
}

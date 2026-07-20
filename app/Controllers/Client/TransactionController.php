<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\CompteModel;
use App\Models\TransactionModel;

class TransactionController extends BaseController
{
    public function index()
    {
        $compte = (new CompteModel())->findByClientId((int) $this->session->get('clientId'));

        if (! $compte) {
            return view('client/transactions/index', ['transactions' => [], 'pager' => null, 'compte' => null]);
        }

        $transactionModel = new TransactionModel();

        $transactions = $transactionModel
            ->select('transactions.*, types_transaction.libelle as type_libelle, cs.numero_compte as source_numero, cd.numero_compte as destination_numero')
            ->join('types_transaction', 'types_transaction.id = transactions.type_transaction_id')
            ->join('comptes cs', 'cs.id = transactions.compte_source_id', 'left')
            ->join('comptes cd', 'cd.id = transactions.compte_destination_id', 'left')
            ->groupStart()
                ->where('transactions.compte_source_id', $compte['id'])
                ->orWhere('transactions.compte_destination_id', $compte['id'])
            ->groupEnd()
            ->orderBy('transactions.date_transaction', 'DESC')
            ->paginate(20);

        return view('client/transactions/index', [
            'transactions' => $transactions,
            'pager'        => $transactionModel->pager,
            'compte'       => $compte,
        ]);
    }
}

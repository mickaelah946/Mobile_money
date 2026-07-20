<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\MouvementModel;
use App\Models\TransactionModel;

class TransactionController extends BaseController
{
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
    }

    public function index()
    {
        $transactions = $this->transactionModel
            ->select('transactions.*, types_transaction.libelle as type_libelle, cs.numero_compte as source_numero, cd.numero_compte as destination_numero')
            ->join('types_transaction', 'types_transaction.id = transactions.type_transaction_id')
            ->join('comptes cs', 'cs.id = transactions.compte_source_id', 'left')
            ->join('comptes cd', 'cd.id = transactions.compte_destination_id', 'left')
            ->orderBy('transactions.date_transaction', 'DESC')
            ->paginate(20);

        return view('transactions/index', [
            'transactions' => $transactions,
            'pager'        => $this->transactionModel->pager,
        ]);
    }

    public function show(int $id)
    {
        $transaction = $this->transactionModel
            ->select('transactions.*, types_transaction.libelle as type_libelle, cs.numero_compte as source_numero, cd.numero_compte as destination_numero')
            ->join('types_transaction', 'types_transaction.id = transactions.type_transaction_id')
            ->join('comptes cs', 'cs.id = transactions.compte_source_id', 'left')
            ->join('comptes cd', 'cd.id = transactions.compte_destination_id', 'left')
            ->where('transactions.id', $id)
            ->first();

        if (! $transaction) {
            return redirect()->to('/transactions')->with('error', 'Transaction introuvable.');
        }

        $mouvements = (new MouvementModel())->where('transaction_id', $id)->findAll();

        return view('transactions/show', ['transaction' => $transaction, 'mouvements' => $mouvements]);
    }
}

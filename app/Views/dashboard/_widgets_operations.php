<?php
$clientModel      = new \App\Models\ClientModel();
$transactionModel = new \App\Models\TransactionModel();

$transactionsAujourdhui = $transactionModel
    ->where('statut', 'REUSSIE')
    ->where('date_transaction >=', date('Y-m-d 00:00:00'))
    ->countAllResults();

$volumeTotal = $transactionModel->selectSum('montant')->where('statut', 'REUSSIE')->get()->getRow('montant');
?>
<div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Clients actifs</h6>
            <p class="card-text fs-4"><?= $clientModel->where('statut', 'ACTIF')->countAllResults() ?></p>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Transactions du jour</h6>
            <p class="card-text fs-4"><?= $transactionsAujourdhui ?></p>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Volume transactionnel</h6>
            <p class="card-text fs-4"><?= formatMontant((float) ($volumeTotal ?? 0)) ?></p>
        </div>
    </div>
</div>

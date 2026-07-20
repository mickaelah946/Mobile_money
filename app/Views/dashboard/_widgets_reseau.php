<?php
$agentModel  = new \App\Models\AgentModel();
$compteModel = new \App\Models\CompteModel();
$parametreModel = new \App\Models\ParametreSystemeModel();

$seuilAlerte = (float) $parametreModel->getValeur('SEUIL_ALERTE_FLOTTE_AGENT', 0);
$comptesAgents = $compteModel->where('type_compte', 'AGENT')->findAll();
$flotteTotale = array_sum(array_column($comptesAgents, 'solde'));
$nbAlertes = count(array_filter($comptesAgents, static fn ($c) => (float) $c['solde'] < $seuilAlerte));
?>
<div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Agents actifs</h6>
            <p class="card-text fs-4"><?= $agentModel->where('statut', 'ACTIF')->countAllResults() ?></p>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Flotte totale</h6>
            <p class="card-text fs-4"><?= formatMontant($flotteTotale) ?></p>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Alertes seuils</h6>
            <p class="card-text fs-4 <?= $nbAlertes > 0 ? 'text-danger' : '' ?>"><?= $nbAlertes ?></p>
        </div>
    </div>
</div>

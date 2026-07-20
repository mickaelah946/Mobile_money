<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Rapports</h1>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Agents actifs</h6>
            <p class="fs-4 mb-0"><?= $nbAgentsActifs ?> / <?= $nbAgentsTotal ?></p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Flotte totale</h6>
            <p class="fs-4 mb-0"><?= formatMontant($flotteTotale) ?></p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Agents sous le seuil d'alerte</h6>
            <p class="fs-4 mb-0"><?= count($agentsAlerte) ?></p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Frais collectés (total)</h6>
            <p class="fs-4 mb-0"><?= formatMontant($fraisCollectes) ?></p>
        </div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-primary"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Gains via frais — notre opérateur</h6>
            <p class="fs-4 mb-0"><?= formatMontant($fraisOperateur) ?></p>
            <small class="text-muted">Dépôts, retraits, transferts internes, recharges agent</small>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card border-secondary"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Gains via frais — autres opérateurs</h6>
            <p class="fs-4 mb-0"><?= formatMontant($fraisAutresOperateurs) ?></p>
            <small class="text-muted">Tarif de transfert + commission inter-opérateur sur les transferts externes</small>
        </div></div>
    </div>
</div>

<?php if (! empty($agentsAlerte)): ?>
<div class="card mb-4">
    <div class="card-header bg-warning-subtle">Alertes flotte agent</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Agent</th><th>Solde flotte</th></tr></thead>
            <tbody>
                <?php foreach ($agentsAlerte as $c): ?>
                    <tr>
                        <td><?= esc($c['code_agent'] . ' — ' . $c['agent_nom'] . ' ' . $c['agent_prenom']) ?></td>
                        <td class="text-danger"><?= formatMontant($c['solde']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">Situation des montants à envoyer à chaque opérateur</div>
    <div class="card-body pb-0">
        <p class="text-muted small mb-3">
            Montant net (hors frais) des transferts sortants vers chaque opérateur externe,
            à reverser physiquement pour compenser ces transferts.
        </p>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Opérateur</th><th>Nombre de transferts</th><th>Montant à reverser</th></tr></thead>
            <tbody>
                <?php if (empty($montantsParOperateur)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-4">Aucun transfert vers un autre opérateur pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($montantsParOperateur as $nomOperateur => $donnees): ?>
                        <tr>
                            <td><?= esc($nomOperateur) ?></td>
                            <td><?= (int) $donnees['nombre'] ?></td>
                            <td class="fw-semibold"><?= formatMontant($donnees['montant']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Répartition des transactions réussies par type</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Type</th><th>Nombre</th><th>Volume</th><th>Frais générés</th></tr></thead>
            <tbody>
                <?php if (empty($repartition)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Aucune transaction pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($repartition as $r): ?>
                        <tr>
                            <td><?= esc($r['type_libelle']) ?></td>
                            <td><?= (int) $r['nombre'] ?></td>
                            <td><?= formatMontant($r['volume']) ?></td>
                            <td><?= formatMontant($r['frais']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

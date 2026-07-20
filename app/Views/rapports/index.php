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
            <h6 class="card-subtitle text-muted mb-2">Frais collectés (opérateur)</h6>
            <p class="fs-4 mb-0"><?= formatMontant($fraisCollectes) ?></p>
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

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Transaction <?= esc($transaction['reference']) ?></h1>
    <a href="<?= base_url('transactions') ?>" class="btn btn-outline-secondary">Retour</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <table class="table table-sm mb-0">
            <tr><th style="width:220px">Type</th><td><?= esc($transaction['type_libelle']) ?></td></tr>
            <tr><th>Compte source</th><td><?= esc($transaction['source_numero'] ?? '-') ?></td></tr>
            <tr><th>Compte destination</th><td><?= esc($transaction['destination_numero'] ?? '-') ?></td></tr>
            <tr><th>Montant</th><td><?= formatMontant($transaction['montant']) ?></td></tr>
            <tr><th>Frais</th><td><?= formatMontant($transaction['frais']) ?></td></tr>
            <tr><th>Montant total</th><td><?= formatMontant($transaction['montant_total']) ?></td></tr>
            <tr><th>Statut</th><td><span class="badge bg-secondary"><?= esc($transaction['statut']) ?></span></td></tr>
            <?php if (! empty($transaction['motif_echec'])): ?>
                <tr><th>Motif d'échec</th><td class="text-danger"><?= esc($transaction['motif_echec']) ?></td></tr>
            <?php endif; ?>
            <tr><th>Description</th><td><?= esc($transaction['description'] ?? '-') ?></td></tr>
            <tr><th>Date</th><td><?= formatDate($transaction['date_transaction']) ?></td></tr>
            <tr><th>Traitée le</th><td><?= formatDate($transaction['date_traitement']) ?></td></tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Écritures comptables (partie double)</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr><th>Compte</th><th>Sens</th><th>Montant</th><th>Solde avant</th><th>Solde après</th></tr>
            </thead>
            <tbody>
                <?php foreach ($mouvements as $mvt): ?>
                    <tr>
                        <td><?= esc($mvt['compte_id']) ?></td>
                        <td><span class="badge bg-<?= $mvt['sens'] === 'CREDIT' ? 'success' : 'danger' ?>"><?= esc($mvt['sens']) ?></span></td>
                        <td><?= formatMontant($mvt['montant']) ?></td>
                        <td><?= formatMontant($mvt['solde_avant']) ?></td>
                        <td><?= formatMontant($mvt['solde_apres']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

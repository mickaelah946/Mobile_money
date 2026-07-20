<?= $this->extend('client/layouts/main') ?>
<?= $this->section('content') ?>

<?php $client = currentClient(); ?>

<h1 class="h4 mb-4">Bonjour <?= esc($client['prenom']) ?> 👋</h1>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Solde disponible</h6>
                <p class="display-6 mb-0 text-success"><?= formatMontant($compte['solde'] ?? 0) ?></p>
                <?php if ($compte): ?>
                    <small class="text-muted">Compte <?= esc($compte['numero_compte']) ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-3">Actions rapides</h6>
                <a href="<?= base_url('client/transfert') ?>" class="btn btn-success me-2">Transférer de l'argent</a>
                <a href="<?= base_url('client/transfert-multiple') ?>" class="btn btn-outline-success">Envoi multiple</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Dernières opérations
        <a href="<?= base_url('client/transactions') ?>" class="small">Voir tout l'historique</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Date</th><th>Sens</th><th>Montant</th></tr></thead>
            <tbody>
                <?php if (empty($mouvements)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-4">Aucune opération pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($mouvements as $mvt): ?>
                        <tr>
                            <td><?= formatDate($mvt['date_mouvement']) ?></td>
                            <td><span class="badge bg-<?= $mvt['sens'] === 'CREDIT' ? 'success' : 'danger' ?>"><?= $mvt['sens'] === 'CREDIT' ? 'Reçu' : 'Envoyé' ?></span></td>
                            <td><?= formatMontant($mvt['montant']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

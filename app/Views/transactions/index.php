<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Transactions</h1>
    <div>
        <a href="<?= base_url('transactions/depot') ?>" class="btn btn-success">+ Dépôt</a>
        <a href="<?= base_url('transactions/retrait') ?>" class="btn btn-warning">+ Retrait</a>
        <a href="<?= base_url('transactions/transfert') ?>" class="btn btn-primary">+ Transfert</a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Montant</th>
                    <th>Frais</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Aucune transaction pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?= esc($t['reference']) ?></td>
                            <td><?= esc($t['type_libelle']) ?></td>
                            <td><?= esc($t['source_numero'] ?? '-') ?></td>
                            <td><?= esc($t['destination_numero'] ?? '-') ?></td>
                            <td><?= formatMontant($t['montant']) ?></td>
                            <td><?= formatMontant($t['frais']) ?></td>
                            <td>
                                <?php
                                    $badge = match ($t['statut']) {
                                        'REUSSIE' => 'success',
                                        'EN_ATTENTE' => 'secondary',
                                        'ANNULEE' => 'warning',
                                        default => 'danger',
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= esc($t['statut']) ?></span>
                            </td>
                            <td><?= formatDate($t['date_transaction']) ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('transactions/' . $t['id']) ?>" class="btn btn-sm btn-outline-secondary">Détail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('partials/pagination') ?>

<?= $this->endSection() ?>

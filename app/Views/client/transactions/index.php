<?= $this->extend('client/layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Historique de mes opérations</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Sens</th>
                    <th>Montant</th>
                    <th>Frais</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucune opération pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($transactions as $t): ?>
                        <?php $envoye = $compte && (int) $t['compte_source_id'] === (int) $compte['id']; ?>
                        <tr>
                            <td><?= formatDate($t['date_transaction']) ?></td>
                            <td><?= esc($t['type_libelle']) ?></td>
                            <td>
                                <span class="badge bg-<?= $envoye ? 'danger' : 'success' ?>"><?= $envoye ? 'Envoyé' : 'Reçu' ?></span>
                            </td>
                            <td><?= formatMontant($t['montant']) ?></td>
                            <td><?= $envoye ? formatMontant($t['frais']) : '-' ?></td>
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
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pager): ?>
    <div class="d-flex justify-content-center mt-3"><?= $pager->links() ?></div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Journal d'audit</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Cible</th>
                    <th>Détails</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucune activité enregistrée.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= formatDate($log['date_action']) ?></td>
                            <td><?= esc(trim(($log['utilisateur_nom'] ?? '') . ' ' . ($log['utilisateur_prenom'] ?? '')) ?: '-') ?></td>
                            <td><span class="badge bg-secondary"><?= esc($log['action']) ?></span></td>
                            <td><?= esc(($log['cible_table'] ?? '') . ($log['cible_id'] ? ' #' . $log['cible_id'] : '')) ?></td>
                            <td class="small text-muted"><?= esc($log['details'] ?? '-') ?></td>
                            <td class="small text-muted"><?= esc($log['adresse_ip'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('partials/pagination') ?>

<?= $this->endSection() ?>

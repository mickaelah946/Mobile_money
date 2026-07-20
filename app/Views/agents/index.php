<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Agents</h1>
    <a href="<?= base_url('agents/create') ?>" class="btn btn-primary">+ Nouvel agent</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Code agent</th>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Zone</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($agents)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun agent pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($agents as $agent): ?>
                        <tr>
                            <td><?= esc($agent['code_agent']) ?></td>
                            <td><?= esc($agent['nom'] . ' ' . $agent['prenom']) ?></td>
                            <td><?= esc($agent['telephone']) ?></td>
                            <td><?= esc($agent['zone'] ?? '-') ?></td>
                            <td>
                                <?php
                                    $badge = match ($agent['statut']) {
                                        'ACTIF' => 'success',
                                        'SUSPENDU' => 'warning',
                                        default => 'danger',
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= esc($agent['statut']) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('agents/' . $agent['id']) ?>" class="btn btn-sm btn-outline-secondary">Voir</a>
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

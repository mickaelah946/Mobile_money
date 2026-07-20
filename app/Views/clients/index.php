<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Clients</h1>
    <a href="<?= base_url('clients/create') ?>" class="btn btn-primary">+ Nouveau client</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>N° client</th>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun client pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= esc($client['numero_client']) ?></td>
                            <td><?= esc($client['nom'] . ' ' . $client['prenom']) ?></td>
                            <td><?= esc($client['telephone']) ?></td>
                            <td>
                                <?php
                                    $badge = match ($client['statut']) {
                                        'ACTIF' => 'success',
                                        'SUSPENDU' => 'warning',
                                        default => 'danger',
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= esc($client['statut']) ?></span>
                            </td>
                            <td><?= formatDate($client['date_creation']) ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('clients/' . $client['id']) ?>" class="btn btn-sm btn-outline-secondary">Voir</a>
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

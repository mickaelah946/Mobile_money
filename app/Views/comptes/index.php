<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3">Comptes</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>N° compte</th>
                    <th>Type</th>
                    <th>Propriétaire</th>
                    <th>Solde</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comptes)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun compte pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($comptes as $compte): ?>
                        <?php
                            $proprietaire = match ($compte['type_compte']) {
                                'CLIENT' => trim(($compte['client_nom'] ?? '') . ' ' . ($compte['client_prenom'] ?? '')),
                                'AGENT'  => trim(($compte['agent_nom'] ?? '') . ' ' . ($compte['agent_prenom'] ?? '')),
                                default  => 'Compte système',
                            };
                        ?>
                        <tr>
                            <td><?= esc($compte['numero_compte']) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($compte['type_compte']) ?></span></td>
                            <td><?= esc($proprietaire ?: '-') ?></td>
                            <td><?= formatMontant($compte['solde']) ?></td>
                            <td>
                                <?php
                                    $badge = match ($compte['statut']) {
                                        'ACTIF' => 'success',
                                        'SUSPENDU' => 'warning',
                                        default => 'danger',
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= esc($compte['statut']) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('comptes/' . $compte['id']) ?>" class="btn btn-sm btn-outline-secondary">Voir</a>
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

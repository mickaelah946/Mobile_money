<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Utilisateurs internes</h1>
    <a href="<?= base_url('utilisateurs/create') ?>" class="btn btn-primary">+ Nouvel utilisateur</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($utilisateurs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun utilisateur.</td></tr>
                <?php else: ?>
                    <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td><?= esc($u['matricule']) ?></td>
                            <td><?= esc($u['nom'] . ' ' . $u['prenom']) ?></td>
                            <td><?= esc($u['email']) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($u['role_libelle']) ?></span></td>
                            <td>
                                <?php
                                    $badge = match ($u['statut']) {
                                        'ACTIF' => 'success',
                                        'SUSPENDU' => 'warning',
                                        default => 'danger',
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= esc($u['statut']) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('utilisateurs/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                <?= form_open('utilisateurs/' . $u['id'] . '/statut', ['class' => 'd-inline']) ?>
                                    <select name="statut" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <?php foreach (['ACTIF', 'SUSPENDU', 'INACTIF'] as $statut): ?>
                                            <option value="<?= $statut ?>" <?= $u['statut'] === $statut ? 'selected' : '' ?>><?= $statut ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?= form_close() ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Préfixes des autres opérateurs</h1>
    <a href="<?= base_url('parametres/prefixes/create') ?>" class="btn btn-primary">+ Nouveau préfixe</a>
</div>

<p class="text-muted">
    Un numéro dont le préfixe n'apparaît pas dans cette liste est considéré comme appartenant à notre propre réseau.
</p>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Préfixe</th>
                    <th>Opérateur</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($prefixes)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Aucun préfixe configuré.</td></tr>
                <?php else: ?>
                    <?php foreach ($prefixes as $prefixe): ?>
                        <tr>
                            <td><?= esc($prefixe['prefixe']) ?></td>
                            <td><?= esc($prefixe['operateur_nom']) ?></td>
                            <td>
                                <span class="badge bg-<?= $prefixe['actif'] ? 'success' : 'secondary' ?>"><?= $prefixe['actif'] ? 'Actif' : 'Inactif' ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('parametres/prefixes/' . $prefixe['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                <?= form_open('parametres/prefixes/' . $prefixe['id'] . '/statut', ['class' => 'd-inline']) ?>
                                    <button type="submit" class="btn btn-sm btn-outline-warning" data-confirm="Confirmer le changement de statut ?">
                                        <?= $prefixe['actif'] ? 'Désactiver' : 'Activer' ?>
                                    </button>
                                <?= form_close() ?>
                                <?= form_open('parametres/prefixes/' . $prefixe['id'] . '/delete', ['class' => 'd-inline']) ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Supprimer définitivement ce préfixe ?">
                                        Supprimer
                                    </button>
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
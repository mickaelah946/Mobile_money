<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Grille tarifaire</h1>
    <a href="<?= base_url('tarifs/create') ?>" class="btn btn-primary">+ Nouveau tarif</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Tranche</th>
                    <th>Frais fixe</th>
                    <th>Frais %</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tarifs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun tarif défini.</td></tr>
                <?php else: ?>
                    <?php foreach ($tarifs as $tarif): ?>
                        <tr>
                            <td><?= esc($tarif['type_libelle']) ?></td>
                            <td><?= formatMontant($tarif['montant_min']) ?> — <?= formatMontant($tarif['montant_max']) ?></td>
                            <td><?= formatMontant($tarif['frais_fixe']) ?></td>
                            <td><?= esc($tarif['frais_pourcentage']) ?> %</td>
                            <td>
                                <span class="badge bg-<?= $tarif['actif'] ? 'success' : 'secondary' ?>"><?= $tarif['actif'] ? 'Actif' : 'Inactif' ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('tarifs/' . $tarif['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                <?= form_open('tarifs/' . $tarif['id'] . '/statut', ['class' => 'd-inline']) ?>
                                    <button type="submit" class="btn btn-sm btn-outline-warning" data-confirm="Confirmer le changement de statut ?">
                                        <?= $tarif['actif'] ? 'Désactiver' : 'Activer' ?>
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

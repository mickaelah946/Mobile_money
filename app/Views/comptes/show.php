<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Compte <?= esc($compte['numero_compte']) ?></h1>
    <a href="<?= base_url('comptes') ?>" class="btn btn-outline-secondary">Retour</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Solde</h6>
            <p class="fs-4 mb-0"><?= formatMontant($compte['solde']) ?></p>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Plafond</h6>
            <p class="fs-4 mb-0"><?= formatMontant($compte['plafond']) ?></p>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Statut</h6>
            <?= form_open('comptes/' . $compte['id'] . '/statut') ?>
            <div class="input-group">
                <select name="statut" class="form-select form-select-sm">
                    <?php foreach (['ACTIF', 'SUSPENDU', 'BLOQUE'] as $statut): ?>
                        <option value="<?= $statut ?>" <?= $compte['statut'] === $statut ? 'selected' : '' ?>><?= $statut ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-warning" data-confirm="Confirmer le changement de statut ?">OK</button>
            </div>
            <?= form_close() ?>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">Historique des mouvements</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Sens</th>
                    <th>Montant</th>
                    <th>Solde avant</th>
                    <th>Solde après</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mouvements)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun mouvement pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($mouvements as $mvt): ?>
                        <tr>
                            <td><?= formatDate($mvt['date_mouvement']) ?></td>
                            <td>
                                <span class="badge bg-<?= $mvt['sens'] === 'CREDIT' ? 'success' : 'danger' ?>"><?= esc($mvt['sens']) ?></span>
                            </td>
                            <td><?= formatMontant($mvt['montant']) ?></td>
                            <td><?= formatMontant($mvt['solde_avant']) ?></td>
                            <td><?= formatMontant($mvt['solde_apres']) ?></td>
                            <td>
                                <a href="<?= base_url('transactions/' . $mvt['transaction_id']) ?>" class="btn btn-sm btn-link">Détail transaction</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><?= esc($agent['nom'] . ' ' . $agent['prenom']) ?></h1>
    <div>
        <a href="<?= base_url('agents/' . $agent['id'] . '/edit') ?>" class="btn btn-outline-secondary">Modifier</a>
        <a href="<?= base_url('agents') ?>" class="btn btn-outline-secondary">Retour</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h6 text-muted">Informations agent</h2>
                <table class="table table-sm mb-0">
                    <tr><th>Code agent</th><td><?= esc($agent['code_agent']) ?></td></tr>
                    <tr><th>Téléphone</th><td><?= esc($agent['telephone']) ?></td></tr>
                    <tr><th>Zone</th><td><?= esc($agent['zone'] ?? '-') ?></td></tr>
                    <tr><th>Adresse</th><td><?= esc($agent['adresse'] ?? '-') ?></td></tr>
                    <tr><th>Statut</th><td><span class="badge bg-secondary"><?= esc($agent['statut']) ?></span></td></tr>
                    <tr><th>Créé le</th><td><?= formatDate($agent['date_creation']) ?></td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h6 text-muted">Compte flotte</h2>
                <?php if ($compte): ?>
                    <table class="table table-sm mb-3">
                        <tr><th>N° compte</th><td><?= esc($compte['numero_compte']) ?></td></tr>
                        <tr><th>Solde flotte</th><td><?= formatMontant($compte['solde']) ?></td></tr>
                        <tr><th>Statut</th><td><span class="badge bg-secondary"><?= esc($compte['statut']) ?></span></td></tr>
                    </table>
                    <a href="<?= base_url('agents/' . $agent['id'] . '/recharge') ?>" class="btn btn-sm btn-success">Recharger la flotte</a>
                    <a href="<?= base_url('comptes/' . $compte['id']) ?>" class="btn btn-sm btn-outline-primary">Historique du compte</a>
                <?php else: ?>
                    <p class="text-muted mb-0">Aucun compte associé.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h2 class="h6 text-muted">Changer le statut</h2>
                <?= form_open('agents/' . $agent['id'] . '/statut') ?>
                <div class="input-group">
                    <select name="statut" class="form-select">
                        <?php foreach (['ACTIF', 'SUSPENDU', 'BLOQUE'] as $statut): ?>
                            <option value="<?= $statut ?>" <?= $agent['statut'] === $statut ? 'selected' : '' ?>><?= $statut ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-outline-warning" data-confirm="Confirmer le changement de statut ?">Appliquer</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

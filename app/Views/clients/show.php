<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><?= esc($client['nom'] . ' ' . $client['prenom']) ?></h1>
    <div>
        <a href="<?= base_url('clients/' . $client['id'] . '/edit') ?>" class="btn btn-outline-secondary">Modifier</a>
        <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary">Retour</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h6 text-muted">Informations client</h2>
                <table class="table table-sm mb-0">
                    <tr><th>N° client</th><td><?= esc($client['numero_client']) ?></td></tr>
                    <tr><th>Téléphone</th><td><?= esc($client['telephone']) ?></td></tr>
                    <tr><th>Pièce</th><td><?= esc(($client['type_piece'] ?? '-') . ' ' . ($client['numero_piece'] ?? '')) ?></td></tr>
                    <tr><th>Date de naissance</th><td><?= esc($client['date_naissance'] ?? '-') ?></td></tr>
                    <tr><th>Adresse</th><td><?= esc($client['adresse'] ?? '-') ?></td></tr>
                    <tr><th>Statut</th><td><span class="badge bg-secondary"><?= esc($client['statut']) ?></span></td></tr>
                    <tr><th>Créé le</th><td><?= formatDate($client['date_creation']) ?></td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h6 text-muted">Compte associé</h2>
                <?php if ($compte): ?>
                    <table class="table table-sm mb-3">
                        <tr><th>N° compte</th><td><?= esc($compte['numero_compte']) ?></td></tr>
                        <tr><th>Solde</th><td><?= formatMontant($compte['solde']) ?></td></tr>
                        <tr><th>Plafond</th><td><?= formatMontant($compte['plafond']) ?></td></tr>
                        <tr><th>Statut</th><td><span class="badge bg-secondary"><?= esc($compte['statut']) ?></span></td></tr>
                    </table>
                    <a href="<?= base_url('comptes/' . $compte['id']) ?>" class="btn btn-sm btn-outline-primary">Voir l'historique du compte</a>
                <?php else: ?>
                    <p class="text-muted mb-0">Aucun compte associé.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h2 class="h6 text-muted">Changer le statut</h2>
                <?= form_open('clients/' . $client['id'] . '/statut') ?>
                <div class="input-group">
                    <select name="statut" class="form-select">
                        <?php foreach (['ACTIF', 'SUSPENDU', 'BLOQUE'] as $statut): ?>
                            <option value="<?= $statut ?>" <?= $client['statut'] === $statut ? 'selected' : '' ?>><?= $statut ?></option>
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

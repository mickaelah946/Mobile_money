<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Recharger la flotte — <?= esc($agent['nom'] . ' ' . $agent['prenom']) ?></h1>

<?php if (session('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <p>Solde flotte actuel : <strong><?= formatMontant($compte['solde'] ?? 0) ?></strong></p>

        <?= form_open('agents/' . $agent['id'] . '/recharge') ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Montant à recharger</label>
                <input type="number" step="0.01" min="0.01" name="montant" class="form-control" value="<?= esc(old('montant')) ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Description (optionnel)</label>
                <input type="text" name="description" class="form-control" value="<?= esc(old('description')) ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Recharger</button>
            <a href="<?= base_url('agents/' . $agent['id']) ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>

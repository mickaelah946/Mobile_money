<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Nouveau tarif</h1>

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
        <?= form_open('tarifs') ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Type de transaction</label>
                <select name="type_transaction_id" class="form-select" required>
                    <option value="">— Choisir —</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= esc($type['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Montant min</label>
                <input type="number" step="0.01" name="montant_min" class="form-control" value="<?= esc(old('montant_min')) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Montant max</label>
                <input type="number" step="0.01" name="montant_max" class="form-control" value="<?= esc(old('montant_max')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Frais fixe</label>
                <input type="number" step="0.01" name="frais_fixe" class="form-control" value="<?= esc(old('frais_fixe') ?? '0') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Frais en pourcentage (%)</label>
                <input type="number" step="0.01" name="frais_pourcentage" class="form-control" value="<?= esc(old('frais_pourcentage') ?? '0') ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Créer le tarif</button>
            <a href="<?= base_url('tarifs') ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>

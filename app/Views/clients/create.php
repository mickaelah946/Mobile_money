<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Nouveau client</h1>

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
        <?= form_open('clients') ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= esc(old('nom')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= esc(old('prenom')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Téléphone</label>
                <input type="text" name="telephone" class="form-control" value="<?= esc(old('telephone')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Type de pièce</label>
                <select name="type_piece" class="form-select">
                    <option value="">—</option>
                    <option value="CNI">CNI</option>
                    <option value="PASSEPORT">Passeport</option>
                    <option value="PERMIS">Permis</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Numéro de pièce</label>
                <input type="text" name="numero_piece" class="form-control" value="<?= esc(old('numero_piece')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control" value="<?= esc(old('date_naissance')) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Adresse</label>
                <textarea name="adresse" class="form-control" rows="2"><?= esc(old('adresse')) ?></textarea>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Créer le client</button>
            <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>

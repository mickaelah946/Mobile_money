<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Modifier le client <?= esc($client['numero_client']) ?></h1>

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
        <?= form_open('clients/' . $client['id']) ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= esc(old('nom') ?? $client['nom']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= esc(old('prenom') ?? $client['prenom']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Téléphone</label>
                <input type="text" name="telephone" class="form-control" value="<?= esc(old('telephone') ?? $client['telephone']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Type de pièce</label>
                <select name="type_piece" class="form-select">
                    <option value="">—</option>
                    <?php foreach (['CNI', 'PASSEPORT', 'PERMIS'] as $type): ?>
                        <option value="<?= $type ?>" <?= $client['type_piece'] === $type ? 'selected' : '' ?>><?= $type ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Numéro de pièce</label>
                <input type="text" name="numero_piece" class="form-control" value="<?= esc(old('numero_piece') ?? $client['numero_piece']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control" value="<?= esc(old('date_naissance') ?? $client['date_naissance']) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Adresse</label>
                <textarea name="adresse" class="form-control" rows="2"><?= esc(old('adresse') ?? $client['adresse']) ?></textarea>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="<?= base_url('clients/' . $client['id']) ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>

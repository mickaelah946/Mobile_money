<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Nouveau préfixe opérateur</h1>

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
        <?= form_open('parametres/prefixes') ?>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Préfixe</label>
                <input type="text" name="prefixe" class="form-control" placeholder="ex: 032" value="<?= esc(old('prefixe')) ?>" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Nom de l'opérateur</label>
                <input type="text" name="operateur_nom" class="form-control" placeholder="ex: Orange Money" value="<?= esc(old('operateur_nom')) ?>" required>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Créer le préfixe</button>
            <a href="<?= base_url('parametres/prefixes') ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
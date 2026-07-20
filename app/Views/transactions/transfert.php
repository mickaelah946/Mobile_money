<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Nouveau transfert</h1>

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
        <?= form_open('transactions/transfert') ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Client émetteur</label>
                <select name="client_source_id" class="form-select" required>
                    <option value="">— Choisir un client —</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>"><?= esc($client['numero_client'] . ' — ' . $client['nom'] . ' ' . $client['prenom'] . ' (' . $client['telephone'] . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Client bénéficiaire</label>
                <select name="client_destination_id" class="form-select" required>
                    <option value="">— Choisir un client —</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>"><?= esc($client['numero_client'] . ' — ' . $client['nom'] . ' ' . $client['prenom'] . ' (' . $client['telephone'] . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Montant</label>
                <input type="number" step="0.01" min="0.01" name="montant" class="form-control" value="<?= esc(old('montant')) ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Description (optionnel)</label>
                <input type="text" name="description" class="form-control" value="<?= esc(old('description')) ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Valider le transfert</button>
            <a href="<?= base_url('transactions') ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>

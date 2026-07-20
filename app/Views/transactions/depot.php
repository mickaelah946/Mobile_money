<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Nouveau dépôt</h1>

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
        <?= form_open('transactions/depot') ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Client</label>
                <select name="client_id" class="form-select" required>
                    <option value="">— Choisir un client —</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>"><?= esc($client['numero_client'] . ' — ' . $client['nom'] . ' ' . $client['prenom'] . ' (' . $client['telephone'] . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Agent (optionnel)</label>
                <select name="agent_id" class="form-select">
                    <option value="">— Opération guichet —</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?= $agent['id'] ?>"><?= esc($agent['code_agent'] . ' — ' . $agent['nom'] . ' ' . $agent['prenom']) ?></option>
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
            <button type="submit" class="btn btn-success">Valider le dépôt</button>
            <a href="<?= base_url('transactions') ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>

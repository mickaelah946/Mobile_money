<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Envoi multiple vers plusieurs numéros</h1>

<div class="alert alert-info">
    Réservé aux bénéficiaires de notre propre réseau. Le montant total saisi
    est divisé automatiquement à parts égales entre tous les numéros.
</div>

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
        <?= form_open('transactions/transfert-multiple') ?>
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
                <label class="form-label">Montant total à répartir</label>
                <input type="number" step="0.01" min="0.01" name="montant_total" class="form-control" value="<?= esc(old('montant_total')) ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label">Numéros bénéficiaires (2 minimum)</label>
                <div id="numeros-container">
                    <div class="input-group mb-2">
                        <input type="text" name="numeros[]" class="form-control" placeholder="Ex: 0331234567" required>
                        <button type="button" class="btn btn-outline-danger btn-remove-numero" disabled>&times;</button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" name="numeros[]" class="form-control" placeholder="Ex: 0339876543" required>
                        <button type="button" class="btn btn-outline-danger btn-remove-numero" disabled>&times;</button>
                    </div>
                </div>
                <button type="button" id="btn-add-numero" class="btn btn-sm btn-outline-secondary">+ Ajouter un numéro</button>
            </div>

            <div class="col-12">
                <label class="form-label">Description (optionnel)</label>
                <input type="text" name="description" class="form-control" value="<?= esc(old('description')) ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Valider l'envoi multiple</button>
            <a href="<?= base_url('transactions') ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
        <?= form_close() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('numeros-container');
    var addBtn = document.getElementById('btn-add-numero');

    function refreshRemoveButtons() {
        var rows = container.querySelectorAll('.input-group');
        rows.forEach(function (row) {
            row.querySelector('.btn-remove-numero').disabled = rows.length <= 2;
        });
    }

    addBtn.addEventListener('click', function () {
        var row = document.createElement('div');
        row.className = 'input-group mb-2';
        row.innerHTML = '<input type="text" name="numeros[]" class="form-control" placeholder="Ex: 0331234567" required>' +
            '<button type="button" class="btn btn-outline-danger btn-remove-numero">&times;</button>';
        container.appendChild(row);
        refreshRemoveButtons();
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-numero')) {
            e.target.closest('.input-group').remove();
            refreshRemoveButtons();
        }
    });
});
</script>

<?= $this->endSection() ?>

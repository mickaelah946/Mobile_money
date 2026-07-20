<?= $this->extend('client/layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Envoi multiple</h1>

<div class="alert alert-info">
    Réservé aux bénéficiaires de notre propre opérateur. Le montant total saisi
    est divisé automatiquement à parts égales entre tous les numéros.
</div>

<p class="text-muted">
    Solde disponible : <strong><?= formatMontant($compte['solde'] ?? 0) ?></strong>
</p>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <?= form_open('client/transfert-multiple') ?>
        <div class="mb-3">
            <label class="form-label">Montant total à répartir</label>
            <input type="number" step="0.01" min="0.01" name="montant_total" class="form-control" value="<?= esc(old('montant_total')) ?>" required>
        </div>

        <label class="form-label">Numéros bénéficiaires (2 minimum)</label>
        <div id="numeros-container">
            <div class="input-group mb-2">
                <input type="text" name="numeros[]" class="form-control" placeholder="Ex: 0311234567" required>
                <button type="button" class="btn btn-outline-danger btn-remove-numero" disabled>&times;</button>
            </div>
            <div class="input-group mb-2">
                <input type="text" name="numeros[]" class="form-control" placeholder="Ex: 0319876543" required>
                <button type="button" class="btn btn-outline-danger btn-remove-numero" disabled>&times;</button>
            </div>
        </div>
        <button type="button" id="btn-add-numero" class="btn btn-sm btn-outline-secondary mb-3">+ Ajouter un numéro</button>

        <div class="mb-3">
            <label class="form-label">Description (optionnel)</label>
            <input type="text" name="description" class="form-control" value="<?= esc(old('description')) ?>">
        </div>

        <button type="submit" class="btn btn-success">Valider l'envoi multiple</button>
        <a href="<?= base_url('client/dashboard') ?>" class="btn btn-outline-secondary">Annuler</a>
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
        row.innerHTML = '<input type="text" name="numeros[]" class="form-control" placeholder="Ex: 0311234567" required>' +
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

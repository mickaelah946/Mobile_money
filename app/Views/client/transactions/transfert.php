<?= $this->extend('client/layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Transférer de l'argent</h1>

<p class="text-muted">
    Solde disponible : <strong><?= formatMontant($compte['solde'] ?? 0) ?></strong>
</p>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <?= form_open('client/transfert') ?>
        <div class="mb-3">
            <label class="form-label">Numéro du bénéficiaire</label>
            <input type="text" name="telephone_destinataire" class="form-control" value="<?= esc(old('telephone_destinataire')) ?>" placeholder="Ex: 0311234567" required>
            <div class="form-text">Un numéro d'un autre opérateur est détecté automatiquement (frais différents, pas d'option "frais de retrait inclus").</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Montant</label>
            <input type="number" step="0.01" min="0.01" name="montant" class="form-control" value="<?= esc(old('montant')) ?>" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="frais_retrait_inclus" value="1" class="form-check-input" id="fraisRetraitInclus">
            <label class="form-check-label" for="fraisRetraitInclus">
                Inclure les frais de retrait du bénéficiaire (uniquement si c'est un client de notre opérateur)
            </label>
        </div>
        <div class="mb-3">
            <label class="form-label">Description (optionnel)</label>
            <input type="text" name="description" class="form-control" value="<?= esc(old('description')) ?>">
        </div>

        <button type="submit" class="btn btn-success">Envoyer</button>
        <a href="<?= base_url('client/dashboard') ?>" class="btn btn-outline-secondary">Annuler</a>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Paramètres système</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Clé</th>
                    <th>Description</th>
                    <th>Valeur</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parametres as $parametre): ?>
                    <tr>
                        <td><code><?= esc($parametre['cle']) ?></code></td>
                        <td class="text-muted small"><?= esc($parametre['description']) ?></td>
                        <td colspan="2">
                            <?= form_open('parametres/' . $parametre['id'], ['class' => 'd-flex gap-2']) ?>
                                <input type="text" name="valeur" class="form-control form-control-sm" value="<?= esc($parametre['valeur']) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">Enregistrer</button>
                            <?= form_close() ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

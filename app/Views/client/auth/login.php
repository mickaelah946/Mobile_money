<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace — Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-success-subtle d-flex align-items-center" style="min-height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h1 class="h4 mb-1 text-center">Mon espace Mobile Money</h1>
                        <p class="text-muted text-center small mb-4">Entrez votre numéro pour accéder à votre compte</p>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>

                        <?= form_open('client/login') ?>
                        <div class="mb-3">
                            <label class="form-label">Numéro de téléphone</label>
                            <input type="text" name="telephone" class="form-control form-control-lg" placeholder="Ex: 0311234567" value="<?= esc(old('telephone')) ?>" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100">Accéder à mon compte</button>
                        <?= form_close() ?>

                        <hr class="my-4">
                        <p class="text-center small mb-0">
                            <a href="<?= base_url('login') ?>">Espace opérateur (personnel)</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

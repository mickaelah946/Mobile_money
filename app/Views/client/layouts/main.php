<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Mon espace — Mobile Money') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success px-3">
        <a class="navbar-brand mb-0 h1" href="<?= base_url('client/dashboard') ?>">Mon espace Mobile Money</a>
        <?php $client = currentClient(); ?>
        <?php if ($client): ?>
            <div class="d-flex align-items-center text-light">
                <span class="me-3"><?= esc($client['prenom'] . ' ' . $client['nom']) ?></span>
                <a href="<?= base_url('client/logout') ?>" class="btn btn-sm btn-outline-light">Déconnexion</a>
            </div>
        <?php endif; ?>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 bg-white border-end min-vh-100 py-3">
                <div class="nav flex-column">
                    <a class="nav-link" href="<?= base_url('client/dashboard') ?>">Accueil</a>
                    <a class="nav-link" href="<?= base_url('client/transfert') ?>">Transférer</a>
                    <a class="nav-link" href="<?= base_url('client/transfert-multiple') ?>">Envoi multiple</a>
                    <a class="nav-link" href="<?= base_url('client/transactions') ?>">Historique</a>
                </div>
            </nav>

            <main class="col-md-10 py-4">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>

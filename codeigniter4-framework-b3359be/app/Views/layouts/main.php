<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Mobile Money — Opérateur') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/navbar') ?>

    <div class="d-flex">
        <?= $this->include('partials/sidebar') ?>

        <main class="mm-content flex-grow-1">
            <?= $this->include('partials/flash_messages') ?>
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <?= $this->include('partials/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>

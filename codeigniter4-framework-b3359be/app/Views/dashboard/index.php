<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-4">Tableau de bord</h1>

<h2 class="h6 text-muted">Comptes & opérations <small>(Développeur A)</small></h2>
<div class="row g-3 mb-4">
    <?= $this->include('dashboard/_widgets_operations') ?>
</div>

<h2 class="h6 text-muted">Réseau & administration <small>(Développeur B)</small></h2>
<div class="row g-3 mb-4">
    <?= $this->include('dashboard/_widgets_reseau') ?>
</div>

<?= $this->endSection() ?>

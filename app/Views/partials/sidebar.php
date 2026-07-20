<?php if (isLoggedIn()): ?>
<aside class="mm-sidebar bg-dark">
    <nav class="nav flex-column p-3">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">Tableau de bord</a>

        <span class="text-uppercase text-secondary small mt-3 mb-1">Comptes & opérations</span>
        <a class="nav-link" href="<?= base_url('clients') ?>">Clients</a>
        <a class="nav-link" href="<?= base_url('comptes') ?>">Comptes</a>
        <a class="nav-link" href="<?= base_url('transactions') ?>">Transactions</a>

        <span class="text-uppercase text-secondary small mt-3 mb-1">Réseau & administration</span>
        <a class="nav-link" href="<?= base_url('agents') ?>">Agents</a>
        <a class="nav-link" href="<?= base_url('tarifs') ?>">Grille tarifaire</a>
        <?php if (hasRole(['ADMIN', 'SUPER_ADMIN'])): ?>
            <a class="nav-link" href="<?= base_url('parametres') ?>">Paramètres</a>
        <?php endif; ?>
        <?php if (hasRole('SUPER_ADMIN')): ?>
            <a class="nav-link" href="<?= base_url('utilisateurs') ?>">Utilisateurs</a>
        <?php endif; ?>
        <a class="nav-link" href="<?= base_url('rapports') ?>">Rapports</a>
        <a class="nav-link" href="<?= base_url('logs') ?>">Journal d'audit</a>
    </nav>
</aside>
<?php endif; ?>

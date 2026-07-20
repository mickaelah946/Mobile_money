<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand mb-0 h1">Mobile Money — Espace Opérateur</span>

    <?php $user = currentUser(); ?>
    <?php if ($user): ?>
        <div class="d-flex align-items-center text-light">
            <span class="me-3">
                <?= esc($user['prenom'] . ' ' . $user['nom']) ?>
                <span class="badge bg-secondary ms-1"><?= esc($user['roleLabel'] ?? $user['roleCode']) ?></span>
            </span>
            <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-light">Déconnexion</a>
        </div>
    <?php endif; ?>
</nav>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Kopipiro</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'active fw-bold text-warning' : ''; ?>" href="index.php">Katalog Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'cart.php') ? 'active fw-bold text-warning' : ''; ?>" href="cart.php">Keranjang Belanja</a>
                </li>
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'cashier')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'view_orders.php') ? 'active fw-bold text-warning' : ''; ?>" href="view_orders.php">Kelola Pesanan</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'owner'): ?>
                    <li class="nav-item me-2">
                        <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'admin/index.php') ? 'active fw-bold text-warning' : ''; ?>" href="admin/index.php">Panel Produk</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item me-3 text-light small">
                        Halo, <strong class="text-warning"><?= htmlspecialchars($_SESSION['username']); ?></strong> 
                        <span class="badge bg-secondary ms-1"><?= ucfirst($_SESSION['role']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger btn-sm" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-light btn-sm" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
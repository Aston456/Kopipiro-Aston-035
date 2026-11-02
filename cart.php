<?php
    session_start();
    include 'config.php';

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($action === 'add' && isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else if ($action === 'sub' && isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id]['quantity'] > 1) {
                $_SESSION['cart'][$id]['quantity'] -= 1;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        } else if ($action === 'delete' && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        header("Location: cart.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container my-5">
        <div class="mb-4">
            <h2 class="fw-bold">Keranjang Belanja Anda</h2>
            <p class="text-muted">Periksa kembali pesanan Anda sebelum melanjutkan ke pembayaran</p>
        </div>
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">Produk</th>
                                            <th scope="col">Harga</th>
                                            <th scope="col" class="text-center">Kuantitas</th>
                                            <th scope="col">Subtotal</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $subtotal_keseluruhan = 0;
                                        foreach ($_SESSION['cart'] as $id => $item): 
                                            $subtotal = $item['price'] * $item['quantity'];
                                            $subtotal_keseluruhan += $subtotal;
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" class="me-3" onerror="this.src='uploads/default.jpg'">
                                                        <div>
                                                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <a href="cart.php?action=sub&id=<?= $id ?>" class="btn btn-sm btn-outline-secondary px-2">-</a>
                                                    <span class="mx-2 fw-bold"><?= $item['quantity'] ?></span>
                                                    <a href="cart.php?action=add&id=<?= $id ?>" class="btn btn-sm btn-outline-secondary px-2">+</a>
                                                </td>
                                                <td class="fw-bold text-success">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <a href="cart.php?action=delete&id=<?= $id ?>" class="btn btn-sm btn-danger">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Ringkasan Pesanan</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Harga Item</span>
                                <span class="fw-bold">Rp <?= number_format($subtotal_keseluruhan, 0, ',', '.') ?></span>
                            </div>
                            <?php 
                            $diskon = 0;
                            $is_member = (isset($_SESSION['role']) && $_SESSION['role'] === 'customer');
                            if ($is_member) {
                                $diskon = $subtotal_keseluruhan * 0.10; // Diskon 10%
                            }
                            $total_bayar = $subtotal_keseluruhan - $diskon;
                            ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Diskon Member (10%)</span>
                                <span class="text-danger fw-bold">
                                    <?php if ($is_member): ?>
                                        - Rp <?= number_format($diskon, 0, ',', '.') ?>
                                    <?php else: ?>
                                        Rp 0 (Non-Member)
                                    <?php endif; ?>
                                </span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold fs-5">Total Pembayaran</span>
                                <span class="fw-bold fs-5 text-success">Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
                            </div>
                            <?php if (!$is_member): ?>
                                <div class="alert alert-warning small py-2 mb-3">
                                    💡 <a href="login.php" class="alert-link">Login sebagai member</a> untuk mendapatkan potongan diskon 10%!
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success small py-2 mb-3">
                                    🎉 Diskon member 10% berhasil diterapkan!
                                </div>
                            <?php endif; ?>
                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-dark btn-lg">Proses Checkout</a>
                                <a href="index.php" class="btn btn-outline-secondary">← Lanjutkan Belanja</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <h4 class="text-muted mb-3">Keranjang belanja kamu masih kosong.</h4>
                <a href="index.php" class="btn btn-dark">Lihat Katalog Menu</a>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
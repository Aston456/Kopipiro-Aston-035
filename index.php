<?php
    session_start();
    include 'config.php';

    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_image = $_POST['product_image'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'price' => $product_price,
                'image' => $product_image,
                'quantity' => 1
            ];
        }

        header("Location: index.php");
        exit;
    }

    $result = mysqli_query($conn, "SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Menu Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container my-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Menu Kopipiro</h1>
            <p class="text-muted">Pilih menu favoritmu dan nikmati harimu dengan secangkir kopi</p>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer'): ?>
                <div class="alert alert-success d-inline-block py-2 px-4 shadow-sm">
                    🎉 Halo Member! Kamu berhak mendapatkan Diskon 10% saat checkout.
                </div>
            <?php else: ?>
                <div class="alert alert-warning d-inline-block py-2 px-4 shadow-sm small">
                    💡 <a href="login.php" class="alert-link">Login sebagai member</a> untuk menikmati potongan diskon 10%!
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: cover;" onerror="this.src='uploads/default.jpeg'">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0 fw-bold"><?= htmlspecialchars($row['name']) ?></h5>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($row['category']) ?></span>
                                </div>
                                <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($row['description']) ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fs-5 fw-bold text-success">Rp <?= number_format($row['price'], 0, ',', '.') ?></span>
                                    <span class="text-warning small">⭐ <?= htmlspecialchars($row['rating']) ?></span>
                                </div>
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['name']) ?>">
                                    <input type="hidden" name="product_price" value="<?= $row['price'] ?>">
                                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($row['image']) ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-dark w-100">🛒 Tambah ke Keranjang</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada menu tersedia saat ini. Tunggu update terbaru dari Kopipiro!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
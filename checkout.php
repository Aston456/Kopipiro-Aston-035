<?php
    session_start();
    include 'config.php';

    if (empty($_SESSION['cart'])) {
        header("Location: index.php");
        exit;
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    $is_member = (isset($_SESSION['role']) && $_SESSION['role'] === 'customer');

    $total_price = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    $discount = $is_member ? 0.10 * $total_price : 0;
    $final_total = $total_price - $discount;

    $status = 'Pending';
    mysqli_begin_transaction($conn);

    try {
        $stmt_order = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_price, discount, status) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_order, "idds", $user_id, $total_price, $discount, $status);
        mysqli_stmt_execute($stmt_order);

        $order_id = mysqli_insert_id($conn);
        $stmt_detail = mysqli_prepare($conn, "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

        foreach ($_SESSION['cart'] as $product_id => $item) {
            mysqli_stmt_bind_param($stmt_detail, "iiid", $order_id, $product_id, $item['quantity'], $item['price']);
            mysqli_stmt_execute($stmt_detail);
        }

        mysqli_commit($conn);
        unset($_SESSION['cart']);
        $success_order_id = $order_id;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        die("Terjadi kesalahan saat memproses pesanan: " . $e->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card shadow-sm border-0 p-4">
                    <div class="card-body">
                        <div class="mb-4">
                            <span class="fs-1 text-success">✅</span>
                        </div>
                        <h2 class="fw-bold mb-3">Pesanan Berhasil Dibuat!</h2>
                        <p class="text-muted mb-4">Terima kasih telah memesan di Kopipiro. Nomor pesanan Anda adalah <strong class="text-dark">#<?= $success_order_id; ?></strong>.</p>
                        <div class="alert alert-secondary small mb-4">
                            Status pesanan Anda saat ini: <strong class="text-warning bg-dark px-2 py-1 rounded">Pending</strong>. Silakan tunjukkan nomor pesanan ini ke kasir untuk proses pembayaran dan validasi.
                        </div>
                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-dark">Kembali ke Katalog Menu</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
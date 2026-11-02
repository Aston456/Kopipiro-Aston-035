<?php
    session_start();
    include 'config.php';

    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'owner') && ($_SESSION['role'] !== 'cashier')) {
        header("Location: login.php");
        exit;
    }

    if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $check_q = mysqli_query($conn, "SELECT status FROM orders WHERE id = $order_id");
    $current_order = mysqli_fetch_assoc($check_q);

    if ($current_order && $current_order['status'] !== 'Completed') {
        $update_stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "si", $new_status, $order_id);
        mysqli_stmt_execute($update_stmt);
    }

    header("Location: view_orders.php");
    exit;
}

    $result = mysqli_query($conn, "SELECT orders.*, users.username FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY created_at DESC");

    $chart_dates = [];
    $chart_totals = [];

    if ($_SESSION['role'] === 'owner') {
        $chart_query = "SELECT DATE(created_at) as tgl, SUM(total_price) as total_harian FROM orders WHERE status = 'Completed' GROUP BY DATE(created_at) ORDER BY tgl ASC LIMIT 7";
        $chart_res = mysqli_query($conn, $chart_query);
        while ($c_row = mysqli_fetch_assoc($chart_res)) {
            $chart_dates[] = $c_row['tgl'];
            $chart_totals[] = $c_row['total_harian'];
        }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Daftar Pesanan Masuk</h2>
                <p class="text-muted mb-0">Kelola dan validasi status pesanan pelanggan di sini</p>
            </div>
            <div>
                <span class="badge bg-dark fs-6 px-3 py-2">Role: <?= ucfirst($_SESSION['role']); ?></span>
                <?php if ($_SESSION['role'] === 'owner'): ?>
                    <a href="excel_generate.php" class="btn btn-success font-weight-bold">📥 Unduh Excel</a>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($_SESSION['role'] === 'owner'): ?>
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-header bg-dark text-white fw-bold">
                    📊 Grafik Penjualan Harian (Status: Completed)
                </div>
                <div class="card-body">
                    <?php if (!empty($chart_dates)): ?>
                        <canvas id="salesChart" style="max-height: 300px;"></canvas>
                    <?php else: ?>
                        <p class="text-muted text-center my-3">Belum ada data transaksi berstatus Completed untuk grafik.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">ID Order</th>
                                <th scope="col">Pelanggan</th>
                                <th scope="col">Total Harga</th>
                                <th scope="col">Diskon</th>
                                <th scope="col">Total Bayar</th>
                                <th scope="col">Status</th>
                                <th scope="col">Waktu Pesan</th>
                                <th scope="col" class="text-center">Aksi / Validasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="fw-bold">#<?= $row['id']; ?></td>
                                        <td><?= $row['username'] ? htmlspecialchars($row['username']) : '<span class="text-muted fst-italic">Tamu (Guest)</span>'; ?></td>
                                        <td>Rp <?= number_format($row['total_price'], 0, ',', '.'); ?></td>
                                        <td class="text-danger">Rp <?= number_format($row['discount'], 0, ',', '.'); ?></td>
                                        <td class="fw-bold text-success">Rp <?= number_format($row['total_price'] - $row['discount'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($row['status'] === 'Pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php elseif ($row['status'] === 'Completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($row['status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted"><?= $row['created_at']; ?></td>
                                        <td class="text-center">
                                            <?php if ($row['status'] === 'Completed'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Selesai</span>
                                            <?php else: ?>
                                                <form method="POST" class="d-inline-flex align-items-center">
                                                    <input type="hidden" name="order_id" value="<?= $row['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm me-2" style="width: 130px;">
                                                        <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Completed" <?= $row['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                    </select>
                                                    <button type="submit" name="update_status" class="btn btn-sm btn-dark">Update</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada pesanan yang masuk.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php if ($_SESSION['role'] === 'owner' && !empty($chart_dates)): ?>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_dates); ?>,
                datasets: [{
                    label: 'Total Pendapatan (Rp)',
                    data: <?= json_encode($chart_totals); ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
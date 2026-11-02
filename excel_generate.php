<?php
    session_start();
    include 'config.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
        header("Location: login.php");
        exit;
    }

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Penjualan_Kopipiro_" . date('Y-m-d_H-i-s') . ".xls");
    
    $result = mysqli_query($conn, "SELECT orders.*, users.username FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC");
?>

<h2>Laporan Penjualan Kopipiro</h2>
<p>Tanggal Cetak: <?= date('d-m-Y H:i:s'); ?></p>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr style="background-color: #333; color: #fff;">
            <th>ID Order</th>
            <th>Pelanggan</th>
            <th>Total Harga (Rp)</th>
            <th>Diskon (Rp)</th>
            <th>Total Bayar (Rp)</th>
            <th>Status</th>
            <th>Waktu Pesan</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $grand_total = 0;
        if (mysqli_num_rows($result) > 0): 
            while ($row = mysqli_fetch_assoc($result)): 
                if ($row['status'] === 'Completed') {
                    $grand_total += $row['total_price'] - $row['discount'];
                }
        ?>
            <tr>
                <td>#<?= $row['id']; ?></td>
                <td><?= $row['username'] ? htmlspecialchars($row['username']) : 'Tamu (Guest)'; ?></td>
                <td><?= number_format($row['total_price'], 0, ',', '.'); ?></td>
                <td><?= number_format($row['discount'], 0, ',', '.'); ?></td>
                <td><?= number_format($row['total_price'] - $row['discount'], 0, ',', '.'); ?></td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td><?= $row['created_at']; ?></td>
            </tr>
        <?php 
            endwhile; 
        endif; 
        ?>
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <td colspan="4" align="right">Total Pendapatan (Pesanan Completed):</td>
            <td colspan="3">Rp <?= number_format($grand_total, 0, ',', '.'); ?></td>
        </tr>
    </tbody>
</table>
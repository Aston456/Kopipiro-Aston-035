<?php
    session_start();
    include '../config.php';

    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'owner')) {
        header("Location: ../login.php");
        exit;
    }

    $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow=sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Owner Kopipiro</a>
            <div class="d-flex align-items-center">
                <a href="../index.php" class="btn btn-outline-light btn-sm me-2">Lihat Website Utama</a>
                <a href="../login.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Manajemen Produk</h1>
            <p class="text-muted">Kelola produk Kopipiro</p>
            <a href="create.php" class="btn btn-dark">+ Tambah Produk Baru</a>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Gambar</th>
                                <th scope="col">Nama Produk</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Rating</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <td><img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" onerror="this.src='../uploads/default.jpg'"></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['category']) ?></span></td>
                                        <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                                        <td>⭐ <?= htmlspecialchars($row['rating']) ?></td>
                                        <td class="text-center">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning text-white me-1">Edit</a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada produk yang ditambahkan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
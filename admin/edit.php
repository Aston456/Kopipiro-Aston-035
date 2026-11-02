<?php
    session_start();
    include '../config.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
        header("Location: ../login.php");
        exit;
    }

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $error = "";
    $success = "";

    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['update_product'])) {
        $name = trim($_POST['name']);
        $category = trim($_POST['category']);
        $description = trim($_POST['description']);
        $price = trim($_POST['price']);
        $rating = trim($_POST['rating']);
        $current_image = $product['image'];

        $image_name = $_FILES['image']['name'];
        if ($image_name != "") {
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($image_ext, $allowed_ext)) {
                $new_image_name = uniqid('prod_', true) . '.' . $image_ext;
                $upload_dir = '../uploads/';

                if (move_uploaded_file($image_tmp, $upload_dir . $new_image_name)) {
                    if ($current_image != 'default.jpg' && file_exists($upload_dir . $current_image)) {
                        unlink($upload_dir . $current_image);
                    }
                    $image = $new_image_name;
                } else {
                    $error = "Gagal mengunggah gambar baru.";
                }
            } else {
                $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
            }
        } else {
            $image = $current_image;
        }

        if (!$error) {
            $update_stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, category = ?, description = ?, price = ?, image = ?, rating = ? WHERE id = ?");
            mysqli_stmt_bind_param($update_stmt, "sssdssi", $name, $category, $description, $price, $image, $rating, $id);

            if (mysqli_stmt_execute($update_stmt)) {
                $success = "Produk berhasil diperbarui! <a href='index.php'>Kembali ke daftar produk</a>";
                $product['name'] = $name;
                $product['category'] = $category;
                $product['description'] = $description;
                $product['price'] = $price;
                $product['rating'] = $rating;
                $product['image'] = $image;
            } else {
                $error = "Gagal memperbarui database: " . mysqli_error($conn);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">☕ Admin Panel - Kopipiro</a>
            <a href="index.php" class="btn btn-outline-light btn-sm">Kembali ke Daftar Produk</a>
        </div>
    </nav>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Edit Menu Produk</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success py-2"><?= $success ?></div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="Coffee" <?= $product['category'] === 'Coffee' ? 'selected' : '' ?>>Coffee (Kopi)</option>
                                    <option value="Non-Coffee" <?= $product['category'] === 'Non-Coffee' ? 'selected' : '' ?>>Non-Coffee (Minuman Non Kaffein)</option>
                                    <option value="Toast" <?= $product['category'] === 'Toast' ? 'selected' : '' ?>>Toast (Roti Bakar)</option>
                                    <option value="Snack" <?= $product['category'] === 'Snack' ? 'selected' : '' ?>>Snack / Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" id="price" name="price" step="100" value="<?= $product['price'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating (1.0 - 5.0)</label>
                                <input type="number" class="form-control" id="rating" name="rating" step="0.1" min="1" max="5" value="<?= $product['rating'] ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Gambar Saat Ini:</label>
                                <div class="mb-2">
                                    <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;" onerror="this.src='../uploads/default.jpg'">
                                </div>
                                <label for="image" class="form-label">Ganti Gambar (Opsional)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar.</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" name="update_product" class="btn btn-dark">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
    session_start();
    include '../config.php';

    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'owner')) {
        header("Location: ../login.php");
        exit;
    }

    $error= "";
    $success= "";

    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $rating = $_POST['rating'];
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];

        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if ($image_name != "") {
            $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($image_ext, $allowed_ext)) {
                $new_image_name = uniqid('prod_', true) . '.' . $image_ext;
                
                if (move_uploaded_file($image_tmp, $upload_dir . $new_image_name)) {
                    $image = $new_image_name;
                } else {
                    $error = "Gagal mengunggah gambar.";
                }
            } else {
                $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
            }
        } else {
            $image = 'default.jpg';
        }

    if (!$error) {
        $stmt = mysqli_prepare($conn, "INSERT INTO products (name, category, description, price, image, rating) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssdss", $name, $category, $description, $price, $image, $rating);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Produk berhasil ditambahkan! <a href='index.php'>Lihat daftar produk</a>";
        } else {
            $error = "Gagal menyimpan ke database: " . mysqli_error($conn);
        }
    }
        
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Kopipiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">☕ Admin Panel - Kopipiro</a>
            <a href="../index.php" class="btn btn-outline-light btn-sm">Lihat Website Utama</a>
        </div>
    </nav>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Tambah Menu Produk Kopipiro</h5>
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
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Coffee">Coffee</option>
                                    <option value="Non-Coffee">Non-Coffee</option>
                                    <option value="Toast">Toast</option>
                                    <option value="Snack">Snack / Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" id="price" name="price" step="100" required>
                            </div>
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating (1.0 - 5.0)</label>
                                <input type="number" class="form-control" id="rating" name="rating" step="0.1" min="1" max="5" value="5.0" required>
                            </div>
                            <div class="mb-4">
                                <label for="image" class="form-label">Unggah Gambar Produk</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Format yang didukung: JPG, JPEG, PNG, WEBP.</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="../index.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="add_product" class="btn btn-dark">Simpan Produk</button>
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
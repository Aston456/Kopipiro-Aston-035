<?php
    session_start();
    include 'config.php';

    $error= "";
    $success = "";

    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = 'customer';
    
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
    
        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Username sudah digunakan. Silahkan pilih yang lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $role);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Registrasi berhasil! Silahkan <a href='login.php'>Login di sini</a>.";
            } else {
                $error = "Terjadi kesalahan: " . mysqli_error($conn);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center" style="margin-top: 15vh;">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white text-center">
                        <h5 class="mb-0">Daftar Member Kopipiro</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger py-2"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                        <div class="alert alert-success py-2"><?= $success ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required autofocus>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="register" class="btn btn-dark">Daftar</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <p class="small text-muted mb-0">Sudah punya akun? <a href="login.php" class="text-decoration-none">Login di sini</a></p>
                            <div class="mt-2">
                                <a href="index.php" class="text-decoration-none">Lewati dan belanja sebagai tamu</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
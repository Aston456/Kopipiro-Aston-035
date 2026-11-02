<?php
    session_start();
    include '../config.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
        header("Location: ../login.php");
        exit;
    }

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $stmt = mysqli_prepare($conn, "SELECT image FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        $image_file = $product['image'];
        
        if ($image_file != 'default.jpg' && file_exists('../uploads/' . $image_file)) {
            unlink('../uploads/' . $image_file);
        }

        $delete_stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
        mysqli_stmt_bind_param($delete_stmt, "i", $id);
        mysqli_stmt_execute($delete_stmt);
    }

    header("Location: index.php");
    exit;
?>
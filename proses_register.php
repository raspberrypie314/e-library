<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_key = isset($_POST['admin_key']) ? clean_input($_POST['admin_key']) : '';
    
    // Validasi input
    $errors = [];
    
    // Validasi username
    if (empty($username)) {
        $errors[] = "Username harus diisi!";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter!";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username hanya boleh mengandung huruf, angka, dan underscore!";
    }
    
    // Validasi password
    if (empty($password)) {
        $errors[] = "Password harus diisi!";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter!";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak cocok!";
    }
    
    // Jika ada error, kembali ke register
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        redirect('register.php');
    }
    
    // Cek apakah username sudah ada
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Username sudah digunakan!";
        $check_stmt->close();
        redirect('register.php');
    }
    $check_stmt->close();
    
    // Tentukan tipe user
    $user_type = 'user';
    if (!empty($admin_key)) {
        if ($admin_key === 'renaissance') {
            $user_type = 'admin';
        } else {
            $_SESSION['error'] = "Kunci admin salah!";
            redirect('register.php');
        }
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    $insert_sql = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sss", $username, $hashed_password, $user_type);
    
    if ($insert_stmt->execute()) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        $insert_stmt->close();
        redirect('login.php');
    } else {
        $_SESSION['error'] = "Terjadi kesalahan. Silakan coba lagi!";
        $insert_stmt->close();
        redirect('register.php');
    }
} else {
    // Jika bukan POST request, redirect ke register
    redirect('register.php');
}
?>

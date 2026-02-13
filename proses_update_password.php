<?php
require_once 'config.php';

// Cek login
if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    $errors = [];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "Semua field harus diisi!";
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = "Password baru dan konfirmasi password tidak cocok!";
    }
    
    if (strlen($new_password) < 6) {
        $errors[] = "Password baru minimal 6 karakter!";
    }
    
    // Jika ada error, kembali ke profile
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        redirect('profile.php');
    }
    
    // Cek password saat ini
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($current_password, $user['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['success'] = "Password berhasil diperbarui!";
                $update_stmt->close();
                redirect('profile.php');
            } else {
                $_SESSION['error'] = "Terjadi kesalahan. Silakan coba lagi!";
                $update_stmt->close();
                redirect('profile.php');
            }
        } else {
            $_SESSION['error'] = "Password saat ini salah!";
            $stmt->close();
            redirect('profile.php');
        }
    } else {
        $_SESSION['error'] = "User tidak ditemukan!";
        $stmt->close();
        redirect('profile.php');
    }
    
    $stmt->close();
} else {
    // Jika bukan POST request, redirect ke profile
    redirect('profile.php');
}
?>

<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username dan password harus diisi!";
        redirect('login.php');
    }
    
    // Query untuk mencari user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['login_time'] = time();
            
            // Set session timeout (30 menit)
            $_SESSION['session_expire'] = time() + (30 * 60);
            
            // Redirect ke dashboard
            redirect('dashboard.php');
        } else {
            $_SESSION['error'] = "Password salah!";
            redirect('login.php');
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
        redirect('login.php');
    }
    
    $stmt->close();
} else {
    // Jika bukan POST request, redirect ke login
    redirect('login.php');
}
?>

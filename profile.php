<?php
require_once 'config.php';

// Cek login
if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];
$error = '';
$success = '';

// Proses update password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi tidak cocok!";
    } elseif (strlen($new_password) < 6) {
        $error = "Password baru minimal 6 karakter!";
    } else {
        // Cek password saat ini
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (password_verify($current_password, $user['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_stmt->execute()) {
                $success = "Password berhasil diperbarui!";
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi!";
            }
            
            $update_stmt->close();
        } else {
            $error = "Password saat ini salah!";
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Perpustakaan Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --white: #FFFFFF;
            --espresso: #4B433A;
            --taupe: #A19688;
            --beige: #DFD5C7;
            --sage: #AFAA6F;
            --text-dark: #333333;
            --text-light: #666666;
            --shadow: rgba(75, 67, 58, 0.1);
            --shadow-dark: rgba(75, 67, 58, 0.2);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: var(--white);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            background-color: var(--espresso);
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px var(--shadow-dark);
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 70px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo i {
            font-size: 28px;
            color: var(--beige);
        }

        .logo h1 {
            font-size: 24px;
            color: var(--beige);
            font-weight: 600;
        }

        /* Hamburger Menu Button */
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--beige);
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .sidebar-toggle:hover {
            background-color: rgba(223, 213, 199, 0.1);
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background-color: var(--espresso);
            padding-top: 80px;
            box-shadow: 4px 0 20px var(--shadow-dark);
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1001;
            overflow-y: auto;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background-color: var(--sage);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            border-bottom: 2px solid var(--beige);
        }

        .sidebar-header h3 {
            color: var(--white);
            font-size: 20px;
            font-weight: 600;
        }

        .sidebar-close {
            background: none;
            border: none;
            color: var(--white);
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: var(--beige);
            text-decoration: none;
            transition: var(--transition);
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: rgba(223, 213, 199, 0.1);
            border-left-color: var(--sage);
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background-color: var(--beige);
            border-left-color: var(--sage);
            color: var(--espresso);
        }

        .sidebar-menu i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* User Info in Navbar */
        .user-info-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--beige);
        }

        .user-info-nav i {
            font-size: 20px;
        }

        .user-type-nav {
            background-color: var(--sage);
            color: var(--white);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Profile Container */
        .profile-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 0 30px;
        }

        .profile-card {
            background-color: var(--white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px var(--shadow);
            border: 2px solid var(--beige);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--beige);
        }

        .profile-header i {
            font-size: 60px;
            color: var(--sage);
            margin-bottom: 15px;
        }

        .profile-header h2 {
            font-size: 28px;
            color: var(--espresso);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .profile-header p {
            color: var(--taupe);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--espresso);
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--beige);
            border-radius: 8px;
            background-color: var(--white);
            color: var(--text-dark);
            font-size: 16px;
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(175, 170, 111, 0.2);
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: var(--taupe);
            font-size: 12px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background-color: var(--sage);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn:hover {
            background-color: var(--espresso);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }

        .alert-error {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid #28a745;
        }

        .user-info-display {
            background-color: var(--beige);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid var(--taupe);
        }

        .user-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--taupe);
        }

        .user-info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .user-info-label {
            font-weight: 600;
            color: var(--espresso);
        }

        .user-info-value {
            color: var(--espresso);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
            }

            .logo h1 {
                font-size: 20px;
            }

            .profile-container {
                margin: 20px auto;
                padding: 0 20px;
            }

            .profile-card {
                padding: 30px 20px;
            }

            .profile-header i {
                font-size: 50px;
            }

            .profile-header h2 {
                font-size: 24px;
            }

            .user-info-nav span:not(.user-type-nav) {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 0 15px;
            }

            .logo h1 {
                font-size: 18px;
            }

            .profile-container {
                padding: 0 15px;
            }

            .profile-card {
                padding: 25px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <i class="fas fa-book"></i>
            <h1>Edit Profil</h1>
        </div>
        
        <div class="user-info-nav">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($username); ?></span>
            <span class="user-type-nav"><?php echo ucfirst($user_type); ?></span>
        </div>
    </nav>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu Navigasi</h3>
            <button class="sidebar-close" id="sidebarClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="profile.php" class="active">
                    <i class="fas fa-user-cog"></i>
                    <span>Edit Profil</span>
                </a>
            </li>
            <?php if ($user_type == 'admin'): ?>
                <li>
                    <a href="user_mgmt.php">
                        <i class="fas fa-users-cog"></i>
                        <span>Manajemen User</span>
                    </a>
                </li>
                <li>
                    <a href="book_mgmt.php">
                        <i class="fas fa-book-medical"></i>
                        <span>Manajemen Buku</span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Profile Container -->
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <i class="fas fa-user-cog"></i>
                <h2>Update Password</h2>
                <p>Perbarui kata sandi Anda</p>
            </div>
            
            <!-- User Info Display -->
            <div class="user-info-display">
                <div class="user-info-item">
                    <span class="user-info-label">Username:</span>
                    <span class="user-info-value"><?php echo htmlspecialchars($username); ?></span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Tipe Akun:</span>
                    <span class="user-info-value"><?php echo ucfirst($user_type); ?></span>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="current_password"><i class="fas fa-lock"></i> Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <small>Masukkan password Anda saat ini</small>
                </div>
                
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-key"></i> Password Baru</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small>Minimal 6 karakter</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-key"></i> Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <small>Masukkan kembali password baru</small>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </form>
        </div>
    </div>

    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
        });

        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            }
        });

        // Password validation
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            
            // Validasi password match
            confirmPassword.addEventListener('input', function() {
                if (newPassword.value !== this.value) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = 'var(--beige)';
                }
            });
            
            newPassword.addEventListener('input', function() {
                if (confirmPassword.value && this.value !== confirmPassword.value) {
                    confirmPassword.style.borderColor = '#dc3545';
                } else if (confirmPassword.value) {
                    confirmPassword.style.borderColor = 'var(--beige)';
                }
                
                if (this.value.length < 6 && this.value.length > 0) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = 'var(--beige)';
                }
            });
            
            // Menghilangkan pesan setelah 5 detik
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });

        // Responsive adjustments
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>

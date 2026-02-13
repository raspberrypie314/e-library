<?php
require_once 'config.php';

// Jika sudah login, redirect ke dashboard
if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_key = isset($_POST['admin_key']) ? clean_input($_POST['admin_key']) : '';
    
    // Validasi
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah username sudah ada
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Tentukan tipe user
            $user_type = 'user';
            if (!empty($admin_key)) {
                if ($admin_key === 'renaissance') {
                    $user_type = 'admin';
                } else {
                    $error = "Kunci admin salah!";
                }
            }
            
            if (!$error) {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user baru
                $insert_sql = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("sss", $username, $hashed_password, $user_type);
                
                if ($insert_stmt->execute()) {
                    $success = "Registrasi berhasil! Silakan login.";
                    // Tunggu 2 detik kemudian redirect ke login
                    header("refresh:2;url=login.php");
                } else {
                    $error = "Terjadi kesalahan. Silakan coba lagi!";
                }
                
                $insert_stmt->close();
            }
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Perpustakaan Digital</title>
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
        }

        body {
            background-color: var(--white);
            color: var(--espresso);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background-color: var(--white);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(75, 67, 58, 0.2);
            border: 2px solid var(--beige);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 60px;
            color: var(--sage);
            margin-bottom: 15px;
        }

        .logo h1 {
            font-size: 28px;
            color: var(--espresso);
            letter-spacing: 1px;
        }

        .logo p {
            color: var(--taupe);
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
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
            color: var(--espresso);
            font-size: 16px;
            transition: all 0.3s;
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
            transition: all 0.3s;
            border: 2px solid var(--sage);
        }

        .btn:hover {
            background-color: var(--espresso);
            border-color: var(--espresso);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--beige);
        }

        .login-link a {
            color: var(--sage);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
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

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
            }
            
            .logo i {
                font-size: 50px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <i class="fas fa-user-plus"></i>
            <h1>Registrasi Akun</h1>
            <p>Buat akun baru untuk mengakses perpustakaan digital</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" required autofocus>
                <small>Minimal 3 karakter, huruf dan angka saja</small>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
                <small>Minimal 6 karakter</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="admin_key"><i class="fas fa-key"></i> Kunci Admin (Opsional)</label>
                <input type="password" id="admin_key" name="admin_key" placeholder="Masukkan kunci admin jika diperlukan">
                <small>Hanya untuk registrasi akun admin</small>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </form>
        
        <div class="login-link">
            <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
        </div>
    </div>

    <script>
        // Validasi form
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const username = document.getElementById('username');
            
            // Validasi username
            username.addEventListener('input', function() {
                const usernameRegex = /^[a-zA-Z0-9_]{3,}$/;
                if (!usernameRegex.test(this.value)) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = 'var(--beige)';
                }
            });
            
            // Validasi password match
            confirmPassword.addEventListener('input', function() {
                if (password.value !== this.value) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = 'var(--beige)';
                }
            });
            
            password.addEventListener('input', function() {
                if (confirmPassword.value && this.value !== confirmPassword.value) {
                    confirmPassword.style.borderColor = '#dc3545';
                } else if (confirmPassword.value) {
                    confirmPassword.style.borderColor = 'var(--beige)';
                }
                
                if (this.value.length < 6) {
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
    </script>
</body>
</html>

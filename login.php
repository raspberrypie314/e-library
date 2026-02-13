<?php
require_once 'config.php';

// Jika sudah login, redirect ke dashboard
if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            
            redirect('dashboard.php');
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(175, 170, 111, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(223, 213, 199, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(161, 150, 136, 0.05) 0%, transparent 50%);
            z-index: -1;
        }

        /* Login Container */
        .login-container {
            background-color: var(--white);
            border-radius: 20px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 40px var(--shadow-dark);
            position: relative;
            z-index: 100;
            border: 2px solid var(--beige);
            transform: translateY(0);
            transition: var(--transition);
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px var(--shadow-dark);
        }

        /* Logo Section */
        .logo {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid var(--beige);
        }

        .logo i {
            font-size: 70px;
            color: var(--sage);
            margin-bottom: 20px;
            display: inline-block;
            transition: var(--transition);
        }

        .logo:hover i {
            transform: rotate(-5deg) scale(1.1);
        }

        .logo h1 {
            font-size: 32px;
            color: var(--espresso);
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .logo p {
            color: var(--taupe);
            font-size: 16px;
            font-weight: 500;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--espresso);
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--beige);
            border-radius: 12px;
            background-color: var(--white);
            color: var(--text-dark);
            font-size: 16px;
            transition: var(--transition);
            box-shadow: 0 3px 10px var(--shadow);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(175, 170, 111, 0.2);
        }

        .form-group input::placeholder {
            color: var(--taupe);
        }

        /* Password Toggle */
        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--taupe);
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            background-color: var(--beige);
            color: var(--espresso);
        }

        /* Submit Button */
        .btn {
            width: 100%;
            padding: 18px;
            background-color: var(--sage);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(175, 170, 111, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            letter-spacing: 0.5px;
        }

        .btn:hover {
            background-color: var(--espresso);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(75, 67, 58, 0.3);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .btn i {
            font-size: 20px;
        }

        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid var(--beige);
        }

        .register-link p {
            color: var(--taupe);
            font-size: 16px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .register-link a {
            color: var(--espresso);
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            padding: 12px 30px;
            background-color: var(--beige);
            border-radius: 10px;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 2px solid transparent;
        }

        .register-link a:hover {
            background-color: transparent;
            color: var(--sage);
            border-color: var(--sage);
            transform: translateY(-2px);
        }

        .register-link a i {
            font-size: 18px;
        }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 600;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert-error {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left-color: #dc3545;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border-left-color: #28a745;
        }

        .alert i {
            font-size: 20px;
        }

        /* Extra Links */
        .extra-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 14px;
        }

        .extra-links a {
            color: var(--taupe);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .extra-links a:hover {
            color: var(--sage);
            text-decoration: underline;
        }

        .extra-links a i {
            font-size: 14px;
        }

        /* Form Field Icons */
        .form-group label i {
            color: var(--sage);
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .login-container {
                padding: 35px;
            }
            
            .logo i {
                font-size: 60px;
            }
            
            .logo h1 {
                font-size: 28px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
                border-radius: 15px;
            }
            
            .logo i {
                font-size: 50px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
            
            .form-group input {
                padding: 14px 18px;
            }
            
            .btn {
                padding: 16px;
                font-size: 16px;
            }
            
            .extra-links {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
            
            .register-link a {
                padding: 10px 25px;
                font-size: 15px;
            }
        }

        /* Floating Animation Elements */
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--beige), var(--sage));
            opacity: 0.08;
            animation: float 20s infinite ease-in-out;
            z-index: -1;
        }

        .floating-element:nth-child(1) {
            width: 200px;
            height: 200px;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 150px;
            height: 150px;
            bottom: -75px;
            left: -75px;
            animation-delay: -7s;
        }

        .floating-element:nth-child(3) {
            width: 100px;
            height: 100px;
            top: 30%;
            left: 10%;
            animation-delay: -14s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        /* Focus Animation for Inputs */
        @keyframes inputFocus {
            0% {
                box-shadow: 0 0 0 0 rgba(175, 170, 111, 0.2);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(175, 170, 111, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(175, 170, 111, 0);
            }
        }

        .form-group input:focus {
            animation: inputFocus 0.6s ease-out;
        }

        /* Loading State for Button */
        .btn.loading {
            position: relative;
            color: transparent;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid var(--white);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Elements -->
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>

    <!-- Login Container -->
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-book-open"></i>
            <h1>Perpustakaan Digital</h1>
            <p>Silakan login untuk mengakses sistem</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $success; ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" required autofocus 
                       placeholder="Masukkan username Anda" autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required 
                           placeholder="Masukkan password Anda" autocomplete="current-password">
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Show Password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="extra-links">
                <a href="#" onclick="showForgotPassword()">
                    <i class="fas fa-key"></i> Lupa Password?
                </a>
                <a href="index.php">
                    <i class="fas fa-home"></i> Halaman Utama
                </a>
            </div>
            
            <button type="submit" class="btn" id="submitBtn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="register-link">
            <p>Belum memiliki akun?</p>
            <a href="register.php">
                <i class="fas fa-user-plus"></i> Daftar Akun Baru
            </a>
        </div>
    </div>

    <script>
        // Password Toggle Functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                const icon = this.querySelector('i');
                icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
                
                // Add animation effect
                this.style.transform = 'translateY(-50%) scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-50%) scale(1)';
                }, 200);
            });
            
            // Keyboard shortcut for toggling password (Alt+P)
            passwordInput.addEventListener('keydown', function(e) {
                if (e.altKey && e.key === 'p') {
                    e.preventDefault();
                    togglePassword.click();
                }
            });
        }
        
        // Form Validation
        const loginForm = document.getElementById('loginForm');
        const usernameInput = document.getElementById('username');
        const submitBtn = document.getElementById('submitBtn');
        
        if (loginForm) {
            // Real-time validation
            usernameInput.addEventListener('input', validateUsername);
            passwordInput.addEventListener('input', validatePassword);
            
            // Form submission
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateForm()) {
                    // Show loading state
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Submit form after short delay to show loading animation
                    setTimeout(() => {
                        this.submit();
                    }, 800);
                }
            });
            
            // Auto-focus username field
            usernameInput.focus();
            
            // Enter key to submit
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.altKey && !e.ctrlKey) {
                    const focused = document.activeElement;
                    if (focused.tagName === 'INPUT' && focused.type !== 'button') {
                        e.preventDefault();
                        loginForm.dispatchEvent(new Event('submit'));
                    }
                }
            });
        }
        
        function validateUsername() {
            const value = usernameInput.value.trim();
            const isValid = value.length >= 3;
            
            setFieldState(usernameInput, isValid, 
                isValid ? '' : 'Username minimal 3 karakter');
            return isValid;
        }
        
        function validatePassword() {
            const value = passwordInput.value;
            const isValid = value.length >= 6;
            
            setFieldState(passwordInput, isValid,
                isValid ? '' : 'Password minimal 6 karakter');
            return isValid;
        }
        
        function validateForm() {
            const isUsernameValid = validateUsername();
            const isPasswordValid = validatePassword();
            
            if (!isUsernameValid || !isPasswordValid) {
                showFormError('Harap perbaiki kesalahan sebelum melanjutkan');
                return false;
            }
            
            return true;
        }
        
        function setFieldState(input, isValid, message) {
            const parent = input.parentElement;
            
            // Remove existing error message
            const existingError = parent.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
            
            if (isValid) {
                input.style.borderColor = 'var(--sage)';
                input.style.boxShadow = '0 0 0 3px rgba(175, 170, 111, 0.2)';
            } else {
                input.style.borderColor = '#dc3545';
                input.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, 0.2)';
                
                // Add error message
                if (message) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
                    errorDiv.style.cssText = `
                        color: #dc3545;
                        font-size: 13px;
                        margin-top: 8px;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        animation: slideDown 0.3s ease;
                    `;
                    parent.appendChild(errorDiv);
                }
            }
        }
        
        function showFormError(message) {
            // Remove existing form error
            const existingError = document.querySelector('.form-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Create error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-error form-error';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            errorDiv.style.cssText = `
                margin-top: 20px;
                animation: slideDown 0.3s ease;
            `;
            
            // Insert before submit button
            submitBtn.parentElement.insertBefore(errorDiv, submitBtn);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.style.opacity = '0';
                    errorDiv.style.transform = 'translateY(-10px)';
                    setTimeout(() => errorDiv.remove(), 300);
                }
            }, 5000);
        }
        
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }, 5000);
        });
        
        // Forgot password function
        function showForgotPassword() {
            alert('Untuk reset password, silakan hubungi administrator sistem.');
            return false;
        }
        
        // Add page transition effects
        document.addEventListener('DOMContentLoaded', function() {
            // Fade in page
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.4s ease';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
            
            // Add floating elements periodically
            setInterval(() => {
                createFloatingElement();
            }, 10000);
        });
        
        function createFloatingElement() {
            const element = document.createElement('div');
            element.className = 'floating-element';
            
            // Random properties
            const size = Math.random() * 50 + 50;
            const posX = Math.random() * 100;
            const posY = Math.random() * 100;
            const delay = Math.random() * -20;
            
            element.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${posX}%;
                top: ${posY}%;
                animation-delay: ${delay}s;
                opacity: ${Math.random() * 0.05 + 0.03};
            `;
            
            document.body.appendChild(element);
            
            // Remove after animation completes
            setTimeout(() => {
                element.style.opacity = '0';
                element.style.transition = 'opacity 1s ease';
                setTimeout(() => {
                    if (element.parentNode) {
                        element.remove();
                    }
                }, 1000);
            }, 20000);
        }
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                document.body.style.opacity = '1';
            }
        });
    </script>
</body>
</html>

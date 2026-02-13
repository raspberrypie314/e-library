<?php
require_once 'config.php';

// Cek login dan hak akses admin
if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];

// Ambil semua user kecuali admin yang sedang login
$current_user_id = $_SESSION['user_id'];
$sql = "SELECT id, username, user_type, created_at FROM users WHERE id != ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$users_result = $stmt->get_result();

// Hitung total user
$total_users = $users_result->num_rows;

// Pesan success/error
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Perpustakaan Digital</title>
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

        /* Container */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--beige);
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-title i {
            font-size: 32px;
            color: var(--espresso);
        }

        .header-title h2 {
            font-size: 28px;
            color: var(--espresso);
        }

        .header-stats {
            background-color: var(--beige);
            padding: 15px 25px;
            border-radius: 10px;
            border: 2px solid var(--taupe);
        }

        .header-stats span {
            font-weight: 600;
            color: var(--espresso);
            font-size: 18px;
        }

        /* Table Styles */
        .table-container {
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid var(--beige);
            margin-bottom: 30px;
            box-shadow: 0 5px 15px var(--shadow);
        }

        .table-responsive {
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .users-table thead {
            background-color: var(--sage);
        }

        .users-table th {
            padding: 18px 20px;
            text-align: left;
            color: var(--white);
            font-weight: 600;
            border-bottom: 2px solid var(--taupe);
            white-space: nowrap;
        }

        .users-table tbody tr {
            border-bottom: 1px solid var(--beige);
            transition: var(--transition);
        }

        .users-table tbody tr:hover {
            background-color: rgba(175, 170, 111, 0.1);
        }

        .users-table tbody tr:last-child {
            border-bottom: none;
        }

        .users-table td {
            padding: 16px 20px;
            color: var(--text-dark);
        }

        .user-type-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .user-type-badge.admin {
            background-color: rgba(175, 170, 111, 0.2);
            color: var(--espresso);
            border: 1px solid var(--sage);
        }

        .user-type-badge.user {
            background-color: rgba(223, 213, 199, 0.3);
            color: var(--espresso);
            border: 1px solid var(--taupe);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .action-button.delete {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .action-button.delete:hover {
            background-color: #dc3545;
            color: white;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1002;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: var(--white);
            border-radius: 15px;
            width: 100%;
            max-width: 500px;
            border: 2px solid var(--sage);
            box-shadow: 0 10px 30px var(--shadow-dark);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid var(--beige);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--sage);
            border-radius: 13px 13px 0 0;
        }

        .modal-header h3 {
            color: var(--white);
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--white);
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: var(--transition);
        }

        .modal-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 20px;
        }

        .warning-message {
            background-color: rgba(220, 53, 69, 0.05);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .warning-message i {
            font-size: 40px;
            color: #dc3545;
            margin-bottom: 15px;
            display: block;
        }

        .warning-message h4 {
            color: #dc3545;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .warning-message p {
            color: var(--text-dark);
            line-height: 1.5;
        }

        .user-info {
            background-color: var(--beige);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid var(--taupe);
        }

        .user-info-item {
            display: flex;
            margin-bottom: 10px;
        }

        .user-info-item:last-child {
            margin-bottom: 0;
        }

        .user-info-label {
            font-weight: 600;
            color: var(--espresso);
            min-width: 100px;
        }

        .user-info-value {
            color: var(--espresso);
        }

        .modal-actions {
            padding: 20px;
            border-top: 1px solid var(--beige);
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .modal-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-button.delete {
            background-color: #dc3545;
            color: white;
        }

        .modal-button.delete:hover {
            background-color: #c82333;
        }

        .modal-button.cancel {
            background-color: var(--taupe);
            color: var(--white);
        }

        .modal-button.cancel:hover {
            background-color: var(--espresso);
        }

        /* Message Alert */
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: 600;
            z-index: 1003;
            animation: slideIn 0.3s ease;
            max-width: 400px;
            box-shadow: 0 4px 15px var(--shadow-dark);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .alert-success {
            background-color: #28a745;
            color: white;
            border-left: 4px solid #1e7e34;
        }

        .alert-error {
            background-color: #dc3545;
            color: white;
            border-left: 4px solid #bd2130;
        }

        .alert-info {
            background-color: var(--sage);
            color: var(--white);
            border-left: 4px solid var(--espresso);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--taupe);
            background-color: var(--white);
            border-radius: 10px;
            border: 2px solid var(--beige);
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--espresso);
        }

        .empty-state p {
            font-size: 16px;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
            }

            .logo h1 {
                font-size: 20px;
            }

            .container {
                padding: 0 15px;
                margin: 20px auto;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .header-title h2 {
                font-size: 24px;
            }

            .header-stats {
                align-self: stretch;
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .action-button {
                justify-content: center;
            }

            .user-info-nav span:not(.user-type-nav) {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .modal-content {
                max-height: 80vh;
                overflow-y: auto;
            }

            .modal-actions {
                flex-direction: column;
            }

            .modal-button {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 0 15px;
            }

            .logo h1 {
                font-size: 18px;
            }

            .container {
                padding: 0 10px;
            }

            .users-table th,
            .users-table td {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Message Alert -->
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <i class="fas fa-book"></i>
            <h1>Manajemen User</h1>
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
                <a href="profile.php">
                    <i class="fas fa-user-cog"></i>
                    <span>Edit Profil</span>
                </a>
            </li>
            <li>
                <a href="user_mgmt.php" class="active">
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
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Container -->
    <div class="container">
        <div class="header">
            <div class="header-title">
                <i class="fas fa-users-cog"></i>
                <h2>Daftar User Terdaftar</h2>
            </div>
            
            <div class="header-stats">
                Total User: <span><?php echo $total_users; ?></span>
            </div>
        </div>

        <?php if ($total_users > 0): ?>
            <div class="table-container">
                <div class="table-responsive">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-user"></i> Username</th>
                                <th><i class="fas fa-user-tag"></i> Tipe User</th>
                                <th><i class="fas fa-calendar"></i> Tanggal Bergabung</th>
                                <th><i class="fas fa-cog"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users_result->fetch_assoc()): ?>
                                <?php
                                $created_date = new DateTime($user['created_at']);
                                $formatted_date = $created_date->format('d/m/Y H:i');
                                ?>
                                <tr data-user-id="<?php echo $user['id']; ?>">
                                    <td>#<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td>
                                        <span class="user-type-badge <?php echo $user['user_type']; ?>">
                                            <?php echo ucfirst($user['user_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $formatted_date; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-button delete" 
                                                    onclick="showDeleteModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo $user['user_type']; ?>')">
                                                <i class="fas fa-trash"></i> Hapus User
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <h3>Tidak ada user lain</h3>
                <p>Belum ada user lain yang terdaftar dalam sistem</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus User</h3>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="warning-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <h4>PERINGATAN: Tindakan ini tidak dapat dibatalkan!</h4>
                    <p>Semua data yang terkait dengan user ini akan dihapus secara permanen dari sistem. Pastikan Anda yakin ingin melanjutkan.</p>
                </div>
                
                <div class="user-info" id="userInfo">
                    <!-- User info will be populated here -->
                </div>
            </div>
            <div class="modal-actions">
                <button class="modal-button delete" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Hapus Permanen
                </button>
                <button class="modal-button cancel" onclick="closeModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
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

        let currentUserId = null;

        // Modal functions
        const deleteModal = document.getElementById('deleteModal');

        function showDeleteModal(userId, username, userType) {
            currentUserId = userId;
            
            // Populate user info
            document.getElementById('userInfo').innerHTML = `
                <div class="user-info-item">
                    <span class="user-info-label">User ID:</span>
                    <span class="user-info-value">#${userId.toString().padStart(4, '0')}</span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Username:</span>
                    <span class="user-info-value">${username}</span>
                </div>
                <div class="user-info-item">
                    <span class="user-info-label">Tipe User:</span>
                    <span class="user-info-value">${userType.charAt(0).toUpperCase() + userType.slice(1)}</span>
                </div>
            `;
            
            // Set up delete button
            document.getElementById('confirmDeleteBtn').onclick = () => deleteUser(userId);
            
            deleteModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            deleteModal.classList.remove('active');
            document.body.style.overflow = 'auto';
            currentUserId = null;
        }

        function deleteUser(userId) {
            if (!userId) return;
            
            // Show confirmation dialog
            if (!confirm('Apakah Anda yakin ingin menghapus user ini secara permanen?')) {
                return;
            }
            
            // Send delete request
            fetch('proses_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User berhasil dihapus!');
                    // Remove the user row from table
                    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
                    if (userRow) {
                        userRow.style.transition = 'all 0.3s';
                        userRow.style.opacity = '0';
                        userRow.style.transform = 'translateX(-100%)';
                        setTimeout(() => userRow.remove(), 300);
                    }
                    // Update total users count
                    const totalUsersElement = document.querySelector('.header-stats span');
                    if (totalUsersElement) {
                        const currentCount = parseInt(totalUsersElement.textContent);
                        totalUsersElement.textContent = currentCount - 1;
                    }
                } else {
                    alert('Gagal menghapus user: ' + data.message);
                }
                closeModal();
            })
            .catch(error => {
                alert('Terjadi kesalahan: ' + error.message);
                closeModal();
            });
        }

        // Close modal with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Close modal when clicking outside
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                closeModal();
            }
        });

        // Auto-hide message alert
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);

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

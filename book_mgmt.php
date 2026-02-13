<?php
require_once 'config.php';

// Cek login dan hak akses admin
if (!is_logged_in() || !is_admin()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];

// Mode: 'books' atau 'genres'
$mode = isset($_GET['mode']) && in_array($_GET['mode'], ['books', 'genres']) ? $_GET['mode'] : 'books';

// Edit book/genre ID
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;

// Ambil data untuk edit
$edit_data = null;
if ($edit_id > 0) {
    if ($mode === 'books') {
        $sql = "SELECT b.*, g.nama_genre FROM books b 
                JOIN genres g ON b.genre_id = g.id 
                WHERE b.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_data = $result->fetch_assoc();
        $stmt->close();
    } else {
        $sql = "SELECT * FROM genres WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_data = $result->fetch_assoc();
        $stmt->close();
    }
}

// Ambil semua genre untuk dropdown
$genres_result = $conn->query("SELECT * FROM genres ORDER BY nama_genre");

// Ambil semua buku dengan genre
if ($mode === 'books') {
    $books_sql = "SELECT b.*, g.nama_genre FROM books b 
                  JOIN genres g ON b.genre_id = g.id 
                  ORDER BY b.created_at DESC";
    $books_result = $conn->query($books_sql);
    $total_books = $books_result->num_rows;
} else {
    $genres_list = $conn->query("SELECT g.*, COUNT(b.id) as book_count 
                                 FROM genres g 
                                 LEFT JOIN books b ON g.id = b.genre_id 
                                 GROUP BY g.id 
                                 ORDER BY g.nama_genre");
    $total_genres = $genres_list->num_rows;
}

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
    <title>Manajemen Buku - Perpustakaan Digital</title>
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

        /* Tabs */
        .tabs {
            display: flex;
            background-color: var(--beige);
            border-radius: 10px;
            border: 2px solid var(--taupe);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .tab {
            flex: 1;
            padding: 15px 20px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            background: none;
            color: var(--espresso);
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .tab:hover {
            background-color: rgba(175, 170, 111, 0.1);
        }

        .tab.active {
            background-color: var(--sage);
            color: var(--white);
        }

        /* Form Styles */
        .form-container {
            background-color: var(--white);
            border-radius: 10px;
            padding: 30px;
            border: 2px solid var(--beige);
            margin-bottom: 30px;
            box-shadow: 0 5px 15px var(--shadow);
        }

        .form-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--beige);
        }

        .form-header i {
            font-size: 24px;
            color: var(--sage);
        }

        .form-header h3 {
            font-size: 22px;
            color: var(--espresso);
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .form-group.full-width {
            flex: 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--espresso);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--beige);
            border-radius: 8px;
            background-color: var(--white);
            color: var(--text-dark);
            font-size: 16px;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(175, 170, 111, 0.2);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: var(--taupe);
            font-size: 12px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--beige);
        }

        .form-button {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-button.save {
            background-color: var(--sage);
            color: var(--white);
        }

        .form-button.save:hover {
            background-color: var(--espresso);
        }

        .form-button.cancel {
            background-color: var(--taupe);
            color: var(--white);
        }

        .form-button.cancel:hover {
            background-color: var(--espresso);
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

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .data-table thead {
            background-color: var(--sage);
        }

        .data-table th {
            padding: 18px 20px;
            text-align: left;
            color: var(--white);
            font-weight: 600;
            border-bottom: 2px solid var(--taupe);
            white-space: nowrap;
        }

        .data-table tbody tr {
            border-bottom: 1px solid var(--beige);
            transition: var(--transition);
        }

        .data-table tbody tr:hover {
            background-color: rgba(175, 170, 111, 0.1);
        }

        .data-table tbody tr:last-child {
            border-bottom: none;
        }

        .data-table td {
            padding: 16px 20px;
            color: var(--text-dark);
            vertical-align: top;
        }

        .book-title {
            font-weight: 600;
            color: var(--espresso);
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .book-author {
            font-size: 14px;
            color: var(--taupe);
        }

        .book-description {
            font-size: 14px;
            line-height: 1.4;
            color: var(--text-light);
            margin-top: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .genre-badge {
            display: inline-block;
            padding: 5px 12px;
            background-color: rgba(175, 170, 111, 0.1);
            color: var(--espresso);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--sage);
        }

        .genre-book-count {
            display: inline-block;
            padding: 2px 8px;
            background-color: var(--beige);
            color: var(--espresso);
            border-radius: 10px;
            font-size: 11px;
            margin-left: 5px;
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
            text-decoration: none;
        }

        .action-button.edit {
            background-color: rgba(175, 170, 111, 0.1);
            color: var(--espresso);
            border: 1px solid var(--sage);
        }

        .action-button.edit:hover {
            background-color: var(--sage);
            color: var(--white);
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

            .form-row {
                flex-direction: column;
                gap: 15px;
            }

            .form-container {
                padding: 20px;
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
            .tabs {
                flex-direction: column;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-button {
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

            .data-table th,
            .data-table td {
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
            <h1>Manajemen Buku</h1>
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
                <a href="user_mgmt.php">
                    <i class="fas fa-users-cog"></i>
                    <span>Manajemen User</span>
                </a>
            </li>
            <li>
                <a href="book_mgmt.php" class="active">
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
                <i class="fas fa-book-medical"></i>
                <h2><?php echo $mode === 'books' ? 'Manajemen Buku' : 'Manajemen Genre'; ?></h2>
            </div>
            
            <div class="header-stats">
                Total <?php echo $mode === 'books' ? 'Buku' : 'Genre'; ?>: 
                <span><?php echo $mode === 'books' ? $total_books : $total_genres; ?></span>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab <?php echo $mode === 'books' ? 'active' : ''; ?>" 
                    onclick="window.location.href='book_mgmt.php?mode=books'">
                <i class="fas fa-book"></i> Buku
            </button>
            <button class="tab <?php echo $mode === 'genres' ? 'active' : ''; ?>" 
                    onclick="window.location.href='book_mgmt.php?mode=genres'">
                <i class="fas fa-bookmark"></i> Genre
            </button>
        </div>

        <?php if ($mode === 'books'): ?>
            <!-- Form Tambah/Edit Buku -->
            <div class="form-container">
                <div class="form-header">
                    <i class="fas fa-<?php echo $edit_id ? 'edit' : 'plus'; ?>"></i>
                    <h3><?php echo $edit_id ? 'Edit Buku' : 'Tambah Buku Baru'; ?></h3>
                </div>
                
                <form method="POST" action="proses_buku.php" id="bookForm">
                    <input type="hidden" name="action" value="<?php echo $edit_id ? 'update' : 'create'; ?>">
                    <?php if ($edit_id): ?>
                        <input type="hidden" name="book_id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="judul"><i class="fas fa-heading"></i> Judul Buku</label>
                            <input type="text" id="judul" name="judul" required
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>">
                            <small>Masukkan judul buku lengkap</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="pengarang"><i class="fas fa-user"></i> Pengarang</label>
                            <input type="text" id="pengarang" name="pengarang" required
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['pengarang']) : ''; ?>">
                            <small>Nama penulis/pengarang</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tahun"><i class="fas fa-calendar"></i> Tahun Terbit</label>
                            <input type="number" id="tahun" name="tahun" required min="1000" max="2100"
                                   value="<?php echo $edit_data ? $edit_data['tahun'] : date('Y'); ?>">
                            <small>Tahun publikasi buku</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="genre_id"><i class="fas fa-bookmark"></i> Genre</label>
                            <select id="genre_id" name="genre_id" required>
                                <option value="">Pilih Genre</option>
                                <?php 
                                $genres_result->data_seek(0); // Reset pointer
                                while ($genre = $genres_result->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $genre['id']; ?>"
                                        <?php echo ($edit_data && $edit_data['genre_id'] == $genre['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($genre['nama_genre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <small>Pilih kategori genre buku</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="halaman"><i class="fas fa-file"></i> Jumlah Halaman</label>
                            <input type="number" id="halaman" name="halaman" required min="1"
                                   value="<?php echo $edit_data ? $edit_data['halaman'] : ''; ?>">
                            <small>Total halaman buku</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="link_buku"><i class="fas fa-link"></i> Link Buku (URL)</label>
                            <input type="url" id="link_buku" name="link_buku"
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['link_buku']) : ''; ?>">
                            <small>Link ke sumber buku (opsional)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="link_foto"><i class="fas fa-image"></i> Link Foto (URL)</label>
                            <input type="url" id="link_foto" name="link_foto"
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['link_foto']) : ''; ?>">
                            <small>Link ke gambar sampul buku (opsional)</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="deskripsi"><i class="fas fa-align-left"></i> Deskripsi Buku</label>
                            <textarea id="deskripsi" name="deskripsi" required><?php echo $edit_data ? htmlspecialchars($edit_data['deskripsi']) : ''; ?></textarea>
                            <small>Deskripsi lengkap tentang buku</small>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="form-button save">
                            <i class="fas fa-save"></i> <?php echo $edit_id ? 'Update Buku' : 'Simpan Buku'; ?>
                        </button>
                        <?php if ($edit_id): ?>
                            <a href="book_mgmt.php?mode=books" class="form-button cancel">
                                <i class="fas fa-times"></i> Batal Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Daftar Buku -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-book"></i> Informasi Buku</th>
                                <th><i class="fas fa-bookmark"></i> Genre</th>
                                <th><i class="fas fa-calendar"></i> Tahun</th>
                                <th><i class="fas fa-cog"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_books > 0): ?>
                                <?php 
                                $books_result->data_seek(0); // Reset pointer untuk loop
                                while ($book = $books_result->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td>#<?php echo str_pad($book['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <div class="book-title"><?php echo htmlspecialchars($book['judul']); ?></div>
                                            <div class="book-author"><?php echo htmlspecialchars($book['pengarang']); ?></div>
                                            <div class="book-description"><?php echo htmlspecialchars(substr($book['deskripsi'], 0, 100)); ?>...</div>
                                        </td>
                                        <td>
                                            <span class="genre-badge"><?php echo htmlspecialchars($book['nama_genre']); ?></span>
                                        </td>
                                        <td><?php echo $book['tahun']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="book_mgmt.php?mode=books&edit=<?php echo $book['id']; ?>" 
                                                   class="action-button edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button class="action-button delete" 
                                                        onclick="deleteBook(<?php echo $book['id']; ?>, '<?php echo htmlspecialchars(addslashes($book['judul'])); ?>')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="fas fa-book"></i>
                                            <h3>Belum ada buku</h3>
                                            <p>Tambahkan buku pertama Anda menggunakan form di atas</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <!-- Form Tambah/Edit Genre -->
            <div class="form-container">
                <div class="form-header">
                    <i class="fas fa-<?php echo $edit_id ? 'edit' : 'plus'; ?>"></i>
                    <h3><?php echo $edit_id ? 'Edit Genre' : 'Tambah Genre Baru'; ?></h3>
                </div>
                
                <form method="POST" action="proses_genre.php" id="genreForm">
                    <input type="hidden" name="action" value="<?php echo $edit_id ? 'update' : 'create'; ?>">
                    <?php if ($edit_id): ?>
                        <input type="hidden" name="genre_id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="nama_genre"><i class="fas fa-bookmark"></i> Nama Genre</label>
                            <input type="text" id="nama_genre" name="nama_genre" required
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama_genre']) : ''; ?>">
                            <small>Masukkan nama genre (unik, tidak boleh sama dengan yang sudah ada)</small>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="form-button save">
                            <i class="fas fa-save"></i> <?php echo $edit_id ? 'Update Genre' : 'Simpan Genre'; ?>
                        </button>
                        <?php if ($edit_id): ?>
                            <a href="book_mgmt.php?mode=genres" class="form-button cancel">
                                <i class="fas fa-times"></i> Batal Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Daftar Genre -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-bookmark"></i> Nama Genre</th>
                                <th><i class="fas fa-book"></i> Jumlah Buku</th>
                                <th><i class="fas fa-cog"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_genres > 0): ?>
                                <?php $genres_list->data_seek(0); // Reset pointer ?>
                                <?php while ($genre = $genres_list->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($genre['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <span class="genre-badge"><?php echo htmlspecialchars($genre['nama_genre']); ?></span>
                                        </td>
                                        <td>
                                            <span class="genre-book-count"><?php echo $genre['book_count']; ?> buku</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="book_mgmt.php?mode=genres&edit=<?php echo $genre['id']; ?>" 
                                                   class="action-button edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <?php if ($genre['book_count'] == 0): ?>
                                                    <button class="action-button delete" 
                                                            onclick="deleteGenre(<?php echo $genre['id']; ?>, '<?php echo htmlspecialchars(addslashes($genre['nama_genre'])); ?>')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                <?php else: ?>
                                                    <span style="color: var(--taupe); font-size: 12px;">
                                                        <i class="fas fa-info-circle"></i> Ada <?php echo $genre['book_count']; ?> buku
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <i class="fas fa-bookmark"></i>
                                            <h3>Belum ada genre</h3>
                                            <p>Tambahkan genre pertama Anda menggunakan form di atas</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
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

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const bookForm = document.getElementById('bookForm');
            const genreForm = document.getElementById('genreForm');
            
            if (bookForm) {
                bookForm.addEventListener('submit', function(e) {
                    const tahun = document.getElementById('tahun');
                    const halaman = document.getElementById('halaman');
                    
                    if (tahun.value < 1000 || tahun.value > 2100) {
                        e.preventDefault();
                        alert('Tahun terbit harus antara 1000 dan 2100');
                        tahun.focus();
                        return false;
                    }
                    
                    if (halaman.value < 1) {
                        e.preventDefault();
                        alert('Jumlah halaman minimal 1');
                        halaman.focus();
                        return false;
                    }
                    
                    // Validasi URL opsional
                    const linkBuku = document.getElementById('link_buku');
                    const linkFoto = document.getElementById('link_foto');
                    
                    if (linkBuku.value && !isValidUrl(linkBuku.value)) {
                        e.preventDefault();
                        alert('Link buku harus berupa URL yang valid');
                        linkBuku.focus();
                        return false;
                    }
                    
                    if (linkFoto.value && !isValidUrl(linkFoto.value)) {
                        e.preventDefault();
                        alert('Link foto harus berupa URL yang valid');
                        linkFoto.focus();
                        return false;
                    }
                });
            }
            
            if (genreForm) {
                genreForm.addEventListener('submit', function(e) {
                    const namaGenre = document.getElementById('nama_genre');
                    
                    if (namaGenre.value.length < 2) {
                        e.preventDefault();
                        alert('Nama genre minimal 2 karakter');
                        namaGenre.focus();
                        return false;
                    }
                });
            }
            
            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }
        });
        
        // Delete functions
        function deleteBook(bookId, bookTitle) {
            if (confirm(`Apakah Anda yakin ingin menghapus buku "${bookTitle}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
                fetch('proses_buku.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&book_id=${bookId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Buku berhasil dihapus!');
                        window.location.reload();
                    } else {
                        alert('Gagal menghapus buku: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error.message);
                });
            }
        }
        
        function deleteGenre(genreId, genreName) {
            if (confirm(`Apakah Anda yakin ingin menghapus genre "${genreName}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
                fetch('proses_genre.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&genre_id=${genreId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Genre berhasil dihapus!');
                        window.location.reload();
                    } else {
                        alert('Gagal menghapus genre: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error.message);
                });
            }
        }
        
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

<?php
require_once 'config.php';

// Cek login
if (!is_logged_in()) {
    redirect('login.php');
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];

// Ambil parameter pencarian dan filter
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$genre = isset($_GET['genre']) ? intval($_GET['genre']) : 0;
$sort = isset($_GET['sort']) ? clean_input($_GET['sort']) : 'judul_asc';

// Parse sort parameter
$sort_options = [
    'judul_asc' => 'b.judul ASC',
    'judul_desc' => 'b.judul DESC',
    'pengarang_asc' => 'b.pengarang ASC',
    'pengarang_desc' => 'b.pengarang DESC',
    'tahun_asc' => 'b.tahun ASC',
    'tahun_desc' => 'b.tahun DESC'
];
$order_by = isset($sort_options[$sort]) ? $sort_options[$sort] : 'b.judul ASC';

// Query untuk mendapatkan buku
$sql = "SELECT b.*, g.nama_genre FROM books b 
        JOIN genres g ON b.genre_id = g.id 
        WHERE 1=1";

$params = [];
$types = "";

// Filter pencarian
if (!empty($search)) {
    $sql .= " AND (b.judul LIKE ? OR b.pengarang LIKE ? OR b.deskripsi LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Filter genre
if ($genre > 0) {
    $sql .= " AND b.genre_id = ?";
    $params[] = $genre;
    $types .= "i";
}

// Order by
$sql .= " ORDER BY $order_by";

// Prepare statement
$stmt = $conn->prepare($sql);

// Bind parameters jika ada
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$books_result = $stmt->get_result();

// Ambil semua genre untuk dropdown
$genres_result = $conn->query("SELECT * FROM genres ORDER BY nama_genre");

// Hitung total buku
$total_books = $books_result->num_rows;

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
    <title>Dashboard - Perpustakaan Digital</title>
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
            overflow-x: hidden;
            line-height: 1.6;
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
            transition: var(--transition);
        }

        .logo-desktop {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-right: auto;
        }

        .logo-desktop i {
            font-size: 28px;
            color: var(--beige);
            transition: var(--transition);
        }

        .logo-desktop h1 {
            font-size: 24px;
            color: var(--beige);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .user-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: var(--beige);
            padding: 8px 24px;
            border-radius: 30px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .user-info:hover {
            border-color: var(--sage);
            transform: translateY(-2px);
        }

        .user-info i {
            font-size: 18px;
            color: var(--espresso);
        }

        .user-info span {
            color: var(--espresso);
            font-weight: 500;
            font-size: 15px;
        }

        .user-type {
            background-color: var(--sage);
            color: var(--white);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-form {
            display: flex;
            background-color: var(--white);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 3px 12px var(--shadow);
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .search-form:focus-within {
            border-color: var(--sage);
            box-shadow: 0 5px 20px var(--shadow);
        }

        .search-input {
            flex-grow: 1;
            padding: 14px 24px;
            border: none;
            background-color: transparent;
            color: var(--espresso);
            font-size: 16px;
            min-width: 350px;
            outline: none;
        }

        .search-input::placeholder {
            color: var(--taupe);
        }

        .search-button {
            background-color: var(--sage);
            color: var(--white);
            border: none;
            padding: 14px 24px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 60px;
        }

        .search-button:hover {
            background-color: var(--espresso);
        }

        /* Sidebar Styles */
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--beige);
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .sidebar-toggle:hover {
            background-color: rgba(223, 213, 199, 0.1);
            transform: rotate(90deg);
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 300px;
            background-color: var(--espresso);
            padding-top: 80px;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
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
            padding: 0 30px;
            border-bottom: 3px solid var(--beige);
        }

        .sidebar-header h3 {
            color: var(--white);
            font-size: 22px;
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
            transform: rotate(90deg);
        }

        .sidebar-menu {
            list-style: none;
            padding: 30px 0;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 18px 30px;
            color: var(--beige);
            text-decoration: none;
            transition: var(--transition);
            border-left: 4px solid transparent;
            font-size: 16px;
        }

        .sidebar-menu a:hover {
            background-color: rgba(223, 213, 199, 0.1);
            border-left-color: var(--sage);
            transform: translateX(8px);
        }

        .sidebar-menu a.active {
            background-color: var(--beige);
            border-left-color: var(--sage);
            color: var(--espresso);
        }

        .sidebar-menu i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(75, 67, 58, 0.7);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            backdrop-filter: blur(3px);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

/* Book Detail Modal Styles */
.book-detail-paragraphs {
    display: flex;
    flex-direction: column;
    gap: 25px;
    padding: 10px 5px;
}

.book-detail-row {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--beige);
}

.book-detail-row:last-of-type {
    border-bottom: none;
}

.detail-label {
    min-width: 160px;
    font-weight: 600;
    color: var(--espresso);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    line-height: 1.4;
}

.detail-label i {
    font-size: 18px;
    width: 24px;
    text-align: center;
    color: var(--sage);
}

.detail-value {
    flex: 1;
    color: var(--text-dark);
    font-size: 16px;
    line-height: 1.6;
    padding-left: 10px;
}

.book-description-section {
    background-color: var(--beige);
    padding: 25px;
    border-radius: 15px;
    border: 2px solid var(--taupe);
    margin-top: 10px;
}

.book-description-section .detail-label {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--sage);
    font-size: 18px;
    color: var(--espresso);
}

.book-description-full {
    color: var(--text-dark);
    line-height: 1.8;
    font-size: 16px;
    max-height: 300px;
    overflow-y: auto;
    padding-right: 15px;
    white-space: pre-wrap;
}

.book-description-full::-webkit-scrollbar {
    width: 8px;
}

.book-description-full::-webkit-scrollbar-track {
    background: rgba(223, 213, 199, 0.5);
    border-radius: 4px;
}

.book-description-full::-webkit-scrollbar-thumb {
    background: var(--taupe);
    border-radius: 4px;
}

.book-description-full::-webkit-scrollbar-thumb:hover {
    background: var(--sage);
}

/* Tombol Baca Sekarang */
.read-btn {
    display: inline-flex !important;
    align-items: center;
    gap: 10px;
    padding: 12px 25px !important;
    background-color: var(--sage) !important;
    color: white !important;
    border: none !important;
    border-radius: 10px !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: var(--transition) !important;
    text-decoration: none !important;
    font-size: 15px !important;
    margin-top: 5px !important;
}

.read-btn:hover {
    background-color: var(--espresso) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 5px 15px rgba(75, 67, 58, 0.3) !important;
}

.read-btn i {
    font-size: 14px;
}

/* Responsive untuk modal detail */
@media (max-width: 768px) {
    .book-detail-row {
        flex-direction: column;
        gap: 8px;
        padding-bottom: 15px;
    }
    
    .detail-label {
        min-width: 100%;
        padding-bottom: 5px;
        border-bottom: 1px solid var(--beige);
    }
    
    .detail-value {
        padding-left: 0;
        padding-top: 5px;
    }
    
    .book-description-section {
        padding: 20px;
    }
    
    .book-description-full {
        max-height: 250px;
    }
}

        /* Filter Section */
        .filters {
            background-color: var(--beige);
            padding: 30px;
            margin: 30px;
            border-radius: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            align-items: center;
            box-shadow: 0 5px 20px var(--shadow);
            border: 2px solid var(--taupe);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 250px;
            flex: 1;
        }

        .filter-group label {
            font-weight: 600;
            color: var(--espresso);
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-select {
            padding: 14px 20px;
            border: 2px solid var(--taupe);
            border-radius: 12px;
            background-color: var(--white);
            color: var(--espresso);
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234B433A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 20px center;
            background-size: 16px;
            padding-right: 50px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(175, 170, 111, 0.2);
        }

        .filter-button {
            background-color: var(--espresso);
            color: var(--beige);
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(75, 67, 58, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
        }

        .filter-button:hover {
            background-color: var(--sage);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(175, 170, 111, 0.4);
        }

        /* Books Container */
        .container {
            padding: 30px;
            min-height: calc(100vh - 250px);
        }

        .books-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 3px solid var(--beige);
        }

        .books-count {
            font-size: 20px;
            font-weight: 600;
            color: var(--espresso);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .books-count span {
            color: var(--sage);
            background-color: var(--beige);
            padding: 8px 18px;
            border-radius: 25px;
            font-weight: 700;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 35px;
            margin-bottom: 50px;
        }

        .book-card {
            background-color: var(--white);
            border-radius: 20px;
            overflow: hidden;
            transition: var(--transition);
            border: 2px solid var(--beige);
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 5px 20px var(--shadow);
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(75, 67, 58, 0.15);
            border-color: var(--sage);
        }

        .book-image {
            height: 250px;
            overflow: hidden;
            position: relative;
        }

        .book-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .book-card:hover .book-image img {
            transform: scale(1.08);
        }

        .book-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 60%, rgba(75, 67, 58, 0.1));
        }

        .book-content {
            padding: 30px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .book-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--espresso);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 25px;
        }

        .book-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--taupe);
            background-color: var(--beige);
            padding: 8px 16px;
            border-radius: 20px;
            transition: var(--transition);
        }

        .book-meta-item:hover {
            background-color: var(--sage);
            color: var(--white);
        }

        .book-meta-item i {
            font-size: 13px;
        }

        .book-description {
            color: var(--text-light);
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            position: relative;
        }

        .book-actions {
            display: flex;
            gap: 15px;
            margin-top: auto;
        }

        .book-button {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .book-button.view {
            background-color: var(--sage);
            color: var(--white);
            border: 2px solid var(--sage);
        }

        .book-button.view:hover {
            background-color: transparent;
            color: var(--sage);
            transform: translateY(-2px);
        }

        .book-button.edit {
            background-color: var(--espresso);
            color: var(--beige);
            border: 2px solid var(--espresso);
            display: <?php echo $user_type == 'admin' ? 'flex' : 'none'; ?>;
        }

        .book-button.edit:hover {
            background-color: transparent;
            color: var(--espresso);
            transform: translateY(-2px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(75, 67, 58, 0.85);
            z-index: 1002;
            justify-content: center;
            align-items: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex;
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: var(--white);
            border-radius: 25px;
            width: 100%;
            max-width: 800px;
            max-height: 85vh;
            overflow-y: auto;
            border: 3px solid var(--sage);
            transform: translateY(30px);
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 30px;
            border-bottom: 3px solid var(--beige);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--espresso);
            border-radius: 25px 25px 0 0;
        }

        .modal-header h3 {
            color: var(--beige);
            font-size: 28px;
            font-weight: 700;
            max-width: 80%;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--beige);
            font-size: 28px;
            cursor: pointer;
            padding: 10px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: var(--transition);
        }

        .modal-close:hover {
            background-color: rgba(223, 213, 199, 0.2);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 40px;
        }

        .book-detail-item {
            margin-bottom: 25px;
        }

        .book-detail-item label {
            display: block;
            font-weight: 600;
            color: var(--espresso);
            margin-bottom: 10px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .book-detail-item .value {
            color: var(--text-dark);
            padding: 18px;
            background-color: var(--beige);
            border-radius: 12px;
            border: 2px solid var(--taupe);
            line-height: 1.7;
            font-size: 16px;
        }

        .book-detail-item .value a {
            color: var(--sage);
            text-decoration: none;
            word-break: break-all;
            transition: var(--transition);
            font-weight: 500;
        }

        .book-detail-item .value a:hover {
            color: var(--espresso);
            text-decoration: underline;
        }

        .book-description-full {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 20px;
            line-height: 1.8;
        }

        .book-description-full::-webkit-scrollbar {
            width: 10px;
        }

        .book-description-full::-webkit-scrollbar-track {
            background: var(--beige);
            border-radius: 5px;
        }

        .book-description-full::-webkit-scrollbar-thumb {
            background: var(--taupe);
            border-radius: 5px;
        }

        .book-description-full::-webkit-scrollbar-thumb:hover {
            background: var(--sage);
        }

        .modal-actions {
            padding: 30px;
            border-top: 3px solid var(--beige);
            display: flex;
            gap: 20px;
            justify-content: flex-end;
            background-color: var(--beige);
            border-radius: 0 0 25px 25px;
        }

        .modal-button {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modal-button.edit {
            background-color: var(--sage);
            color: var(--white);
            border: 2px solid var(--sage);
        }

        .modal-button.edit:hover {
            background-color: transparent;
            color: var(--sage);
            transform: translateY(-3px);
        }

        .modal-button.delete {
            background-color: #d9534f;
            color: white;
            border: 2px solid #d9534f;
        }

        .modal-button.delete:hover {
            background-color: transparent;
            color: #d9534f;
            transform: translateY(-3px);
        }

        .modal-button.close {
            background-color: var(--espresso);
            color: var(--beige);
            border: 2px solid var(--espresso);
        }

        .modal-button.close:hover {
            background-color: transparent;
            color: var(--espresso);
            transform: translateY(-3px);
        }

        /* Add Book Button (Admin Only) */
        .add-book-btn {
            position: fixed;
            bottom: 50px;
            right: 50px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sage), var(--espresso));
            color: var(--beige);
            border: none;
            font-size: 32px;
            cursor: pointer;
            display: <?php echo $user_type == 'admin' ? 'flex' : 'none'; ?>;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(75, 67, 58, 0.4);
            transition: var(--transition);
            z-index: 100;
            border: 3px solid var(--beige);
        }

        .add-book-btn:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 15px 40px rgba(75, 67, 58, 0.6);
            background: linear-gradient(135deg, var(--espresso), var(--sage));
        }

        /* Message Alert */
        .alert {
            position: fixed;
            top: 100px;
            right: 30px;
            padding: 20px 35px;
            border-radius: 15px;
            font-weight: 600;
            z-index: 1003;
            animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 20px;
            transform-origin: top right;
            border-left: 5px solid;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        .alert-success {
            background-color: #2ecc71;
            color: white;
            border-left-color: #27ae60;
        }

        .alert-error {
            background-color: #e74c3c;
            color: white;
            border-left-color: #c0392b;
        }

        .alert-info {
            background-color: var(--sage);
            color: var(--white);
            border-left-color: var(--espresso);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 100px 20px;
            color: var(--taupe);
        }

        .empty-state i {
            font-size: 100px;
            margin-bottom: 30px;
            color: var(--beige);
            opacity: 0.8;
        }

        .empty-state h3 {
            font-size: 32px;
            margin-bottom: 20px;
            color: var(--espresso);
            font-weight: 700;
        }

        .empty-state p {
            font-size: 20px;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.8;
            color: var(--text-light);
        }

        /* Loading Spinner */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 80px;
            min-height: 300px;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid var(--beige);
            border-top-color: var(--sage);
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 30px;
            }
            
            .search-input {
                min-width: 300px;
            }
        }

        @media (max-width: 992px) {
            .logo-desktop {
                display: none;
            }
            
            .user-center {
                position: static;
                transform: none;
                margin-right: auto;
                margin-left: 20px;
            }
            
            .search-container {
                margin-left: auto;
            }
            
            .search-input {
                min-width: 250px;
            }
            
            .navbar {
                padding: 0 20px;
                height: 65px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0 15px;
                height: 60px;
            }
            
            .sidebar-toggle {
                font-size: 22px;
                padding: 8px;
                margin-right: 10px;
            }
            
            .logo-desktop {
                display: none;
            }
            
            .user-center {
                display: none;
            }
            
            .search-container {
                flex-grow: 1;
                margin: 0 10px;
                gap: 10px;
            }
            
            .search-input {
                min-width: 0;
                width: 100%;
                padding: 12px 20px;
            }
            
            .search-button {
                padding: 12px 20px;
                min-width: 50px;
            }
            
            .user-info-mobile {
                display: flex;
                align-items: center;
                gap: 10px;
                background-color: var(--beige);
                padding: 6px 15px;
                border-radius: 25px;
                color: var(--espresso);
                font-weight: 500;
                font-size: 14px;
            }
            
            .user-type-mobile {
                background-color: var(--sage);
                color: var(--white);
                padding: 4px 12px;
                border-radius: 15px;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
            }
            
            .filters {
                margin: 20px;
                padding: 25px;
                flex-direction: column;
                align-items: stretch;
                gap: 25px;
            }
            
            .filter-group {
                min-width: 100%;
            }
            
            .container {
                padding: 20px;
            }
            
            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 25px;
            }
            
            .add-book-btn {
                bottom: 30px;
                right: 30px;
                width: 70px;
                height: 70px;
                font-size: 28px;
            }
            
            .alert {
                top: 80px;
                right: 20px;
                left: 20px;
                max-width: none;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                padding: 0 10px;
                height: 55px;
            }
            
            .search-input {
                padding: 10px 15px;
                font-size: 14px;
            }
            
            .search-button {
                padding: 10px 15px;
                min-width: 45px;
            }
            
            .sidebar-toggle {
                font-size: 20px;
                padding: 6px;
            }
            
            .books-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .book-card {
                max-width: 100%;
            }
            
            .modal-content {
                max-height: 90vh;
                margin: 10px;
            }
            
            .modal-header {
                padding: 20px;
            }
            
            .modal-header h3 {
                font-size: 22px;
            }
            
            .modal-body {
                padding: 25px;
            }
            
            .modal-actions {
                padding: 20px;
                flex-direction: column;
            }
            
            .modal-button {
                width: 100%;
                justify-content: center;
            }
            
            .book-actions {
                flex-direction: column;
            }
            
            .book-button {
                width: 100%;
            }
            
            .add-book-btn {
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            
            .filters {
                margin: 15px;
                padding: 20px;
                border-radius: 15px;
            }
            
            .filter-button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Message Alert -->
    <?php if ($message): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Logo Desktop -->
        <div class="logo-desktop">
            <i class="fas fa-book"></i>
            <h1>Perpustakaan Digital</h1>
        </div>

        <!-- User Info Center -->
        <div class="user-center">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($username); ?></span>
                <span class="user-type"><?php echo ucfirst($user_type); ?></span>
            </div>
        </div>

        <!-- Search Container -->
        <div class="search-container">
            <form class="search-form" id="searchForm" method="GET" action="dashboard.php">
                <input type="text" 
                       class="search-input" 
                       name="search" 
                       placeholder="Cari buku berdasarkan judul, pengarang, atau deskripsi..."
                       value="<?php echo htmlspecialchars($search); ?>"
                       id="searchInput"
                       autocomplete="off">
                <button type="submit" class="search-button" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            <!-- User Info Mobile -->
            <div class="user-info-mobile">
                <span><?php echo htmlspecialchars($username); ?></span>
                <span class="user-type-mobile"><?php echo ucfirst($user_type); ?></span>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu Navigasi</h3>
            <button class="sidebar-close" id="sidebarClose" aria-label="Close Sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="active">
                    <i class="fas fa-home"></i>
                    <span>Halaman Utama</span>
                </a>
            </li>
            <li>
                <a href="profile.php">
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

    <!-- Filters -->
    <div class="filters">
        <form method="GET" action="dashboard.php" id="filterForm">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            
            <div class="filter-group">
                <label for="genre"><i class="fas fa-filter"></i> Filter Genre</label>
                <select name="genre" id="genre" class="filter-select">
                    <option value="0">Semua Genre</option>
                    <?php 
                    // Reset pointer untuk loop kedua
                    $genres_result->data_seek(0);
                    while ($genre_row = $genres_result->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $genre_row['id']; ?>" 
                            <?php echo $genre == $genre_row['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($genre_row['nama_genre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="sort"><i class="fas fa-sort"></i> Urutkan Berdasarkan</label>
                <select name="sort" id="sort" class="filter-select">
                    <option value="judul_asc" <?php echo $sort == 'judul_asc' ? 'selected' : ''; ?>>Judul A-Z</option>
                    <option value="judul_desc" <?php echo $sort == 'judul_desc' ? 'selected' : ''; ?>>Judul Z-A</option>
                    <option value="pengarang_asc" <?php echo $sort == 'pengarang_asc' ? 'selected' : ''; ?>>Pengarang A-Z</option>
                    <option value="pengarang_desc" <?php echo $sort == 'pengarang_desc' ? 'selected' : ''; ?>>Pengarang Z-A</option>
                    <option value="tahun_asc" <?php echo $sort == 'tahun_asc' ? 'selected' : ''; ?>>Tahun Terlama</option>
                    <option value="tahun_desc" <?php echo $sort == 'tahun_desc' ? 'selected' : ''; ?>>Tahun Terbaru</option>
                </select>
            </div>

            <button type="submit" class="filter-button">
                <i class="fas fa-check"></i> Terapkan Filter
            </button>
            
            <?php if ($search || $genre > 0 || $sort != 'judul_asc'): ?>
                <button type="button" class="filter-button" onclick="resetFilters()" style="background-color: var(--taupe);">
                    <i class="fas fa-times"></i> Reset Filter
                </button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Main Content -->
    <main class="container">
        <div class="books-header">
            <div class="books-count">
                <i class="fas fa-book"></i>
                Menampilkan <span><?php echo $total_books; ?></span> buku
                <?php if (!empty($search)): ?>
                    untuk pencarian "<strong><?php echo htmlspecialchars($search); ?></strong>"
                <?php endif; ?>
            </div>
        </div>

        <?php if ($total_books > 0): ?>
            <div class="books-grid" id="booksGrid">
                <?php while ($book = $books_result->fetch_assoc()): ?>
                    <div class="book-card" data-book-id="<?php echo $book['id']; ?>">
                        <div class="book-image">
                            <img src="<?php echo $book['link_foto'] ? htmlspecialchars($book['link_foto']) : 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=250&fit=crop'; ?>" 
                                 alt="<?php echo htmlspecialchars($book['judul']); ?>"
                                 onerror="this.src='https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=250&fit=crop'">
                            <div class="book-image-overlay"></div>
                        </div>
                        <div class="book-content">
                            <h3 class="book-title"><?php echo htmlspecialchars($book['judul']); ?></h3>
                            
                            <div class="book-meta">
                                <div class="book-meta-item">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo htmlspecialchars($book['pengarang']); ?></span>
                                </div>
                                <div class="book-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo $book['tahun']; ?></span>
                                </div>
                                <div class="book-meta-item">
                                    <i class="fas fa-bookmark"></i>
                                    <span><?php echo htmlspecialchars($book['nama_genre']); ?></span>
                                </div>
                                <div class="book-meta-item">
                                    <i class="fas fa-file"></i>
                                    <span><?php echo $book['halaman']; ?> hal</span>
                                </div>
                            </div>

                            <div class="book-description">
                                <?php echo htmlspecialchars(substr($book['deskripsi'], 0, 180)); ?>...
                            </div>

                            <div class="book-actions">
                                <button class="book-button view" onclick="showBookDetail(<?php echo $book['id']; ?>)">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <?php if ($user_type == 'admin'): ?>
                                    <a href="book_mgmt.php?mode=books&edit=<?php echo $book['id']; ?>" class="book-button edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h3>Tidak ada buku ditemukan</h3>
                <p>
                    <?php if (empty($search)): ?>
                        Belum ada buku dalam koleksi. <?php echo $user_type == 'admin' ? 'Tambahkan buku pertama Anda!' : 'Silakan hubungi admin untuk menambahkan buku.'; ?>
                    <?php else: ?>
                        Tidak ada buku yang cocok dengan pencarian "<?php echo htmlspecialchars($search); ?>". Coba kata kunci lain atau reset filter.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Add Book Button (Admin Only) -->
    <?php if ($user_type == 'admin'): ?>
        <button class="add-book-btn" onclick="window.location.href='book_mgmt.php?mode=books'" aria-label="Add New Book">
            <i class="fas fa-plus"></i>
        </button>
    <?php endif; ?>

    <!-- Book Detail Modal -->
    <div class="modal" id="bookDetailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalBookTitle">Detail Buku</h3>
                <button class="modal-close" id="modalClose" aria-label="Close Modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBookContent">
                <!-- Content will be loaded via JavaScript -->
            </div>
            <?php if ($user_type == 'admin'): ?>
                <div class="modal-actions">
                    <button class="modal-button edit" id="editBookBtn">
                        <i class="fas fa-edit"></i> Edit Buku
                    </button>
                    <button class="modal-button delete" id="deleteBookBtn">
                        <i class="fas fa-trash"></i> Hapus Buku
                    </button>
                    <button class="modal-button close" id="closeModalBtn">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            <?php else: ?>
                <div class="modal-actions">
                    <button class="modal-button close" id="closeModalBtn">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Smooth Sidebar Functions
        class SidebarManager {
            constructor() {
                this.sidebar = document.getElementById('sidebar');
                this.sidebarToggle = document.getElementById('sidebarToggle');
                this.sidebarClose = document.getElementById('sidebarClose');
                this.sidebarOverlay = document.getElementById('sidebarOverlay');
                this.isOpen = false;
                
                this.init();
            }
            
            init() {
                // Toggle sidebar
                this.sidebarToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggle();
                });
                
                this.sidebarClose.addEventListener('click', () => this.close());
                this.sidebarOverlay.addEventListener('click', () => this.close());
                
                // Close on ESC
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isOpen) {
                        this.close();
                    }
                });
                
                // Prevent body scroll when sidebar is open
                this.sidebar.addEventListener('touchmove', (e) => {
                    if (this.isOpen) e.preventDefault();
                }, { passive: false });
                
                // Close sidebar when clicking on a link (mobile)
                document.querySelectorAll('.sidebar-menu a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth <= 768) {
                            this.close();
                        }
                    });
                });
            }
            
            toggle() {
                if (this.isOpen) {
                    this.close();
                } else {
                    this.open();
                }
            }
            
            open() {
                this.sidebar.classList.add('active');
                this.sidebarOverlay.classList.add('active');
                this.isOpen = true;
                document.body.style.overflow = 'hidden';
                document.body.style.paddingRight = window.innerWidth - document.documentElement.clientWidth + 'px';
            }
            
            close() {
                this.sidebar.classList.remove('active');
                this.sidebarOverlay.classList.remove('active');
                this.isOpen = false;
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '';
            }
        }

        // Smooth Modal Functions
        class ModalManager {
            constructor() {
                this.modal = document.getElementById('bookDetailModal');
                this.modalClose = document.getElementById('modalClose');
                this.closeModalBtn = document.getElementById('closeModalBtn');
                this.currentBookId = null;
                
                this.init();
            }
            
            init() {
                // Close buttons
                this.modalClose.addEventListener('click', () => this.close());
                this.closeModalBtn.addEventListener('click', () => this.close());
                
                // Close on ESC
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.modal.classList.contains('active')) {
                        this.close();
                    }
                });
                
                // Close on overlay click
                this.modal.addEventListener('click', (e) => {
                    if (e.target === this.modal) {
                        this.close();
                    }
                });
                
                // Prevent modal close when clicking inside
                this.modal.querySelector('.modal-content').addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }
            
            async show(bookId) {
                this.currentBookId = bookId;
                
                // Show loading state
                document.getElementById('modalBookContent').innerHTML = `
                    <div class="loading">
                        <div class="spinner"></div>
                    </div>
                `;
                
                // Open modal with animation
                this.modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                document.body.style.paddingRight = window.innerWidth - document.documentElement.clientWidth + 'px';
                
                try {
                    // Fetch book details
                    const response = await fetch(`get_book_detail.php?id=${bookId}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        const book = data.book;
                        this.populateModal(book);
                    } else {
                        this.showError('Buku tidak ditemukan');
                    }
                } catch (error) {
                    this.showError('Terjadi kesalahan saat memuat data');
                    console.error('Error:', error);
                }
            }
           populateModal(book) {
    document.getElementById('modalBookTitle').textContent = book.judul;
    
    // Tombol Baca Sekarang - hanya ditampilkan jika ada link
    const bacaSekarangButton = book.link_buku ? 
        `<button onclick="window.open('${this.escapeHtml(book.link_buku)}', '_blank')" 
                class="modal-button read-btn" 
                style="background-color: var(--sage); color: white; margin-top: 10px;">
            <i class="fas fa-external-link-alt"></i> Baca Sekarang
        </button>` : 
        '<p style="color: var(--taupe); font-style: italic; margin-top: 10px;">Link buku tidak tersedia</p>';
    
    document.getElementById('modalBookContent').innerHTML = `
        <div class="book-detail-paragraphs">
            <div class="book-detail-row">
                <span class="detail-label"><i class="fas fa-user"></i> Pengarang</span>
                <span class="detail-value">${this.escapeHtml(book.pengarang)}</span>
            </div>
            
            <div class="book-detail-row">
                <span class="detail-label"><i class="fas fa-calendar"></i> Tahun Terbit</span>
                <span class="detail-value">${book.tahun}</span>
            </div>
            
            <div class="book-detail-row">
                <span class="detail-label"><i class="fas fa-bookmark"></i> Genre</span>
                <span class="detail-value">${this.escapeHtml(book.nama_genre)}</span>
            </div>
            
            <div class="book-detail-row">
                <span class="detail-label"><i class="fas fa-file"></i> Jumlah Halaman</span>
                <span class="detail-value">${book.halaman} halaman</span>
            </div>
            
            <div class="book-detail-row">
                <span class="detail-label"><i class="fas fa-book-open"></i> Akses Buku</span>
                <div class="detail-value">${bacaSekarangButton}</div>
            </div>
            
            <div class="book-description-section">
                <div class="detail-label"><i class="fas fa-align-left"></i> Deskripsi Buku</div>
                <div class="book-description-full">${this.escapeHtml(book.deskripsi)}</div>
            </div>
        </div>
    `;
    
    // Set up action buttons
    const editBtn = document.getElementById('editBookBtn');
    const deleteBtn = document.getElementById('deleteBookBtn');
    
    if (editBtn) {
        editBtn.onclick = () => {
            window.location.href = `book_mgmt.php?mode=books&edit=${this.currentBookId}`;
        };
    }
    
    if (deleteBtn) {
        deleteBtn.onclick = () => this.deleteBook(this.currentBookId);
    }
} 
            showError(message) {
                document.getElementById('modalBookContent').innerHTML = `
                    <div class="empty-state" style="padding: 40px 20px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3 style="font-size: 24px;">${message}</h3>
                    </div>
                `;
            }
            
            close() {
                this.modal.classList.remove('active');
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '';
                this.currentBookId = null;
            }
            
            async deleteBook(bookId) {
                if (!confirm('Apakah Anda yakin ingin menghapus buku ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
                    return;
                }
                
                try {
                    const response = await fetch(`proses_buku.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete&book_id=${bookId}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Show success message
                        this.showAlert('Buku berhasil dihapus!', 'success');
                        this.close();
                        
                        // Remove book card from grid
                        const bookCard = document.querySelector(`.book-card[data-book-id="${bookId}"]`);
                        if (bookCard) {
                            bookCard.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                            bookCard.style.opacity = '0';
                            bookCard.style.transform = 'translateY(-20px) scale(0.9)';
                            
                            setTimeout(() => {
                                bookCard.remove();
                                // Update books count
                                this.updateBooksCount(-1);
                            }, 500);
                        } else {
                            // If card not found, reload page
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } else {
                        this.showAlert('Gagal menghapus buku: ' + data.message, 'error');
                    }
                } catch (error) {
                    this.showAlert('Terjadi kesalahan: ' + error.message, 'error');
                }
            }
            
            showAlert(message, type) {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type}`;
                alert.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                `;
                
                document.body.appendChild(alert);
                
                // Remove after 5 seconds
                setTimeout(() => {
                    alert.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(100%) scale(0.9)';
                    
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            }
            
            updateBooksCount(change) {
                const countElement = document.querySelector('.books-count span');
                if (countElement) {
                    const currentCount = parseInt(countElement.textContent);
                    countElement.textContent = currentCount + change;
                    
                    // Add animation
                    countElement.style.transition = 'all 0.3s ease';
                    countElement.style.transform = 'scale(1.2)';
                    
                    setTimeout(() => {
                        countElement.style.transform = 'scale(1)';
                    }, 300);
                }
            }
            
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }

        // Search Manager with debouncing
        class SearchManager {
            constructor() {
                this.searchInput = document.getElementById('searchInput');
                this.searchForm = document.getElementById('searchForm');
                this.timeout = null;
                this.debounceDelay = 600;
                this.lastSearch = '';
                
                this.init();
            }
            
            init() {
                // Real-time search with debouncing
                this.searchInput.addEventListener('input', (e) => {
                    const currentSearch = e.target.value.trim();
                    
                    if (currentSearch === this.lastSearch) return;
                    
                    this.lastSearch = currentSearch;
                    clearTimeout(this.timeout);
                    
                    // Show loading state
                    this.showLoading();
                    
                    this.timeout = setTimeout(() => {
                        this.searchForm.submit();
                    }, this.debounceDelay);
                });
                
                // Prevent form submission on Enter for real-time search
                this.searchForm.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(this.timeout);
                        this.searchForm.submit();
                    }
                });
                
                // Clear button functionality
                this.searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.searchInput.value) {
                        this.searchInput.value = '';
                        this.searchInput.focus();
                        this.searchForm.submit();
                    }
                });
            }
            
            showLoading() {
                const searchButton = this.searchForm.querySelector('.search-button');
                if (searchButton) {
                    const originalHTML = searchButton.innerHTML;
                    searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    searchButton.disabled = true;
                    
                    // Revert after delay
                    setTimeout(() => {
                        searchButton.innerHTML = originalHTML;
                        searchButton.disabled = false;
                    }, this.debounceDelay);
                }
            }
        }

        // Filter Manager
        class FilterManager {
            constructor() {
                this.genreSelect = document.getElementById('genre');
                this.sortSelect = document.getElementById('sort');
                this.filterForm = document.getElementById('filterForm');
                
                this.init();
            }
            
            init() {
                // Auto-submit on change with loading state
                [this.genreSelect, this.sortSelect].forEach(select => {
                    select.addEventListener('change', () => {
                        this.showLoading();
                        this.filterForm.submit();
                    });
                });
                
                // Add keyboard navigation
                this.genreSelect.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        this.filterForm.submit();
                    }
                });
            }
            
            showLoading() {
                const submitButton = this.filterForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    const originalHTML = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    submitButton.disabled = true;
                    
                    // Revert after 2 seconds (fallback)
                    setTimeout(() => {
                        submitButton.innerHTML = originalHTML;
                        submitButton.disabled = false;
                    }, 2000);
                }
            }
            
            resetFilters() {
                window.location.href = 'dashboard.php';
            }
        }

        // Alert Manager
        class AlertManager {
            constructor() {
                this.alert = document.querySelector('.alert');
                
                if (this.alert) {
                    this.init();
                }
            }
            
            init() {
                // Auto-hide after 5 seconds
                this.hideTimeout = setTimeout(() => {
                    this.hide();
                }, 5000);
                
                // Hide on click
                this.alert.addEventListener('click', () => {
                    clearTimeout(this.hideTimeout);
                    this.hide();
                });
                
                // Hide on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.alert) {
                        clearTimeout(this.hideTimeout);
                        this.hide();
                    }
                });
            }
            
            hide() {
                if (!this.alert) return;
                
                this.alert.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                this.alert.style.opacity = '0';
                this.alert.style.transform = 'translateX(100%) scale(0.9)';
                
                setTimeout(() => {
                    if (this.alert && this.alert.parentNode) {
                        this.alert.remove();
                        this.alert = null;
                    }
                }, 500);
            }
        }

        // Initialize all managers when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize managers
            window.sidebarManager = new SidebarManager();
            window.modalManager = new ModalManager();
            window.searchManager = new SearchManager();
            window.filterManager = new FilterManager();
            window.alertManager = new AlertManager();
            
            // Global functions for onclick handlers
            window.showBookDetail = (bookId) => {
                modalManager.show(bookId);
            };
            
            window.resetFilters = () => {
                filterManager.resetFilters();
            };
            
            // Add smooth hover effects to book cards
            const bookCards = document.querySelectorAll('.book-card');
            bookCards.forEach(card => {
                card.style.willChange = 'transform, box-shadow, border-color';
                
                // Add touch feedback for mobile
                card.addEventListener('touchstart', () => {
                    card.style.transform = 'translateY(-2px)';
                }, { passive: true });
                
                card.addEventListener('touchend', () => {
                    card.style.transform = '';
                }, { passive: true });
            });
            
            // Add keyboard navigation for book cards
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    const focusedCard = document.activeElement.closest('.book-card');
                    if (focusedCard) {
                        focusedCard.style.boxShadow = '0 0 0 3px var(--sage), 0 5px 20px var(--shadow)';
                        
                        // Remove shadow from other cards
                        bookCards.forEach(card => {
                            if (card !== focusedCard) {
                                card.style.boxShadow = '';
                            }
                        });
                    }
                }
            });
            
            // Smooth page transitions
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.4s ease';
            
            requestAnimationFrame(() => {
                document.body.style.opacity = '1';
            });
            
            // Prevent FOUC (Flash of Unstyled Content)
            document.documentElement.style.visibility = 'visible';
        });

        // Handle smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                document.body.style.opacity = '1';
            }
        });

        // Handle beforeunload for smooth exit
        window.addEventListener('beforeunload', () => {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.2s ease';
        });
    </script>
</body>
</html>

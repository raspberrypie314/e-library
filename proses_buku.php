<?php
require_once 'config.php';

// Set header JSON untuk AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    header('Content-Type: application/json');
}

// Cek login dan hak akses admin untuk CRUD operations
if (!is_logged_in()) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['success' => false, 'message' => 'Not authorized']);
    } else {
        redirect('login.php');
    }
    exit;
}

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? clean_input($_POST['action']) : '';
    
    switch ($action) {
        case 'create':
            createBook();
            break;
        case 'update':
            updateBook();
            break;
        case 'delete':
            deleteBook();
            break;
        default:
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
            } else {
                $_SESSION['error'] = 'Invalid action';
                redirect('book_mgmt.php?mode=books');
            }
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Handle DELETE request dari AJAX
    parse_str(file_get_contents("php://input"), $delete_vars);
    $book_id = isset($delete_vars['id']) ? intval($delete_vars['id']) : 0;
    deleteBookAjax($book_id);
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    } else {
        redirect('dashboard.php');
    }
}

function createBook() {
    global $conn;
    
    // Cek hak akses admin
    if (!is_admin()) {
        $_SESSION['error'] = 'Hanya admin yang dapat menambah buku';
        redirect('book_mgmt.php?mode=books');
    }
    
    // Validasi input
    $errors = [];
    
    $judul = clean_input($_POST['judul']);
    $pengarang = clean_input($_POST['pengarang']);
    $tahun = intval($_POST['tahun']);
    $genre_id = intval($_POST['genre_id']);
    $halaman = intval($_POST['halaman']);
    $link_buku = clean_input($_POST['link_buku']);
    $link_foto = clean_input($_POST['link_foto']);
    $deskripsi = clean_input($_POST['deskripsi']);
    
    if (empty($judul)) $errors[] = "Judul harus diisi";
    if (empty($pengarang)) $errors[] = "Pengarang harus diisi";
    if ($tahun < 1000 || $tahun > 2100) $errors[] = "Tahun harus antara 1000 dan 2100";
    if ($genre_id <= 0) $errors[] = "Genre harus dipilih";
    if ($halaman <= 0) $errors[] = "Jumlah halaman harus valid";
    if (empty($deskripsi)) $errors[] = "Deskripsi harus diisi";
    
    // Validasi URL jika diisi
    if (!empty($link_buku) && !filter_var($link_buku, FILTER_VALIDATE_URL)) {
        $errors[] = "Link buku harus berupa URL yang valid";
    }
    
    if (!empty($link_foto) && !filter_var($link_foto, FILTER_VALIDATE_URL)) {
        $errors[] = "Link foto harus berupa URL yang valid";
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        redirect('book_mgmt.php?mode=books');
    }
    
    // Default image jika link foto kosong
    if (empty($link_foto)) {
        $link_foto = 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=200&fit=crop';
    }
    
    // Insert buku baru
    $sql = "INSERT INTO books (judul, pengarang, tahun, genre_id, halaman, link_buku, link_foto, deskripsi) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiisss", $judul, $pengarang, $tahun, $genre_id, $halaman, $link_buku, $link_foto, $deskripsi);
    
    if ($stmt->execute()) {
        $book_id = $stmt->insert_id;
        $stmt->close();
        
        // Log activity
        logActivity("create_book", $book_id, "Book created: $judul by $pengarang");
        
        $_SESSION['success'] = "Buku berhasil ditambahkan!";
        redirect('book_mgmt.php?mode=books');
    } else {
        $stmt->close();
        $_SESSION['error'] = "Gagal menambahkan buku. Silakan coba lagi.";
        redirect('book_mgmt.php?mode=books');
    }
}

function updateBook() {
    global $conn;
    
    // Cek hak akses admin
    if (!is_admin()) {
        $_SESSION['error'] = 'Hanya admin yang dapat mengedit buku';
        redirect('book_mgmt.php?mode=books');
    }
    
    if (!isset($_POST['book_id'])) {
        $_SESSION['error'] = 'Book ID tidak ditemukan';
        redirect('book_mgmt.php?mode=books');
    }
    
    $book_id = intval($_POST['book_id']);
    
    // Validasi input
    $errors = [];
    
    $judul = clean_input($_POST['judul']);
    $pengarang = clean_input($_POST['pengarang']);
    $tahun = intval($_POST['tahun']);
    $genre_id = intval($_POST['genre_id']);
    $halaman = intval($_POST['halaman']);
    $link_buku = clean_input($_POST['link_buku']);
    $link_foto = clean_input($_POST['link_foto']);
    $deskripsi = clean_input($_POST['deskripsi']);
    
    if (empty($judul)) $errors[] = "Judul harus diisi";
    if (empty($pengarang)) $errors[] = "Pengarang harus diisi";
    if ($tahun < 1000 || $tahun > 2100) $errors[] = "Tahun harus antara 1000 dan 2100";
    if ($genre_id <= 0) $errors[] = "Genre harus dipilih";
    if ($halaman <= 0) $errors[] = "Jumlah halaman harus valid";
    if (empty($deskripsi)) $errors[] = "Deskripsi harus diisi";
    
    // Validasi URL jika diisi
    if (!empty($link_buku) && !filter_var($link_buku, FILTER_VALIDATE_URL)) {
        $errors[] = "Link buku harus berupa URL yang valid";
    }
    
    if (!empty($link_foto) && !filter_var($link_foto, FILTER_VALIDATE_URL)) {
        $errors[] = "Link foto harus berupa URL yang valid";
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        redirect("book_mgmt.php?mode=books&edit=$book_id");
    }
    
    // Default image jika link foto kosong
    if (empty($link_foto)) {
        $link_foto = 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=200&fit=crop';
    }
    
    // Update buku
    $sql = "UPDATE books SET judul = ?, pengarang = ?, tahun = ?, genre_id = ?, 
            halaman = ?, link_buku = ?, link_foto = ?, deskripsi = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiisssi", $judul, $pengarang, $tahun, $genre_id, $halaman, 
                      $link_buku, $link_foto, $deskripsi, $book_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Log activity
        logActivity("update_book", $book_id, "Book updated: $judul by $pengarang");
        
        $_SESSION['success'] = "Buku berhasil diperbarui!";
        redirect('book_mgmt.php?mode=books');
    } else {
        $stmt->close();
        $_SESSION['error'] = "Gagal memperbarui buku. Silakan coba lagi.";
        redirect("book_mgmt.php?mode=books&edit=$book_id");
    }
}

function deleteBook() {
    global $conn;
    
    // Cek hak akses admin
    if (!is_admin()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
        } else {
            $_SESSION['error'] = 'Hanya admin yang dapat menghapus buku';
            redirect('book_mgmt.php?mode=books');
        }
        return;
    }
    
    if (!isset($_POST['book_id'])) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Book ID required']);
        } else {
            $_SESSION['error'] = 'Book ID tidak ditemukan';
            redirect('book_mgmt.php?mode=books');
        }
        return;
    }
    
    $book_id = intval($_POST['book_id']);
    deleteBookById($book_id);
}

function deleteBookAjax($book_id) {
    global $conn;
    
    // Cek hak akses admin
    if (!is_admin()) {
        echo json_encode(['success' => false, 'message' => 'Not authorized']);
        exit;
    }
    
    if ($book_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Book ID']);
        exit;
    }
    
    deleteBookById($book_id);
}

function deleteBookById($book_id) {
    global $conn;
    
    // Get book info for logging
    $info_sql = "SELECT judul, pengarang FROM books WHERE id = ?";
    $info_stmt = $conn->prepare($info_sql);
    $info_stmt->bind_param("i", $book_id);
    $info_stmt->execute();
    $info_result = $info_stmt->get_result();
    
    if ($info_result->num_rows == 0) {
        $info_stmt->close();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Book not found']);
        } else {
            $_SESSION['error'] = 'Buku tidak ditemukan';
            redirect('book_mgmt.php?mode=books');
        }
        return;
    }
    
    $book_info = $info_result->fetch_assoc();
    $info_stmt->close();
    
    // Delete book
    $sql = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Log activity
        logActivity("delete_book", $book_id, "Book deleted: " . $book_info['judul'] . " by " . $book_info['pengarang']);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => true, 'message' => 'Book deleted successfully']);
        } else {
            $_SESSION['success'] = "Buku berhasil dihapus!";
            redirect('book_mgmt.php?mode=books');
        }
    } else {
        $stmt->close();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Failed to delete book']);
        } else {
            $_SESSION['error'] = "Gagal menghapus buku. Silakan coba lagi.";
            redirect('book_mgmt.php?mode=books');
        }
    }
}

function logActivity($action, $target_id, $details) {
    global $conn;
    
    if (!is_logged_in()) return;
    
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO activity_logs (user_id, action, target_id, details, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $stmt->bind_param("isssss", $user_id, $action, $target_id, $details, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
}
?>

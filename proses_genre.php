<?php
require_once 'config.php';

// Set header JSON untuk AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    header('Content-Type: application/json');
}

// Cek login dan hak akses admin
if (!is_logged_in() || !is_admin()) {
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
            createGenre();
            break;
        case 'update':
            updateGenre();
            break;
        case 'delete':
            deleteGenre();
            break;
        default:
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
            } else {
                $_SESSION['error'] = 'Invalid action';
                redirect('book_mgmt.php?mode=genres');
            }
            break;
    }
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    } else {
        redirect('dashboard.php');
    }
}

function createGenre() {
    global $conn;
    
    // Validasi input
    $errors = [];
    
    $nama_genre = clean_input($_POST['nama_genre']);
    
    if (empty($nama_genre)) {
        $errors[] = "Nama genre harus diisi";
    } elseif (strlen($nama_genre) < 2) {
        $errors[] = "Nama genre minimal 2 karakter";
    } elseif (strlen($nama_genre) > 50) {
        $errors[] = "Nama genre maksimal 50 karakter";
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        redirect('book_mgmt.php?mode=genres');
    }
    
    // Cek jika genre sudah ada
    $check_sql = "SELECT id FROM genres WHERE LOWER(nama_genre) = LOWER(?)";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $nama_genre);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        $_SESSION['error'] = "Genre '$nama_genre' sudah ada!";
        redirect('book_mgmt.php?mode=genres');
    }
    $check_stmt->close();
    
    // Insert genre baru
    $sql = "INSERT INTO genres (nama_genre) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nama_genre);
    
    if ($stmt->execute()) {
        $genre_id = $stmt->insert_id;
        $stmt->close();
        
        // Log activity
        logActivity("create_genre", $genre_id, "Genre created: $nama_genre");
        
        $_SESSION['success'] = "Genre berhasil ditambahkan!";
        redirect('book_mgmt.php?mode=genres');
    } else {
        $stmt->close();
        $_SESSION['error'] = "Gagal menambahkan genre. Silakan coba lagi.";
        redirect('book_mgmt.php?mode=genres');
    }
}

function updateGenre() {
    global $conn;
    
    if (!isset($_POST['genre_id'])) {
        $_SESSION['error'] = 'Genre ID tidak ditemukan';
        redirect('book_mgmt.php?mode=genres');
    }
    
    $genre_id = intval($_POST['genre_id']);
    
    // Validasi input
    $errors = [];
    
    $nama_genre = clean_input($_POST['nama_genre']);
    
    if (empty($nama_genre)) {
        $errors[] = "Nama genre harus diisi";
    } elseif (strlen($nama_genre) < 2) {
        $errors[] = "Nama genre minimal 2 karakter";
    } elseif (strlen($nama_genre) > 50) {
        $errors[] = "Nama genre maksimal 50 karakter";
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        redirect("book_mgmt.php?mode=genres&edit=$genre_id");
    }
    
    // Cek jika genre sudah ada (kecuali untuk genre ini sendiri)
    $check_sql = "SELECT id FROM genres WHERE LOWER(nama_genre) = LOWER(?) AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $nama_genre, $genre_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        $_SESSION['error'] = "Genre '$nama_genre' sudah ada!";
        redirect("book_mgmt.php?mode=genres&edit=$genre_id");
    }
    $check_stmt->close();
    
    // Update genre
    $sql = "UPDATE genres SET nama_genre = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nama_genre, $genre_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Log activity
        logActivity("update_genre", $genre_id, "Genre updated to: $nama_genre");
        
        $_SESSION['success'] = "Genre berhasil diperbarui!";
        redirect('book_mgmt.php?mode=genres');
    } else {
        $stmt->close();
        $_SESSION['error'] = "Gagal memperbarui genre. Silakan coba lagi.";
        redirect("book_mgmt.php?mode=genres&edit=$genre_id");
    }
}

function deleteGenre() {
    global $conn;
    
    if (!isset($_POST['genre_id'])) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Genre ID required']);
        } else {
            $_SESSION['error'] = 'Genre ID tidak ditemukan';
            redirect('book_mgmt.php?mode=genres');
        }
        return;
    }
    
    $genre_id = intval($_POST['genre_id']);
    
    // Cek jika genre sedang digunakan oleh buku
    $check_sql = "SELECT COUNT(*) as book_count FROM books WHERE genre_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $genre_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $row = $check_result->fetch_assoc();
    $check_stmt->close();
    
    if ($row['book_count'] > 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete genre that has books']);
        } else {
            $_SESSION['error'] = "Tidak dapat menghapus genre yang masih memiliki buku!";
            redirect('book_mgmt.php?mode=genres');
        }
        return;
    }
    
    // Get genre info for logging
    $info_sql = "SELECT nama_genre FROM genres WHERE id = ?";
    $info_stmt = $conn->prepare($info_sql);
    $info_stmt->bind_param("i", $genre_id);
    $info_stmt->execute();
    $info_result = $info_stmt->get_result();
    
    if ($info_result->num_rows == 0) {
        $info_stmt->close();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Genre not found']);
        } else {
            $_SESSION['error'] = 'Genre tidak ditemukan';
            redirect('book_mgmt.php?mode=genres');
        }
        return;
    }
    
    $genre_info = $info_result->fetch_assoc();
    $info_stmt->close();
    
    // Delete genre
    $sql = "DELETE FROM genres WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $genre_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Log activity
        logActivity("delete_genre", $genre_id, "Genre deleted: " . $genre_info['nama_genre']);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => true, 'message' => 'Genre deleted successfully']);
        } else {
            $_SESSION['success'] = "Genre berhasil dihapus!";
            redirect('book_mgmt.php?mode=genres');
        }
    } else {
        $stmt->close();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'Failed to delete genre']);
        } else {
            $_SESSION['error'] = "Gagal menghapus genre. Silakan coba lagi.";
            redirect('book_mgmt.php?mode=genres');
        }
    }
}

function logActivity($action, $target_id, $details) {
    global $conn;
    
    if (!is_logged_in()) return;
    
    $user_id = $_SESSION['user_id'];
    
    // Cek apakah tabel activity_logs ada
    $check_table = $conn->query("SHOW TABLES LIKE 'activity_logs'");
    
    if ($check_table->num_rows == 0) {
        // Create activity_logs table if not exists
        $create_table_sql = "CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            action VARCHAR(50) NOT NULL,
            target_id INT,
            details TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        $conn->query($create_table_sql);
    }
    
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

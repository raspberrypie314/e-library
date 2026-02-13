<?php
require_once 'config.php';

// Set header JSON
header('Content-Type: application/json');

// Cek login dan hak akses admin
if (!is_logged_in() || !is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

// Cek method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get action
$action = isset($_POST['action']) ? clean_input($_POST['action']) : '';

if ($action != 'delete') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Get user ID
if (!isset($_POST['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

$user_id = intval($_POST['user_id']);
$current_user_id = $_SESSION['user_id'];

// Validasi: user tidak bisa menghapus dirinya sendiri
if ($user_id == $current_user_id) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
    exit;
}

// Validasi: cek jika user adalah admin
$check_sql = "SELECT user_type FROM users WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    $check_stmt->close();
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user = $check_result->fetch_assoc();
$check_stmt->close();

// Hapus user
$delete_sql = "DELETE FROM users WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $user_id);

if ($delete_stmt->execute()) {
    $delete_stmt->close();
    
    // Log activity
    $log_sql = "INSERT INTO user_logs (user_id, action, target_user_id, details) VALUES (?, ?, ?, ?)";
    $log_stmt = $conn->prepare($log_sql);
    $action_type = 'delete_user';
    $details = "User deleted: ID $user_id, Type: " . $user['user_type'];
    $log_stmt->bind_param("isis", $current_user_id, $action_type, $user_id, $details);
    $log_stmt->execute();
    $log_stmt->close();
    
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} else {
    $delete_stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
}
?>

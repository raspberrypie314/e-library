<?php
require_once 'config.php';

// Set header JSON
header('Content-Type: application/json');

// Cek login
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

// Get book ID
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Book ID required']);
    exit;
}

$book_id = intval($_GET['id']);

// Query untuk mendapatkan detail buku
$sql = "SELECT b.*, g.nama_genre FROM books b 
        JOIN genres g ON b.genre_id = g.id 
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $book = $result->fetch_assoc();
    echo json_encode(['success' => true, 'book' => $book]);
} else {
    echo json_encode(['success' => false, 'message' => 'Book not found']);
}

$stmt->close();
?>

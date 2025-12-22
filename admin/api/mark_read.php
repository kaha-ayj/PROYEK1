<?php
// admin/api/mark_read.php
session_start();
include __DIR__ . '/../../config/koneksi.php'; // ✅ PERBAIKAN: path yang benar
header('Content-Type: application/json');

// Cek session admin
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_POST['user_id'] ?? 0;

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE chat 
        SET is_read = 1 
        WHERE penggunaID = ? 
        AND sender = 'user'
        AND (is_read = 0 OR is_read IS NULL)
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'updated' => $stmt->affected_rows
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
$conn->close();
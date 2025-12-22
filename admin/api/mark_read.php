<?php
// admin/api/mark_read.php
session_start();
include __DIR__ . '/../../config/koneksi.php'; 
header('Content-Type: application/json');

// 1. Validasi Admin (Sudah sesuai dengan struktur session login baru)
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak: Bukan admin']);
    exit;
}

// 2. Ambil user_id dari POST (ID user yang pesannya ingin ditandai sudah dibaca)
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID User tidak valid']);
    exit;
}

try {
    // 3. Update status pesan dari 'user' menjadi sudah dibaca (is_read = 1)
    // Kita tambahkan pengecekan agar hanya pesan dari 'user' yang diupdate oleh admin
    $stmt = $conn->prepare("
        UPDATE chat 
        SET is_read = 1 
        WHERE penggunaID = ? 
        AND sender = 'user'
        AND is_read = 0
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Kirim respon sukses beserta jumlah baris yang berubah
    echo json_encode([
        'success' => true,
        'message' => 'Pesan ditandai telah dibaca',
        'updated_rows' => $stmt->affected_rows
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    // Mencegah output HTML error jika SQL gagal
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
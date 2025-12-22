<?php
session_start();
header('Content-Type: application/json');
include __DIR__ . '/../../config/koneksi.php';

// Fungsi validasi admin
function isAdmin() {
    return (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin');
}

// Ambil ID Admin dari session
function getAdminId() {
    return $_SESSION['user']['penggunaID'] ?? 0;
}

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak: Bukan admin']);
    exit;
}

// Ambil input JSON dari fetch JavaScript
$data = json_decode(file_get_contents('php://input'), true);

$user_id = (int)($data['penggunaID'] ?? 0); 
$message = trim($data['message'] ?? '');

// Cek kelengkapan data
if (!$user_id || !$message) {
    echo json_encode(['success' => false, 'message' => 'ID Pengguna atau Pesan kosong']);
    exit;
}

$admin_id = getAdminId();
if ($admin_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Sesi admin hilang, silakan login ulang']);
    exit;
}

try {
    // Pastikan tabel 'chat' memiliki kolom: penggunaID, admin_id, sender, message, created_at
    $stmt = $conn->prepare("INSERT INTO chat (penggunaID, admin_id, sender, message, created_at) VALUES (?, ?, 'admin', ?, NOW())");
    $stmt->bind_param('iis', $user_id, $admin_id, $message);
    $success = $stmt->execute();

    echo json_encode([
        'success' => $success, 
        'message' => $success ? 'Pesan terkirim' : 'Gagal simpan ke database'
    ]);

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
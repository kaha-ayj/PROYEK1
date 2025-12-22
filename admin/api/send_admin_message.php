<?php
session_start();
header('Content-Type: application/json');
include __DIR__ . '/../../config/koneksi.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    echo json_encode(['success'=>false,'message'=>'Bukan admin']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = (int)($data['user_id'] ?? 0);
$message = trim($data['message'] ?? '');

if(!$user_id || !$message){
    echo json_encode(['success'=>false,'message'=>'Data tidak lengkap']);
    exit;
}

// ✅ PERBAIKAN: Tambahkan admin_id
$admin_id = $_SESSION['user']['id'] ?? 0;
$stmt = $conn->prepare("INSERT INTO chat (penggunaID, admin_id, sender, message, created_at) VALUES (?, ?, 'admin', ?, NOW())");
$stmt->bind_param('iis', $user_id, $admin_id, $message);
$success = $stmt->execute();

echo json_encode(['success'=>$success, 'message' => $success ? 'Pesan terkirim' : 'Gagal kirim']);
$stmt->close();
$conn->close();
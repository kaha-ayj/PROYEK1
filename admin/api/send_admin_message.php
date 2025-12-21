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

// Simpan pesan admin
$stmt = $conn->prepare("INSERT INTO chat (penggunaID, sender, message) VALUES (?, 'admin', ?)");
$stmt->bind_param('is', $user_id, $message);
$success = $stmt->execute();

echo json_encode(['success'=>$success]);
$conn->close();

<?php //admin/api/get_user.php
session_start();
header('Content-Type: application/json');

// Cek session admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    echo json_encode(['success'=>false,'message'=>'Bukan admin']);
    exit;
}

// Path koneksi
require_once __DIR__ . '/../../config/koneksi.php';

// Ambil daftar user dengan pesan terakhir
$sql = "SELECT p.penggunaID, p.nama, c.message AS last_message, c.created_at
        FROM pengguna p
        LEFT JOIN chat c ON c.chat_id = (
            SELECT chat_id FROM chat 
            WHERE penggunaID = p.penggunaID
            ORDER BY created_at DESC LIMIT 1
        )
        ORDER BY c.created_at DESC";

$result = $conn->query($sql);

$users = [];
while($row = $result->fetch_assoc()){
    $users[] = [
        'user_id' => (int)$row['penggunaID'],
        'username' => $row['nama'],
        'last_message' => $row['last_message'] ?? '',
        'last_time' => $row['created_at'] ?? ''
    ];
}

echo json_encode([
    'success' => true,
    'data' => $users
]);

$conn->close();

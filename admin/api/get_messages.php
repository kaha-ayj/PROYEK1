<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/koneksi.php';

// Tangkap ID yang dikirim dari fetch(`${API_GET_MESSAGES}?penggunaID=${userId}`)
$id = $_GET['penggunaID'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    exit;
}

// Ambil semua pesan antara admin dan user ini
$sql = "SELECT message, sender, created_at 
        FROM chat 
        WHERE penggunaID = ? 
        ORDER BY created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while($row = $result->fetch_assoc()) {
    $messages[] = [
        'message' => $row['message'],
        'sender'  => $row['sender'], // 'user' atau 'admin'
        'time'    => date('H:i', strtotime($row['created_at']))
    ];
}

echo json_encode(['success' => true, 'data' => $messages]);
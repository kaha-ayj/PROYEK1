<?php
header('Content-Type: application/json');
session_start();
include(__DIR__ . "/../../config/koneksi.php");

// Pastikan session ID diambil dengan benar
$user_id = $_SESSION['penggunaID'] ?? $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 0;

$response = ['success' => false, 'messages' => []];

if ($user_id > 0) {
    // Ambil SEMUA pesan untuk user ini (baik yang dikirim user maupun admin)
    $query = "SELECT sender, message, DATE_FORMAT(created_at, '%H:%i') as time, created_at 
              FROM chat 
              WHERE penggunaID = ? 
              ORDER BY created_at ASC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['messages'][] = [
            'sender' => $row['sender'], // pastikan di DB isinya 'admin' atau 'user'
            'message' => $row['message'],
            'time' => $row['time'],
            'created_at' => $row['created_at']
        ];
    }
    $response['success'] = true;
}

echo json_encode($response);
<?php
session_start();
include __DIR__ . '/../../config/koneksi.php';
header('Content-Type: application/json');

// Ambil user_id dari session
$user_id = $_SESSION['penggunaID'] ?? 0;
$result = ['success' => false, 'messages' => []];

if ($user_id) {
    $stmt = $conn->prepare("
        SELECT sender, message, DATE_FORMAT(created_at, '%H:%i') as time
        FROM chat
        WHERE penggunaID = ?
        ORDER BY created_at ASC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }

    $result['success'] = true;
    $result['messages'] = $messages;
}

echo json_encode($result);

<?php
header('Content-Type: application/json');
session_start();
error_reporting(0);

include(__DIR__ . '/../../config/koneksi.php');

$db = isset($koneksi) ? $koneksi : (isset($conn) ? $conn : null);

$response = ['success' => false, 'messages' => []];

if (!$db) {
    echo json_encode(['success' => false, 'error' => 'Koneksi database gagal']);
    exit;
}

// Tentukan user_id
$admin_mode = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
$user_id = $admin_mode ? ($_GET['user_id'] ?? 0) : ($_SESSION['penggunaID'] ?? 0);

if ($user_id > 0) {
    // Ambil data chat berdasarkan user_id
    $query = "SELECT sender, message, DATE_FORMAT(created_at, '%H:%i') as time, created_at 
              FROM chat 
              WHERE penggunaID = ? 
              ORDER BY created_at ASC";

    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['messages'][] = $row;
    }
    $response['success'] = true;
}

echo json_encode($response);

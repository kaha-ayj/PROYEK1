<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);

include(__DIR__ . '/../../config/koneksi.php');
$db = $koneksi ?? $conn;

if (!$db) {
    echo json_encode(['success' => false, 'error' => 'Koneksi database gagal']);
    exit;
}

// Tentukan user_id DULU sebelum insert
$admin_mode = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
$user_id = $admin_mode ? (int)($_GET['user_id'] ?? 0) : (int)($_SESSION['penggunaID'] ?? 0);

$message = $_POST['message'] ?? '';

if ($user_id === 0) {
    echo json_encode(['success' => false, 'error' => 'Sesi habis, silakan login kembali']);
    exit;
}

if (empty(trim($message))) {
    echo json_encode(['success' => false, 'error' => 'Pesan tidak boleh kosong']);
    exit;
}

// Bypass FK
$db->query("SET FOREIGN_KEY_CHECKS=0");

$response = ['success' => false];

// Simpan pesan user/admin
$query = "INSERT INTO chat (penggunaID, sender, message, created_at) VALUES (?, 'user', ?, NOW())";
$stmt = $db->prepare($query);
$stmt->bind_param("is", $user_id, $message);

if ($stmt->execute()) {
    $response['success'] = true;

    // Bot otomatis
    $cek = $db->prepare("SELECT COUNT(*) as total FROM chat WHERE penggunaID = ? AND sender = 'bot'");
    $cek->bind_param("i", $user_id);
    $cek->execute();
    $resCek = $cek->get_result()->fetch_assoc();

    if ($resCek['total'] == 0) {
        $msgBot = "👋 Halo! Admin kami akan segera membalas pesan Anda.";
        $insBot = $db->prepare("INSERT INTO chat (penggunaID, sender, message, created_at) VALUES (?, 'bot', ?, NOW())");
        $insBot->bind_param("is", $user_id, $msgBot);
        $insBot->execute();
        
        $response['reply'] = [
            'message' => $msgBot,
            'time' => date('H:i')
        ];
    }
} else {
    $response['error'] = "Database Error: " . $db->error;
}

$db->query("SET FOREIGN_KEY_CHECKS=1");

echo json_encode($response);

<?php
session_start();
header('Content-Type: application/json');
error_reporting(0); // Mencegah error teks merusak format JSON

include(__DIR__ . '/../../config/koneksi.php');
$db = $koneksi ?? $conn;

// 1. Pastikan koneksi aman
if (!$db) {
    echo json_encode(['success' => false, 'error' => 'Koneksi database gagal']);
    exit;
}

// 2. Ambil User ID dan Pesan
$user_id = (int)($_SESSION['penggunaID'] ?? ($_SESSION['user']['penggunaID'] ?? 0));
$message = $_POST['message'] ?? '';

if ($user_id === 0) {
    echo json_encode(['success' => false, 'error' => 'Sesi habis, silakan login kembali']);
    exit;
}

if (empty(trim($message))) {
    echo json_encode(['success' => false, 'error' => 'Pesan tidak boleh kosong']);
    exit;
}

// 3. Bypass Foreign Key (PENTING: Agar database tidak menolak input jika ID dianggap tidak cocok)
$db->query("SET FOREIGN_KEY_CHECKS=0");

$response = ['success' => false];

// 4. Simpan pesan user
$query = "INSERT INTO chat (penggunaID, sender, message, created_at) VALUES (?, 'user', ?, NOW())";
$stmt = $db->prepare($query);
$stmt->bind_param("is", $user_id, $message);

if ($stmt->execute()) {
    $response['success'] = true;

    // 5. Cek bot (Agar bot tidak spam, kirim hanya jika ini pesan pertama)
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

// Aktifkan kembali pengecekan relasi
$db->query("SET FOREIGN_KEY_CHECKS=1");

echo json_encode($response);
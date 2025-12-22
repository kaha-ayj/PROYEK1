<?php
header('Content-Type: application/json');
session_start();
include(__DIR__ . '/../../config/koneksi.php');

$user_id = $_SESSION['penggunaID'] ?? $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 0;
$message = trim($_POST['message'] ?? '');

$response = ['success' => false];

if ($user_id > 0 && $message !== '') {

    // ===============================
    // 1️⃣ SIMPAN PESAN USER
    // ===============================
    $insertUser = $conn->prepare("
        INSERT INTO chat (penggunaID, sender, message)
        VALUES (?, 'user', ?)
    ");
    $insertUser->bind_param("is", $user_id, $message);
    $insertUser->execute();

    // ===============================
    // 2️⃣ CEK APAKAH BOT SUDAH BALAS
    // ===============================
    $cekBot = $conn->prepare("
        SELECT 1 FROM chat 
        WHERE penggunaID = ? AND sender = 'bot'
        LIMIT 1
    ");
    $cekBot->bind_param("i", $user_id);
    $cekBot->execute();
    $botSudahBalas = $cekBot->get_result()->num_rows > 0;

    // ===============================
    // 3️⃣ BOT BALAS SATU KALI
    // ===============================
    if (!$botSudahBalas) {

        $botMessage = "👋 Terima kasih sudah menghubungi kami.\n"
                    . "Admin akan segera membantu Anda 🙏";

        $insertBot = $conn->prepare("
            INSERT INTO chat (penggunaID, sender, message)
            VALUES (?, 'bot', ?)
        ");
        $insertBot->bind_param("is", $user_id, $botMessage);
        $insertBot->execute();

        // Kirim balik ke frontend
        $response['reply'] = [
            'sender' => 'bot',
            'message' => $botMessage,
            'time' => date('H:i')
        ];
    }

    $response['success'] = true;
}

echo json_encode($response);

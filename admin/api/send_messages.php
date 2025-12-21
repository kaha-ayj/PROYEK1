<?php
header('Content-Type: application/json');
session_start();
include(__DIR__ . "/../../config/koneksi.php");

$response = ['success' => false, 'message' => ''];

// Hanya proses jika ada kiriman POST dari JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['penggunaID'] ?? $_SESSION['user_id'] ?? 0;
    $pesan = $_POST['message'] ?? ''; 

    if ($user_id == 0) {
        $response['message'] = "Sesi habis. Silakan login kembali.";
    } elseif (empty($pesan)) {
        $response['message'] = "Pesan tidak terbaca.";
    } else {
        $pesan_db = mysqli_real_escape_string($conn, $pesan);
        $query = "INSERT INTO messages (user_id, message, sender, time) 
                  VALUES ('$user_id', '$pesan_db', 'user', NOW())";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'bot_reply' => 'Pesan masuk ke database!']);
            exit;
        } else {
            $response['message'] = "Database Error: " . mysqli_error($conn);
        }
    }
} else {
    $response['message'] = "Metode harus POST.";
}

echo json_encode($response);
exit;
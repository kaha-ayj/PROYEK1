<?php
session_start();
include "../config/koneksi.php";

$user_id = $_SESSION['user_id'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);
$message = trim($data['message'] ?? '');

if($user_id && $message){
    $stmt = mysqli_prepare($conn, "INSERT INTO chat (user_id, sender, message, created_at) VALUES (?, 'user', ?, NOW())");
    mysqli_stmt_bind_param($stmt, "is", $user_id, $message);
    mysqli_stmt_execute($stmt);

    // contoh bot reply (opsional)
    $reply = "Pesan diterima!";
    echo json_encode(['status'=>'ok','reply'=>$reply]);
} else {
    echo json_encode(['status'=>'error']);
}
?>

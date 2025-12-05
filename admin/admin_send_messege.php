<?php
session_start();
include "../config/koneksi.php";

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;
$message = trim($data['message'] ?? '');

if($user_id && $message){
    $stmt = mysqli_prepare($conn,"INSERT INTO chat (user_id,sender,message,created_at) VALUES (?,?,?,NOW())");
    $sender='admin';
    mysqli_stmt_bind_param($stmt,"iss",$user_id,$sender,$message);
    mysqli_stmt_execute($stmt);
    echo json_encode(['status'=>'ok']);
} else {
    echo json_encode(['status'=>'error']);
}
?>

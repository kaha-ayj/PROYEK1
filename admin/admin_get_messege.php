<?php
include "config/koneksi.php";

$user_id = $_GET['user_id'] ?? null;
$chats = [];

if($user_id){
    $stmt = mysqli_prepare($conn, "SELECT * FROM chat WHERE user_id=? ORDER BY created_at ASC");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($res)){
        $chats[] = ['sender'=>$row['sender'], 'message'=>$row['message']];
    }
}

echo json_encode($chats);
?>

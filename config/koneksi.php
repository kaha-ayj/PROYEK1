<?php
$host = "sql113.infinityfree.com";
$user = "if0_40751259";
$pass = "XTlJ8Jq69Vqs9J";
$db   = "if0_40751259_proyek_1";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Koneksi DB gagal: ' . mysqli_connect_error()]);
    exit;
}

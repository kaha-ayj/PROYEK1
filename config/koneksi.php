<?php
$host = "sql212.infinityfree.com";
$user = "if0_40742969";
$pass = "th41fmsj";
$db   = "if0_40742969_proyek_1";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Koneksi DB gagal: ' . mysqli_connect_error()]);
    exit;
}

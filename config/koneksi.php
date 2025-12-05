<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "proyek_1";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Koneksi DB gagal: ' . mysqli_connect_error()]);
    exit;
}

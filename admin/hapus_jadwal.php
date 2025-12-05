<?php
session_start();
require_once '../config/koneksi.php';

$jadwalID = $_GET['id'] ?? null;
if (!$jadwalID) {
    die("ID Jadwal tidak valid.");
}

$query_select = "SELECT waktuMulai FROM jadwal WHERE jadwalID = ?";
$stmt_select = mysqli_prepare($conn, $query_select);
mysqli_stmt_bind_param($stmt_select, "i", $jadwalID);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$data_jadwal = mysqli_fetch_assoc($result);
$tanggal_redirect = $data_jadwal ? date('Y-m-d', strtotime($data_jadwal['waktuMulai'])) : date('Y-m-d');


$query_delete = "DELETE FROM jadwal WHERE jadwalID = ?";
$stmt_delete = mysqli_prepare($conn, $query_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $jadwalID);

$status_redirect = 'status=sukses_hapus';
if (!mysqli_stmt_execute($stmt_delete)) {
    $status_redirect = 'status=gagal_hapus&error=' . urlencode('Tidak bisa menghapus slot yang sudah dipesan.');
}
mysqli_stmt_close($stmt_delete);
mysqli_close($conn);

header("Location: jadwal.php?tanggal=$tanggal_redirect&$status_redirect");
exit();
?>
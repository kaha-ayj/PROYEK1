<?php
session_start();
require_once '../config/koneksi.php';

$pemesananID = $_GET['id'] ?? null;
if (!$pemesananID) {
    die("ID Pesanan tidak valid.");
}

$stmt_get = mysqli_prepare($conn, "SELECT jadwalID FROM pemesanan WHERE pemesananID = ?");
mysqli_stmt_bind_param($stmt_get, "i", $pemesananID);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$data_pesanan = mysqli_fetch_assoc($result_get);
$jadwalID = $data_pesanan['jadwalID'] ?? null;
mysqli_stmt_close($stmt_get);

$stmt_delete = mysqli_prepare($conn, "DELETE FROM pemesanan WHERE pemesananID = ?");
mysqli_stmt_bind_param($stmt_delete, "i", $pemesananID);
$status_redirect = 'status=sukses_hapus';

if (mysqli_stmt_execute($stmt_delete)) {
    if ($jadwalID) {
        $stmt_jadwal = mysqli_prepare($conn, "UPDATE jadwal SET status = 'Tersedia' WHERE jadwalID = ?");
        mysqli_stmt_bind_param($stmt_jadwal, "i", $jadwalID);
        mysqli_stmt_execute($stmt_jadwal);
        mysqli_stmt_close($stmt_jadwal);
    }
} else {
    $status_redirect = 'status=gagal_hapus&error=' . urlencode(mysqli_error($conn));
}

mysqli_stmt_close($stmt_delete);
mysqli_close($conn);

header("Location: order.php?$status_redirect");
exit();
?>
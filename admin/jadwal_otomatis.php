<?php
// PASTIKAN LOKASI KONEKSI SUDAH BENAR
require_once 'config/koneksi.php';

// --- KONFIGURASI JADWAL ---
$tanggal_target = date('Y-m-d'); // Tanggal hari ini, ubah ke tanggal lain jika perlu (misal: '2025-11-19')
$jam_buka = 9;   // Mulai jam 9 pagi (09:00)
$jam_tutup = 22; // Sampai jam 10 malam (22:00)

// Status default
$status_default = 'Tersedia';

// =======================================================
// 1. Ambil semua Lapangan ID
// =======================================================
$lapanganIDs = [];
$query_lapangan = "SELECT lapanganID FROM lapangan";
$result_lapangan = mysqli_query($conn, $query_lapangan);
while ($row = mysqli_fetch_assoc($result_lapangan)) {
    $lapanganIDs[] = $row['lapanganID'];
}

if (empty($lapanganIDs)) {
    die("ERROR: Tidak ada data lapangan di database Anda.");
}

// =======================================================
// 2. Cek apakah jadwal untuk tanggal tersebut sudah ada
// =======================================================
$query_check = "SELECT COUNT(*) AS total FROM jadwal WHERE DATE(waktuMulai) = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "s", $tanggal_target);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$data_check = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check);

if ($data_check['total'] > 0) {
    die("Jadwal untuk tanggal **" . $tanggal_target . "** sudah ada (" . $data_check['total'] . " slot). Tidak ada data baru yang dimasukkan.");
}

// =======================================================
// 3. Masukkan Jadwal Baru
// =======================================================
$slot_tersimpan = 0;
$query_insert = "INSERT INTO jadwal (lapanganID, waktuMulai, waktuSelesai, status) VALUES (?, ?, ?, ?)";
$stmt_insert = mysqli_prepare($conn, $query_insert);

foreach ($lapanganIDs as $id_lap) {
    for ($i = $jam_buka; $i < $jam_tutup; $i++) {
        $waktu_mulai = "$tanggal_target " . str_pad($i, 2, '0', STR_PAD_LEFT) . ":00:00";
        $waktu_selesai = "$tanggal_target " . str_pad($i + 1, 2, '0', STR_PAD_LEFT) . ":00:00";
        
        // Bind dan eksekusi
        mysqli_stmt_bind_param($stmt_insert, "isss", $id_lap, $waktu_mulai, $waktu_selesai, $status_default);
        if (mysqli_stmt_execute($stmt_insert)) {
            $slot_tersimpan++;
        }
    }
}
mysqli_stmt_close($stmt_insert);
mysqli_close($conn);

echo "<h2>✅ Selesai!</h2>";
echo "Berhasil menambahkan **$slot_tersimpan** slot jadwal tersedia untuk **" . $tanggal_target . "** ke **" . count($lapanganIDs) . "** lapangan.";

?>
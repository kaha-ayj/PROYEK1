<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/config/koneksi.php';

$tgl_mulai = new DateTime('2025-01-01');
$tgl_akhir = new DateTime('2025-12-31');

$jam_mulai = 9;   // 09:00
$jam_akhir = 23;  // 23:00 (slot terakhir 23-24)

$lapangan_ids = [1, 2, 3, 4]; // sesuaikan

$total_insert = 0;

/* =====================
   GENERATE JADWAL
===================== */
while ($tgl_mulai <= $tgl_akhir) {

    for ($jam = $jam_mulai; $jam < $jam_akhir; $jam++) {

        $waktuMulai = clone $tgl_mulai;
        $waktuMulai->setTime($jam, 0);

        $waktuSelesai = clone $waktuMulai;
        $waktuSelesai->modify('+1 hour');

        foreach ($lapangan_ids as $lapanganID) {

            // CEK BENTROK
            $cek = mysqli_prepare(
                $conn,
                "SELECT jadwalID FROM jadwal 
                 WHERE lapanganID = ?
                 AND waktuMulai = ?
                 AND waktuSelesai = ?"
            );

            mysqli_stmt_bind_param(
                $cek,
                "iss",
                $lapanganID,
                $waktuMulai->format('Y-m-d H:i:s'),
                $waktuSelesai->format('Y-m-d H:i:s')
            );
            mysqli_stmt_execute($cek);
            mysqli_stmt_store_result($cek);

            if (mysqli_stmt_num_rows($cek) == 0) {

                // INSERT JADWAL BARU
                $insert = mysqli_prepare(
                    $conn,
                    "INSERT INTO jadwal 
                    (lapanganID, waktuMulai, waktuSelesai, status)
                    VALUES (?, ?, ?, 'Tersedia')"
                );

                mysqli_stmt_bind_param(
                    $insert,
                    "iss",
                    $lapanganID,
                    $waktuMulai->format('Y-m-d H:i:s'),
                    $waktuSelesai->format('Y-m-d H:i:s')
                );
                mysqli_stmt_execute($insert);

                $total_insert++;
            }
        }
    }

    $tgl_mulai->modify('+1 day');
}

echo "✅ SELESAI<br>";
echo "Total jadwal baru ditambahkan: <b>$total_insert</b>";

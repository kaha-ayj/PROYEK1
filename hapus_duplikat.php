<?php
include 'config/koneksi.php';

$lapangan_ids = [1,2,3,4];

foreach ($lapangan_ids as $lapanganID) {
    $sql = "
    DELETE j1 FROM jadwal j1
    INNER JOIN jadwal j2 
    WHERE 
        j1.jadwalID > j2.jadwalID
        AND j1.lapanganID = j2.lapanganID
        AND j1.waktuMulai = j2.waktuMulai
        AND j1.waktuSelesai = j2.waktuSelesai
    ";
    mysqli_query($conn, $sql);
}

echo "✅ Duplikat terhapus";

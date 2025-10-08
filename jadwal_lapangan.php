<?php
// Data jadwal sementara (biasanya nanti dari database)
$jadwal = [
    ["lapangan" => "Lapangan 1", "tanggal" => "2025-09-25", "jam" => "09.00 - 10.00", "status" => "sudah"],
    ["lapangan" => "Lapangan 2", "tanggal" => "2025-09-25", "jam" => "09.00 - 10.00", "status" => "bisa"],
    ["lapangan" => "Lapangan 3", "tanggal" => "2025-09-25", "jam" => "09.00 - 10.00", "status" => "bisa"],
];

// Ambil parameter dari URL (kalau ada)
$lapanganDipilih = isset($_GET['lapangan']) ? $_GET['lapangan'] : 'Lapangan 1';
$tanggalDipilih  = isset($_GET['tanggal']) ? $_GET['tanggal'] : '2025-09-25';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lapangan.Aja - Pilih Jadwal</title>
<body>

<div class="container">
    <h2>Pilih Tanggal dan Jadwal</h2>

    <h3>Nama Lapangan</h3>
    <div class="lapangan-btn">
        <a href="?lapangan=Lapangan 1" class="<?= $lapanganDipilih == 'Lapangan 1' ? 'active' : '' ?>">Lapangan 1</a>
        <a href="?lapangan=Lapangan 2" class="<?= $lapanganDipilih == 'Lapangan 2' ? 'active' : '' ?>">Lapangan 2</a>
        <a href="?lapangan=Lapangan 3" class="<?= $lapanganDipilih == 'Lapangan 3' ? 'active' : '' ?>">Lapangan 3</a>
    </div>

    <p><b><?= date("d F Y", strtotime($tanggalDipilih)) ?></b></p>

    <div class="jadwal">
        <?php
        $adaJadwal = false;
        foreach ($jadwal as $j) {
            if ($j['lapangan'] == $lapanganDipilih && $j['tanggal'] == $tanggalDipilih) {
                $adaJadwal = true;
                $statusClass = $j['status'] == 'sudah' ? 'sudah' : 'bisa';
                $statusText = $j['status'] == 'sudah' ? 'Sudah Dibooking' : 'Bisa Dibooking';
                echo "<div class='slot $statusClass'>{$statusText} ({$j['jam']})</div>";
            }
        }

        if (!$adaJadwal) {
            echo "<p>Tidak ada jadwal untuk tanggal ini.</p>";
        }
        ?>
    </div>

    <form method="GET" action="">
        <input type="hidden" name="lapangan" value="<?= $lapanganDipilih ?>">
        <label for="tanggal">Pilih tanggal:</label>
        <input type="date" name="tanggal" value="<?= $tanggalDipilih ?>">
        <button class="btn-pilih" type="submit">Pilih Jadwal</button>
    </form>
</div>

</body>
</html>

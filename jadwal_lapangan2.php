<?php
session_start();
include 'config/koneksi.php';

// ====== DATA DEFAULT ======
$tanggalDipilih = $_GET['tanggal'] ?? '2025-09-25';
$lapanganDipilih = $_GET['lapangan'] ?? 'Lapangan 1';
?>
<!DOCTYPE html>

<html lang="id">
<link rel="stylesheet" href="assets/home.css">
<link rel="stylesheet" href="assets/nav.css">
<head>
    <meta charset="UTF-8">
    <title>Lapangin.Aja | Jadwal Lapangan</title>
    <style>

        main {
    padding-top: 20px; /* biar isi halaman gak nabrak header */
}
    
        h3 {
            font-size: 30x;
            margin-bottom: 20px;
            color: #000;
        }

        .content {
            display: flex;
            justify-content: center;
            max-height: 600px;
            height: 500px;
            gap: 20px;
            border-radius: 15px;
            background-color: #ffffffff;
            
        }

        .left {
            width: 30%;
            margin-top: 50px;
        }
        .left h4{

            margin-bottom: 10px;
        }
        .left h3{
            margin-bottom: 5px;
        }


        .left img {
            width: 100%;
            height: 180px;
            border-radius: 8px;
            background: #dfe6e9;
        }

        .lapangan-list a {
            display: block;
            text-align: center;
            padding: 8px 0;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            color: #333;
            text-decoration: none;
        }

        .lapangan-list a.active {
            background: #48cae4;
            color: white;
            border: none;
        }

        .right {
            width: 60%;
            margin-top: 50px;
        }

        .tanggal {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .slot {
            width: 220px;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            text-align: center;
        }

        .sudah {
            background: #e74c3c;
            color: white;
        }

        .bisa {
            border: 1px solid #48cae4;
            color: #333;
        }

        .calendar {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(7, 40px);
            gap: 10px;
        }

        .calendar-day {
            text-align: center;
            padding: 10px;
            border-radius: 50%;
            background: #f1f1f1;
            text-decoration: none;
            color: #333;
            transition: background 0.3s;
        }

        .calendar-day:hover {
            background: #48cae4;
            color: white;
        }

        .calendar-day.selected {
            background: #48cae4;
            color: white;
            font-weight: bold;
        }

        .calendar-day.sudah {
            background: #e74c3c;
            color: white;
        }

        .btn-pilih {
            background: #48cae4;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
        }

        .btn-pilih:hover {
            background: #20773bff;
        }
    </style>
</head>
<body>
    <header>
        <?php include 'includes/nav.php'; ?>
    </header>
    <main>
    <div class="container">
        <div class="content">
            <div class="left">
                <h3>Pilih Tanggal dan Jadwal</h3>
                <h4>Nama Lapangan</h4>
                <img src="assets/image/lapangan.png" alt="Lapangan">
                <div class="lapangan-list">
                    <?php
                    $lapanganList = ['Lapangan 1', 'Lapangan 2', 'Lapangan 3'];
                    foreach ($lapanganList as $lap) {
                        $active = ($lapanganDipilih == $lap) ? 'active' : '';
                        echo "<a href='?lapangan=" . urlencode($lap) . "' class='$active'>$lap</a>";
                    }
                    ?>
                </div>
            </div>

            <div class="right">
                <div class="tanggal"><?= date("d F Y", strtotime($tanggalDipilih)) ?></div>

                <?php
                $status = $_SESSION['booked'][$lapanganDipilih][$tanggalDipilih] ?? false;
                if ($status) {
                    echo "<div class='slot sudah'>Sudah Dibooking<br><b>09.00 - 10.00</b></div>";
                } else {
                    echo "<div class='slot bisa'>Bisa Dibooking<br><b>09.00 - 10.00</b></div>";
                }
                ?>

                <div class="calendar">
                    <?php
                    $tahun = date('Y', strtotime($tanggalDipilih));
                    $bulan = date('m', strtotime($tanggalDipilih));
                    $totalHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

                    for ($i = 1; $i <= $totalHari; $i++):
                        $tanggal = sprintf("%04d-%02d-%02d", $tahun, $bulan, $i);
                        $selected = ($tanggal == $tanggalDipilih);
                        $sudah = isset($_SESSION['booked'][$lapanganDipilih][$tanggal]);
                        $class = "calendar-day";
                        if ($selected)
                            $class .= " selected";
                        if ($sudah)
                            $class .= " sudah";

                        echo "<a href='?tanggal={$tanggal}&lapangan=" . urlencode($lapanganDipilih) . "' class='$class'>$i</a>";
                    endfor;
                    ?>
                </div>

                <?php if (!($status)): ?>
                    <a href="pembayaran1.php?tanggal=<?= $tanggalDipilih ?>&lapangan=<?= urlencode($lapanganDipilih) ?>">
                        <button class="btn-pilih">Lanjut ke Pembayaran</button>
                    </a>


                <?php else: ?>
                    <button class="btn-pilih" style="background:#aaa; cursor:not-allowed;">Sudah Dibooking</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </main>
    
</body>

</html>
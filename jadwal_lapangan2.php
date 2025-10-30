<?php
session_start();

// ====== DATA DEFAULT ======
$tanggalDipilih = $_GET['tanggal'] ?? '2025-09-25';
$lapanganDipilih = $_GET['lapangan'] ?? 'Lapangan 1';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lapangin.Aja | Jadwal Lapangan</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #eaf2f7;
            margin: 0;
            padding: 0;
        }

        .container {
            background: #fff;
            width: 80%;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .header {
            padding: 5px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

       .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo-atas {
            height: 55px;
            width: auto;
        }

        .logo-atas img {
            height: 100%;
            width: auto;
            object-fit: contain;

        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: #5d7b87;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #6d6666;
        }

        .right-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search {
            display: flex;
            align-items: center;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .search input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            width: 120px;
        }

        .search-icon {
            margin-left: 100px;
        }

        .btn-profile-img {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .btn-profile-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-profile-img:hover {
            transform: scale(1.05);
        }

        .btn-logout {
            background: #ca4250;
            color: white !important;
            padding: 8px 10px;
            border-radius: 5px;
            transition: background 0.3s;
            font-size: 10px;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: #000;
        }
        h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .content {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .left {
            width: 35%;
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
            background: #38b1cc;
        }
    </style>
</head>

<body>
    <div class="nav">

            <div class="logo">
                <div class="logo-atas">
                    <img src="assets/image/logo.png" alt="logo lapangin.aja">
                </div>
            </div>

            <div class="nav-links">
                <a href="jadwal_lapangan1.php">Lapangan</a>
                <a href="homepage.php" class="active">Home</a>
                <a href="messege1.php">Messege</a>

                <div class="right-section">
                    <div class="search">
                        <input type="text" placeholder="Cari lapangan...">
                        <span class="search-icon">🔍</span>
                    </div>
                </div>

            <?php if (isset($_SESSION['user'])): ?>
            <a href="#" class="btn-profile-img">
                <img src="assets/image/profile.png" alt="Profile"></a>
                <a href="logout.php" class="btn-logout">Logout</a>
                <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>

            </div>
        </div>
    <div class="container">
        <h3>Pilih Tanggal dan Jadwal</h3>
        <div class="content">
            <div class="left">
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
</body>

</html>
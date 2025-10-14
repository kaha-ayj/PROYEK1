<?php
// Data jadwal dummy (biasanya nanti dari database)
$jadwal = [
    ["lapangan" => "Lapangan 1", "tanggal" => "2025-09-25", "jam" => "09.00 - 10.00", "status" => "sudah"],
    ["lapangan" => "Lapangan 2", "tanggal" => "2025-09-25", "jam" => "09.00 - 10.00", "status" => "bisa"],
    ["lapangan" => "Lapangan 3", "tanggal" => "2025-09-25", "jam" => "09.00 - 10.00", "status" => "bisa"]
];

$tanggalDipilih = isset($_GET['tanggal']) ? $_GET['tanggal'] : '2025-09-25';
$lapanganDipilih = isset($_GET['lapangan']) ? $_GET['lapangan'] : 'Lapangan 2';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Lapangan</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #eaf2f7;
            margin: 0;
            padding: 0;
        }

        /* ===== HEADER / NAVBAR ===== */
        header {
            background: white;
            padding: 10px 50px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 300px; /* kecilin logo */
            height: auto;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #0a3d62;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            flex-grow: 1;
            gap: 40px;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .search-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-user input {
            border: 1px solid #ccc;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 14px;
        }

        .user-icon {
            font-size: 20px;
        }

        /* ===== CONTENT ===== */
        .container {
            background: #fff;
            width: 80%;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
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

        .lapangan-list {
            margin-top: 15px;
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

        .calendar div {
            text-align: center;
            padding: 10px;
            border-radius: 50%;
            background: #f1f1f1;
            cursor: pointer;
        }

        .calendar .selected {
            background: #48cae4;
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
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="assets/image/logo.png" alt="logo">
                <span class="logo-text"></span>
            </div>

            <div class="nav-links">
                <a href="#">Lapangan</a>
                <a href="#">Home</a>
                <a href="#">Message</a>
            </div>

            <div class="search-user">
                <input type="text" placeholder="Cari lapangan...">
                <div class="user-icon"></div>
            </div>
        </nav>
    </header>

    <div class="container">
        <h3>PILIH TANGGAL DAN JADWAL</h3>
        <div class="content">
            <div class="left">
                <h4>Nama Lapangan</h4>
                <img src="assets/image/lapangan.png" alt="Gambar Lapangan">
                <div class="lapangan-list">
                    <a href="?lapangan=Lapangan 1" class="<?= $lapanganDipilih == 'Lapangan 1' ? 'active' : '' ?>">Lapangan 1</a>
                    <a href="?lapangan=Lapangan 2" class="<?= $lapanganDipilih == 'Lapangan 2' ? 'active' : '' ?>">Lapangan 2</a>
                    <a href="?lapangan=Lapangan 3" class="<?= $lapanganDipilih == 'Lapangan 3' ? 'active' : '' ?>">Lapangan 3</a>
                </div>
            </div>

            <div class="right">
                <div class="tanggal"><?= date("d F Y", strtotime($tanggalDipilih)) ?></div>
                <?php
                foreach ($jadwal as $j) {
                    if ($j['lapangan'] == $lapanganDipilih && $j['tanggal'] == $tanggalDipilih) {
                        $class = $j['status'] == 'sudah' ? 'sudah' : 'bisa';
                        $text = $j['status'] == 'sudah' ? 'Sudah Dibooking' : 'Bisa Dibooking';
                        echo "<div class='slot $class'>$text<br><b>{$j['jam']}</b></div>";
                    }
                }
                ?>

                <div class="calendar">
                    <?php
                    for ($i = 1; $i <= 30; $i++) {
                        $selected = ($i == 25) ? "selected" : "";
                        echo "<div class='$selected'>$i</div>";
                    }
                    ?>
                </div>

                <button class="btn-pilih">Pilih Jadwal</button>
            </div>
        </div>
    </div>
</body>
</html>

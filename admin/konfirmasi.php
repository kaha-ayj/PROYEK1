<?php
session_start();
// Arahkan ke halaman login jika pengguna belum login atau bukan admin.
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../config/koneksi.php';

// Cek apakah ada tanggal yang dipilih dari kalender, jika tidak, gunakan tanggal hari ini
$selected_date = $_GET['selected_date'] ?? date('Y-m-d');

// 1. Hitung total penyewaan (booking) PADA TANGGAL YANG DIPILIH
// (Kita hitung semua booking yang statusnya bukan dibatalkan)
$query_penyewaan = "SELECT COUNT(p.pemesananID) AS total_penyewaan 
                    FROM pemesanan p
                    JOIN jadwal j ON p.jadwalID = j.jadwalID
                    WHERE DATE(j.waktuMulai) = '$selected_date' AND p.statusPemesanan != 'Dibatalkan'";
$result_penyewaan = $conn->query($query_penyewaan);
$total_penyewaan = $result_penyewaan->fetch_assoc()['total_penyewaan'];

// 2. Hitung total pemasukan PADA TANGGAL YANG DIPILIH
// (Kita hanya hitung yang statusnya sudah 'Lunas')
$query_pemasukan = "SELECT SUM(p.totalBiaya) AS total_pemasukan 
                    FROM pemesanan p
                    JOIN jadwal j ON p.jadwalID = j.jadwalID
                    WHERE DATE(j.waktuMulai) = '$selected_date' AND p.statusPemesanan = 'Lunas'";
$result_pemasukan = $conn->query($query_pemasukan);
$total_pemasukan = $result_pemasukan->fetch_assoc()['total_pemasukan'] ?? 0; // Default 0 jika null
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Lapangin.Aja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #DDEBFB;
            --main-bg: #F7F9FC;
            --widget-blue: #A8D5E2;
            --widget-white: #FFFFFF;
            --text-dark: #2C3E50;
            --text-light: #8A92A6;
            --shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
            --link-active: #2980B9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--main-bg);
            display: flex;
            height: 100vh;
            color: var(--text-dark);
        }

        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            padding: 24px;
            display: flex;
            flex-direction: column;
        }
        .sidebar .logo { margin-bottom: 40px; font-size: 24px; font-weight: 700; }
        .sidebar nav { flex-grow: 1; }
        .sidebar nav a {
            display: block;
            color: var(--text-dark);
            text-decoration: none;
            padding: 12px 0;
            font-weight: 500;
            font-size: 16px;
            transition: color 0.2s;
        }
        .sidebar nav a.active, .sidebar nav a:hover { color: var(--link-active); }
        .sidebar .profile { margin-top: auto; display: flex; align-items: center; gap: 12px; }
        .profile-pic { width: 40px; height: 40px; background-color: #8A92A6; border-radius: 50%; }
        .profile-info strong { font-size: 14px; }
        .profile-info a { text-decoration:none; color: #E74C3C; font-size: 12px; }

        .main-content {
            flex-grow: 1;
            padding: 32px;
            overflow-y: auto;
        }
        .main-content h1 { font-size: 28px; margin-bottom: 32px; }
        .widgets-container { display: flex; gap: 32px; flex-wrap: wrap; }
        .main-widgets { display: flex; flex-direction: column; gap: 32px; flex-basis: 300px; flex-grow: 1; }
        
        .widget {
            padding: 24px;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }
        .widget .value { font-size: 48px; font-weight: 700; }
        .widget .title { font-size: 16px; font-weight: 500; }
        .widget-blue { background-color: var(--widget-blue); }
        .widget-white { background-color: var(--widget-white); }
        
        /* Ini adalah widget kalender baru */
        .date-picker-widget {
            background-color: var(--widget-white);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--shadow);
            flex-basis: 300px;
            flex-grow: 1;
        }
        .date-picker-widget label {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 16px;
            display: block;
        }
        .date-picker-widget input[type="date"] {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 2px solid var(--sidebar-bg);
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 500;
        }

        /* Kotak info untuk tanggal yang dipilih */
        .info-box {
            margin-top: 20px;
            background-color: var(--sidebar-bg);
            padding: 16px;
            border-radius: 12px;
            text-align: center;
        }
        .info-box p { font-size: 14px; }
        .info-box strong {
            font-size: 18px;
            color: var(--link-active);
        }

    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">Lapangin.Aja</div>
        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="konfirmasi.php">Order</a>
            <a href="jadwal.php">Jadwal</a>
            <a href="lapangan.php">Lapangan</a>
        </nav>
        <div class="profile">
            <div class="profile-pic"></div>
            <div class="profile-info">
                <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong><br>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <h1>Dashboard</h1>
        <div class="widgets-container">
            <div class="main-widgets">
                <div class="widget widget-blue">
                    <p class="value"><?php echo $total_penyewaan; ?></p>
                    <p class="title">Penyewaan</p>
                </div>
                <div class="widget widget-white">
                    <p class="value">Rp. <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></p>
                    <p class="title">Pemasukan</p>
                </div>
            </div>

            <div class="date-picker-widget">
                <form action="dashboard.php" method="GET" id="date-filter-form">
                    <label for="date-picker">Pilih Tanggal</label>
                    <input type="date" id="date-picker" name="selected_date" 
                           value="<?php echo $selected_date; ?>"
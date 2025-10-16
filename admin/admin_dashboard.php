<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include __DIR__ . '/../config/koneksi.php';

// Jumlah pesanan menunggu konfirmasi
$query_konfirmasi = "SELECT COUNT(pemesananID) AS total_konfirmasi FROM pemesanan WHERE statusPemesanan = 'Menunggu Konfirmasi'";
$result_konfirmasi = $conn->query($query_konfirmasi);
$total_konfirmasi = $result_konfirmasi->fetch_assoc()['total_konfirmasi'] ?? 0;

// Total pemasukan
$query_pemasukan = "SELECT SUM(totalBiaya) AS total_pemasukan FROM pemesanan WHERE statusPemesanan = 'Lunas'";
$result_pemasukan = $conn->query($query_pemasukan);
$total_pemasukan = $result_pemasukan->fetch_assoc()['total_pemasukan'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Lapangin.Aja</title>
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .sidebar .logo {
            margin-bottom: 40px;
            font-size: 24px;
            font-weight: 700;
        }

        .sidebar nav {
            flex-grow: 1;
        }

        .sidebar nav a {
            display: block;
            color: var(--text-dark);
            text-decoration: none;
            padding: 12px 0;
            font-weight: 500;
            font-size: 16px;
            transition: color 0.2s;
        }

        .sidebar nav a.active,
        .sidebar nav a:hover {
            color: #2980B9;
        }

        .sidebar .profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .main-content {
            flex-grow: 1;
            padding: 32px;
            overflow-y: auto;
        }

        .main-content h1 {
            font-size: 28px;
            margin-bottom: 32px;
        }

        .widgets-container {
            display: flex;
            gap: 32px;
        }

        .main-widgets {
            display: flex;
            flex-direction: column;
            gap: 32px;
            flex-basis: 300px;
        }

        .widget {
            padding: 24px;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        .widget .value {
            font-size: 48px;
            font-weight: 700;
        }

        .widget .title {
            font-size: 16px;
            font-weight: 500;
        }

        .widget-blue {
            background-color: var(--widget-blue);
        }

        .widget-white {
            background-color: var(--widget-white);
        }

        .calendar-widget {
            background-color: var(--widget-white);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--shadow);
            flex-grow: 1;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header span {
            font-weight: 600;
            font-size: 18px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            text-align: center;
        }

        .calendar-grid div {
            padding: 8px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .calendar-grid .day {
            color: var(--text-light);
        }

        .calendar-grid .date.today {
            background-color: var(--text-dark);
            color: white;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo">Lapangin.Aja</div>
        <nav>
            <a href="admin_dashboard.php" class="active">Dashboard</a>
            <a href="konfirmasi.php">Order</a>
            <a href="jadwal.php">Jadwal</a>
            <a href="lapangan.php">Lapangan</a>
        </nav>
        <div class="profile">
            <div style="width: 40px; height: 40px; background-color: grey; border-radius: 50%;"></div>
            <div>
                <strong><?php echo htmlspecialchars($_SESSION['user']['nama']); ?></strong><br>
                <a href="../logout.php" style="text-decoration:none; color: #E74C3C;">Logout</a>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <h1>Dashboard</h1>
        <div class="widgets-container">
            <div class="main-widgets">
                <div class="widget widget-blue">
                    <p class="value"><?php echo $total_konfirmasi; ?></p>
                    <p class="title">Penyewaan</p>
                </div>
                <div class="widget widget-white">
                    <p class="value">Rp. <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></p>
                    <p class="title">Pemasukan</p>
                </div>
            </div>

            <!-- Kalender tetap -->
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>

<?php
session_start();
// Arahkan ke halaman login jika pengguna belum login atau bukan admin.
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../config/koneksi.php';

// 1. Hitung jumlah pesanan yang perlu dikonfirmasi (untuk widget "Penyewaan")
$query_konfirmasi = "SELECT COUNT(pemesananID) AS total_konfirmasi FROM pemesanan WHERE statusPemesanan = 'Menunggu Konfirmasi'";
$result_konfirmasi = $conn->query($query_konfirmasi);
$total_konfirmasi = $result_konfirmasi->fetch_assoc()['total_konfirmasi'];

// 2. Hitung total pemasukan dari pesanan yang sudah lunas
$query_pemasukan = "SELECT SUM(totalBiaya) AS total_pemasukan FROM pemesanan WHERE statusPemesanan = 'Lunas'";
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
            <a href="index.php" class="active">Dashboard</a>
            <a href="konfirmasi.php">Order</a>
            <a href="jadwal.php">Jadwal</a>
            <a href="lapangan.php">Lapangan</a>
        </nav>
        <div class="profile">
            <div style="width: 40px; height: 40px; background-color: grey; border-radius: 50%;"></div>
            <div>
                <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong><br>
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

            <div class="calendar-widget">
                <div class="calendar-header">
                    <button style="border:none; background:transparent; cursor:pointer;">&lt;</button>
                    <span>October 2025</span>
                    <button style="border:none; background:transparent; cursor:pointer;">&gt;</button>
                </div>
                <div class="calendar-grid">
                    <div class="day">S</div>
                    <div class="day">M</div>
                    <div class="day">T</div>
                    <div class="day">W</div>
                    <div class="day">T</div>
                    <div class="day">F</div>
                    <div class="day">S</div>
                    <div class="date"></div>
                    <div class="date"></div>
                    <div class="date"></div>
                    <div class="date">1</div>
                    <div class="date">2</div>
                    <div class="date">3</div>
                    <div class="date">4</div>
                    <div class="date">5</div>
                    <div class="date">6</div>
                    <div class="date">7</div>
                    <div class="date">8</div>
                    <div class="date">9</div>
                    <div class="date">10</div>
                    <div class="date">11</div>
                    <div class="date">12</div>
                    <div class="date">13</div>
                    <div class="date">14</div>
                    <div class="date">15</div>
                    <div class="date today">16</div>
                    <div class="date">17</div>
                    <div class="date">18</div>
                    <div class="date">19</div>
                    <div class="date">20</div>
                    <div class="date">21</div>
                    <div class="date">22</div>
                    <div class="date">23</div>
                    <div class="date">24</div>
                    <div class="date">25</div>
                    <div class="date">26</div>
                    <div class="date">27</div>
                    <div class="date">28</div>
                    <div class="date">29</div>
                    <div class="date">30</div>
                    <div class="date">31</div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
<?php
$conn->close();
?>
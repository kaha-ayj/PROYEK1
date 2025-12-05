<?php
require_once '../config/koneksi.php';

$selectedDate = $_GET['tanggal'] ?? date('Y-m-d');

$timestamp = strtotime($selectedDate);
$currentMonthName = date('F Y', $timestamp);
$currentYear = date('Y', $timestamp);
$currentMonth = date('m', $timestamp);

$prevMonthDate = date('Y-m-d', strtotime('-1 month', $timestamp));
$nextMonthDate = date('Y-m-d', strtotime('+1 month', $timestamp));


$jumlahPenyewaan = 0;
$totalPemasukan = 0;

$sql_penyewaan = "SELECT COUNT(p.pemesananID) as jumlah 
                  FROM pemesanan p
                  JOIN jadwal j ON p.jadwalID = j.jadwalID
                  WHERE DATE(j.waktuMulai) = ?"; // Gunakan DATE() untuk mengabaikan jam

$stmt_penyewaan = mysqli_prepare($conn, $sql_penyewaan);
mysqli_stmt_bind_param($stmt_penyewaan, "s", $selectedDate);
mysqli_stmt_execute($stmt_penyewaan);
$result_penyewaan = mysqli_stmt_get_result($stmt_penyewaan);
if ($result_penyewaan) {
    $jumlahPenyewaan = mysqli_fetch_assoc($result_penyewaan)['jumlah'];
}

$sql_pemasukan = "SELECT SUM(p.totalBiaya) as total 
                  FROM pemesanan p
                  JOIN jadwal j ON p.jadwalID = j.jadwalID
                  WHERE DATE(j.waktuMulai) = ? AND p.statusPemesanan = 'Lunas'"; // 'Lunas' sesuai DB Anda

$stmt_pemasukan = mysqli_prepare($conn, $sql_pemasukan);
mysqli_stmt_bind_param($stmt_pemasukan, "s", $selectedDate);
mysqli_stmt_execute($stmt_pemasukan);
$result_pemasukan = mysqli_stmt_get_result($stmt_pemasukan);
if ($result_pemasukan) {
    $total = mysqli_fetch_assoc($result_pemasukan)['total'];
    $totalPemasukan = $total ?? 0;
}

mysqli_stmt_close($stmt_penyewaan);
mysqli_stmt_close($stmt_pemasukan);



function buatKalender($year, $month, $selectedDate)
{
    $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
    $firstDayOfWeek = date('N', mktime(0, 0, 0, $month, 1, $year)); // 1 (Senin) - 7 (Minggu)

    $days = ['M', 'S', 'S', 'R', 'K', 'J', 'S'];
    foreach ($days as $day) {
        echo "<div class='calendar-day day-header'>$day</div>";
    }

    for ($i = 1; $i < $firstDayOfWeek; $i++) {
        echo "<div class='calendar-day empty'></div>";
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dateString = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);

        $class = ($dateString == $selectedDate) ? 'active' : '';

        echo "<a href='?tanggal=$dateString' class='calendar-day $class'>$day</a>";
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lapangin.Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-light: #F0F4F8;
            --bg-sidebar: #DDE8F3;
            --text-dark: #2C3E50;
            --card-blue: #AED6F1;
            --card-white: #FFFFFF;
            --date-bg: #EAECEE;
            --date-bg-hover: #D6DBDF;
            --date-bg-active: #3498DB;
            --date-text-active: #FFFFFF;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            background-color: var(--bg-light);
            color: var(--text_dark);
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--bg-sidebar);
            padding: 30px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .sidebar .logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 40px;
        }

        .sidebar nav a {
            display: block;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 25px;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            opacity: 1;
        }

        .sidebar .profile {
            margin-top: auto;
            display: flex;
            align-items: center;
            font-weight: 600;
        }

        .sidebar .profile-icon {
            width: 40px;
            height: 40px;
            background-color: #5D6D7E;
            border-radius: 50%;
            margin-right: 15px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>');
            background-size: 60%;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .main-content h1 {
            font-size: 32px;
            margin-top: 0;
            margin-bottom: 30px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            max-width: 1200px;
        }

        .card {
            background-color: var(--card-white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card.card-blue {
            background-color: var(--card-blue);
        }

        .card h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            opacity: 0.8;
        }

        .card .value {
            font-size: 64px;
            font-weight: 700;
            margin-top: 10px;
            line-height: 1.2;
        }

        .card .value.small {
            font-size: 48px;
        }

        .calendar-card {
            grid-row: 1 / span 2;
            grid-column: 2 / 3;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header h3 {
            margin: 0;
            font-size: 20px;
        }

        .calendar-nav a {
            text-decoration: none;
            font-size: 24px;
            cursor: pointer;
            opacity: 0.5;
            color: var(--text-dark);
            padding: 0 10px;
        }

        .calendar-nav a:hover {
            opacity: 1;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        a.calendar-day {
            text-decoration: none;
            color: var(--text-dark);
            aspect-ratio: 1 / 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: var(--date-bg);
            border-radius: 50%;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s;
        }

        .calendar-day.empty {
            background-color: transparent;
            cursor: default;
        }

        .calendar-day.day-header {
            background-color: transparent;
            font-size: 14px;
            font-weight: 400;
            opacity: 0.6;
            cursor: default;
        }

        a.calendar-day:not(.empty):not(.day-header):hover {
            background-color: var(--date-bg-hover);
        }

        a.calendar-day.active {
            background-color: var(--date-bg-active);
            color: var(--date-text-active);
        }

        .sidebar .profile {
            margin-top: auto;
            display: flex;
            align-items: center;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="logo">Lapangin.Aja</div>
        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="order.php">Order</a>
            <a href="jadwal.php">Jadwal</a>
            <a href="lapangan.php">Lapangan</a>
            <a href="messege.php"> Pesan</a>
        </nav>
        <div class="profile">
            <a href="profil.php"
                style="display: flex; align-items: center; text-decoration: none; color: inherit; flex-grow: 1;">
                <div class="profile-icon"></div>
                <span>Profile</span>
            </a>

            <a href="logout.php" title="Logout"
                style="text-decoration: none; color: var(--text-dark); margin-left: auto; font-size: 1.5em; opacity: 0.6;">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <h1>Dashboard</h1>

        <div class="dashboard-grid">

            <div class="card card-blue">
                <h2>Penyewaan (<?php echo date('d M Y', $timestamp); ?>)</h2>
                <div class="value" id="penyewaan-count">
                    <?php echo $jumlahPenyewaan; ?>
                </div>
            </div>

            <div class="card">
                <h2>Pemasukan (<?php echo date('d M Y', $timestamp); ?>)</h2>
                <div class="value small" id="pemasukan-total">
                    Rp. <?php echo number_format($totalPemasukan, 0, ',', '.'); ?>
                </div>
            </div>

            <div class="card calendar-card">
                <div class="calendar-header">
                    <div class="calendar-nav">
                        <a href="?tanggal=<?php echo $prevMonthDate; ?>">&lt;</a>
                    </div>
                    <h3><?php echo $currentMonthName; ?></h3>
                    <div class="calendar-nav">
                        <a href="?tanggal=<?php echo $nextMonthDate; ?>">&gt;</a>
                    </div>
                </div>
                <div class="calendar-grid">
                    <?php
                    buatKalender($currentYear, $currentMonth, $selectedDate); // <-- DIUBAH
                    ?>
                </div>
            </div>

        </div>
    </main>
</body>

</html>
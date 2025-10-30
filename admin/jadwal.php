<?php
session_start();
// Arahkan ke halaman login jika pengguna belum login atau bukan admin.
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/koneksi.php';

// Query untuk mengambil data jadwal yang digabungkan dengan data lapangan
$sql = "SELECT j.jadwalID, l.namaLapangan, j.waktuMulai, j.waktuSelesai, l.hargaPerJam, j.status 
        FROM jadwal j
        JOIN lapangan l ON j.lapanganID = l.lapanganID
        ORDER BY j.waktuMulai ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal - Admin Lapangin.Aja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #DDEBFB;
            --main-bg: #F7F9FC;
            --table-header-bg: #A8D5E2;
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

        .sidebar nav a.active, .sidebar nav a:hover {
            color: #2980B9;
        }

        .sidebar .profile {
            margin-top: auto;
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
            font-weight: 700;
            margin-bottom: 24px;
        }
        
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .header-row h2 {
            font-size: 20px;
            font-weight: 600;
        }
        
        .date-display {
            background-color: var(--widget-white);
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 500;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .schedule-table {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .table-row {
            display: grid;
            grid-template-columns: 0.5fr 2fr 1.5fr 1fr 1fr 0.5fr;
            align-items: center;
            padding: 16px 24px;
            border-radius: 16px;
            font-weight: 500;
        }
        
        .table-header {
            background-color: var(--table-header-bg);
            color: var(--text-dark);
            font-weight: 600;
        }

        .table-body-row {
            background-color: var(--widget-white);
            box-shadow: var(--shadow);
        }

        .action-icon {
            text-align: center;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">Lapangin.Aja</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="konfirmasi.php">Order</a>
            <a href="jadwal.php" class="active">Jadwal</a>
            <a href="lapangan.php">Lapangan</a>
        </nav>
        <div class="profile">
            <div style="width: 40px; height: 40px; background-color: #8A92A6; border-radius: 50%;"></div>
            <div>
                <strong><?php echo htmlspecialchars($_SESSION['user']['nama']); ?></strong>
                <a href="../logout.php" style="text-decoration:none; color: #E74C3C; font-size: 14px;">Logout</a>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <h1>Jadwal</h1>
        
        <div class="header-row">
            <h2>Nama Lapangan</h2>
            <div class="date-display">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.8333 3.33331H4.16667C3.24619 3.33331 2.5 4.0795 2.5 4.99998V16.6666C2.5 17.5871 3.24619 18.3333 4.16667 18.3333H15.8333C16.7538 18.3333 17.5 17.5871 17.5 16.6666V4.99998C17.5 4.0795 16.7538 3.33331 15.8333 3.33331Z" stroke="#2C3E50" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.3333 1.66669V5.00002" stroke="#2C3E50" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.66669 1.66669V5.00002" stroke="#2C3E50" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2.5 8.33331H17.5" stroke="#2C3E50" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span><?php echo date("j M Y"); ?></span>
            </div>
        </div>

        <div class="schedule-table">
            <div class="table-row table-header">
                <div>ID</div>
                <div>Jenis Lapangan</div>
                <div>Jam</div>
                <div>Harga</div>
                <div>Status</div>
                <div class="action-icon">Action</div>
            </div>

            <?php
            if ($result->num_rows > 0) {
                // Loop melalui data dan tampilkan di baris tabel
                while($row = $result->fetch_assoc()) {
                    // Format waktu
                    $jam_mulai = date("H:i", strtotime($row['waktuMulai']));
                    $jam_selesai = date("H:i", strtotime($row['waktuSelesai']));
                    $jam_range = $jam_mulai . " - " . $jam_selesai;

                    // Format harga
                    $harga = "Rp. " . number_format($row['hargaPerJam'], 0, ',', '.');
                    
                    // Format status
                    $status_display = ($row['status'] == 'Tersedia') ? 'Siap dipakai' : 'Dipesan';
            ?>
                <div class="table-row table-body-row">
                    <div>#A<?php echo str_pad($row['jadwalID'], 3, '0', STR_PAD_LEFT); ?></div>
                    <div><?php echo htmlspecialchars($row['namaLapangan']); ?></div>
                    <div><?php echo $jam_range; ?></div>
                    <div><?php echo $harga; ?></div>
                    <div><?php echo $status_display; ?></div>
                    <div class="action-icon">
                        <a href="edit_jadwal.php?id=<?php echo $row['jadwalID']; ?>" style="text-decoration:none; color:inherit;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22H15C20 22 22 20 22 15V13" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.04 3.02L8.16 10.9C7.86 11.2 7.56 11.79 7.5 12.22L7.07 15.23C6.91 16.32 7.68 17.08 8.77 16.93L11.78 16.5C12.21 16.44 12.8 16.14 13.1 15.84L20.98 7.96C22.34 6.6 22.98 5.01 20.98 3.02C18.99 1.02 17.4 1.66 16.04 3.02Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/><path d="M14.91 4.15C15.58 6.54 17.45 8.41 19.84 9.08" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<div class='table-row table-body-row' style='text-align:center; grid-column: 1 / -1;'>Tidak ada jadwal yang tersedia.</div>";
            }
            ?>
        </div>
    </main>
</body>
</html>
<?php
$conn->close();
?>
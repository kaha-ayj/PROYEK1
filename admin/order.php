<?php
session_start();
require_once '../config/koneksi.php';

$status_filter = $_GET['status'] ?? 'all';
$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-d', strtotime('-30 days'));
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');

$sql = "SELECT 
            p.pemesananID,
            p.statusPemesanan,
            j.waktuMulai,
            j.waktuSelesai,
            l.namaLapangan,
            l.hargaPerJam
        FROM pemesanan p
        JOIN jadwal j ON p.jadwalID = j.jadwalID
        JOIN lapangan l ON j.lapanganID = l.lapanganID
        WHERE DATE(j.waktuMulai) BETWEEN ? AND ?";

$types = "ss";
$params = [$tgl_mulai, $tgl_selesai];

if ($status_filter == 'pending') {
    $sql .= " AND (p.statusPemesanan = 'Belum Bayar' OR p.statusPemesanan = 'Menunggu Konfirmasi')";
} elseif ($status_filter == 'completed') {
    $sql .= " AND p.statusPemesanan = 'Lunas'";
}

$sql .= " ORDER BY j.waktuMulai DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

function formatStatus($status_db)
{
    switch ($status_db) {
        case 'Lunas':
            return ['text' => 'Completed', 'class' => 'status-completed'];
        case 'Menunggu Konfirmasi':
            return ['text' => 'Pending', 'class' => 'status-pending'];
        case 'Belum Bayar':
            return ['text' => 'Pending', 'class' => 'status-pending'];
        case 'Dibatalkan':
            return ['text' => 'Dibatalkan', 'class' => 'status-dibatalkan'];
        default:
            return ['text' => $status_db, 'class' => ''];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Order - Lapangin.Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-light: #F0F4F8;
            --bg-sidebar: #DDE8F3;
            --text-dark: #2C3E50;
            --card-white: #FFFFFF;
            --header-blue: #AED6F1;
            --status-completed: #1ABC9C;
            --status-pending: #E67E22;
            --status-dibatalkan: #E74C3C;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            background-color: var(--bg-light);
            color: var(--text-dark);
            height: 100vh;
        }

        /* Sidebar */
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
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 32px;
            margin: 0;
        }

        .filter-form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
        }

        .filter-tabs a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .filter-tabs a.active {
            background-color: var(--card-white);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .filter-dates {
            display: flex;
            gap: 10px;
        }

        .date-input {
            display: flex;
            align-items: center;
            background-color: var(--card-white);
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #EAECEE;
        }

        .date-input input {
            border: none;
            font-family: 'Poppins';
            font-weight: 600;
        }

        .date-input button {
            border: none;
            background: #3498DB;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 5px;
        }

        .table-container {
            background-color: var(--card-white);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: var(--header-blue);
        }

        th {
            text-align: left;
            padding: 15px 20px;
            font-size: 14px;
            font-weight: 600;
            opacity: 0.8;
        }

        tbody tr {
            border-bottom: 1px solid #EAECEE;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 20px 20px;
            vertical-align: middle;
        }

        td .status-completed {
            color: var(--status-completed);
            font-weight: 600;
        }

        td .status-pending {
            color: var(--status-pending);
            font-weight: 600;
        }

        td .status-dibatalkan {
            color: var(--status-dibatalkan);
            font-weight: 600;
            text-decoration: line-through;
        }

        .action-btn {
            text-decoration: none;
            color: var(--text-dark);
            font-size: 18px;
            padding: 5px 8px;
            border: 1px solid #EAECEE;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo">Lapangin.Aja</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="order.php" class="active">Order</a>
            <a href="jadwal.php">Jadwal</a>
            <a href="lapangan.php">Lapangan</a>
            <a href="messege.php"> Pesan</a>
        </nav>
        <div class="profile">
            <div class="profile-icon"></div>
            <span>Profile</span>
        </div>
    </aside>

    <main class="main-content">
        <div class="header">
            <h1>Order</h1>
        </div>

        <form action="order.php" method="GET" class="filter-form">
            <div class="filter-tabs">
                <a href="?status=all&tgl_mulai=<?php echo $tgl_mulai; ?>&tgl_selesai=<?php echo $tgl_selesai; ?>"
                    class="<?php echo ($status_filter == 'all') ? 'active' : ''; ?>">All Order</a>
                <a href="?status=pending&tgl_mulai=<?php echo $tgl_mulai; ?>&tgl_selesai=<?php echo $tgl_selesai; ?>"
                    class="<?php echo ($status_filter == 'pending') ? 'active' : ''; ?>">Pending</a>
                <a href="?status=completed&tgl_mulai=<?php echo $tgl_mulai; ?>&tgl_selesai=<?php echo $tgl_selesai; ?>"
                    class="<?php echo ($status_filter == 'completed') ? 'active' : ''; ?>">Completed</a>
            </div>

            <div class="filter-dates">
                <div class="date-input">
                    <i class="fa-solid fa-calendar-days" style="margin-right: 8px;"></i>
                    <input type="date" name="tgl_mulai" value="<?php echo htmlspecialchars($tgl_mulai); ?>">
                </div>
                <div class="date-input">
                    <i class="fa-solid fa-calendar-days" style="margin-right: 8px;"></i>
                    <input type="date" name="tgl_selesai" value="<?php echo htmlspecialchars($tgl_selesai); ?>">
                </div>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                <button type="submit" class="date-input-button"
                    style="border:none; background:#3498DB; color:white; border-radius:8px; padding: 0 15px; cursor:pointer;">Filter</button>
            </div>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Lapangan</th>
                        <th>Tanggal & Jam</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) == 0):
                        echo "<tr><td colspan='7' style='text-align:center;'>Tidak ada order ditemukan.</td></tr>";
                    else:
                        while ($row = mysqli_fetch_assoc($result)):
                            $jam = date('d Okt Y, H:i', strtotime($row['waktuMulai'])) . " - " . date('H:i', strtotime($row['waktuSelesai']));
                            $harga = "Rp. " . number_format($row['hargaPerJam'], 0, ',', '.');
                            $status = formatStatus($row['statusPemesanan']);
                            ?>
                            <tr>
                                <td><strong>#<?php echo $row['pemesananID']; ?></strong></td>
                                <td><?php echo htmlspecialchars($row['namaLapangan']); ?></td>
                                <td><?php echo $jam; ?></td>
                                <td>1</td>
                                <td><?php echo $harga; ?></td>
                                <td><span class="<?php echo $status['class']; ?>"><?php echo $status['text']; ?></span></td>
                                <td>
                                    <a href="edit_order.php?id=<?php echo $row['pemesananID']; ?>" class="action-btn"
                                        title="Edit Status">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="hapus_order.php?id=<?php echo $row['pemesananID']; ?>" class="action-btn"
                                        title="Batalkan Pesanan"
                                        onclick="return confirm('Yakin ingin membatalkan/menghapus pesanan ini?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        endwhile;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
<?php
mysqli_close($conn);
?>
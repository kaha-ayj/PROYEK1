<?php
session_start();
require_once '../config/koneksi.php';

$selectedDate = $_GET['tanggal'] ?? date('Y-m-d');
$selectedLapanganID = $_GET['lapangan_id'] ?? null;

// Query untuk mengambil semua lapangan dengan nama venue
$lapangan_list = [];
$query_all_lapangan = "SELECT l.lapanganID, l.namaLapangan, v.namaVenue 
                       FROM lapangan l
                       JOIN venue v ON l.venueID = v.venueID 
                       ORDER BY v.namaVenue ASC, l.namaLapangan ASC";

$result_all_lapangan = mysqli_query($conn, $query_all_lapangan);
while ($row = mysqli_fetch_assoc($result_all_lapangan)) {
    $lapangan_list[] = $row;
}

// Query untuk mengambil jadwal berdasarkan filter
$query_jadwal = "SELECT j.*, l.namaLapangan, l.hargaPerJam, v.namaVenue
                 FROM jadwal j
                 JOIN lapangan l ON j.lapanganID = l.lapanganID
                 JOIN venue v ON l.venueID = v.venueID
                 WHERE DATE(j.waktuMulai) = ? "; 

$params = [$selectedDate];
$types = "s";

if ($selectedLapanganID) {
    $query_jadwal .= " AND j.lapanganID = ? ";
    $params[] = $selectedLapanganID;
    $types .= "i"; // Changed to integer
}
$query_jadwal .= " ORDER BY j.lapanganID, j.waktuMulai ASC";

$stmt = mysqli_prepare($conn, $query_jadwal);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result_jadwal = mysqli_stmt_get_result($stmt);
} else {
    die("Error preparing statement: " . mysqli_error($conn));
}

// Handle status updates from admin
if (isset($_POST['update_status'])) {
    $jadwal_id = $_POST['jadwal_id'];
    $new_status = $_POST['status'];
    
    $query_update = "UPDATE jadwal SET status = ? WHERE jadwalID = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $new_status, $jadwal_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['success_message'] = "Status jadwal berhasil diupdate!";
        header("Location: jadwal.php?tanggal=" . $selectedDate . "&lapangan_id=" . $selectedLapanganID);
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal mengupdate status jadwal.";
    }
    mysqli_stmt_close($stmt_update);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Slot - Lapangin.Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-light: #F0F4F8;
            --bg-sidebar: #DDE8F3;
            --text-dark: #2C3E50;
            --card-white: #FFFFFF;
            --header-blue: #AED6F1;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            background-color: var(--bg-light);
            color: var(--text-dark);
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

        .filter-container {
            display: flex;
            gap: 15px;
            background-color: var(--card-white);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .filter-group label {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .filter-group select,
        .filter-group input[type="date"] {
            padding: 12px;
            border: 1px solid #DDE8F3;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }

        .tambah-btn {
            display: inline-block;
            text-decoration: none;
            background-color: #3498DB;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .table-container {
            background-color: var(--card-white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table-container h2 {
            margin-top: 0;
            margin-bottom: 25px;
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
            padding: 15px;
            font-size: 14px;
            font-weight: 600;
            opacity: 0.8;
        }

        th:first-child {
            border-radius: 10px 0 0 10px;
        }

        th:last-child {
            border-radius: 0 10px 10px 0;
        }

        tbody tr {
            border-bottom: 1px solid #EAECEE;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 20px 15px;
            vertical-align: middle;
        }

        .action-btn {
            display: inline-block;
            text-decoration: none;
            color: var(--text-dark);
            margin: 0 5px;
            font-size: 18px;
        }

        .action-btn.edit {
            color: #2980B9;
        }

        .action-btn.delete {
            color: #C0392B;
        }

        .status-tersedia {
            color: #1ABC9C;
            font-weight: 600;
        }

        .status-dipesan {
            color: #E74C3C;
            font-weight: 600;
        }

        .status-perbaikan {
            color: #F39C12;
            font-weight: 600;
        }

        /* Notifikasi */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Form update status */
        .status-form {
            display: inline;
        }

        .status-select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-family: 'Poppins', sans-serif;
        }

        .update-btn {
            background: #3498DB;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }

        .update-btn:hover {
            background: #2980B9;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo">Lapangin.Aja</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="order.php">Order</a>
            <a href="jadwal.php" class="active">Jadwal</a>
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
            <h1>Kelola Slot Jadwal</h1>
        </div>
        
        <!-- Notifikasi -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error_message']; ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <form action="jadwal.php" method="GET" class="filter-container">
            <div class="filter-group">
                <label for="lapangan_id">Nama Lapangan</label>
                <select name="lapangan_id" id="lapangan_id" onchange="this.form.submit()">
                    <option value="">-- Semua Lapangan --</option>
                    <?php
                    foreach ($lapangan_list as $lap) {
                        $selected = ($lap['lapanganID'] == $selectedLapanganID) ? 'selected' : '';
                        $display_name = htmlspecialchars($lap['namaVenue']) . " - " . htmlspecialchars($lap['namaLapangan']);
                        
                        echo "<option value=\"" . htmlspecialchars($lap['lapanganID']) . "\" $selected>" . $display_name . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" value="<?php echo htmlspecialchars($selectedDate); ?>"
                    onchange="this.form.submit()">
            </div>
        </form>

        <div class="table-container">
            <h2>Daftar Jadwal pada <?php echo date('d M Y', strtotime($selectedDate)); ?></h2>

            <a href="tambah_jadwal.php" class="tambah-btn">
                <i class="fa-solid fa-plus"></i> Tambah Slot Jadwal Baru
            </a>

            <table>
                <thead>
                    <tr>
                        <th>ID Jadwal</th>
                        <th>Venue</th>
                        <th>Lapangan</th>
                        <th>Jam</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result_jadwal) == 0):
                        echo "<tr><td colspan='7' style='text-align:center;'>Belum ada slot jadwal dibuat untuk tanggal/lapangan ini.</td></tr>";
                    else:
                        while ($row = mysqli_fetch_assoc($result_jadwal)):
                            $jam = date('H:i', strtotime($row['waktuMulai'])) . " - " . date('H:i', strtotime($row['waktuSelesai']));
                            $harga = "Rp. " . number_format($row['hargaPerJam'], 0, ',', '.');
                            $status_class = 'status-' . strtolower($row['status']);
                            ?>
                            <tr>
                                <td><strong>#<?php echo $row['jadwalID']; ?></strong></td>
                                <td><?php echo htmlspecialchars($row['namaVenue']); ?></td>
                                <td><?php echo htmlspecialchars($row['namaLapangan']); ?></td>
                                <td><?php echo $jam; ?></td>
                                <td><?php echo $harga; ?></td>
                                <td>
                                    <form method="POST" action="" class="status-form">
                                        <input type="hidden" name="jadwal_id" value="<?php echo $row['jadwalID']; ?>">
                                        <select name="status" class="status-select" onchange="this.form.submit()">
                                            <option value="Tersedia" <?php echo ($row['status'] == 'Tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                                            <option value="Dipesan" <?php echo ($row['status'] == 'Dipesan') ? 'selected' : ''; ?>>Dipesan</option>
                                            <option value="Perbaikan" <?php echo ($row['status'] == 'Perbaikan') ? 'selected' : ''; ?>>Perbaikan</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td>
                                    <a href="edit_jadwal.php?id=<?php echo $row['jadwalID']; ?>" class="action-btn edit"
                                        title="Edit Jadwal">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="hapus_jadwal.php?id=<?php echo $row['jadwalID']; ?>" class="action-btn delete"
                                        title="Hapus Slot"
                                        onclick="return confirm('Yakin hapus slot ini? (Hanya bisa jika belum dipesan)');">
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
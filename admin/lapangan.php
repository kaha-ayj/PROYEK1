<?php
session_start();
// Path koneksi sudah diperbaiki
require_once '../config/koneksi.php';

// --- PERBAIKAN: GUNAKAN JOIN UNTUK MENGAMBIL NAMA VENUE ---
$query = "SELECT 
            l.lapanganID, 
            l.namaLapangan, 
            l.jenis, 
            l.hargaPerJam, 
            v.namaVenue  /* MENGAMBIL NAMA VENUE DARI TABEL V */
          FROM 
            lapangan l
          JOIN 
            venue v ON l.venueID = v.venueID /* JOIN DENGAN TABEL VENUE */
          ORDER BY 
            v.namaVenue ASC, l.namaLapangan ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lapangan - Lapangin.Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ... CSS Anda (sama seperti sebelumnya) ... */
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

        .sidebar .profile a {
            text-decoration: none;
            color: var(--text-dark);
            margin-left: auto;
            font-size: 1.5em;
            opacity: 0.6;
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
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo">Lapangin.Aja</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="order.php">Order</a>
            <a href="jadwal.php">Jadwal</a>
            <a href="lapangan.php" class="active">Lapangan</a>
            <a href="messege.php"> Pesan </a>
        </nav>
        <div class="profile">
            <div class="profile-icon"></div>
            <span>Profile</span>
            <a href="logout.php" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <div class="header">
            <h1>Kelola Lapangan</h1>
        </div>

        <div class="table-container">
            <h2>Daftar Lapangan</h2>

            <a href="tambah_lapangan.php" class="tambah-btn">
                <i class="fa-solid fa-plus"></i> Tambah Lapangan
            </a>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Venue</th> <th>Nama Lapangan</th> <th>Jenis</th>
                        <th>Harga per Jam</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Hitung jumlah kolom di header (6 kolom: ID, Venue, Nama Lapangan, Jenis, Harga, Action)
                    $colspan = 6; 
                    
                    if (mysqli_num_rows($result) == 0):
                        echo "<tr><td colspan='{$colspan}' style='text-align:center;'>Belum ada data lapangan.</td></tr>";
                    else:
                        while ($row = mysqli_fetch_assoc($result)):
                            $harga = "Rp. " . number_format($row['hargaPerJam'], 0, ',', '.');
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['lapanganID']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['namaVenue']); ?></td> <td><?php echo htmlspecialchars($row['namaLapangan']); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis']); ?></td>
                                <td><?php echo $harga; ?></td>
                                <td>
                                    <a href="edit_lapangan.php?id=<?php echo $row['lapanganID']; ?>" class="action-btn edit" title="Edit Lapangan">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="hapus_lapangan.php?id=<?php echo $row['lapanganID']; ?>" class="action-btn delete" title="Hapus Lapangan"
                                        onclick="return confirm('Yakin hapus lapangan ini?');">
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
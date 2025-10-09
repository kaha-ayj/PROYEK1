<?php
include 'koneksi.php';

if (isset($_POST['tambah_jadwal'])) {
    $lapanganID = $_POST['lapanganID'];
    $waktuMulai = $_POST['waktuMulai'];
    $waktuSelesai = $_POST['waktuSelesai'];

    $sql = "INSERT INTO jadwal (lapanganID, waktuMulai, waktuSelesai, status) VALUES ('$lapanganID', '$waktuMulai', '$waktuSelesai', 'Tersedia')";
    $conn->query($sql);
    header("Location: jadwal.php");
}

if (isset($_GET['hapus'])) {
    $jadwalID = $_GET['hapus'];
    $conn->query("DELETE FROM jadwal WHERE jadwalID=$jadwalID");
    header("Location: jadwal.php");
}

$lapangan_result = $conn->query("SELECT * FROM lapangan");
$jadwal_result = $conn->query("SELECT j.*, l.namaLapangan FROM jadwal j JOIN lapangan l ON j.lapanganID = l.lapanganID ORDER BY j.waktuMulai DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Kelola Jadwal</title>
</head>

<body>
    <h2>Manajemen Jadwal Lapangan</h2>

    <form action="jadwal.php" method="POST">
        <h3>Buat Jadwal Baru</h3>
        Lapangan:
        <select name="lapanganID" required>
            <?php while ($lap = $lapangan_result->fetch_assoc()): ?>
                <option value="<?= $lap['lapanganID'] ?>"><?= $lap['namaLapangan'] ?></option>
            <?php endwhile; ?>
        </select><br>
        Waktu Mulai: <input type="datetime-local" name="waktuMulai" required><br>
        Waktu Selesai: <input type="datetime-local" name="waktuSelesai" required><br>
        <button type="submit" name="tambah_jadwal">Tambah Jadwal</button>
    </form>
    <hr>
    <h3>Daftar Jadwal Tersedia & Dipesan</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID Jadwal</th>
            <th>Nama Lapangan</th>
            <th>Waktu Mulai</th>
            <th>Waktu Selesai</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $jadwal_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['jadwalID'] ?></td>
                <td><?= $row['namaLapangan'] ?></td>
                <td><?= $row['waktuMulai'] ?></td>
                <td><?= $row['waktuSelesai'] ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                    <a href="jadwal.php?hapus=<?= $row['jadwalID'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>
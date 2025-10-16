<?php
session_start();
// if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

include '../config/koneksi.php';

// PROSES CREATE (Tambah Jadwal)
if (isset($_POST['tambah_jadwal'])) {
    $lapanganID = $_POST['lapanganID'];
    $waktuMulai = $_POST['waktuMulai'];
    $waktuSelesai = $_POST['waktuSelesai'];

    $sql = "INSERT INTO jadwal (lapanganID, waktuMulai, waktuSelesai, status) VALUES ('$lapanganID', '$waktuMulai', '$waktuSelesai', 'Tersedia')";
    $conn->query($sql);
    header("Location: jadwal.php");
}

// PROSES DELETE (Hapus Jadwal)
if (isset($_GET['hapus'])) {
    $jadwalID = $_GET['hapus'];
    // Sebaiknya hanya jadwal yang belum dipesan yang bisa dihapus
    $conn->query("DELETE FROM jadwal WHERE jadwalID=$jadwalID AND status='Tersedia'");
    header("Location: jadwal.php");
}

// PROSES READ (Mengambil Data)
$lapangan_result = $conn->query("SELECT * FROM lapangan");
$jadwal_result = $conn->query("SELECT j.*, l.namaLapangan FROM jadwal j JOIN lapangan l ON j.lapanganID = l.lapanganID ORDER BY j.waktuMulai DESC");

include '../includes/header.php';
?>

<h2>Manajemen Jadwal Lapangan</h2>

<form action="jadwal.php" method="POST">
    <h3>Buat Jadwal Baru</h3>
    Lapangan:
    <select name="lapanganID" required>
        <option value="">-- Pilih Lapangan --</option>
        <?php while($lap = $lapangan_result->fetch_assoc()): ?>
            <option value="<?= $lap['lapanganID'] ?>"><?= htmlspecialchars($lap['namaLapangan']) ?></option>
        <?php endwhile; ?>
    </select><br>
    Waktu Mulai: <input type="datetime-local" name="waktuMulai" required><br>
    Waktu Selesai: <input type="datetime-local" name="waktuSelesai" required><br>
    <button type="submit" name="tambah_jadwal">Tambah Jadwal</button>
</form>
<hr>

<h3>Daftar Jadwal</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID Jadwal</th>
        <th>Nama Lapangan</th>
        <th>Waktu Mulai</th>
        <th>Waktu Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while($row = $jadwal_result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['jadwalID'] ?></td>
        <td><?= htmlspecialchars($row['namaLapangan']) ?></td>
        <td><?= date('d M Y, H:i', strtotime($row['waktuMulai'])) ?></td>
        <td><?= date('d M Y, H:i', strtotime($row['waktuSelesai'])) ?></td>
        <td>
            <span style="color: <?= $row['status'] == 'Dipesan' ? 'red' : 'green'; ?>;">
                <?= $row['status'] ?>
            </span>
        </td>
        <td>
            <?php if ($row['status'] == 'Tersedia'): ?>
                <a href="jadwal.php?hapus=<?= $row['jadwalID'] ?>" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php
include '../includes/footer.php';
?>
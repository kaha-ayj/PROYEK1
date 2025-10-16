<?php
session_start();
// if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

include '../config/koneksi.php';

if (isset($_GET['konfirmasi'])) {
    $pemesananID = $_GET['konfirmasi'];
    $conn->query("UPDATE pemesanan SET statusPemesanan = 'Lunas' WHERE pemesananID = $pemesananID");
    $conn->query("UPDATE pembayaran SET status = 'Verified' WHERE pemesananID = $pemesananID");
    header("Location: konfirmasi.php");
}

$sql = "SELECT p.*, u.nama AS nama_penyewa, l.namaLapangan
        FROM pemesanan p
        JOIN pengguna u ON p.penyewaID = u.penggunaID
        JOIN jadwal j ON p.jadwalID = j.jadwalID
        JOIN lapangan l ON j.lapanganID = l.lapanganID
        WHERE p.statusPemesanan = 'Menunggu Konfirmasi'";
$result = $conn->query($sql);

include '../includes/header.php';
?>

<h3>Konfirmasi Pembayaran</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID Pesanan</th>
        <th>Penyewa</th>
        <th>Lapangan</th>
        <th>Total Biaya</th>
        <th>Aksi</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['pemesananID'] ?></td>
            <td><?= htmlspecialchars($row['nama_penyewa']) ?></td>
            <td><?= htmlspecialchars($row['namaLapangan']) ?></td>
            <td><?= $row['totalBiaya'] ?></td>
            <td>
                <a href="konfirmasi.php?konfirmasi=<?= $row['pemesananID'] ?>">Konfirmasi</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">Tidak ada data untuk dikonfirmasi.</td></tr>
    <?php endif; ?>
</table>

<?php
include '../includes/footer.php';
?>
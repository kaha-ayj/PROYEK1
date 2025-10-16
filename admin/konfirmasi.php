<?php
session_start();
// if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
//     header("Location: ../login.php");
//     exit();
// }

include '../config/koneksi.php';

// PROSES UPDATE (Konfirmasi Pembayaran)
if (isset($_GET['konfirmasi'])) {
    $pemesananID = $_GET['konfirmasi'];
    // Ubah status pemesanan
    $conn->query("UPDATE pemesanan SET statusPemesanan = 'Lunas' WHERE pemesananID = $pemesananID");
    // (Opsional) Ubah status di tabel pembayaran
    $conn->query("UPDATE pembayaran SET status = 'Verified' WHERE pemesananID = $pemesananID");
    header("Location: konfirmasi.php?status=sukses");
}

// PROSES READ (Tampilkan Data)
$sql = "SELECT p.*, u.nama AS nama_penyewa, l.namaLapangan, j.waktuMulai
        FROM pemesanan p
        JOIN pengguna u ON p.penyewaID = u.penggunaID
        JOIN jadwal j ON p.jadwalID = j.jadwalID
        JOIN lapangan l ON j.lapanganID = l.lapanganID
        WHERE p.statusPemesanan = 'Menunggu Konfirmasi'
        ORDER BY p.tanggalPemesanan ASC";
$result = $conn->query($sql);

include '../includes/header.php';
?>

<h2>Konfirmasi Pembayaran</h2>
<p>Daftar pemesanan yang menunggu untuk diverifikasi.</p>

<?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
    <p style="color:green;">Pembayaran berhasil dikonfirmasi!</p>
<?php endif; ?>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID Pesanan</th>
        <th>Tanggal Pesan</th>
        <th>Nama Penyewa</th>
        <th>Lapangan & Jadwal</th>
        <th>Total Biaya</th>
        <th>Aksi</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['pemesananID'] ?></td>
            <td><?= date('d M Y, H:i', strtotime($row['tanggalPemesanan'])) ?></td>
            <td><?= htmlspecialchars($row['nama_penyewa']) ?></td>
            <td><?= htmlspecialchars($row['namaLapangan']) ?> (<?= date('d M Y, H:i', strtotime($row['waktuMulai'])) ?>)</td>
            <td>Rp <?= number_format($row['totalBiaya'], 0, ',', '.') ?></td>
            <td>
                <a href="konfirmasi.php?konfirmasi=<?= $row['pemesananID'] ?>" onclick="return confirm('Konfirmasi pembayaran ini?')">Konfirmasi</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6" style="text-align:center;">Tidak ada pembayaran yang perlu dikonfirmasi saat ini.</td></tr>
    <?php endif; ?>
</table>

<?php
include '../includes/footer.php';
?>
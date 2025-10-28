<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/koneksi.php';

// Inisialisasi variabel untuk form edit
$is_edit = false;
$edit_data = ['lapanganID' => '', 'namaLapangan' => '', 'jenis' => '', 'hargaPerJam' => ''];

// PROSES CREATE (Tambah Data)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_lapangan'])) {
    $nama = $conn->real_escape_string($_POST['namaLapangan']);
    $jenis = $conn->real_escape_string($_POST['jenis']);
    $harga = $conn->real_escape_string($_POST['hargaPerJam']);

    $conn->query("INSERT INTO lapangan (namaLapangan, jenis, hargaPerJam) VALUES ('$nama', '$jenis', '$harga')");
    header("Location: lapangan.php");
}

// PROSES UPDATE (Ubah Data)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ubah_lapangan'])) {
    $id = $_POST['lapanganID'];
    $nama = $conn->real_escape_string($_POST['namaLapangan']);
    $jenis = $conn->real_escape_string($_POST['jenis']);
    $harga = $conn->real_escape_string($_POST['hargaPerJam']);

    $conn->query("UPDATE lapangan SET namaLapangan='$nama', jenis='$jenis', hargaPerJam='$harga' WHERE lapanganID=$id");
    header("Location: lapangan.php");
}

// PROSES DELETE (Hapus Data)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM lapangan WHERE lapanganID=$id");
    header("Location: lapangan.php");
}

// Ambil data untuk mode edit
if (isset($_GET['ubah'])) {
    $id = $_GET['ubah'];
    $result = $conn->query("SELECT * FROM lapangan WHERE lapanganID=$id");
    if ($result->num_rows > 0) {
        $is_edit = true;
        $edit_data = $result->fetch_assoc();
    }
}

// PROSES READ (Tampilkan Semua Data)
$lapangan_result = $conn->query("SELECT * FROM lapangan ORDER BY lapanganID");

// Panggil file header
include '../includes/header.php';
?>

<h2>Manajemen Data Lapangan</h2>

<form action="lapangan.php" method="POST">
    <h3><?php echo $is_edit ? 'Ubah Data Lapangan' : 'Tambah Lapangan Baru'; ?></h3>
    <input type="hidden" name="lapanganID" value="<?php echo $edit_data['lapanganID']; ?>">

    Nama Lapangan:
    <input type="text" name="namaLapangan" value="<?php echo $edit_data['namaLapangan']; ?>" required><br>

    Jenis Lapangan (misal: Sintetis/Vinyl):
    <input type="text" name="jenis" value="<?php echo $edit_data['jenis']; ?>"><br>

    Harga per Jam:
    <input type="number" name="hargaPerJam" value="<?php echo $edit_data['hargaPerJam']; ?>" required><br>

    <?php if ($is_edit): ?>
        <button type="submit" name="ubah_lapangan">Simpan Perubahan</button>
        <a href="lapangan.php">Batal Ubah</a>
    <?php else: ?>
        <button type="submit" name="tambah_lapangan">Tambah Lapangan</button>
    <?php endif; ?>
</form>

<hr>

<h3>Daftar Lapangan</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama Lapangan</th>
        <th>Jenis</th>
        <th>Harga/Jam</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $lapangan_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['lapanganID']; ?></td>
            <td><?php echo htmlspecialchars($row['namaLapangan']); ?></td>
            <td><?php echo htmlspecialchars($row['jenis']); ?></td>
            <td><?php echo number_format($row['hargaPerJam'], 0, ',', '.'); ?></td>
            <td>
                <a href="lapangan.php?ubah=<?php echo $row['lapanganID']; ?>">Ubah</a> |
                <a href="lapangan.php?hapus=<?php echo $row['lapanganID']; ?>"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
// Panggil file footer
include '../includes/footer.php';
?>
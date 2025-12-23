<?php
session_start();
/** @var mysqli $conn */

// Perbaikan Path Koneksi untuk Hosting
// Menyesuaikan dengan struktur folder: /config/koneksi.php
include_once __DIR__ . "/../config/koneksi.php";

$error = '';

// Ambil daftar venue untuk dropdown
$query_venue = "SELECT venueID, namaVenue FROM venue ORDER BY namaVenue ASC";
$result_venue = mysqli_query($conn, $query_venue);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaLapangan = $_POST['namaLapangan'];
    $jenis = $_POST['jenis'];
    $hargaPerJam = $_POST['hargaPerJam'];
    $venueID = $_POST['venueID']; // Menangkap venueID dari form

    if (!empty($namaLapangan) && !empty($jenis) && !empty($hargaPerJam) && !empty($venueID)) {
        // Query INSERT sekarang menyertakan venueID
        $query = "INSERT INTO lapangan (namaLapangan, jenis, hargaPerJam, venueID) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        // 'ssdi' = string, string, double, integer
        mysqli_stmt_bind_param($stmt, "ssdi", $namaLapangan, $jenis, $hargaPerJam, $venueID);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: lapangan.php?status=sukses_tambah");
            exit();
        } else {
            $error = "Gagal menambahkan data: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Semua field (termasuk Venue) wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Lapangan - Lapangin.Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F0F4F8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #DDE8F3;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .form-buttons {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            flex: 1;
            text-align: center;
        }

        .btn-submit {
            background-color: #3498DB;
            color: white;
        }

        .btn-cancel {
            background-color: #EAECEE;
            color: #5D6D7E;
        }

        .error {
            color: #C0392B;
            background: #FDEDEC;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Tambah Lapangan Baru</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="venueID">Pilih Venue</label>
                <select name="venueID" id="venueID" required>
                    <option value="">-- Pilih Venue --</option>
                    <?php while ($v = mysqli_fetch_assoc($result_venue)): ?>
                        <option value="<?php echo $v['venueID']; ?>"><?php echo htmlspecialchars($v['namaVenue']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="namaLapangan">Nama Lapangan</label>
                <input type="text" id="namaLapangan" name="namaLapangan" placeholder="Contoh: Lapangan A" required>
            </div>
            <div class="form-group">
                <label for="jenis">Jenis Lapangan</label>
                <input type="text" id="jenis" name="jenis" placeholder="Contoh: Badminton" required>
            </div>
            <div class="form-group">
                <label for="hargaPerJam">Harga per Jam</label>
                <input type="number" id="hargaPerJam" name="hargaPerJam" step="1000" required>
            </div>
            <div class="form-buttons">
                <a href="lapangan.php" class="btn btn-cancel">Batal</a>
                <button type="submit" class="btn btn-submit">Simpan Data</button>
            </div>
        </form>
    </div>
</body>

</html>
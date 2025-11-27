<?php
session_start();
require_once '../config/koneksi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $namaLapangan = $_POST['namaLapangan'];
    $jenis = $_POST['jenis'];
    $hargaPerJam = $_POST['hargaPerJam'];

    // Validasi sederhana
    if (!empty($namaLapangan) && !empty($jenis) && !empty($hargaPerJam)) {

        $query = "INSERT INTO lapangan (namaLapangan, jenis, hargaPerJam) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        // 'ssd' = string, string, double
        mysqli_stmt_bind_param($stmt, "ssd", $namaLapangan, $jenis, $hargaPerJam);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: lapangan.php?status=sukses_tambah");
            exit();
        } else {
            $error = "Gagal menambahkan data: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Semua field wajib diisi.";
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Lapangan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* (CSS Form standar) */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F0F4F8;
            color: #2C3E50;
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

        .form-container h1 {
            margin-top: 0;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input {
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
            font-size: 16px;
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
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Tambah Lapangan Baru</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p><?php endif; ?>

        <form action="tambah_lapangan.php" method="POST">
            <div class="form-group">
                <label for="namaLapangan">Nama Lapangan</label>
                <input type="text" id="namaLapangan" name="namaLapangan" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="jenis">Jenis</label>
                <input type="text" id="jenis" name="jenis" placeholder="" required>
            </div>
            <div class="form-group">
                <label for="hargaPerJam">Harga per Jam</label>
                <input type="number" id="hargaPerJam" name="hargaPerJam" placeholder="" step="1000" required>
            </div>
            <div class="form-buttons">
                <a href="lapangan.php" class="btn btn-cancel">Batal</a>
                <button type="submit" class="btn btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</body>

</html>
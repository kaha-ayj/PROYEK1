<?php
session_start();
require_once '../config/koneksi.php';

$error = '';
$pemesananID = $_GET['id'] ?? null;
if (!$pemesananID) {
    die("ID Pesanan tidak valid.");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_post = $_POST['pemesananID'];
    $new_status = $_POST['statusPemesanan'];

    $stmt_get = mysqli_prepare($conn, "SELECT jadwalID FROM pemesanan WHERE pemesananID = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $id_post);
    mysqli_stmt_execute($stmt_get);
    $result_get = mysqli_stmt_get_result($stmt_get);
    $data_pesanan = mysqli_fetch_assoc($result_get);
    $jadwalID = $data_pesanan['jadwalID'];
    mysqli_stmt_close($stmt_get);

    if ($jadwalID) {
        $query_update = "UPDATE pemesanan SET statusPemesanan = ? WHERE pemesananID = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "si", $new_status, $id_post);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        if ($new_status == 'Lunas' || $new_status == 'Menunggu Konfirmasi') {
            $status_jadwal = 'Dipesan';
        } else {
            $status_jadwal = 'Tersedia';
        }

        $stmt_jadwal = mysqli_prepare($conn, "UPDATE jadwal SET status = ? WHERE jadwalID = ?");
        mysqli_stmt_bind_param($stmt_jadwal, "si", $status_jadwal, $jadwalID);
        mysqli_stmt_execute($stmt_jadwal);
        mysqli_stmt_close($stmt_jadwal);

        header("Location: order.php?status=sukses_update");
        exit();

    } else {
        $error = "Gagal menemukan jadwal terkait.";
    }
}

$query_select = "SELECT p.*, l.namaLapangan, j.waktuMulai, j.waktuSelesai
                 FROM pemesanan p
                 JOIN jadwal j ON p.jadwalID = j.jadwalID
                 JOIN lapangan l ON j.lapanganID = l.lapanganID
                 WHERE p.pemesananID = ?";
$stmt_select = mysqli_prepare($conn, $query_select);
mysqli_stmt_bind_param($stmt_select, "i", $pemesananID);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$data_pesanan = mysqli_fetch_assoc($result);

if (!$data_pesanan) {
    die("Data pesanan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Status Pesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
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

        .form-info {
            background: #F4F6F6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid #E67E22;
        }

        .form-info p {
            margin: 5px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

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
            font-size: 16px;
            flex: 1;
            text-align: center;
        }

        .btn-submit {
            background-color: #E67E22;
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
        <h1>Edit Status Pesanan #<?php echo $data_pesanan['pemesananID']; ?></h1>

        <div class="form-info">
            <p><strong>Lapangan:</strong> <?php echo htmlspecialchars($data_pesanan['namaLapangan']); ?></p>
            <p><strong>Jadwal:</strong> <?php echo date('d M Y, H:i', strtotime($data_pesanan['waktuMulai'])); ?></p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p><?php endif; ?>

        <form action="edit_order.php?id=<?php echo $pemesananID; ?>" method="POST">
            <input type="hidden" name="pemesananID" value="<?php echo $data_pesanan['pemesananID']; ?>">

            <div class="form-group">
                <label for="statusPemesanan">Ubah Status</label>
                <select id="statusPemesanan" name="statusPemesanan">
                    <option value="Belum Bayar" <?php echo ($data_pesanan['statusPemesanan'] == 'Belum Bayar') ? 'selected' : ''; ?>>Belum Bayar</option>
                    <option value="Menunggu Konfirmasi" <?php echo ($data_pesanan['statusPemesanan'] == 'Menunggu Konfirmasi') ? 'selected' : ''; ?>>Menunggu Konfirmasi</option>
                    <option value="Lunas" <?php echo ($data_pesanan['statusPemesanan'] == 'Lunas') ? 'selected' : ''; ?>>
                        Lunas</option>
                    <option value="Dibatalkan" <?php echo ($data_pesanan['statusPemesanan'] == 'Dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                </select>
            </div>

            <div class="form-buttons">
                <a href="order.php" class="btn btn-cancel">Batal</a>
                <button type="submit" class="btn btn-submit">Update Status</button>
            </div>
        </form>
    </div>
</body>

</html>
<?php
session_start();
require_once '../config/koneksi.php';

$error = '';
$jadwalID = $_GET['id'] ?? null;
if (!$jadwalID) {
    die("ID Jadwal tidak valid.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_post = $_POST['jadwalID'];
    $status = $_POST['status'];
    $waktuMulai = $_POST['waktuMulai_hidden'];
    $tanggal_redirect = date('Y-m-d', strtotime($waktuMulai));

    $query_update = "UPDATE jadwal SET status = ? WHERE jadwalID = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "si", $status, $id_post);

    if (mysqli_stmt_execute($stmt_update)) {
        header("Location: jadwal.php?tanggal=$tanggal_redirect&status=sukses_update");
        exit();
    } else {
        $error = "Gagal mengubah status: " . mysqli_error($conn);
    }
}

$query_select = "SELECT j.*, l.namaLapangan 
                 FROM jadwal j
                 JOIN lapangan l ON j.lapanganID = l.lapanganID
                 WHERE j.jadwalID = ?";
$stmt_select = mysqli_prepare($conn, $query_select);
mysqli_stmt_bind_param($stmt_select, "i", $jadwalID);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$data_jadwal = mysqli_fetch_assoc($result);

if (!$data_jadwal) {
    die("Data jadwal tidak ditemukan.");
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Status Jadwal</title>
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

        .form-group input[disabled] {
            background-color: #EAECEE;
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
        <h1>Edit Status Slot</h1>

        <div class="form-info">
            <p><strong>Lapangan:</strong> <?php echo htmlspecialchars($data_jadwal['namaLapangan']); ?></p>
            <p><strong>Tanggal:</strong> <?php echo date('d M Y', strtotime($data_jadwal['waktuMulai'])); ?></p>
            <p><strong>Jam:</strong>
                <?php echo date('H:i', strtotime($data_jadwal['waktuMulai'])) . " - " . date('H:i', strtotime($data_jadwal['waktuSelesai'])); ?>
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p><?php endif; ?>

        <form action="edit_jadwal.php?id=<?php echo $jadwalID; ?>" method="POST">
            <input type="hidden" name="jadwalID" value="<?php echo $data_jadwal['jadwalID']; ?>">
            <input type="hidden" name="waktuMulai_hidden" value="<?php echo $data_jadwal['waktuMulai']; ?>">

            <div class="form-group">
                <label for="status">Ubah Status</label>
                <select id="status" name="status" <?php echo ($data_jadwal['status'] == 'Dipesan') ? 'disabled' : ''; ?>>
                    <option value="Tersedia" <?php echo ($data_jadwal['status'] == 'Tersedia') ? 'selected' : ''; ?>>
                        Tersedia</option>
                    <option value="Perbaikan" <?php echo ($data_jadwal['status'] == 'Perbaikan') ? 'selected' : ''; ?>>
                        Perbaikan</option>
                    <?php if ($data_jadwal['status'] == 'Dipesan'): ?>
                        <option value="Dipesan" selected>Dipesan</option>
                    <?php endif; ?>
                </select>
                <?php if ($data_jadwal['status'] == 'Dipesan'): ?>
                    <small style="color: red;">Anda tidak bisa mengubah status slot yang sudah dipesan.</small>
                <?php endif; ?>
            </div>

            <div class="form-buttons">
                <a href="jadwal.php?tanggal=<?php echo date('Y-m-d', strtotime($data_jadwal['waktuMulai'])); ?>"
                    class="btn btn-cancel">Batal</a>
                <button type="submit" class="btn btn-submit" <?php echo ($data_jadwal['status'] == 'Dipesan') ? 'disabled' : ''; ?>>Update Status</button>
            </div>
        </form>
    </div>
</body>

</html>
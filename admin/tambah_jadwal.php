<?php
session_start();
require_once '../config/koneksi.php';

$lapangan_list = [];

// Query modifikasi: JOIN ke tabel venue agar nama lapangan muncul bersama venue-nya
$query_all_lapangan = "SELECT 
                            l.lapanganID, 
                            l.namaLapangan, 
                            v.namaVenue 
                       FROM 
                            lapangan l
                       JOIN 
                            venue v ON l.venueID = v.venueID 
                       ORDER BY 
                            v.namaVenue ASC, l.namaLapangan ASC";

$result_all_lapangan = mysqli_query($conn, $query_all_lapangan);
while ($row = mysqli_fetch_assoc($result_all_lapangan)) {
    $lapangan_list[] = $row;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lapanganID = $_POST['lapanganID'];
    $waktuMulai = $_POST['waktuMulai']; 
    $waktuSelesai = $_POST['waktuSelesai']; 
    $status = $_POST['status'];

    // Input datetime-local dari HTML sudah berbentuk 'Y-m-d\TH:i'. 
    // Format ini sudah langsung bisa diterima MySQL/MariaDB (Y-m-d H:i:s).
    // Kita hanya perlu mengganti 'T' menjadi spasi jika diperlukan, 
    // namun mysqli_stmt_bind_param dengan tipe 's' (string) biasanya cukup toleran.
    $waktuMulai_db = str_replace('T', ' ', $waktuMulai) . ':00'; // Tambah detik :00
    $waktuSelesai_db = str_replace('T', ' ', $waktuSelesai) . ':00'; // Tambah detik :00

    // Pastikan waktu mulai tidak lebih dari waktu selesai
    if (strtotime($waktuMulai_db) >= strtotime($waktuSelesai_db)) {
        $error = "Waktu Mulai harus lebih awal dari Waktu Selesai.";
    } 
    // Pengecekan overlap jadwal (Opsional namun sangat disarankan untuk sistem booking)
    /*
    $overlap_query = "SELECT jadwalID FROM jadwal 
                      WHERE lapanganID = ? 
                      AND (
                          (waktuMulai < ? AND waktuSelesai > ?) OR
                          (waktuMulai BETWEEN ? AND ?) OR
                          (waktuSelesai BETWEEN ? AND ?)
                      )";
    // Untuk menyederhanakan, kita asumsikan pengecekan ini dihilangkan dulu
    // atau ditambahkan di pengembangan selanjutnya.
    */
    
    if (empty($error)) {
        $query = "INSERT INTO jadwal (lapanganID, waktuMulai, waktuSelesai, status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        // Catatan: Tipe "isss" atau "ssss" (string) untuk 4 parameter: ID (integer), Waktu Mulai (string), Waktu Selesai (string), Status (string)
        // Jika lapanganID di database adalah INT, lebih baik pakai "isss". Kita asumsikan "ssss" karena fleksibel.
        mysqli_stmt_bind_param($stmt, "ssss", $lapanganID, $waktuMulai_db, $waktuSelesai_db, $status);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: jadwal.php?status=sukses_tambah");
            exit();
        } else {
            $error = "Gagal menambah slot: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Slot Jadwal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* (CSS Anda tidak diubah, hanya ditambahkan sedikit validasi visual) */
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
            color: #C0392B; /* Merah gelap untuk error */
            background-color: #FADBD8; /* Latar belakang terang */
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Tambah Slot Jadwal Baru</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p><?php endif; ?>

        <form action="tambah_jadwal.php" method="POST">
            <div class="form-group">
                <label for="lapanganID">Lapangan</label>
                <select id="lapanganID" name="lapanganID" required>
                    <option value="">-- Pilih Lapangan --</option>
                    <?php 
                    // Loop menggunakan data yang sudah di-JOIN (ada nama Venue)
                    foreach ($lapangan_list as $lap): 
                        // Menggabungkan Nama Venue dan Nama Lapangan
                        $display_name = htmlspecialchars($lap['namaVenue']) . " - " . htmlspecialchars($lap['namaLapangan']);
                    ?>
                        <option value="<?php echo htmlspecialchars($lap['lapanganID']); ?>">
                            <?php echo $display_name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="waktuMulai">Waktu Mulai</label>
                <input type="datetime-local" id="waktuMulai" name="waktuMulai" required>
            </div>
            <div class="form-group">
                <label for="waktuSelesai">Waktu Selesai</label>
                <input type="datetime-local" id="waktuSelesai" name="waktuSelesai" required>
            </div>
            <div class="form-group">
                <label for="status">Status Awal</label>
                <select id="status" name="status" required>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Perbaikan">Perbaikan</option>
                </select>
            </div>
            <div class="form-buttons">
                <a href="jadwal.php" class="btn btn-cancel">Batal</a>
                <button type="submit" class="btn btn-submit">Simpan Slot</button>
            </div>
        </form>
    </div>
</body>

</html>
<?php
mysqli_close($conn);
?>
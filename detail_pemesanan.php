<?php 
// Cek apakah session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include koneksi database
include 'config/koneksi.php';

// Ambil order_id dari URL
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "<script>alert('Order ID tidak ditemukan.'); window.location.href='home.php';</script>";
    exit;
}

// Ambil detail pesanan dari database
$query = "SELECT pe.*, j.*, l.namaLapangan, l.hargaPerJam, pb.metodePembayaran, pb.status as statusPembayaran, pb.order_id,
                 p.namaPengguna, p.email
          FROM pemesanan pe
          JOIN pembayaran pb ON pe.pemesananID = pb.pemesananID
          JOIN jadwal j ON pe.jadwalID = j.jadwalID
          JOIN lapangan l ON j.lapanganID = l.lapanganID
          JOIN pengguna p ON pe.penggunaID = p.penggunaID
          WHERE pb.order_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();

if (!$pesanan) {
    echo "<script>alert('Data pesanan tidak ditemukan.'); window.location.href='home.php';</script>";
    exit;
}

// Format data
$tanggalPesanan = date('l, d F Y', strtotime($pesanan['tanggalPemesanan']));
$waktuMulai = date('H:i', strtotime($pesanan['waktuMulai']));
$waktuSelesai = date('H:i', strtotime($pesanan['waktuSelesai']));
$totalBiaya = number_format($pesanan['totalBiaya'], 0, ',', '.');

// Translate hari ke Indonesia
$hari = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
$tanggalPesanan = str_replace(array_keys($hari), array_values($hari), $tanggalPesanan);

// Bulan Indonesia
$bulan = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
$tanggalPesanan = str_replace(array_keys($bulan), array_values($bulan), $tanggalPesanan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Detail Pesanan</title>
    <style>
        /* ===== RESET DAN DASAR ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(to bottom right, #dbe8ea, #c8d8dc);
            min-height: 100vh;
        }

        /* ===== CARD DETAIL PESANAN ===== */
        .container {
            display: flex;
            justify-content: center;
            margin-top: 60px;
            padding: 0 20px;
        }

        .card {
            background: #eaf1ef;
            width: 100%;
            max-width: 750px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #222;
            font-size: 28px;
        }

        .order-id {
            text-align: center;
            margin-bottom: 25px;
            color: #666;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 12px;
            margin-left: 10px;
        }

        .status-success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .detail-box {
            background: #f4f8f7;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-box span {
            font-weight: 600;
            color: #666;
        }

        .detail-box .value {
            font-weight: 700;
            color: #222;
        }

        .total-box {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 8px;
            padding: 15px 18px;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .total-box span {
            font-weight: 600;
        }

        .total-box .value {
            font-weight: 700;
            font-size: 24px;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back {
            background: #fff;
            color: #222;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .btn-back:hover {
            background: #dbe8ea;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: #10b981;
            color: white;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        }

        .btn-primary:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
        }

        footer {
            position: fixed;
            bottom: 10px;
            left: 10px;
        }

        footer img {
            height: 50px;
        }

        @media (max-width: 768px) {
            .card {
                padding: 25px;
            }

            h2 {
                font-size: 22px;
            }

            .btn-container {
                flex-direction: column;
            }

            .detail-box {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
<header>
    <?php include 'includes/nav.php'; ?>
</header>

    <div class="container">
        <div class="card">
            <h2>📋 Detail Pesanan</h2>
            <div class="order-id">
                Order ID: <strong><?= htmlspecialchars($order_id) ?></strong>
                <span class="status-badge <?= $pesanan['statusPembayaran'] == 'success' ? 'status-success' : 'status-pending' ?>">
                    <?= $pesanan['statusPembayaran'] == 'success' ? '✅ Berhasil' : '⏳ Pending' ?>
                </span>
            </div>

            <div class="detail-box">
                <span>👤 Nama Pemesan:</span>
                <span class="value"><?= htmlspecialchars($pesanan['namaPengguna']) ?></span>
            </div>

            <div class="detail-box">
                <span>📧 Email:</span>
                <span class="value"><?= htmlspecialchars($pesanan['email']) ?></span>
            </div>

            <div class="detail-box">
                <span>🏸 Jumlah Lapangan:</span>
                <span class="value">1</span>
            </div>

            <div class="detail-box">
                <span>🔢 ID Jadwal:</span>
                <span class="value">#<?= $pesanan['jadwalID'] ?></span>
            </div>

            <div class="detail-box">
                <span>🏟️ Nama Lapangan:</span>
                <span class="value"><?= htmlspecialchars($pesanan['namaLapangan']) ?></span>
            </div>

            <div class="detail-box">
                <span>📅 Hari, Tanggal:</span>
                <span class="value"><?= $tanggalPesanan ?></span>
            </div>

            <div class="detail-box">
                <span>🕐 Waktu:</span>
                <span class="value"><?= $waktuMulai ?> - <?= $waktuSelesai ?></span>
            </div>

            <div class="detail-box">
                <span>💳 Metode Pembayaran:</span>
                <span class="value"><?= ucfirst($pesanan['metodePembayaran']) ?></span>
            </div>

            <div class="total-box">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>💰 Total Pembayaran:</span>
                    <span class="value">Rp <?= $totalBiaya ?></span>
                </div>
            </div>

            <div class="btn-container">
                <a href="home.php" class="btn btn-back">🏠 Kembali ke Home</a>
                <a href="riwayat_pemesanan.php" class="btn btn-primary">📜 Lihat Semua Pesanan</a>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER IMAGE ===== -->
    <footer>
        <img src="assets/image/image 4.png" alt="Logo Shuttlecock">
    </footer>

</body>
</html>
<?php 
// Cek apakah session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil order_id dari URL
$order_id = $_GET['order_id'] ?? 'UNKNOWN';

// Include koneksi database
require_once 'config/koneksi.php';

$detail_pesanan = null;
if ($order_id !== 'UNKNOWN') {
    // Gunakan prepared statement untuk keamanan
    $query = "SELECT 
                p.pembayaranID,
                p.pemesananID,
                p.penggunaID,
                p.metodePembayaran,
                p.grossAmount,
                p.order_id,
                p.transactionID,
                p.paymentType,
                p.status,
                p.waktuDibuat,
                p.waktuUpdate,
                pe.tanggalPemesanan,
                pe.totalBiaya,
                pe.statusPemesanan,
                j.jadwalID,
                j.waktuMulai,
                j.waktuSelesai,
                j.status as statusJadwal,
                l.lapanganID,
                l.namaLapangan,
                l.venueID,
                v.namaVenue,
                v.alamat as alamatVenue
              FROM pembayaran p
              JOIN pemesanan pe ON p.pemesananID = pe.pemesananID
              JOIN jadwal j ON pe.jadwalID = j.jadwalID
              JOIN lapangan l ON j.lapanganID = l.lapanganID
              LEFT JOIN venue v ON l.venueID = v.venueID
              WHERE p.order_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $detail_pesanan = $result->fetch_assoc();
        $stmt->close();
    }
}

// Format fungsi helper
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

function getStatusBadge($status) {
    $status_lower = strtolower(trim($status));
    
    // Status sukses/berhasil
    if (in_array($status_lower, ['settlement', 'dibayar', 'success', 'paid', 'capture', 'completed'])) {
        return '<span class="status-success">Berhasil Dibayar</span>';
    } 
    // Status pending
    elseif (in_array($status_lower, ['pending', 'menunggu'])) {
        return '<span class="status-pending">Menunggu Pembayaran</span>';
    } 
    // Status expired
    elseif (in_array($status_lower, ['expire', 'expired', 'kadaluarsa'])) {
        return '<span class="status-expired">Kadaluarsa</span>';
    } 
    // Status dibatalkan
    elseif (in_array($status_lower, ['cancel', 'cancelled', 'canceled', 'batal', 'dibatalkan'])) {
        return '<span class="status-cancelled">Dibatalkan</span>';
    }
    // Default - tampilkan status asli dengan style success
    else {
        return '<span class="status-success">' . ucfirst($status) . '</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan - Lapangin.Aja</title>
    <link rel="stylesheet" href="assets/home.css">
    <link rel="stylesheet" href="assets/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    
        .detail-wrapper {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .order-id-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .status-display {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-expired {
            background: #f8d7da;
            color: #721c24;
        }

        .status-cancelled {
            background: #e2e3e5;
            color: #383d41;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .section-column {
            display: flex;
            flex-direction: column;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 0 0 15px 0;
        }

        .info-box {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-left: 4px solid #4CAF50;
        }

        .info-box-content {
            font-size: 15px;
            color: #333;
        }

        .info-label {
            font-weight: normal;
            color: #666;
        }

        .price-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-left: 4px solid #4CAF50;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
            color: #333;
        }

        .price-total {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 2px solid #f0f0f0;
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #4CAF50;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            grid-column: 1 / -1;
        }

        .btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
        }

        .btn-secondary {
            background: white;
            color: #4CAF50;
            border: 2px solid #4CAF50;
        }

        .btn-secondary:hover {
            background: #f5f5f5;
        }

        .back-link {
            display: inline-block;
            color: #4CAF50;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 25px;
        }

        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .detail-wrapper {
                padding: 25px;
            }

            .page-title {
                font-size: 22px;
            }

            .button-group {
                flex-direction: column;
            }

            .container {
                margin: 20px auto;
            }
        }

        @media print {
            body {
                background: white;
            }
            
            header, .back-link, .button-group {
                display: none;
            }

            .detail-wrapper {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<header class="header">
<?php include 'includes/nav.php'; ?>
</header>


<div class="container">
    <a href="pembayaran_sukses.php" class="back-link">← Kembali</a>

    <?php if ($detail_pesanan): ?>
    <div class="detail-wrapper">
        
        <div class="page-title">Detail Pemesanan</div>
        <div class="order-id-text">Order ID: <strong><?php echo htmlspecialchars($order_id); ?></strong></div>
        <?php 
        // Prioritas: gunakan status dari pemesanan (dibayar) jika ada, baru status pembayaran
        $display_status = !empty($detail_pesanan['statusPemesanan']) ? $detail_pesanan['statusPemesanan'] : ($detail_pesanan['status'] ?? 'pending');
        echo getStatusBadge($display_status); 
        ?>

        <div class="content-grid">
            
            <!-- Kolom Kiri -->
            <div class="section-column">
                <!-- Informasi Lapangan -->
                <div class="section-title">Informasi Lapangan</div>
                
                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-label">Venue:</span> 
                        <strong><?php echo htmlspecialchars($detail_pesanan['namaVenue'] ?? '-'); ?></strong>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-label">Nama Lapangan:</span> 
                        <strong><?php echo htmlspecialchars($detail_pesanan['namaLapangan']); ?></strong>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-label">Tanggal Main:</span> 
                        <strong>
                            <?php 
                            if (!empty($detail_pesanan['waktuMulai'])) {
                                echo formatTanggal(date('Y-m-d', strtotime($detail_pesanan['waktuMulai'])));
                            } else {
                                echo '-';
                            }
                            ?>
                        </strong>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-label">Waktu Main:</span> 
                        <strong><?php echo date('H:i', strtotime($detail_pesanan['waktuMulai'])) . ' - ' . date('H:i', strtotime($detail_pesanan['waktuSelesai'])); ?> WIB</strong>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-label">Durasi:</span> 
                        <strong>
                            <?php 
                            $start = new DateTime($detail_pesanan['waktuMulai']);
                            $end = new DateTime($detail_pesanan['waktuSelesai']);
                            $diff = $start->diff($end);
                            echo $diff->h . ' Jam';
                            ?>
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="section-column">
                <!-- Informasi Pemesanan -->
                <div class="section-title">Informasi Pemesanan</div>

                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-label">ID Pemesanan:</span> 
                        <strong>#<?php echo htmlspecialchars($detail_pesanan['pemesananID']); ?></strong>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-label">Tanggal Pemesanan:</span> 
                        <strong>
                            <?php 
                            if (isset($detail_pesanan['tanggalPemesanan'])) {
                                echo date('d/m/Y H:i', strtotime($detail_pesanan['tanggalPemesanan'])) . ' WIB';
                            } else {
                                echo '-';
                            }
                            ?>
                        </strong>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-label">Metode Pembayaran:</span> 
                        <strong><?php echo strtoupper(htmlspecialchars($detail_pesanan['metodePembayaran'] ?? '-')); ?></strong>
                    </div>
                </div>
            
                    <div class="price-total">
                        <span>Total Pembayaran</span>
                        <span><?php echo formatRupiah($detail_pesanan['grossAmount'] ?? $detail_pesanan['totalBiaya'] ?? 0); ?></span>
                    </div>
                </div>
            </div>

        
                <a href="homepage.php" class="btn btn-primary">
                    🏠 Kembali ke Beranda
                </a>
            </div>

        </div>

    </div>

    <?php else: ?>
    <div class="detail-wrapper">
        <div class="empty-state">
            <h2>Pemesanan Tidak Ditemukan</h2>
            <p>Maaf, data pemesanan dengan Order ID tersebut tidak ditemukan di sistem kami.</p>
            <a href="homepage.php" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>
    <?php endif; ?>

</div>

</body>
</html>
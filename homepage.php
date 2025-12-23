<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$penggunaID = $user['penggunaID'];

// --- LOGIKA PENCARIAN ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

// Query database hanya dijalankan jika ada kata kunci pencarian
$result_lapangan = null;
if ($search != "") {
    $sql_lapangan = "SELECT l.*, v.namaVenue FROM lapangan l 
                     LEFT JOIN venue v ON l.venueID = v.venueID 
                     WHERE l.namaLapangan LIKE '%$search%' 
                        OR v.namaVenue LIKE '%$search%' 
                        OR l.jenis LIKE '%$search%'";
    $result_lapangan = mysqli_query($conn, $sql_lapangan);
}

// Data Top 5 (Tetap Statis sesuai kode awal Anda)
$top_lapangan = [
    ['nama' => 'Kelapa Gading', 'lokasi' => 'Jl. Merdeka No. 123', 'harga' => 'Rp 50.000', 'rating' => 4.5],
    ['nama' => 'Gangut Bak', 'lokasi' => 'Jl. Sudirman No. 45', 'harga' => 'Rp 45.000', 'rating' => 4.2],
    ['nama' => 'GOR MINI JATIBARANG', 'lokasi' => 'Jl. Jatibarang No. 67', 'harga' => 'Rp 40.000', 'rating' => 4.0],
    ['nama' => 'ABRAL', 'lokasi' => 'Jl. Pahlawan No. 89', 'harga' => 'Rp 55.000', 'rating' => 4.7],
    ['nama' => 'GOR INDRAMAYU', 'lokasi' => 'Jl. Indramayu No. 12', 'harga' => 'Rp 60.000', 'rating' => 4.8]
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Home - Lapangin.Aja</title>
    <link rel="stylesheet" href="assets/home.css">
    <link rel="stylesheet" href="assets/nav.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* =========================
   HERO RESPONSIVE FIX
   ========================= */

/* Tablet */
@media (max-width: 1024px) {
    .hero-content {
        grid-template-columns: 1fr;
        width: 100%;
        margin-left: 0;
        padding: 40px 30px;
        text-align: center;
    }

    .hero-image {
        order: -1;
        height: auto;
        margin-bottom: 20px;
    }

    .hero-image img {
        max-width: 280px;
    }
}

/* Mobile */
@media (max-width: 768px) {
    .hero {
        padding: 20px 0;
    }

    .hero-content {
        grid-template-columns: 1fr;
        width: 100%;
        margin-left: 0;
        padding: 30px 20px;
        border-radius: 15px;
        gap: 25px;
        text-align: center;
    }

    .hero-text h1 {
        font-size: 22px;
    }

    .hero-text h2 {
        font-size: 16px;
    }

    .hero-text p {
        font-size: 14px;
    }

    .hero-image img {
        max-width: 240px;
    }

    .hero-buttons {
        justify-content: center;
    }
}
/* =========================
   DASHBOARD RESPONSIVE
   ========================= */

/* Tablet */
@media (max-width: 1024px) {

    .dashboard-flex {
        flex-direction: column;
        gap: 30px;
    }

    .schedule-sidebar {
        width: 100%;
    }

    .schedule-card {
        width: 100%;
    }
}

/* Mobile */
@media (max-width: 768px) {

    .dashboard-flex {
        flex-direction: column;
        gap: 25px;
    }

    /* ===== Nearby Section ===== */
    .nearby-grid {
        flex-direction: column;
        gap: 20px;
    }

    .nearby-card {
        max-width: 100%;
        height: auto;
    }

    .nearby-image {
        width: 100%;
    }

    .nearby-image img {
        height: auto;
        max-height: 180px;
    }

    .btn-nearby {
        font-size: 13px;
        padding: 12px;
    }

    /* ===== Schedule ===== */
    .schedule-card {
        padding: 20px;
    }

    .btn-view-details {
        font-size: 14px;
        padding: 12px;
    }

    /* ===== Top Lapangan ===== */
    .lapangan-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .lapangan-image {
        height: 180px;
    }

    .lapangan-content h3 {
        font-size: 16px;
    }

    .price {
        font-size: 16px;
    }
}
/* =========================
   JADWAL KAMU RESPONSIVE
   ========================= */

/* Tablet */
@media (max-width: 1024px) {
    .schedule-sidebar {
        width: 100%;
    }

    .schedule-card {
        padding: 22px;
    }
}

/* Mobile */
@media (max-width: 768px) {

    .schedule-sidebar {
        width: 100%;
    }

    .schedule-card {
        padding: 18px;
        border-radius: 15px;
    }

    .schedule-info p {
        font-size: 13px;
        text-align: center;
    }

    .btn-view-details {
        width: 100%;
        font-size: 14px;
        padding: 12px;
        margin-top: 10px;
    }
}
/* =========================
   JADWAL KAMU RESPONSIVE
   ========================= */

/* Tablet */
@media (max-width: 1024px) {
    .schedule-sidebar {
        width: 100%;
        
    }

    .schedule-card {
        padding: 22px;
    }
}

/* Mobile */
@media (max-width: 768px) {

    .schedule-sidebar {
        width: 100%;
    }

    .schedule-card {
        padding: 18px;
        border-radius: 15px;
    }

    .schedule-info p {
        font-size: 13px;
        text-align: center;
    }

    .btn-view-details {
        width: 100%;
        font-size: 14px;
        padding: 12px;
        margin-top: 10px;
    }
}
/* =========================
   DASHBOARD RESPONSIVE
   ========================= */

/* Tablet */
@media (max-width: 1024px) {

    .dashboard-flex {
        flex-direction: column;
        gap: 30px;
    }

    .schedule-sidebar {
        width: 100%;
    }

    .schedule-card {
        width: 100%;
    }
}

/* Mobile */
@media (max-width: 768px) {

    .dashboard-flex {
        flex-direction: column;
        gap: 5px;
    }

    /* ===== Nearby Section ===== */
    .nearby-grid {
        flex-direction: column;
        gap: 20px;
    }

    .nearby-card {
        max-width: 100%;
        height: auto;
    }

    .nearby-image {
        width: 100%;
    }

    .nearby-image img {
        height: auto;
        max-height: 180px;
    }

    .btn-nearby {
        font-size: 13px;
        padding: 12px;
    }

    /* ===== Schedule ===== */
    .schedule-card {
        padding: 20px;
    }

    .btn-view-details {
        font-size: 14px;
        padding: 12px;
    }

    /* ===== Top Lapangan ===== */
    .lapangan-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .lapangan-image {
        height: 180px;
    }

    .lapangan-content h3 {
        font-size: 16px;
    }

    .price {
        font-size: 16px;
    }
}

.dashboard-flex {
            display: flex;
            gap: 30px;
            align-items: flex-start;
            padding-top: 10px;
        }

        .section-title-dark {
            margin-top: 0 !important;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            font-weight: 700;
        }

        .nearby-section {
            flex: 2;
        }

        .nearby-grid {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .schedule-sidebar {
            flex: 1;
        }

        .schedule-card {
            background: #ffffff;
            width: 100%;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid #000;
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <header class="header">
        <?php include 'includes/nav.php'; ?>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-image"><img src="assets/image/anime.png" alt="Hero"></div>
                <div class="hero-text">
                    <h1>Waktunya olahraga!</h1>
                    <h2>BOOKING LAPANGAN MU SEKARANG!</h2>
                    <p>Nggak ada lagi drama cari lapangan kosong. Bareng Lapangin.aja! 🏸⚡</p>
                    <div class="hero-buttons">
                        <a href="jadwal_lapangan1.php" class="btn-primary">BOOKING LAPANGANMU!</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="main-dashboard">
        <div class="container dashboard-flex">

            <div class="nearby-section">
                <h2 class="section-title-dark">
                    <?= ($search != "") ? "Hasil Pencarian: '$search'" : "Lapangan Terdekat"; ?>
                </h2>

                <div class="nearby-grid">
                    <?php if ($search == ""): ?>
                        <div class="nearby-card">
                            <div class="nearby-content">
                                <h3>Abral</h3>
                                <p><i class="fas fa-map-marker-alt"></i> Jl. Raya Panyindangan Wetan, Indramayu</p>
                            </div>
                            <div class="nearby-image"><img src="assets/image/lap_KG.png" alt="Abral"></div>
                            <a href="jadwal_lapangan1.php" class="btn-nearby">LIHAT TERSEDIAANNYA</a>
                        </div>

                        <div class="nearby-card">
                            <div class="nearby-content">
                                <h3>Kelapa Gading</h3>
                                <p><i class="fas fa-map-marker-alt"></i> Jl. Raya Panyindangan Wetan, Indramayu</p>
                            </div>
                            <div class="nearby-image"><img src="assets/image/lap_KG.png" alt="Kelapa Gading"></div>
                            <a href="jadwal_lapangan1.php" class="btn-nearby">LIHAT TERSEDIAANNYA</a>
                        </div>

                    <?php else: ?>
                        <?php if (mysqli_num_rows($result_lapangan) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result_lapangan)): ?>
                                <div class="nearby-card">
                                    <div class="nearby-content">
                                        <h3><?= htmlspecialchars($row['namaLapangan']); ?></h3>
                                        <p><i class="fas fa-map-marker-alt"></i>
                                            <?= htmlspecialchars($row['namaVenue'] ?? 'Indramayu'); ?></p>
                                    </div>
                                    <div class="nearby-image"><img src="assets/image/lap_KG.png" alt="Lapangan"></div>
                                    <a href="jadwal_lapangan1.php?id=<?= $row['lapanganID']; ?>" class="btn-nearby">LIHAT
                                        TERSEDIAANNYA</a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Lapangan "<?= htmlspecialchars($search) ?>" tidak ditemukan.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="schedule-sidebar">
                <h2 class="section-title-dark">Jadwal Kamu</h2>
                <div class="schedule-card">
                    <div class="schedule-info">
                        <p style="font-size: 14px; opacity: 0.6;">Belum ada jadwal booking aktif.</p>
                    </div>
                    <button class="btn-view-details">LIHAT SELENGKAPNYA</button>
                </div>
            </div>

        </div>
    </section>

    <section class="top-lapangan">
        <div class="container">
            <h2 class="section-title">TOP 5 LAPANGAN BADMINTON TERBAIK DI INDRAMAYU</h2>
            <div class="lapangan-grid">
                <?php foreach ($top_lapangan as $lapangan): ?>
                    <div class="lapangan-card">
                        <div class="lapangan-image">
                            <img src="assets/image/lap1.jpg" alt="<?= $lapangan['nama']; ?>">
                            <div class="rating-badge">
                                Sangat Baik <div class="rating-score"><?= $lapangan['rating']; ?></div>
                            </div>
                        </div>
                        <div class="lapangan-content">
                            <h3><?= $lapangan['nama']; ?></h3>
                            <p class="lapangan-location"><i class="fas fa-map-marker-alt"></i> <?= $lapangan['lokasi']; ?>
                            </p>
                            <div class="lapangan-footer">
                                <div class="price-info">
                                    <p class="price-label">Harga per-Jam</p>
                                    <p class="price"><?= $lapangan['harga']; ?></p>
                                </div>
                                <a href="booking.php" class="btn-cek">Cek Lapangannya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>
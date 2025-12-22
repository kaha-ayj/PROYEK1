<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

// Data lapangan (Top 5)
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Lapangin.Aja</title>
    <link rel="stylesheet" href="assets/home.css">
    <link rel="stylesheet" href="assets/nav.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
   /* Container Utama */
.dashboard-flex {
    display: flex;
    gap: 30px;
    align-items: flex-start; /* WAJIB: Menyejajarkan elemen tepat dari atas */
    padding-top: 10px;
}

/* Menyamakan posisi Judul */
.section-title-dark {
    margin-top: 0 !important; 
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
    font-weight: 700;
    line-height: 1.2; /* Menyamakan tinggi baris judul */
}

/* Sisi Kiri */
.nearby-section {
    flex: 2; 
}

.nearby-grid {
    display: flex;
    gap: 15px;
}

/* Sisi Kanan */
.schedule-sidebar {
    flex: 1;
}

/* Box Jadwal - Perbaikan Width */
.schedule-card {
    background: #ffffff;
    width: 100%; 
    max-width: 400px; 
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    border: 1px solid #000000ff; 
    box-sizing: border-box;
}

/* Grid Info Jadwal */
.schedule-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* Membagi 2 kolom sejajar */
    gap: 15px;
    margin-bottom: 20px;
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
            <div class="hero-image">
                <img src="assets/image/anime.png" alt="Badminton Player">
            </div>
            <div class="hero-text">
                <h1>Waktunya olahraga!</h1>
                <h2>BOOKING LAPANGAN MU SEKARANG!</h2>
                <p>Nggak ada lagi drama cari lapangan kosong. Saatnya smash tanpa ribet, bareng Lapangin.aja! 🏸⚡</p>
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
            <h2 class="section-title-dark">Lapangan Terdekat</h2>
            <div class="nearby-grid">
                <div class="nearby-card">
                    <div class="nearby-content">
                        <h3>Abral</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Raya Panyindangan Wetan, Indramayu</p>
                    </div>
                    <div class="nearby-image">
                        <img src="assets/image/lap_KG.png" alt="Abral">
                    </div>
                    <a href="jadwal_lapangan1.php" class="btn-nearby">LIHAT TERSEDIAANNYA</a>
                </div>

                <div class="nearby-card">
                    <div class="nearby-content">
                        <h3>Kelapa Gading</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Raya Panyindangan Wetan, Indramayu</p>
                    </div>
                    <div class="nearby-image">
                        <img src="assets/image/lap_KG.png" alt="Kelapa Gading">
                    </div>
                    <a href="jadwal_lapangan1.php" class="btn-nearby">LIHAT TERSEDIAANNYA</a>
                </div>
            </div>
        </div>

        <div class="schedule-sidebar">
            <h2 class="section-title-dark">Jadwal Kamu</h2>
            <div class="schedule-card">
                <div class="schedule-info">
                    <div class="schedule-item">
                        <p class="schedule-label">Waktu:</p>
                        <p class="schedule-value">13.00 - 14.00</p>
                    </div>
                    <div class="schedule-item">
                        <p class="schedule-label">Lapangan:</p>
                        <p class="schedule-value">03-Kelapa Gading</p>
                    </div>
                    <div class="schedule-item">
                        <p class="schedule-label">Kode Pengguna:</p>
                        <p class="schedule-value">A001</p>
                    </div>
                    <div class="schedule-item">
                        <p class="schedule-label">Durasi:</p>
                        <p class="schedule-value">1 Jam</p>
                    </div>
                </div>
                <button class="btn-view-details">LIHAT SELENGKAPNYA</button>
            </div>
        </div>

    </div>
</section>

<section class="booking-history">
    <div class="container">
        <h2 class="section-title-dark">Riwayat Booking Terakhir</h2>
        <div class="history-grid">
            <div class="history-card">
                <div class="history-header">
                    <h3>Abral - Lapangan 1</h3>
                    <span class="status-badge">Selesai</span>
                </div>
                <div class="history-details">
                    <p><i class="fas fa-calendar"></i> Sabtu, 18.02 - 19.00</p>
                    <p><i class="fas fa-clock"></i> 10/09/2025</p>
                </div>
            </div>
            </div>
    </div>
</section>

<section class="top-lapangan">
    <div class="container">
        <h2 class="section-title">TOP 5 LAPANGAN BADMINTON TERBAIK DI INDRAMAYU</h2>
        <div class="lapangan-grid">
            <?php foreach($top_lapangan as $index => $lapangan): ?>
            <div class="lapangan-card">
                <div class="lapangan-image">
                    <img src="assets/image/lap1.jpg" alt="<?= $lapangan['nama']; ?>">
                    <div class="rating-badge">
                        Sangat Baik
                        <div class="rating-score"><?= $lapangan['rating']; ?></div>
                    </div>
                </div>
                <div class="lapangan-content">
                    <h3><?= $lapangan['nama']; ?></h3>
                    <p class="lapangan-location"><i class="fas fa-map-marker-alt"></i> <?= $lapangan['lokasi']; ?></p>
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
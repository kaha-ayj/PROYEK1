<?php
session_start();
include 'config/koneksi.php';

// Cek login dulu
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

// Data lapangan (dummy - nanti bisa dari database)
$top_lapangan = [
    [
        'nama' => 'Keldipa Gading',
        'lokasi' => 'Jl. Merdeka No. 123',
        'harga' => 'Rp 50.000',
        'rating' => 4.5,
        'gambar' => 'assets/images/lapangan1.jpg'
    ],
    [
        'nama' => 'Gangut Bak',
        'lokasi' => 'Jl. Sudirman No. 45',
        'harga' => 'Rp 45.000',
        'rating' => 4.2,
        'gambar' => 'assets/images/lapangan2.jpg'
    ],
    [
        'nama' => 'GOR MINI JATIBARANG',
        'lokasi' => 'Jl. Jatibarang No. 67',
        'harga' => 'Rp 40.000',
        'rating' => 4.0,
        'gambar' => 'assets/images/lapangan3.jpg'
    ],
    [
        'nama' => 'ABRAL',
        'lokasi' => 'Jl. Pahlawan No. 89',
        'harga' => 'Rp 55.000',
        'rating' => 4.7,
        'gambar' => 'assets/images/lapangan4.jpg'
    ],
    [
        'nama' => 'GOR INDRAMAYU',
        'lokasi' => 'Jl. Indramayu No. 12',
        'harga' => 'Rp 60.000',
        'rating' => 4.8,
        'gambar' => 'assets/images/lapangan5.jpg'
    ]
];

// Booking terakhir (dummy data)
$booking_terakhir = [
    'lapangan' => 'Lapangan Badminton A',
    'tanggal' => '15 Desember 2024',
    'waktu' => '13:00 - 14:00'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><Home - Lapangin.Aja</title>
    <link rel="stylesheet" href="assets/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<header class="header">
    <div class="container">
        <div class="nav">

            <div class="logo">
                <div class="logo-atas">
                    <img src="assets/image/logo.png" alt="logo lapangin.aja">
                </div>
            </div>

            <div class="nav-links">
                <a href="jadwal_lapangan1.php">Lapangan</a>
                <a href="homepage.php" class="active">Home</a>
                <a href="messege1.php">Messege</a>

                <div class="right-section">
                    <div class="search">
                        <input type="text" placeholder="Cari lapangan...">
                        <span class="search-icon">🔍</span>
                    </div>
                </div>

            <?php if (isset($_SESSION['user'])): ?>
            <a href="#" class="btn-profile-img">
                <img src="assets/image/profile.png" alt="Profile"></a>
                <a href="logout.php" class="btn-logout">Logout</a>
                <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</header>
    

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-image">
                    <img src="assets/image/anime.png" alt="Badminton Player">
                </div>
                <div class="hero-text">
                    <h1>Waktunya olahraga!</h1>
                    <h2>BOOKING LAPANGAN MU SEKARANG!</h2>
                    <p> Nggak ada lagi drama cari lapangan badminton kosong. Mau main santai bareng teman atau latihan serius, 
                        semua bisa kamu booking dalam hitungan detik. Saatnya smash tanpa ribet, bareng Lapangin.aja! 🏸⚡</p>
                <div class="hero-buttons">
                        <a href="jadwal_lapangan1.php" class="btn-primary">BOOKING LAPANGANMU!</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

   <section class="nearby-section">
    <div class="container">
        <h2 class="section-title-dark">Lapangan Terdekat</h2>
        <div class="nearby-grid">

            <!-- Box 1 -->
            <div class="nearby-card">
                <div class="nearby-content">
                    <h3>Abral</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu</p>
                </div>
                <div class="nearby-image">
                    <img src="assets/image/lap_KG.png" alt="Abral">
                </div>
                <div>
                    <a href="jadwal_lapangan1.php" class="btn-nearby">LIHAT TERSEDIAANNYA</a>
                </div>
            </div>

            <div class="nearby-card">
                <div class="nearby-content">
                    <h3>Kelapa Gading</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu</p>
                </div>
                <div class="nearby-image">
                    <img src="assets/image/lap_KG.png" alt="Kelapa Gading">
                </div>
                <div>
                    <a href="jadwal_lapangan1.php" class="btn-nearby">LIHAT TERSEDIAANNYA</a>
                </div>
            </div>

        </div>
    </div>
</section>


    <!-- Riwayat Booking Section -->
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
                        <p><i class="fas fa-clock"></i> 10/09/2024</p>
                    </div>
                </div>
                <div class="history-card">
                    <div class="history-header">
                        <h3>Kelapa Gading - Lapangan 3</h3>
                        <span class="status-badge">Selesai</span>
                    </div>
                    <div class="history-details">
                        <p><i class="fas fa-calendar"></i> Minggu, 18.02 - 18.00</p>
                        <p><i class="fas fa-clock"></i> 20/09/2024</p>
                    </div>
                </div>
                <div class="history-card">
                    <div class="history-header">
                        <h3>Abral - Lapangan 1</h3>
                        <span class="status-badge status-upcoming">Akan Datang</span>
                    </div>
                    <div class="history-details">
                        <p><i class="fas fa-calendar"></i> Jumat, 19.00 - 20.00</p>
                        <p><i class="fas fa-clock"></i> 23/09/2024</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jadwal Kamu Section -->
    <section class="schedule-section">
        <div class="container">
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
    </section>

    <!-- Top Lapangan -->
    <section class="top-lapangan">
        <div class="container">
            <h2 class="section-title">TOP 5 LAPANGAN BADMINTON TERBAIK DI INDRAMAYU</h2>
            <div class="lapangan-grid">
                <?php foreach($top_lapangan as $lapangan): ?>
                <div class="lapangan-card">
                    <div class="lapangan-image">
                        <img src="<?= $lapangan['gambar']; ?>" alt="<?= $lapangan['nama']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=Lapangan'">
                        <div class="rating-badge">
                            Sangat Baik
                            <div class="rating-score"><?= $lapangan['rating']; ?></div>
                        </div>
                    </div>
                    <div class="lapangan-content">
                        <h3><?= $lapangan['nama']; ?></h3>
                        <p class="lapangan-location"><i class="fas fa-map-marker-alt"></i> <?= $lapangan['lokasi']; ?></p>
                        <div class="lapangan-rating">
                            <?php
                            $full = floor($lapangan['rating']);
                            $half = ($lapangan['rating'] - $full) >= 0.5;
                            for($i=0; $i<$full; $i++) echo '<i class="fas fa-star"></i>';
                            if($half) echo '<i class="fas fa-star-half-alt"></i>';
                            for($i=0; $i<5-$full-($half?1:0); $i++) echo '<i class="far fa-star"></i>';
                            ?>
                        </div>
                        <div class="lapangan-footer">
                            <div class="price-info">
                                <p class="price-label">Harga per-Jam</p>
                                <p class="price"><?= $lapangan['harga']; ?></p>
                            </div>
                            <a href="booking.php?lapangan=<?= urlencode($lapangan['nama']); ?>" class="btn-cek">Cek Lapangannya</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Lapangin.Aja</h3>
                    <p>Platform booking lapangan olahraga terpercaya di Indramayu</p>
                </div>
                <div class="footer-section">
                    <h4>Kontak</h4>
                    <p><i class="fas fa-phone"></i> +62 812-3456-7890</p>
                    <p><i class="fas fa-envelope"></i> info@lapanginaja.com</p>
                </div>
                <div class="footer-section">
                    <h4>Follow Kami</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Lapangin.Aja. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
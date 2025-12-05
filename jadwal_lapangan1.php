<?php
session_start();
include 'config/koneksi.php';

// Ambil data VENUE dari database
$venue_list = [];

// Query untuk mengambil venue dari database
$query = "SELECT v.venueID, v.namaVenue, v.alamat 
          FROM venue v 
          ORDER BY v.namaVenue";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Jika ada data di database, ambil dari database
    while ($row = mysqli_fetch_assoc($result)) {
        $venue_list[] = [
            "id" => $row['venueID'],
            "nama" => $row['namaVenue'],
            "deskripsi" => $row['alamat'] ?? "Lokasi venue olahraga terbaik",
            "gambar" => "assets/image/lapangan.png"
        ];
    }
} else {
    // Jika tidak ada data di database, gunakan data dummy
    $venue_list = [
        [
            "id" => 1,
            "nama" => "Kelapa Gading",
            "deskripsi" => "Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu.",
            "gambar" => "assets/image/lapangan.png"
        ],
        [
            "id" => 2,
            "nama" => "GOR MINI JATIBARANG",
            "deskripsi" => "Jl. Mayor Dasuki no. 159, Desa jatibarang, Kec. Jatibarang, Kabupaten Indramayu.",
            "gambar" => "assets/image/lapangan.png"
        ],
        [
            "id" => 3,
            "nama" => "ABRAL",
            "deskripsi" => "Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu.",
            "gambar" => "assets/image/lapangan.png"
        ],
    ];
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/home.css">
    <link rel="stylesheet" href="assets/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Lapangin.Aja | Pilih Venue</title>
    <style>
        /* Konten utama */
        main {
            padding: 40px 80px;
        }

        h2 {
            font-size: 28px;
            color: #222;
            margin-bottom: 10px;
        }

        .filter {
            background: #8fa1a3;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            font-style: italic;
            margin-bottom: 20px;
        }

        .venue-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .card {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 2px 4px 8px rgba(0,0,0,0.1);
            transition: 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 2px 8px 16px rgba(0,0,0,0.15);
        }

        .card img {
            width: 160px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 20px;
        }

        .card-content {
            flex: 1;
        }

        .card-content h3 {
            margin: 0;
            color: #000;
            font-size: 18px;
        }

        .card-content p {
            color: #666;
            font-size: 13px;
            margin: 8px 0 15px 0;
            line-height: 1.4;
        }

        .lihat-jadwal {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .btn-nearby {
            background: #48cae4;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-nearby:hover {
            background: #0096c7;
            transform: translateX(5px);
        }

        .info-lapangan {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
            display: inline-block;
            margin-top: 5px;
        }

        /* Footer dekorasi */
        .footer-icon {
            position: fixed;
            bottom: 15px;
            left: 15px;
            width: 80px;
            opacity: 0.6;
        }

    </style>
</head>
<body>
<header class="header">
<?php include 'includes/nav.php'; ?>
</header>
<main>
    <h2>Pilih Venue</h2>
    <div class="filter">Pilih lokasi venue untuk melihat lapangan yang tersedia</div>

    <div class="venue-container">
        <?php foreach ($venue_list as $venue): ?>
        <div class="card">
            <img src="<?php echo $venue['gambar']; ?>" alt="<?php echo $venue['nama']; ?>">
            <div class="card-content">
                <h3><?php echo $venue['nama']; ?></h3>
                <p><?php echo $venue['deskripsi']; ?></p>
                <div class="info-lapangan">
                    <i class="fas fa-map-marker-alt"></i> Klik untuk melihat daftar lapangan
                </div>
                <div class="lihat-jadwal"> 
                    <!-- Mengirimkan ID VENUE yang dipilih ke halaman lapangan2.php -->
                    <a href="jadwal_lapangan2.php?venue_id=<?php echo $venue['id']; ?>" class="btn-nearby"> 
                        Pilih Venue ›
                    </a> 
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<img src="assets/image/image 4.png" class="footer-icon" alt="Dekorasi">
<?php include 'includes/footer.php'; ?>
</body>
</html>
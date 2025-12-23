<?php
session_start();
include 'config/koneksi.php';

// Ambil data VENUE dari database
$venue_list = [];

$query = "SELECT venueID, namaVenue, alamat, foto, fasilitas
          FROM venue
          ORDER BY namaVenue";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        // Tentukan foto venue (pakai DB, fallback jika kosong / file tidak ada)
        $fotoVenue = "assets/image/lapangan.png";
        if (!empty($row['foto']) && file_exists($row['foto'])) {
            $fotoVenue = $row['foto'];
        }
$fasilitasArr = [];

if (!empty($row['fasilitas'])) {
    $fasilitasArr = array_map(
        'trim',
        explode(',', $row['fasilitas'])
    );
}

       $venue_list[] = [
    "id" => $row['venueID'],
    "nama" => $row['namaVenue'],
    "deskripsi" => $row['alamat'] ?? "Lokasi venue olahraga terbaik",
    "gambar" => $fotoVenue,
    "fasilitas" => $fasilitasArr
];

    }

} else {
    // Data dummy (jaga-jaga kalau DB kosong)
    $venue_list = [
        [
            "id" => 1,
            "nama" => "Kelapa Gading",
            "deskripsi" => "Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu.",
            "gambar" => "assets/image/lap1.jpg"
        ],
        [
            "id" => 2,
            "nama" => "GOR MINI JATIBARANG",
            "deskripsi" => "Jl. Mayor Dasuki no. 159, Desa Jatibarang, Kec. Jatibarang, Kabupaten Indramayu.",
            "gambar" => "assets/image/lap2.jpg"
        ],
        [
            "id" => 3,
            "nama" => "ABRAL",
            "deskripsi" => "Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu.",
            "gambar" => "assets/image/lap3.jpg"
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
        main { padding: 40px 80px; }
        h2 { font-size: 28px; color: #222; margin-bottom: 10px; }
    
@media (max-width: 1024px) {
    main {
        padding: 30px 40px;
    }

    .venue-container {
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 20px;
    }

    .card img {
        width: 140px;
        height: 90px;
    }

    h2 {
        font-size: 24px;
    }
}

/* Mobile */
@media (max-width: 768px) {
    main {
        padding: 20px;
    }

    .filter {
        font-size: 13px;
        padding: 6px 14px;
    }

    .venue-container {
        grid-template-columns: 1fr;
    }

    .card {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }

    .card img {
        width: 100%;
        height: 180px;
        margin-right: 0;
        margin-bottom: 15px;
    }

    .card-content h3 {
        font-size: 17px;
    }

    .card-content p {
        font-size: 13px;
    }

    .lihat-jadwal {
        justify-content: flex-start;
        margin-top: 10px;
    }

    .btn-nearby {
        font-size: 13px;
        padding: 8px 14px;
    }

    .footer-icon {
        width: 60px;
        bottom: 10px;
        left: 10px;
    }
}

/* HP kecil */
@media (max-width: 480px) {
    h2 {
        font-size: 20px;
    }

    .card {
        padding: 15px;
    }

    .card img {
        height: 150px;
    }

    .fasilitas span {
        font-size: 11px;
        padding: 5px 8px;
    }

    .info-lapangan {
        font-size: 11px;
    }

    .btn-nearby {
        font-size: 12px;
    }
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

        .card-content { flex: 1; }

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
            transition: 0.3s;
        }
.fasilitas {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
}

.fasilitas span {
    background: #f1f5f9;
    color: #334155;
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.fasilitas i {
    color: #0ea5e9;
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

        .footer-icon {
            position: fixed;
            bottom: 15px;
            left: 15px;
            width: 80px;
            opacity: 0.6;
        }
        /* =========================
   RESPONSIVE FIX
   ========================= */

/* Tablet */
@media (max-width: 1024px) {
    main {
        padding: 30px 40px;
    }

    .venue-container {
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 20px;
    }

    .card img {
        width: 140px;
        height: 90px;
    }

    h2 {
        font-size: 24px;
    }
}

/* Mobile */
@media (max-width: 768px) {
    main {
        padding: 20px;
    }

    .filter {
        font-size: 13px;
        padding: 6px 14px;
    }

    .venue-container {
        grid-template-columns: 1fr;
    }

    .card {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }

    .card img {
        width: 100%;
        height: 180px;
        margin-right: 0;
        margin-bottom: 15px;
    }

    .card-content h3 {
        font-size: 17px;
    }

    .card-content p {
        font-size: 13px;
    }

    .lihat-jadwal {
        justify-content: flex-start;
        margin-top: 10px;
    }

    .btn-nearby {
        font-size: 13px;
        padding: 8px 14px;
    }

    .footer-icon {
        width: 60px;
        bottom: 10px;
        left: 10px;
    }
}

/* HP kecil */
@media (max-width: 480px) {
    h2 {
        font-size: 20px;
    }

    .card {
        padding: 15px;
    }

    .card img {
        height: 150px;
    }

    .fasilitas span {
        font-size: 11px;
        padding: 5px 8px;
    }

    .info-lapangan {
        font-size: 11px;
    }

    .btn-nearby {
        font-size: 12px;
    }
}

    </style>
</head>

<body>
<header class="header">
    <?php include 'includes/nav.php'; ?>
</header>
<main>
    <h2>Pilih Venue</h2>
    <div class="filter">Pilih lokasi</div>

    <div class="venue-container">
        <?php foreach ($venue_list as $venue): ?>
        <div class="card">
            <img src="<?php echo htmlspecialchars($venue['gambar']); ?>" 
                 alt="<?php echo htmlspecialchars($venue['nama']); ?>">

            <div class="card-content">
                <h3><?php echo htmlspecialchars($venue['nama']); ?></h3>
                
              <div class="fasilitas">
<?php foreach ($venue['fasilitas'] as $f): ?>
    <span>
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($f); ?>
    </span>
<?php endforeach; ?>
</div>


                <div class="info-lapangan">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars($venue['deskripsi']); ?>
                </div>

                <div class="lihat-jadwal">
                    <a href="jadwal_lapangan2.php?venue_id=<?php echo (int)$venue['id']; ?>" 
                       class="btn-nearby">
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

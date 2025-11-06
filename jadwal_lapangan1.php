<?php
// Contoh data dummy (bisa nanti diambil dari database)
session_start();
include 'config/koneksi.php';
$lapangan = [
    [
        "nama" => "Kelapa Gading",
        "deskripsi" => "Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu.",
        "gambar" => "assets/image/lapangan.png"
    ],
    [
        "nama" => "GOR MINI JATIBARANG",
        "deskripsi" => "Jl. Mayor Dasuki no. 159, Desa jatibarang, Kec. Jatibarang, Kabupaten Indramayu.",
        "gambar" => "assets/image/lapangan.png"
    ],
    [
        "nama" => "ABRAL",
        "deskripsi" => "Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu.",
        "gambar" => "assets/image/lapangan.png"
    ],
    [
        "nama" => "CPO",
        "deskripsi" => "Jl. Raya Panyindangan Wetan, Panyindangan Wetan, Kec. Sindang, Kabupaten Indramayu.",
        "gambar" => "assets/image/lapangan.png"
    ],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/home.css">
    <link rel="stylesheet" href="assets/nav.css">
    <title>Lapangin.Aja | Jadwal Lapangan</title>
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
        }

        .lapangan-container {
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
        }

        .card:hover {
            transform: translateY(-5px);
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
        }

        .lihat-jadwal {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            color: #555;
            font-weight: 600;
            cursor: pointer;
        }

        .lihat-jadwal span {
            margin-left: 8px;
            background: #ddd;
            padding: 5px 10px;
            border-radius: 50%;
            font-weight: bold;
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
    <h2>Jadwal Lapangan</h2>
    <div class="filter">Pilih lokasi</div>

    <div class="lapangan-container">
        <?php foreach ($lapangan as $l): ?>
        <div class="card">
            <img src="<?php echo $l['gambar']; ?>" alt="Lapangan">
            <div class="card-content">
                <h3><?php echo $l['nama']; ?></h3>
                <p><?php echo $l['deskripsi']; ?></p>
                <div class="lihat-jadwal"> 
                    <a href="jadwal_lapangan2.php" class="btn-nearby"> Lihat Jadwal ›</a> 
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

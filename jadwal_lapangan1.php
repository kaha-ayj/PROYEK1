<?php
// Contoh data dummy (bisa nanti diambil dari database)
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
    <title>Lapangin.Aja | Jadwal Lapangan</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom right, #d9e7e9, #a6bcc0);
        }

        /* Header */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.5);
            padding: 15px 40px;
            backdrop-filter: blur(5px);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 300px;
        }

        .logo h1 {
            font-size: 22px;
            color: #1c3366;
        }

        nav {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #6b7a89;
            font-weight: 600;
        }

        nav a:hover {
            color: #1c3366;
        }

        .search {
            background: white;
            border-radius: 20px;
            padding: 5px 10px;
            display: flex;
            align-items: center;
        }

        .search input {
            border: none;
            outline: none;
            padding: 5px;
            font-style: italic;
        }

        .user-icon {
            width: 35px;
            height: 35px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

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

<header>
    <div class="logo">
        <img src="assets/image/logo.png" alt="Logo">
        <h1><span style="color:#1c3366;"></span><span style="color:#4773ff;"></span></h1>
    </div>

    <nav class="navbar">
        <a href="#">Lapangan</a>
        <a href="#">Home</a>
        <a href="#">Message</a>
    </nav>

    <div class="right-section">
        <div class="search">
            <input type="text" placeholder="Cari lapangan">
        </div>
        <div class="user-icon">👤</div>
    </div>
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
                <div class="lihat-jadwal">Lihat Jadwal <span>›</span></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<img src="assets/image/image 4.png" class="footer-icon" alt="Dekorasi">

</body>
</html>

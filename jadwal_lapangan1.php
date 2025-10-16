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

.header {
padding: 15px 0;
box-shadow: 0 2px 10px rgba(0,0,0,0.08);
position: sticky;
top: 1;
border-bottom: 2px solid #e0e0e0; 
z-index: 1000; 
}

.nav { 
    display: flex;
    justify-content: space-between;
    height: 30px; align-items: center; 
}

.logo {
    display: flex; 
    align-items: center;
} 
.logo-atas { 
    height: 60px;
    width: auto;
} 
.logo-atas img { 
    height: 100%;
    width: auto; 
    object-fit: contain; 
}

.nav-links { 
    display: flex;
    gap: 25px;
    align-items: center;
}
.nav-links a { 
    font-family: 'Poppins', sans-serif;
    color: #5d7b87;
    color:linear-gradient (#4C5C5A #A1C2BD);
    text-decoration: none; 
    font-weight: 700;
    transition: color 0.3s;
} 

.nav-links a:hover, .nav-links a.active{
    color: #6d6666; 
}
.btn-profile-img {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    overflow: hidden;
    transition: transform 0.2s ease;
}

.btn-profile-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.btn-profile-img:hover {
    transform: scale(1.05);
}

.right-section {
    border-radius: 10px;
}

.btn-logout { 
    background: #ca4250; 
    color: white !important;
    padding: 8px 10px; 
    border-radius: 5px; 
    transition: background 0.3s;
    font-size: 10px;

} 

.btn-logout:hover {
    background: #000000; }

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
                        <img src="assets/image/profile.png" alt="Profile">
                    </a>
                    <a href="login.php" class="btn-login">Login</a>
                <?php else: ?>
                <a href="login.php" class="btn-logout">Logout</a>
                <?php endif; ?>
            </div>
        </div>
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
                <div class="lihat-jadwal"> 
                    <a href="jadwal_lapangan2.php" class="btn-nearby"> Lihat Jadwal ›</a> 
            </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<img src="assets/image/image 4.png" class="footer-icon" alt="Dekorasi">

</body>
</html>

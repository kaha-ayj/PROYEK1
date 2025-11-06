<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Pemesanan Berhasil</title>
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

        /* ===== NAVBAR ===== */.header {
    padding: 5px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo-atas {
            height: 55px;
            width: auto;
        }

        .logo-atas img {
            height: 100%;
            width: auto;
            object-fit: contain;

        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: #5d7b87;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #6d6666;
        }

        .right-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search {
            display: flex;
            align-items: center;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .search input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            width: 120px;
        }

        .search-icon {
            margin-left: 100px;
        }

        .btn-profile-img {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
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

        .btn-logout {
            background: #ca4250;
            color: white !important;
            padding: 8px 10px;
            border-radius: 5px;
            transition: background 0.3s;
            font-size: 10px;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: #000;
        }

        /* ===== KONTEN UTAMA ===== */
        .container {
            display: flex;
            justify-content: center;
            margin-top: 80px;
        }

        .card {
            background: #eaf1ef;
            width: 75%;
            max-width: 900px;
            padding: 100px 50px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .card img {
            width: 350px;
            border-radius: 10px;
        }

        .text-area {
            flex: 1;
            margin-left: 40px;
        }

        .text-area h2 {
            font-size: 22px;
            color: #222;
            margin-bottom: 20px;
        }

        .btn-detail {
            background: #20b624;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 3px 5px rgba(0,0,0,0.2);
        }

        .btn-detail:hover {
            background: #18a31d;
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
                flex-direction: column;
                text-align: center;
            }
            .text-area {
                margin: 30px 0 0 0;
            }
        }
    </style>
</head>
<body>
<header class="header">
<?php include 'includes/nav.php'; ?>
</header>
    <!-- ===== HALAMAN BERHASIL ===== -->
    <div class="container">
        <div class="card">
            <img src="assets/image/image 46.png" alt="Gambar Pemesanan"> <!-- Kosongkan dulu gambarnya -->
            
            <div class="text-area">
                <h2>Yeayy Pemesanan Lapangan<br>Sudah berhasil!!</h2>
                <button class="btn-detail" onclick="window.location.href='detail_pesanan.php'">Cek Detail Pesanan</button>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER IMAGE ===== -->
    <footer>
        <img src="assets/image/image 4.png" alt="Logo Shuttlecock"> <!-- Kosongkan dulu -->
    </footer>
<?php include 'includes/footer.php'; ?>
</body>
</html>

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

        /* ===== NAVBAR ===== */
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 50px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        nav .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        nav .logo img {
            height: 40px;
        }

        nav .logo span {
            font-size: 20px;
            font-weight: 700;
            color: #1a3365;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        nav ul li {
            font-weight: 500;
            color: #8da5a6;
            transition: 0.3s;
        }

        nav ul li:hover {
            color: #000;
        }

        nav .search {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        nav input[type="text"] {
            border: none;
            border-radius: 20px;
            padding: 6px 12px;
            outline: none;
            width: 150px;
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

    <!-- ===== NAVBAR ===== -->
    <nav>
        <div class="logo">
            <img src="assets/image/logo.png" alt="Logo"> <!-- Kosongkan dulu logo -->
            <span></span>
        </div>

        <ul>
            <li><i>Lapangan</i></li>
            <li><i>Home</i></li>
            <li><i>Message</i></li>
        </ul>

        <div class="search">
            <input type="text" placeholder="Cari lapangan">
            🔍
        </div>
    </nav>

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

</body>
</html>

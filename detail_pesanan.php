<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Detail Pesanan</title>
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

        /* ===== CARD DETAIL PESANAN ===== */
        .container {
            display: flex;
            justify-content: center;
            margin-top: 60px;
        }

        .card {
            background: #eaf1ef;
            width: 70%;
            max-width: 750px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: left;
            margin-bottom: 25px;
            color: #222;
        }

        .detail-box {
            background: #f4f8f7;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .detail-box span {
            font-weight: 600;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            background: #fff;
            padding: 8px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: 0.3s;
        }

        .btn-back:hover {
            background: #dbe8ea;
        }

        footer {
            position: fixed;
            bottom: 10px;
            left: 10px;
        }

        footer img {
            height: 50px;
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

    <!-- ===== DETAIL PESANAN ===== -->
    <div class="container">
        <div class="card">
            <h2>Detail Pesanan</h2>

            <div class="detail-box"><span>Jumlah Lapangan:</span> 1</div>
            <div class="detail-box"><span>Kode Lapangan:</span> 03</div>
            <div class="detail-box"><span>Nama Lapangan:</span> Kelapa Gading</div>
            <div class="detail-box"><span>Hari, Tanggal:</span> Senin, 22 September 2025</div>
            <div class="detail-box"><span>Waktu:</span> 13.00 - 14.00</div>
            <div class="detail-box"><span>Total:</span> Rp. 50.000</div>

            <button class="btn-back" onclick="window.history.back()">Kembali</button>
        </div>
    </div>

    <!-- ===== FOOTER IMAGE ===== -->
    <footer>
        <img src="assets/image/image 4.png" alt="Logo Shuttlecock"> <!-- Kosongkan dulu gambar -->
    </footer>

</body>
</html>

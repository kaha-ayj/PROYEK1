<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Kode Pembayaran</title>
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
            margin-top: 70px;
        }

        .card {
            background: #eaf1ef;
            width: 60%;
            max-width: 600px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            color: #222;
            font-size: 22px;
            margin-bottom: 25px;
        }

        .kode-box {
            background: #fff;
            padding: 25px 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            display: inline-block;
        }

        .kode {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .btn-copy {
            background: #e5e3e3;
            color: #333;
            border: none;
            border-radius: 5px;
            padding: 5px 25px;
            font-style: italic;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 2px 3px rgba(0,0,0,0.2);
            transition: 0.3s;
        }

        .btn-copy:hover {
            background: #dcdcdc;
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

    <!-- ===== KODE PEMBAYARAN ===== -->
    <div class="container">
        <div class="card">
            <h2>Kode Pembayaran</h2>

            <div class="kode-box">
                <?php
                // generate kode random otomatis
                function generateKode($length = 12) {
                    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    $kode = '';
                    for ($i = 0; $i < $length; $i++) {
                        $kode .= $chars[rand(0, strlen($chars) - 1)];
                    }
                    return $kode;
                }
                $kodePembayaran = generateKode();
                echo "<div class='kode' id='kode'>$kodePembayaran</div>";
                ?>
                <button class="btn-copy" onclick="salinKode()">salin kode</button>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER IMAGE ===== -->
    <footer>
        <img src="assets/image/image 4.png" alt="Logo Shuttlecock"> <!-- Kosongkan dulu -->
    </footer>

    <!-- ===== SCRIPT SALIN KODE ===== -->
    <script>
        function salinKode() {
            const kode = document.getElementById('kode').textContent;
            navigator.clipboard.writeText(kode);
            alert('Kode berhasil disalin: ' + kode);
        }
    </script>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Chat Admin</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom right, #eaf2f2, #cfd8dc);
        }

        /* Navbar */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #eaf2f2;
            padding: 10px 50px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            height: 40px;
        }

        .logo h2 {
            color: #1e2f97;
            font-style: italic;
            margin: 0;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .menu a {
            text-decoration: none;
            color: #6b7a8f;
            font-weight: 600;
            font-style: italic;
        }

        .menu a.active {
            color: #1e2f97;
            text-decoration: underline;
        }

        .search {
            background: white;
            padding: 5px 10px;
            border-radius: 15px;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
        }

        .search input {
            border: none;
            outline: none;
        }

        .icon {
            background: #fff;
            border-radius: 50%;
            padding: 8px;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }

        /* Container utama */
        .content {
            background: #e8f4f3;
            width: 80%;
            margin: 50px auto;
            padding: 50px;
            border-radius: 15px;
            min-height: 400px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .content h2 {
            font-weight: 700;
            text-align: left;
            color: #000;
            text-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }

        /* Chat Box */
        .chat-box {
            background: #f5fafa;
            margin-top: 30px;
            padding: 15px 20px;
            border-radius: 8px;
            width: 40%;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }

        .chat-box img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .chat-box .info {
            font-size: 14px;
        }

        .chat-box .info .name {
            font-weight: bold;
        }

        .chat-box .info .message {
            color: #555;
            font-style: italic;
        }

        footer img {
            position: fixed;
            bottom: 15px;
            left: 15px;
            width: 70px;
            opacity: 0.7;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <img src="assets/image/logo.png" alt="Logo">
            <h2></h2>
        </div>
        <ul>
    <div class="menu">
            <a href="#">Lapangan</a>
            <a href="#">Home</a>
            <a href="#" class="active">Message</a>
            <div class="search">
                <input type="text" placeholder="Cari lapangan">
                🔍
            </div>
            <div class="icon">👤</div>
        </div>
    </div>
        </ul>

    <!-- Konten -->
    <div class="content">
        <h2>Chat dengan Admin</h2>

        <div class="chat-box">
            <img src="assets/image/image 49.png" alt="Admin">
            <div class="info">
                <div class="name">Reinayla</div>
                <div class="message">Ada kesulitan? chat dengan admin</div>
            </div>
        </div>
    </div>

    <!-- Logo bawah -->
    <footer>
        <img src="assets/image/image 4.png" alt="Shuttlecock">
    </footer>

</body>
</html>

<?php
include("config/koneksi.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT password FROM pengguna WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo "<script>alert('Email sudah terdaftar!'); window.location='signup.php';</script>";
        exit;
    }

    // Insert data baru
    $stmt = $conn->prepare("INSERT INTO pengguna (nama, email, password, role) VALUES (?, ?, ?, 'penyewa')");
    $stmt->bind_param("sss", $nama, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        echo "<script>alert('Terjadi kesalahan saat mendaftar.'); window.location='signup.php';</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Sign Up</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
    body {
        background: linear-gradient(to bottom right, #E7F2EF 50%, #708993 -50%);
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    .main-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 100px);
        padding: 1px;
        position: relative; 
    }

    .welcome-content {
        text-align: center;
        margin-bottom: 1px;
        margin-top: -20px;
    }

    .right-panel {
        background-color: var(--white);
        border-radius: var(--border-radius);
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        margin-bottom: 300px;
        z-index: 2; 
    }


    .player-image {
        width: 300px;
        max-width: 90%;
        height: auto;
        position: absolute;
        right: 60px;   
        bottom: 50px;  
    }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <img src="assets/image/logo.png" alt="Lapangin.Aja Logo">
        </div>
        <div class="buttons">
            <a href="login.php"><button class="login">LOGIN</button></a>
            <a href="signup.php"><button class="signup">SIGN UP</button></a>
        </div>
    </header>

    <div class="main-container">
        <div class="welcome-content">
            <h1 class="welcome-title">Selamat Datang</h1>
            <p class="welcome-subtitle">Daftarkan akun anda untuk mulai menggunakan layanan kami</p>
        </div>

        <div class="form-container">
            <h2 class="form-title">Buat Akun</h2>
            <img src="assets/image/google.png" alt="Google logo" class="google-logo">
            <p class="google-text">atau gunakan akun Google anda untuk registrasi</p>

            <form class="registration-form" method="POST" action="">
                <div class="form-group">
                    <input type="text" name="nama" class="form-input" placeholder="Nama" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                </div>
                <div class="submit-area">
                    <button type="submit" class="submit-btn">Daftar</button>
                    <p>Sudah punya akun? <a href="login.php" class="signin-link">Sign in</a></p>
                </div>
            </form>
        </div>

        <!-- gambar sekarang di kanan -->
        <img src="assets/image/image2.png" alt="A badminton player celebrating a point" class="player-image">
    </div>
</body>
</html>

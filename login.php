<?php
// PERPANJANG UMUR SESSION 24 JAM
ini_set('session.gc_maxlifetime', 86400); // 24 jam
session_set_cookie_params([
    'lifetime' => 86400,   // 24 jam
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

include "config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT penggunaID, nama, email, password, role, foto 
         FROM pengguna WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Email tidak ditemukan'); window.location='login.php';</script>";
        exit;
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        echo "<script>alert('Password salah'); window.location='login.php';</script>";
        exit;
    }

    // SIMPAN SESSION STABIL
    $_SESSION['user'] = [
        'penggunaID' => $user['penggunaID'],
        'nama'       => $user['nama'],
        'email'      => $user['email'],
        'foto'       => $user['foto'] ?? 'default.png',
        'role'       => $user['role']
    ];
    $_SESSION['penggunaID'] = $user['penggunaID']; // opsional, kalau halaman lain pakai

    // Redirect sesuai role
    if ($user['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: homepage.php");
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Login</title>
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
    }

    .welcome-content {
        text-align: center;
        margin-bottom: 5px;
        margin-top: -10px;
    }

    .right-panel {
        background-color: var(--white);
        border-radius: var(--border-radius);
        padding: 30px;
        width: 100%;
        max-width: 400px;
        margin-bottom: 300px;
    }

    .player-image {
        width: 300px;
        max-width: 90%;
        height: auto;
        position: absolute;
        right: 60px;   
        bottom: 50px;  
    }
    /* Container untuk mengatur posisi gambar */
.hero-image-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* Class khusus untuk gambar pemain */
.img-hero-player {
    max-width: 100%;
    height: auto;
    object-fit: contain;
    /* Memberikan efek bayangan agar gambar terlihat lebih hidup */
    filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));
    /* Animasi halus saat halaman dimuat */
    animation: fadeInUp 1s ease-out;
}

/* Animasi sederhana agar gambar muncul dari bawah */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsif: Jika dibuka di HP, gambar tidak terlalu besar */
@media (max-width: 768px) {
    .hero-image-container {
        order: -1; /* Gambar muncul di atas teks saat di HP */
        margin-bottom: 20px;
    }
    .img-hero-player {
        max-height: 300px;
    }
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
            <h1 class="welcome-title">Selamat Datang Kembali</h1>
            <p class="welcome-subtitle">Masuk ke akun anda untuk melanjutkan</p>
        </div>
            <h3 class="form-title">Masuk</h3>
                <form class="registration-form" method="POST" action="">
                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-input" placeholder="Password" required>
                    </div>
                        <div class="form-container">
                <img src="assets/image/google.png" class="google-logo">
                <p class="google-text">atau gunakan akun google anda untuk login</p>

                    <div class="submit-area">
                        <button type="submit" class="submit-btn">Login</button>
                        <p>Belum punya akun? <a href="signup.php" class="signin-link">Sign up</a></p>
                    </div>
                </form>
            </div>
        </div>

    
    </div>
</body>
</html>
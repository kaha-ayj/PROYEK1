<?php
session_start();
include("config/koneksi.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT penggunaID, nama, email, password, role FROM pengguna WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // Tentukan role
            if ($email === 'admin@lapanginaja.com') {
                $user['role'] = 'admin';
            } else {
                $user['role'] = 'penyewa';
            }

            // Simpan ke session
            $_SESSION['user'] = [
                'id' => $user['penggunaID'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                echo "<script>alert('Login berhasil sebagai admin!'); window.location='admin/admin_dashboard.php';</script>";
            } else {
                echo "<script>alert('Login berhasil!'); window.location='homepage.php';</script>";
            }
            exit;

        } else {
            echo "<script>alert('Password salah!'); window.location='login.php';</script>";
            exit;
        }

    } else {
        echo "<script>alert('Email tidak ditemukan!'); window.location='login.php';</script>";
        exit;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <?php include("includes/header.php"); ?>

    <div class="welcome-content">
        <h1 class="welcome-title">Selamat Datang Kembali</h1>
        <p class="welcome-subtitle">Masuk ke akun anda untuk melanjutkan</p>
    </div>

    <img src="assets/image/image2.png" alt="A badminton player celebrating a point" class="player-image">

    <div class="right-panel">
        <nav class="auth-nav">
            <a href="#" class="auth-btn login-btn active">LOGIN</a>
            <a href="signup.php" class="auth-btn signup-btn">SIGN UP</a>
        </nav>

        <div class="form-container">
            <h2 class="form-title">Masuk</h2>
            <img src="assets/image/google.png" alt="Google logo" class="google-logo">
            <p class="google-text">atau gunakan akun google anda untuk login</p>

            <form class="registration-form" method="POST" action="">
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                </div>
                <div class="submit-area">
                    <button type="submit" class="submit-btn">Login</button>
                    <p>Belum punya akun? <a href="signup.php" class="signin-link">Sign up</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

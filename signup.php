<?php
include("config/koneksi.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // cek email
   // cek email
$stmt = $conn->prepare("SELECT password FROM pengguna WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // cek apakah password lama belum di-hash (tidak mengandung '$2y$')
    if (strpos($row['password'], '$2y$') === false) {
        // update password lama jadi hash baru
        $stmt_update = $conn->prepare("UPDATE pengguna SET password=? WHERE email=?");
        $stmt_update->bind_param("ss", $hashed_password, $email);
        $stmt_update->execute();
        $stmt_update->close();
        header("Location: login.php");
        exit;
    } else {
        echo "<script>alert('Email sudah terdaftar!'); window.location='login.php';</script>";
        exit;
    }
}

    // insert data
    $stmt = $conn->prepare("INSERT INTO pengguna (nama, email, password, role) VALUES (?, ?, ?, 'penyewa')");
$stmt->bind_param("sss", $nama, $email, $hashed_password);

    if ($stmt->execute()) {
        // langsung ke login tanpa alert
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
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
</head>

<body>
    <?php include("includes/header.php"); ?>

    <div class="welcome-content">
        <h1 class="welcome-title">Selamat Datang</h1>
        <p class="welcome-subtitle">Daftarkan Akun anda dan Mulai menggunakan Layanan Kami</p>
    </div>

    <img src="assets/image/image2.png" alt="A badminton player celebrating a point" class="player-image">

    <div class="right-panel">
        <nav class="auth-nav">
            <a href="login.php" class="auth-btn login-btn">LOGIN</a>
            <a href="#" class="auth-btn signup-btn active">SIGN UP</a>
        </nav>

        <div class="form-container">
            <h2 class="form-title">Buat Akun</h2>
            <img src="assets/image/google.png" alt="Google logo" class="google-logo">
            <p class="google-text">atau gunakan akun google anda untuk registrasi</p>

            <!-- form dihubungkan dengan PHP di atas -->
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
    </div>
</body>
</html>

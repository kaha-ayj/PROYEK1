<?php include("includes/header.php"); ?>
<!DOCTYPE html>
<html lang="is">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.Aja | Sign Up</title>
    <link rel="stylesheet" href= "assets/style.css">
</head>
<body>
    <div class="container">
        <div class="left-side">
            <h2> Selamat Datang </h2>
            <p> Daftarkan akun anda dan mulai menggunakan layanan kami </p>
            <img src="assets/image/image2.png" alt="pemain badminton">
        </div>
        <div class="right-side">
            <div class="top-buttons">
                <a href="login.php"> <button class="btn"> LOGIN </button></a>
                <a href="signup.up"> <button class="btn active"> SIGN UP </button></a>
            </div>
            <form action="signup_proses.php" method="post">
                <input type="text" name="nama" placeholder="Nama" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="submit"> Sign Up </button>
            </form>
        </div>
    </div>
</body>
</html>
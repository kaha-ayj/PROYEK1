<?php include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangin.aja | Login </title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="container">
        <div class="login-box">
            <h2> LOGIN </h2>
            <form action="/backend/login_proses.php" method="post">
                <input type="text" name="username" placeholder="Nama" required>
                <input type="password" name="password" placeholder="Password" required>

                <p class="signup-link"> Belum punya akun?
                <a href="signup_proses.php"> Daftar </a>
                <img src="assets/image/google.png" alt="google" class="google-icon"
                </p>

                <button type="submit" class="signin-btn"> Sign In </button>
            </form>
        </div>
        <div class="right-side">
            <p> Daftarkan akun anda dan mulai <br> menggunakan layanan kami</p>
            <img src="assets/image/image2.png" alt="pemain badminton">
        </div>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
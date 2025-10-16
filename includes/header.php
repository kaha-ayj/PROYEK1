<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<header class="header">
        <link rel="stylesheet" href="assets/home.css">
<div class="container">
    <div class="nav">
    <div class="logo">
        <div class="logo-atas">
        <img src="assets/image/logo.png" alt="logo lapangin.aja">
        </div>
    </div>

    <div class="nav-links">
        <a href="jadwal_lapangan1.php">Lapangan</a>
        <a href="homepage.php" class="active">Home</a>
        <a href="messege1.php">Messege</a>

        <div class="right-section">
        <div class="search">
            <input type="text" placeholder="Cari lapangan...">
            <span class="search-icon">🔍</span>
        </div>
        </div>

        <?php if (isset($_SESSION['user'])): ?>
        <a href="#" class="btn-profile-img">
            <img src="assets/image/profile.png" alt="Profile">
        </a>
        <a href="login.php" class="btn-logout">Logout</a>
        <?php else: ?>
        <a href="login.php" class="btn-login">Login</a>
        <?php endif; ?>

    </div> 
    </div> 
</div> 
    
</body>
</html>
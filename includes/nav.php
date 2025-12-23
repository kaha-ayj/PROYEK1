<link rel="stylesheet" href="assets/nav.css">

<?php
// Konfigurasi Foto Profil
$upload_path = 'assets/profile/';
$default_profile = 'assets/image/profile.png';
$user_photo = $default_profile;

if (isset($_SESSION['user']['foto']) && !empty($_SESSION['user']['foto'])) {
    $file_path = $upload_path . $_SESSION['user']['foto'];
    if (file_exists($file_path)) {
        $user_photo = $file_path;
    }
}
?>

<header>
    <div class="container">
        <div class="nav">
            <div class="logo">
                <div class="logo-atas">
                    <a href="homepage.php">
                        <img src="assets/image/logo.png" alt="logo" title="Kembali ke Beranda">
                    </a>
                </div>
            </div>

            <div class="nav-links">
                <a href="jadwal_lapangan1.php"
                    class="<?= basename($_SERVER['PHP_SELF']) == 'jadwal_lapangan1.php' ? 'active' : '' ?>">Lapangan</a>
                <a href="homepage.php"
                    class="<?= basename($_SERVER['PHP_SELF']) == 'homepage.php' ? 'active' : '' ?>">Home</a>
                <a href="messege1.php"
                    class="<?= basename($_SERVER['PHP_SELF']) == 'messege1.php' ? 'active' : '' ?>">Message</a>

                <div class="right-section">
                    <form action="homepage.php" method="GET" class="search">
                        <input type="text" name="search" placeholder="Cari lapangan..." autocomplete="off"
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button type="submit" class="search-icon">🔍</button>
                    </form>
                </div>

                <?php if (isset($_SESSION['user'])): ?>
                    <div class="user-menu">
                        <a href="profile.php" class="btn-profile-img">
                            <img src="<?= $user_photo ?>?t=<?= time(); ?>" alt="Profile"
                                style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        </a>
                        <div class="hamburger" id="hamburgerMenu">
                            <span></span><span></span><span></span>
                        </div>
                        <div class="dropdown" id="dropdownMenu">
                            <div class="dropdown-header">
                                <img src="<?= $user_photo ?>?t=<?= time(); ?>" alt="Profile"
                                    style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                <span
                                    class="dropdown-username"><?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Pengguna'); ?></span>
                            </div>
                            <hr>
                            <a href="profile.php">Profil</a>
                            <a href="logout.php" onclick="return confirm('Yakin mau logout?')">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<script>
    const hamburger = document.getElementById('hamburgerMenu');
    const dropdown = document.getElementById('dropdownMenu');

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            dropdown.classList.toggle('show');
        });
    }

    document.addEventListener('click', (e) => {
        if (hamburger && dropdown) {
            if (!hamburger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        }
    });
</script>
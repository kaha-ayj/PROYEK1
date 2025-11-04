<link rel="stylesheet" href="assets/nav.css">

<header class="header">
    <div class="container">
        <div class="nav">
            <div class="logo">
                <div class="logo-atas">
                    <a href="homepage.php">
                        <img src="assets/image/logo.png" alt="logo lapangin.aja" title="Kembali ke Beranda">
                    </a>
                </div>
            </div>

            <div class="nav-links">
                <a href="jadwal_lapangan1.php" class="<?= basename($_SERVER['PHP_SELF']) == 'jadwal_lapangan1.php' ? 'active' : '' ?>">Lapangan</a>
                <a href="homepage.php" class="<?= basename($_SERVER['PHP_SELF']) == 'homepage.php' ? 'active' : '' ?>">Home</a>
                <a href="messege1.php" class="<?= basename($_SERVER['PHP_SELF']) == 'messege1.php' ? 'active' : '' ?>">Messege</a>

                <div class="right-section">
                    <form action="search.php" method="GET" class="search">
                        <input type="text" name="q" placeholder="Cari lapangan..." autocomplete="off">
                        <button type="submit" class="search-icon" title="Cari">🔍</button>
                    </form>
                </div>

                <?php if (isset($_SESSION['user'])): ?>
                    <div class="user-menu">
                        <!-- Profil di navbar -->
                        <a href="profile.php" class="btn-profile-img" title="Profil">
                            <img src="assets/image/profile.png" alt="Profile">
                        </a>

                        <!-- Tombol hamburger -->
                        <div class="hamburger" id="hamburgerMenu">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                        <!-- Dropdown menu -->
                        <div class="dropdown" id="dropdownMenu">
                            <div class="dropdown-header">
                                <img src="assets/image/profile.png" alt="Profile" class="dropdown-profile">
                                <span class="dropdown-username"><?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Pengguna'); ?></span>
                            </div>
                            <hr>
                            <a href="profile.php">Profil</a>
                            <a href="settings.php">Pengaturan</a>
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

    hamburger.addEventListener('click', () => {
        dropdown.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!hamburger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
</script>

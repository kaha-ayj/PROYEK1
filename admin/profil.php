<?php
session_start();
require_once '../config/koneksi.php';

// Cek sesi login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Gunakan 'penggunaID' sesuai struktur database Anda
$userID = $_SESSION['user']['penggunaID'] ?? $_SESSION['user']['id'] ?? 0;

if ($userID == 0) {
    header("Location: ../login.php");
    exit;
}

// Ambil data terbaru dari database
$stmt = mysqli_prepare($conn, "SELECT * FROM pengguna WHERE penggunaID = ?");
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$adminData = mysqli_fetch_assoc($result);

if (!$adminData) {
    die("Error: Data pengguna tidak ditemukan di database.");
}

// Tentukan Mode: 'view' atau 'edit'
$mode = isset($_GET['action']) && $_GET['action'] == 'edit' ? 'edit' : 'view';

// --- LOGIKA UPDATE PROFIL (Hanya dijalankan saat POST) ---
$sukses_msg = "";
$error_msg = "";

if (isset($_POST['update_profile'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password_baru = $_POST['password_baru'];
    $role = $_POST['role']; 
    
    // 1. Cek Upload Foto
    $foto_nama = $adminData['foto'] ?? 'default.png'; 
    
    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != "") {
        $foto_file = $_FILES['foto'];
        $ext = pathinfo($foto_file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png'];
        
        if (in_array(strtolower($ext), $allowed)) {
            $foto_nama = "admin_" . $userID . "_" . time() . "." . $ext;
            $target = "../assets/image/" . $foto_nama;
            
            if (move_uploaded_file($foto_file['tmp_name'], $target)) {
                // (Opsional) Hapus foto lama
                if ($adminData['foto'] != 'default.png' && !empty($adminData['foto']) && file_exists("../assets/image/" . $adminData['foto'])) {
                    unlink("../assets/image/" . $adminData['foto']);
                }
            } else {
                $error_msg = "Gagal mengupload gambar.";
            }
        } else {
            $error_msg = "Format gambar harus JPG atau PNG.";
        }
    }

    // 2. Update Database
    if (empty($error_msg)) {
        if (!empty($password_baru)) {
            $password_hash = md5($password_baru); 
            $query_update = "UPDATE pengguna SET nama=?, email=?, password=?, foto=?, role=? WHERE penggunaID=?";
            $stmt_update = mysqli_prepare($conn, $query_update);
            mysqli_stmt_bind_param($stmt_update, "sssssi", $nama, $email, $password_hash, $foto_nama, $role, $userID);
        } else {
            $query_update = "UPDATE pengguna SET nama=?, email=?, foto=?, role=? WHERE penggunaID=?";
            $stmt_update = mysqli_prepare($conn, $query_update);
            mysqli_stmt_bind_param($stmt_update, "ssssi", $nama, $email, $foto_nama, $role, $userID);
        }

        if (mysqli_stmt_execute($stmt_update)) {
            $sukses_msg = "Profil berhasil diperbarui!";
            // Update session
            $_SESSION['user']['nama'] = $nama; 
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['role'] = $role;
            
            // Refresh data dan KEMBALI KE MODE VIEW
            $stmt = mysqli_prepare($conn, "SELECT * FROM pengguna WHERE penggunaID = ?");
            mysqli_stmt_bind_param($stmt, "i", $userID);
            mysqli_stmt_execute($stmt);
            $adminData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            $mode = 'view'; // Reset ke mode view setelah simpan
        } else {
            $error_msg = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Lapangin.Aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-light: #F0F4F8; --bg-sidebar: #DDE8F3; --text-dark: #2C3E50;
            --card-white: #FFFFFF; --header-blue: #AED6F1; --primary: #3498DB;
        }
        body {
            font-family: 'Poppins', sans-serif; margin: 0; display: flex;
            background-color: var(--bg-light); color: var(--text-dark); height: 100vh;
        }
        /* Sidebar Styles (Tetap sama) */
        .sidebar {
            width: 250px; background-color: var(--bg-sidebar); padding: 30px;
            display: flex; flex-direction: column; flex-shrink: 0;
        }
        .sidebar .logo { font-size: 24px; font-weight: 700; margin-bottom: 40px; }
        .sidebar nav a {
            display: block; text-decoration: none; color: var(--text-dark);
            font-size: 18px; font-weight: 600; margin-bottom: 25px; opacity: 0.7;
        }
        .sidebar nav a:hover, .sidebar nav a.active { opacity: 1; }
        .sidebar .profile {
            margin-top: auto; display: flex; align-items: center; font-weight: 600;
        }
        .sidebar .profile-icon {
            width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 15px; background: #ccc;
        }
        
        .main-content { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 32px; margin: 0; }

        /* Profile Card Styles */
        .profile-container {
            background-color: var(--card-white); border-radius: 15px; padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); max-width: 800px;
        }
        .profile-grid { display: grid; grid-template-columns: 200px 1fr; gap: 40px; }
        
        /* Foto Styles */
        .photo-section { text-align: center; }
        .photo-preview {
            width: 150px; height: 150px; border-radius: 50%; object-fit: cover;
            border: 4px solid var(--header-blue); margin-bottom: 15px;
        }
        .file-upload-label {
            display: inline-block; padding: 8px 15px; background-color: var(--primary);
            color: white; border-radius: 6px; cursor: pointer;
            font-size: 14px; font-weight: 600; transition: background 0.2s;
        }
        .file-upload-label:hover { background-color: #2980B9; }
        input[type="file"] { display: none; }

        /* Data Styles */
        .data-section { display: flex; flex-direction: column; gap: 20px; }
        .data-item { border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .data-label { font-weight: 600; color: #777; font-size: 14px; margin-bottom: 5px; display: block;}
        .data-value { font-weight: 600; color: var(--text-dark); font-size: 18px; }

        /* Input Styles (untuk mode edit) */
        .input-group label {
            display: block; font-weight: 600; margin-bottom: 8px; color: #555;
        }
        .input-group input, .input-group select {
            width: 100%; padding: 12px; border: 1px solid #DDE8F3;
            border-radius: 8px; font-size: 16px; box-sizing: border-box;
        }
        .input-group input:focus, .input-group select:focus { outline: none; border-color: var(--primary); }
        
        /* Buttons */
        .btn-edit {
            background-color: #3498DB; color: white; border: none; text-decoration: none;
            padding: 12px 30px; border-radius: 8px; font-size: 16px; font-weight: 600;
            display: inline-block; cursor: pointer; transition: background 0.2s; text-align: center;
        }
        .btn-edit:hover { background-color: #2980B9; }
        
        .btn-save {
            background-color: #27AE60; color: white; border: none;
            padding: 12px 30px; border-radius: 8px; font-size: 16px; font-weight: 600;
            cursor: pointer; margin-right: 10px;
        }
        .btn-save:hover { background-color: #219150; }

        .btn-cancel {
            background-color: #95A5A6; color: white; border: none; text-decoration: none;
            padding: 12px 30px; border-radius: 8px; font-size: 16px; font-weight: 600;
            cursor: pointer; display: inline-block;
        }
        .btn-cancel:hover { background-color: #7F8C8D; }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background-color: #D4EDDA; color: #155724; }
        .alert-error { background-color: #F8D7DA; color: #721C24; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">Lapangin.Aja</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="order.php">Order</a>
            <a href="jadwal.php">Jadwal</a>
            <a href="lapangan.php">Lapangan</a>
            <a href="profil.php" class="active">Profile</a>
        </nav>
        <div class="profile">
            <?php 
                $fotoSidebar = !empty($adminData['foto']) ? $adminData['foto'] : 'default.png'; 
                $fotoPathSidebar = "../assets/image/" . $fotoSidebar;
                if(!file_exists($fotoPathSidebar)) { $fotoPathSidebar = "../assets/image/default.png"; }
            ?>
            <a href="profil.php" style="display: flex; align-items: center; text-decoration: none; color: inherit; flex-grow: 1;">
                <img src="<?php echo $fotoPathSidebar; ?>" class="profile-icon">
                <span><?php echo htmlspecialchars($adminData['nama']); ?></span>
            </a>
            <a href="logout.php" title="Logout" style="margin-left: auto; color: inherit;">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <div class="header">
            <h1><?php echo ($mode == 'edit') ? 'Edit Profil' : 'Profil Admin'; ?></h1>
        </div>

        <?php if ($sukses_msg): ?>
            <div class="alert alert-success"><?php echo $sukses_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="profile-container">
            
            <?php if ($mode == 'view'): ?>
                <div class="profile-grid">
                    <div class="photo-section">
                        <?php 
                            $fotoPath = "../assets/image/" . ($adminData['foto'] ?? 'default.png');
                            if (!file_exists($fotoPath) || empty($adminData['foto'])) { $fotoPath = "../assets/image/default.png"; }
                        ?>
                        <img src="<?php echo $fotoPath; ?>" alt="Foto Profil" class="photo-preview">
                    </div>
                    
                    <div class="data-section">
                        <div class="data-item">
                            <span class="data-label">Nama Lengkap</span>
                            <span class="data-value"><?php echo htmlspecialchars($adminData['nama']); ?></span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Email</span>
                            <span class="data-value"><?php echo htmlspecialchars($adminData['email']); ?></span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Role</span>
                            <span class="data-value" style="text-transform: capitalize;"><?php echo htmlspecialchars($adminData['role']); ?></span>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <a href="profil.php?action=edit" class="btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i> Edit Profil
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <form action="profil.php?action=edit" method="POST" enctype="multipart/form-data" class="profile-grid profile-form">
                    
                    <div class="photo-section">
                        <?php 
                            $fotoPath = "../assets/image/" . ($adminData['foto'] ?? 'default.png');
                            if (!file_exists($fotoPath) || empty($adminData['foto'])) { $fotoPath = "../assets/image/default.png"; }
                        ?>
                        <img src="<?php echo $fotoPath; ?>" alt="Foto Profil" class="photo-preview" id="previewImg">
                        <label for="fotoInput" class="file-upload-label">
                            <i class="fa-solid fa-camera"></i> Ganti Foto
                        </label>
                        <input type="file" name="foto" id="fotoInput" accept="image/*" onchange="previewFile()">
                    </div>

                    <div class="data-section">
                        <div class="input-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" value="<?php echo htmlspecialchars($adminData['nama']); ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($adminData['email']); ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Password Baru <small>(Kosongkan jika tidak ingin mengganti)</small></label>
                            <input type="password" name="password_baru" placeholder="Masukkan password baru...">
                        </div>

                        <div class="input-group">
                            <label>Role</label>
                            <select name="role" required>
                                <option value="admin" <?php echo ($adminData['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="penyewa" <?php echo ($adminData['role'] == 'penyewa') ? 'selected' : ''; ?>>Penyewa</option>
                            </select>
                        </div>

                        <div style="margin-top: 20px;">
                            <button type="submit" name="update_profile" class="btn-save">Simpan Perubahan</button>
                            <a href="profil.php" class="btn-cancel">Batal</a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </main>

    <script>
        function previewFile() {
            const preview = document.getElementById('previewImg');
            const file = document.querySelector('input[type=file]').files[0];
            const reader = new FileReader();

            reader.addEventListener("load", function () {
                preview.src = reader.result;
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
<?php
mysqli_close($conn);
?>
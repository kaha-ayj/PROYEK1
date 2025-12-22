<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Cek status untuk notifikasi
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['penggunaID'];
    $nama    = trim($_POST['nama']);
    $fotoBaru = $_SESSION['user']['foto'];

    if ($nama === '') {
        die('Nama tidak boleh kosong');
    }

    if (!empty($_FILES['foto']['name'])) {
        $file = $_FILES['foto'];
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $path = 'assets/profile/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $path)) {
                if ($fotoBaru && $fotoBaru !== 'default.png' && file_exists('assets/profile/' . $fotoBaru)) {
                    @unlink('assets/profile/' . $fotoBaru);
                }
                $fotoBaru = $filename;
            }
        }
    }

    $stmt = $conn->prepare("UPDATE pengguna SET nama=?, foto=? WHERE penggunaID=?");
    $stmt->bind_param("ssi", $nama, $fotoBaru, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['user']['nama'] = $nama;
        $_SESSION['user']['foto'] = $fotoBaru;
        // Redirect dengan parameter sukses
        header("Location: profile.php?status=success");
        exit;
    }
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --success-color: #10b981;
            --bg-color: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container & Back Button */
        .wrapper {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 15px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .profile-card {
            background: #fff;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            position: relative;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--success-color);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.5s ease forwards, fadeOut 0.5s ease 3s forwards;
            z-index: 1000;
        }

        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; visibility: hidden; } }

        /* Avatar */
        .avatar-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 25px;
        }

        .avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f0f4ff;
            cursor: pointer;
        }

        .cam-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary-color);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff;
            font-size: 14px;
        }

        /* Form */
        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-left: 5px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            box-sizing: border-box;
            margin-top: 5px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }

        #foto { display: none; }
    </style>
</head>
<body>

<?php if ($status === 'success'): ?>
    <div class="toast" id="toast">
        ✅ Profil berhasil diperbarui!
    </div>
<?php endif; ?>

<div class="wrapper">
    <a href="homepage.php" class="back-link">
        ← Kembali ke Beranda
    </a>

    <div class="profile-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="avatar-wrapper">
                <label for="foto">
                    <img id="preview" class="avatar" 
                         src="assets/profile/<?= htmlspecialchars($user['foto']); ?>">
                    <div class="cam-icon">📷</div>
                </label>
                <input type="file" name="foto" id="foto" accept="image/*">
            </div>

            <div class="form-group">
                <label>Nama Pengguna</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email (Permanen)</label>
                <input type="text" value="<?= htmlspecialchars($user['email']); ?>" disabled 
                       style="background: #f1f5f9; color: #94a3b8;">
            </div>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    // Preview Gambar
    document.getElementById('foto').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            document.getElementById('preview').src = URL.createObjectURL(this.files[0]);
        }
    });

    // Hilangkan Toast setelah 3 detik
    setTimeout(() => {
        const toast = document.getElementById('toast');
        if(toast) toast.style.display = 'none';
    }, 3500);
</script>

</body>
</html>
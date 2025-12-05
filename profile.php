<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$id"));
?>

<!DOCTYPE html>
<html>
<head>
<title>Profil Saya</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">

<div class="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-md">
    <h2 class="text-2xl font-bold mb-6">Profil Pengguna</h2>

    <form action="update_profile.php" method="POST" enctype="multipart/form-data">

        <div class="flex items-center gap-6 mb-6">
            <img src="uploads/profile/<?= $data['photo'] ?: 'default.png' ?>" 
                 class="w-24 h-24 rounded-full object-cover border" id="previewImage">

            <div>
                <label class="font-semibold">Ubah Foto</label>
                <input type="file" name="photo" accept="image/*"
                       class="mt-2 border p-2 rounded-md" onchange="preview(event)">
            </div>
        </div>

        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="font-semibold">Nama</label>
                <input type="text" name="name" value="<?= $data['name'] ?>" 
                       class="w-full border p-3 rounded-md mt-1" required>
            </div>

            <div>
                <label class="font-semibold">Email</label>
                <input type="email" name="email" value="<?= $data['email'] ?>" 
                       class="w-full border p-3 rounded-md mt-1" required>
            </div>

            <div>
                <label class="font-semibold">Nomor HP</label>
                <input type="text" name="phone" value="<?= $data['phone'] ?>" 
                       class="w-full border p-3 rounded-md mt-1">
            </div>
        </div>

        <button type="submit" 
                class="mt-6 bg-green-500 text-white px-5 py-3 rounded-xl hover:bg-green-600">
            Simpan Perubahan
        </button>
    </form>
</div>

<script>
    function preview(event) {
        const img = document.getElementById('previewImage');
        img.src = URL.createObjectURL(event.target.files[0]);
    }
</script>

</body>
</html>
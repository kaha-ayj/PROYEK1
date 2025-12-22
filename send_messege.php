<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json; charset=utf-8');
session_start();
include(__DIR__ . "/../../config/koneksi.php");
// SET HEADER JSON
header('Content-Type: application/json');

// CEK LOGIN DENGAN FLEKSIBEL
$isLoggedIn = false;
$penggunaID = null;
$userName = 'Pengguna';

// Cek semua kemungkinan session keys
if (isset($_SESSION['penggunaID'])) {
    $penggunaID = $_SESSION['penggunaID'];
    $isLoggedIn = true;
} elseif (isset($_SESSION['user']['id'])) {
    $penggunaID = $_SESSION['user']['id'];
    $isLoggedIn = true;
    $_SESSION['penggunaID'] = $penggunaID;
}

// Ambil nama user
if (isset($_SESSION['nama'])) {
    $userName = $_SESSION['nama'];
} elseif (isset($_SESSION['user']['nama'])) {
    $userName = $_SESSION['user']['nama'];
} elseif (isset($_SESSION['username'])) {
    $userName = $_SESSION['username'];
}

// Jika belum login
if (!$isLoggedIn || $penggunaID === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit;
}

require_once 'config/koneksi.php';

// Cek metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid'
    ]);
    exit;
}

// Ambil data POST (dukung form data dan json)
$message = '';
if (isset($_POST['message'])) {
    $message = trim($_POST['message']);
} else {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = isset($input['message']) ? trim($input['message']) : '';
}

if (empty($message)) {
    echo json_encode([
        'success' => false,
        'message' => 'Pesan tidak boleh kosong'
    ]);
    exit;
}

try {
    // 1. Simpan pesan user
    $created_at = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO chat (penggunaID, sender, message, created_at) VALUES (?, 'user', ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("iss", $penggunaID, $message, $created_at);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan pesan: " . $stmt->error);
    }
    
    $message_id = $stmt->insert_id;
    $stmt->close();
    
    // 2. Cek apakah ini pesan pertama user
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM chat WHERE penggunaID = ? AND sender = 'user'");
    $check_stmt->bind_param("i", $penggunaID);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    $check_stmt->close();
    
    $bot_reply = null;
    
    // 3. Jika pesan pertama, kirim balasan bot
    if ($row['count'] == 1) {
        $bot_message = "👋 Halo $userName! Selamat datang di Lapangin.Aja!\n\nSaya adalah bot otomatis. Admin akan merespons pesan Anda dalam 1-5 menit.\n\nUntuk bantuan cepat, Anda bisa menanyakan:\n• 🏸 Info lapangan & harga\n• 📅 Jadwal ketersediaan\n• 💰 Promo & diskon\n• 📍 Lokasi & fasilitas";
        
        $bot_stmt = $conn->prepare("INSERT INTO chat (penggunaID, sender, message, created_at) VALUES (?, 'bot', ?, ?)");
        $bot_stmt->bind_param("iss", $penggunaID, $bot_message, $created_at);
        
        if ($bot_stmt->execute()) {
            $bot_reply = $bot_message;
        }
        $bot_stmt->close();
    }
    
    // 4. Update waktu terakhir user
    $update_stmt = $conn->prepare("UPDATE pengguna SET last_chat = ? WHERE penggunaID = ?");
    $update_stmt->bind_param("si", $created_at, $penggunaID);
    $update_stmt->execute();
    $update_stmt->close();
    
    ob_clean(); // Bersihkan buffer
echo json_encode($response);
    // Response sukses
    echo json_encode([
        'success' => true,
        'message' => 'Pesan berhasil dikirim',
        'message_id' => $message_id,
        'bot_reply' => $bot_reply,
        'timestamp' => date('H:i'),
        'user_name' => $userName
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log("Chat Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
    ]);
}

if (isset($conn)) {
    $conn->close();
}
exit;
?>
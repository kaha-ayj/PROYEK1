<?php
// admin/api/user_send.php
header('Content-Type: application/json');
session_start();

// Allow CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

// 1. Ambil input JSON di awal agar bisa digunakan untuk user_id maupun message
$input = json_decode(file_get_contents('php://input'), true);

// 2. FIX: Ambil user ID sesuai struktur login baru ['user']['penggunaID']
$user_id = 0;
if (isset($_SESSION['user']['penggunaID'])) {
    $user_id = $_SESSION['user']['penggunaID'];
} elseif (isset($_SESSION['penggunaID'])) {
    $user_id = $_SESSION['penggunaID'];
} elseif (isset($input['user_id'])) {
    $user_id = $input['user_id'];
} elseif (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
}

if ($user_id == 0) {
    echo json_encode(['success' => false, 'error' => 'User ID tidak valid atau belum login']);
    exit();
}

// 3. Get message dari JSON atau POST
$message = $input['message'] ?? $_POST['message'] ?? '';

if (empty(trim($message))) {
    echo json_encode(['success' => false, 'error' => 'Pesan kosong']);
    exit();
}

// Database connection (Gunakan config/koneksi.php jika sudah ada agar lebih rapi)
// include __DIR__ . '/../../config/koneksi.php'; 
// Jika file koneksi.php sudah ada, hapus blok mysqli di bawah dan gunakan $conn dari include
$conn = null;
try {
    $conn = new mysqli('localhost', 'root', '', 'proyek_1'); // Pastikan nama DB benar 'proyek_1' atau 'lapangin_aja'
    if ($conn->connect_error) {
        throw new Exception("Koneksi gagal: " . $conn->connect_error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error', 'debug' => $e->getMessage()]);
    exit();
}

try {
    // Save user message
    $stmt = $conn->prepare("INSERT INTO chat (penggunaID, message, sender, created_at) VALUES (?, ?, 'user', NOW())");
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
    $message_id = $stmt->insert_id;

    // Generate bot response
    $bot_response = generateBotResponse($message);

    // Save bot response
    $bot_stmt = $conn->prepare("INSERT INTO chat (penggunaID, message, sender, created_at) VALUES (?, ?, 'bot', NOW())");
    $bot_stmt->bind_param("is", $user_id, $bot_response);
    $bot_stmt->execute();

    echo json_encode([
        'success' => true,
        'message_id' => $message_id,
        'bot_response' => $bot_response
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Gagal menyimpan pesan', 'debug' => $e->getMessage()]);
}

function generateBotResponse($message) {
    $msg = strtolower($message);
    
    $responses = [
        'harga'   => "🏸 **Harga Lapangan**:\n• Futsal: Rp 150.000/jam\n• Basket: Rp 120.000/jam\n• Voli: Rp 100.000/jam\n\n💰 **Promo**: Weekdays diskon 20%, member free booking 1x/bulan!",
        'jadwal'  => "📅 **Cek Jadwal**:\nSilakan buka menu 'Jadwal' untuk melihat ketersediaan lapangan real-time!",
        'booking' => "✅ **Booking Lapangan**:\n1. Buka menu 'Booking'\n2. Pilih jenis lapangan\n3. Pilih tanggal & jam\n4. Bayar online",
        'lokasi'  => "📍 **Lokasi Lapangin.Aja**:\nJl. Olahraga No. 123, Kota Anda\n🕐 Buka: 08:00 - 22:00 (Setiap Hari)",
        'promo'   => "🎉 **PROMO TERBARU**:\n1. Weekdays diskon 20%\n2. Paket 5x booking = free 1x",
        'default' => "Halo! Terima kasih telah menghubungi Lapangin.Aja! 😊\n\nAda yang bisa saya bantu terkait info harga, jadwal, atau booking?"
    ];
    
    foreach ($responses as $keyword => $response) {
        if (strpos($msg, $keyword) !== false) {
            return $response;
        }
    }
    
    return $responses['default'];
}
?>
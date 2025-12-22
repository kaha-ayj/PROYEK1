<?php
// admin/api/user_send.php
header('Content-Type: application/json');
session_start();

// Allow CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

// Get user ID from session or request
$user_id = 0;
if (isset($_SESSION['penggunaID'])) {
    $user_id = $_SESSION['penggunaID'];
} elseif (isset($_SESSION['user']['id'])) {
    $user_id = $_SESSION['user']['id'];
} elseif (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
} else {
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $input['user_id'] ?? 0;
}

if ($user_id == 0) {
    echo json_encode(['success' => false, 'error' => 'User ID tidak valid']);
    exit();
}

// Get message
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? $_POST['message'] ?? '';

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Pesan kosong']);
    exit();
}

// Database connection
$conn = null;
try {
    $conn = new mysqli('localhost', 'root', '', 'lapangin_aja');
    if ($conn->connect_error) {
        throw new Exception("Koneksi gagal: " . $conn->connect_error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error', 'debug' => $e->getMessage()]);
    exit();
}

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

function generateBotResponse($message) {
    $msg = strtolower($message);
    
    $responses = [
        'harga' => "🏸 **Harga Lapangan**:\n• Futsal: Rp 150.000/jam\n• Basket: Rp 120.000/jam\n• Voli: Rp 100.000/jam\n\n💰 **Promo**: Weekdays diskon 20%, member free booking 1x/bulan!",
        'jadwal' => "📅 **Cek Jadwal**:\nSilakan buka menu 'Jadwal' untuk melihat ketersediaan lapangan real-time!\n\nAtau beri tahu tanggal yang Anda inginkan, saya bantu cek.",
        'booking' => "✅ **Booking Lapangan**:\n1. Buka menu 'Booking'\n2. Pilih jenis lapangan\n3. Pilih tanggal & jam\n4. Bayar online\n\nButuh bantuan booking?",
        'lokasi' => "📍 **Lokasi Lapangin.Aja**:\nJl. Olahraga No. 123, Kota Anda\n🕐 Buka: 08:00 - 22:00 (Setiap Hari)\n🚗 Free Parking Area",
        'promo' => "🎉 **PROMO TERBARU**:\n1. Weekdays diskon 20%\n2. Member: 1 free booking/bulan\n3. Paket 5x booking = free 1x\n4. Early bird booking diskon 15%\n\nMau info detail?",
        'default' => "Halo! Terima kasih telah menghubungi Lapangin.Aja! 😊\n\nSaya siap membantu Anda dengan:\n• Info harga & promo\n• Cek jadwal lapangan\n• Bantuan booking\n• Info lokasi & fasilitas\n\nAda yang bisa saya bantu?"
    ];
    
    foreach ($responses as $keyword => $response) {
        if (strpos($msg, $keyword) !== false) {
            return $response;
        }
    }
    
    return $responses['default'];
}
?>
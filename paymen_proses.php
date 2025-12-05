<?php
// payment_proses.php - versi clean & final

session_start();
ini_set('display_errors', 0);
error_reporting(0);

// Bersihkan output buffer
while (ob_get_level()) { ob_end_clean(); }

// Header JSON
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/config/midtrans.php';

use Midtrans\Snap;

// --- Validasi login ---
if (!isset($_SESSION['penggunaID'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login']);
    exit;
}

$jadwal_id    = $_POST['jadwal_id'] ?? null;
$metode       = $_POST['metode'] ?? 'midtrans';
$prosesBayar  = $_POST['prosesBayar'] ?? null;

if (!$prosesBayar || !$jadwal_id) {
    echo json_encode(['success' => false, 'message' => 'Request tidak valid']);
    exit;
}

// --- Ambil data jadwal + lapangan ---
$stmt = $conn->prepare("
    SELECT j.*, l.namaLapangan, l.hargaPerJam 
    FROM jadwal j 
    JOIN lapangan l ON j.lapanganID=l.lapanganID
    WHERE j.jadwalID=?
");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'DB prepare error']);
    exit;
}
$stmt->bind_param("i", $jadwal_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Jadwal tidak ditemukan']);
    exit;
}

// --- Data transaksi ---
$penggunaID = $_SESSION['penggunaID'];
$totalBiaya = (int)$data['hargaPerJam'];
$order_id   = "ORDER-" . $penggunaID . "-" . time();

// --- Insert pemesanan & pembayaran ---
try {
    // Pemesanan
    $stmt2 = $conn->prepare("
        INSERT INTO pemesanan (penggunaID, jadwalID, totalBiaya, statusPemesanan)
        VALUES (?, ?, ?, 'pending')
    ");
    $stmt2->bind_param("iii", $penggunaID, $jadwal_id, $totalBiaya);
    $stmt2->execute();
    $pemesananID = $stmt2->insert_id;

    // Pembayaran
    $stmt3 = $conn->prepare("
        INSERT INTO pembayaran (pemesananID, penggunaID, metodePembayaran, grossAmount, transactionStatus, order_id)
        VALUES (?, ?, ?, ?, 'pending', ?)
    ");
    $stmt3->bind_param("iisis", $pemesananID, $penggunaID, $metode, $totalBiaya, $order_id);
    $stmt3->execute();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data pemesanan']);
    exit;
}

// --- Generate Snap Token ---
$transaction = [
    'transaction_details' => [
        'order_id'    => $order_id,
        'gross_amount'=> $totalBiaya
    ],
    'customer_details' => [
        'first_name' => $_SESSION['namaPengguna'] ?? 'Customer',
        'email'      => $_SESSION['email'] ?? 'customer@example.com',
        'phone'      => $_SESSION['noTelepon'] ?? '081234567890'
    ]
];

try {
    $snapToken = Snap::getSnapToken($transaction);

    // Pastikan output buffer bersih
    while (ob_get_level()) { ob_end_clean(); }

    echo json_encode([
        'success'       => true,
        'snap_token'    => $snapToken,
        'order_id'      => $order_id,
        'pemesanan_id'  => $pemesananID
    ]);
    exit;

} catch (Exception $e) {
    error_log('Midtrans Error: ' . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Midtrans error. Silakan coba lagi.'
    ]);
    exit;
}

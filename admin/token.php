<?php
session_start();

include 'config/koneksi.php'; 
require_once 'vendor/autoload.php';

\Midtrans\Config::$serverKey = "Mid-server-dmei7dxtLXKwMtj3hmnuFKcn";
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Ambil data dari frontend
$data = json_decode(file_get_contents("php://input"), true);

$jadwal_id  = $data['jadwal_id'] ?? null;
$penggunaID = $_SESSION['penggunaID'] ?? null;

if (!$jadwal_id || !$penggunaID) {
    echo json_encode(["error" => "Data tidak lengkap"]);
    exit;
}

// Ambil data jadwal + lapangan
$sql = "SELECT j.*, l.namaLapangan, l.hargaPerJam 
        FROM jadwal j 
        JOIN lapangan l ON j.lapanganID=l.lapanganID 
        WHERE j.jadwalID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $jadwal_id);
$stmt->execute();
$result = $stmt->get_result();
$jadwal = $result->fetch_assoc();

if (!$jadwal) {
    echo json_encode(["error" => "Jadwal tidak ditemukan"]);
    exit;
}

$grossAmount = $jadwal['hargaPerJam'];

// Generate order_id unik
$order_id = "ORD-" . time() . "-" . rand(1000, 9999);

// INSERT ke tabel pembayaran (status: pending)
$insert = $conn->prepare("
    INSERT INTO pembayaran (pemesananID, penggunaID, metodePembayaran, grossAmount, order_id, status)
    VALUES (?, ?, 'Midtrans', ?, ?, 'pending')
");
$insert->bind_param("iiis", $jadwal_id, $penggunaID, $grossAmount, $order_id);
$insert->execute();

// Detail transaksi Midtrans
$transaction = [
    'transaction_details' => [
        'order_id'      => $order_id,
        'gross_amount'  => $grossAmount,
    ],
    'customer_details' => [
        'first_name' => "User",
        'email'      => "user@example.com"
    ]
];

$snapToken = \Midtrans\Snap::getSnapToken($transaction);

echo json_encode(["token" => $snapToken, "order_id" => $order_id]);

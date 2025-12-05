<?php
session_start();
include 'config/koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

$jadwal_id = $data['jadwal_id'];
$penggunaID = $_SESSION['penggunaID'] ?? null;
$grossAmount = $data['gross_amount'];
$transaction_id = $data['transaction_id'];
$order_id = $data['order_id'];
$payment_type = $data['payment_type'];
$transaction_status = $data['transaction_status'];

if(!$penggunaID){
    echo json_encode(['success'=>false, 'message'=>'Belum login']);
    exit;
}
// Insert pemesanan
$q1 = "INSERT INTO pemesanan (penggunaID, jadwalID, totalBiaya, statusPemesanan) VALUES (?, ?, ?, 'dibayar')";
$stmt1 = $conn->prepare($q1);
$stmt1->bind_param("iii", $penggunaID, $jadwal_id, $grossAmount);
$stmt1->execute();
$pemesananID = $stmt1->insert_id;

// Insert pembayaran
$q2 = "INSERT INTO pembayaran (pemesananID, penggunaID, metodePembayaran, grossAmount, transactionStatus, transactionID, orderID)
       VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt2 = $conn->prepare($q2);
$stmt2->bind_param("iisisss", $pemesananID, $penggunaID, $payment_type, $grossAmount, $transaction_status, $transaction_id, $order_id);
$stmt2->execute();

// Update jadwal jadi dipesan
$conn->query("UPDATE jadwal SET status='Dipesan' WHERE jadwalID=$jadwal_id");

echo json_encode(['success'=>true]);

<?php
include 'config/koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? null;
$status   = $data['status'] ?? null;

if ($order_id && $status) {
    $q = "UPDATE pembayaran SET transactionStatus=? WHERE order_id=?";
    $stmt = $conn->prepare($q);
    $stmt->bind_param("ss", $status, $order_id);
    $stmt->execute();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
}

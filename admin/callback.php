<?php
include 'config/koneksi.php';
require_once 'vendor/autoload.php';

use Midtrans\Notification;

$notif = new Notification();

$order_id           = $notif->order_id;
$transaction_status = $notif->transaction_status;
$payment_type       = $notif->payment_type;
$transaction_id     = $notif->transaction_id;
$fraud_status       = $notif->fraud_status ?? null;

$status = "pending";

// Mapping status
if ($transaction_status == 'capture') {

    if ($payment_type == 'credit_card' && $fraud_status == 'challenge') {
        $status = "challenge";
    } else {
        $status = "paid";
    }

} elseif ($transaction_status == 'settlement') {
    $status = "paid";

} elseif ($transaction_status == 'pending') {
    $status = "pending";

} elseif ($transaction_status == 'deny') {
    $status = "deny";

} elseif ($transaction_status == 'expire') {
    $status = "expired";

} elseif ($transaction_status == 'cancel') {
    $status = "cancel";
}

// Update tabel pembayaran
$query = "
    UPDATE pembayaran 
    SET status=?, paymentType=?, transactionID=?, waktuUpdate=NOW()
    WHERE order_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $status, $payment_type, $transaction_id, $order_id);
$stmt->execute();

// WAJIB balas 200
http_response_code(200);
echo "OK";

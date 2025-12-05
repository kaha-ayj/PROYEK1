<?php
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
require_once 'config/midtrans.php'; // pastikan ini benar

use Midtrans\Notification;

$notif = new Notification();

$order_id = $notif->order_id;
$transaction = $notif->transaction_status;
$fraud = $notif->fraud_status;

// koneksi database
include 'config/koneksi.php';

if ($transaction == 'capture') {
    if ($fraud == 'challenge') {
        $status = 'challenge';
    } else {
        $status = 'success';
    }
} else if ($transaction == 'settlement') {
    $status = 'success';
} else if ($transaction == 'pending') {
    $status = 'pending';
} else if ($transaction == 'deny') {
    $status = 'denied';
} else if ($transaction == 'expire') {
    $status = 'expired';
} else if ($transaction == 'cancel') {
    $status = 'canceled';
}

// update ke database
$q = "UPDATE pembayaran SET status='$status' WHERE order_id='$order_id'";
mysqli_query($conn, $q);

echo "OK";

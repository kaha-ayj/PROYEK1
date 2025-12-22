<?php //admin/api/get_message.php
session_start();
header('Content-Type: application/json');
include __DIR__ . '/../../config/koneksi.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    echo json_encode(['success'=>false,'message'=>'Bukan admin']);
    exit;
}

$penggunaID = isset($_GET['penggunaID']) ? (int)$_GET['penggunaID'] : 0;

$sql = "SELECT sender, message, created_at FROM chat WHERE penggunaID=? ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $penggunaID);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode(['success'=>true,'data'=>$data]);
$conn->close();

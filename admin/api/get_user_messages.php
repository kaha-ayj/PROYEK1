<?php
// 1. Pastikan tidak ada spasi atau baris kosong sebelum tag <?php
header('Content-Type: application/json');
session_start();

// 2. Matikan laporan error agar tidak merusak format JSON
error_reporting(0); 

// 3. Sertakan file koneksi
include(__DIR__ . '/../../config/koneksi.php');

// Deteksi otomatis variabel koneksi ($koneksi atau $conn)
$db = isset($koneksi) ? $koneksi : (isset($conn) ? $conn : null);

$response = ['success' => false, 'messages' => []];

if (!$db) {
    echo json_encode(['success' => false, 'error' => 'Koneksi database gagal']);
    exit;
}

// 4. Ambil User ID dari session
$user_id = $_SESSION['penggunaID'] ?? ($_SESSION['user']['penggunaID'] ?? 0);

if ($user_id > 0) {
    // Ambil data chat berdasarkan penggunaID
    $query = "SELECT sender, message, DATE_FORMAT(created_at, '%H:%i') as time, created_at 
              FROM chat 
              WHERE penggunaID = ? 
              ORDER BY created_at ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['messages'][] = $row;
    }
    $response['success'] = true;
}

// 5. Kirim respon JSON murni
echo json_encode($response);
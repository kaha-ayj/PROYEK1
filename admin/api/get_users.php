<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/koneksi.php';

// Query ini mengambil SEMUA user yang bukan admin
// Kita gunakan LEFT JOIN agar user yang belum pernah chat tetap muncul
$sql = "SELECT p.penggunaID, p.nama, 
               (SELECT message FROM chat 
                WHERE penggunaID = p.penggunaID 
                ORDER BY created_at DESC LIMIT 1) as last_msg,
               (SELECT created_at FROM chat 
                WHERE penggunaID = p.penggunaID 
                ORDER BY created_at DESC LIMIT 1) as last_time
        FROM pengguna p
        WHERE p.role != 'admin'
        ORDER BY last_time DESC"; // User dengan chat terbaru akan tetap di atas

$result = $conn->query($sql);
$data = [];

if ($result) {
    while($row = $result->fetch_assoc()) {
        $data[] = [
            'user_id'      => $row['penggunaID'],
            'username'     => $row['nama'],
            // Jika belum ada pesan, berikan keterangan default
            'last_message' => $row['last_msg'] ?? 'Belum ada percakapan',
            'last_time'    => $row['last_time'] ? date('H:i', strtotime($row['last_time'])) : ''
        ];
    }
}

echo json_encode(['success' => true, 'data' => $data]);
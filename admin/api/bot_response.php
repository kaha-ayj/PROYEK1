<?php
// admin/api/bot_response.php
function getBotResponse($user_message) {
    $responses = [
        'halo' => 'Halo! Selamat datang di layanan kami. Ada yang bisa saya bantu?',
        'sewa' => 'Untuk menyewa lapangan, silakan pilih lapangan yang tersedia di menu "Booking".',
        'harga' => 'Harga sewa mulai dari Rp 100.000/jam. Silakan cek detail di menu "Harga".',
        'jadwal' => 'Anda bisa cek jadwal kosong di menu "Jadwal Lapangan".',
        'admin' => 'Admin akan membalas pesan Anda segera. Mohon tunggu ya!',
    ];
    
    $message_lower = strtolower(trim($user_message));
    
    foreach ($responses as $keyword => $response) {
        if (strpos($message_lower, $keyword) !== false) {
            return $response;
        }
    }
    
    return 'Terima kasih telah menghubungi kami. Admin akan segera merespons pesan Anda.';
}
?>
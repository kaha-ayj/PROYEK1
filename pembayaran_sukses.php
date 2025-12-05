<?php 
// Cek apakah session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil order_id dari URL
$order_id = $_GET['order_id'] ?? 'UNKNOWN';

// Include koneksi database
require_once 'config/koneksi.php';

$detail_pesanan = null;
if ($order_id !== 'UNKNOWN') {
    // Gunakan prepared statement untuk keamanan
    $query = "SELECT p.*, pe.*, j.*, l.namaLapangan 
              FROM pembayaran p
              JOIN pemesanan pe ON p.pemesananID = pe.pemesananID
              JOIN jadwal j ON pe.jadwalID = j.jadwalID
              JOIN lapangan l ON j.lapanganID = l.lapanganID
              WHERE p.order_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $detail_pesanan = $result->fetch_assoc();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/home.css">
<link rel="stylesheet" href="assets/nav.css">
<title>Pembayaran Berhasil - Lapangin.Aja</title>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');

  body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f0fdf4;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .header {
    position: relative;
    z-index: 100;
  }

  .success-container {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
  }

  .success-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 30px;
    max-width: 1000px; 
    width: 100%;
    padding: 60px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center; 
    gap: 60px;
    animation: slideUp 0.6s ease-out;
    text-align: left;
  }

  .left-section {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .right-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(50px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .success-card h1 {
    font-size: 36px;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 20px;
    line-height: 1.2;
  }

  .success-card p {
    font-size: 16px;
    color: #6b7280;
    margin-bottom: 35px;
    line-height: 1.6;
    max-width: 90%;
  }

  .btn-group {
    width: 100%;
    display: flex;
    justify-content: flex-start;
  }

  .btn {
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .btn-primary {
    background: #10b981;
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
  }

  .btn-primary:hover {
    background: #059669;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
  }

  /* --- ILUSTRASI --- */
  .illustration {
    width: 320px; 
    height: 320px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 20px;
    box-shadow: 0 20px 40px rgba(139, 92, 246, 0.25);
    animation: float 6s ease-in-out infinite;
  }

  .illustration-inner {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #6ee7b7 0%, #34d399 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    border: 6px solid rgba(255,255,255,0.3);
  }

  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
  }

  /* --- ANIMASI PEMAIN & PROPERTI --- */
  .players { width: 100%; height: 100%; position: relative; }
  .court-line { position: absolute; width: 90%; height: 2px; background: rgba(255,255,255,0.8); top: 50%; left: 5%; }
  .net { position: absolute; width: 4px; height: 70%; background: white; left: 50%; top: 15%; transform: translateX(-50%); border-radius: 2px; box-shadow: 0 0 10px rgba(0,0,0,0.1); z-index: 2; }
  
  /* Style Pemain */
  .player {
    position: absolute; width: 40px; height: 60px;
    animation: bounce 1.5s ease-in-out infinite;
    z-index: 1;
  }
  .player1 { left: 15%; top: 30%; animation-delay: 0s; }
  .player2 { right: 15%; top: 30%; animation-delay: 0.75s; } /* Delay disesuaikan dengan kok */
  .player3 { left: 20%; bottom: 25%; animation-delay: 0.4s; }
  .player4 { right: 20%; bottom: 25%; animation-delay: 1.2s; }

  @keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
  }

  .player-head { width: 14px; height: 14px; background: #fca5a5; border-radius: 50%; margin: 0 auto 3px; }
  .player-body { width: 22px; height: 26px; background: #db2777; margin: 0 auto; border-radius: 6px; position: relative;}
  .player2 .player-body, .player4 .player-body { background: #7c3aed; }

  /* --- RAKET --- */
  .racket {
    position: absolute;
    width: 12px;
    height: 18px;
    border: 2px solid white;
    border-radius: 50% 50% 40% 40%; /* Bentuk kepala raket */
    top: 5px;
    right: -12px;
    transform: rotate(30deg);
  }
  /* Gagang Raket */
  .racket::after {
    content: ''; position: absolute; bottom: -8px; left: 50%; width: 3px; height: 8px; background: white; transform: translateX(-50%);
  }
  /* Penyesuaian posisi raket untuk pemain kanan */
  .player2 .racket, .player4 .racket { left: -12px; right: auto; transform: rotate(-30deg); }


  /* --- SHUTTLECOCK (KOK) --- */
  .shuttlecock {
    position: absolute;
    width: 12px; height: 14px;
    z-index: 3;
    /* Animasi Rally */
    animation: rally 1.5s ease-in-out infinite;
  }
  /* Kepala Kok */
  .shuttle-head { width: 8px; height: 6px; background: white; border-radius: 0 0 50% 50%; position: absolute; bottom: 0; left: 2px;}
  /* Bulu Kok */
  .shuttle-feathers { width: 12px; height: 9px; background: rgba(255,255,255,0.8); clip-path: polygon(0 0, 100% 0, 85% 100%, 15% 100%); position: absolute; top: 0; left: 0;}

  /* Animasi Lintasan Kok Melambung */
  @keyframes rally {
    0% { left: 20%; top: 35%; transform: rotate(-45deg); } /* Pukul dari kiri */
    25% { left: 50%; top: 15%; transform: rotate(0deg); }  /* Puncak di atas net */
    50% { left: 80%; top: 35%; transform: rotate(45deg); }  /* Sampai di kanan */
    75% { left: 50%; top: 20%; transform: rotate(0deg); }  /* Balik ke tengah */
    100% { left: 20%; top: 35%; transform: rotate(-45deg); } /* Balik ke kiri */
  }


  @media (max-width: 968px) {
    .success-card {
      flex-direction: column;
      text-align: center;
      padding: 40px 30px;
      gap: 40px;
    }
    .right-section { align-items: center; }
    .btn-group { justify-content: center; }
    .success-card h1 { font-size: 28px; }
    .success-card p { max-width: 100%; }
    .illustration { width: 280px; height: 280px; }
  }
</style>
</head>
<body>

<header class="header">
    <?php include 'includes/nav.php'; ?>
</header>

<div class="success-container">
  <div class="success-card">
    
    <div class="left-section">
      <div class="illustration">
        <div class="illustration-inner">
          <div class="players">
            <div class="court-line"></div>
            <div class="net"></div>
            
            <div class="shuttlecock">
                <div class="shuttle-feathers"></div>
                <div class="shuttle-head"></div>
            </div>

            <div class="player player1">
                <div class="player-head"></div>
                <div class="player-body">
                    <div class="racket"></div> </div>
            </div>
            
            <div class="player player2">
                <div class="player-head"></div>
                <div class="player-body">
                    <div class="racket"></div> </div>
            </div>
            
            <div class="player player3"><div class="player-head"></div><div class="player-body"><div class="racket"></div></div></div>
            <div class="player player4"><div class="player-head"></div><div class="player-body"><div class="racket"></div></div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="right-section">
      <h1>Yeay, Pemesanan<br>Lapangan Sudah Berhasil!</h1>
      <div class="btn-group">
        <a href="detail_pemesanan.php<?php echo $order_id !== 'UNKNOWN' ? '?order_id='.$order_id : ''; ?>" class="btn btn-primary">
          Cek Detail Pesanan
        </a>
      </div>
    </div>

  </div>
</div>

</body>
</html>
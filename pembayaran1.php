<?php 
// Tampilkan semua error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek apakah session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DEBUG: Log session
error_log("=== DEBUG SESSION ===");
error_log("Session ID: " . session_id());
error_log("Session data: " . print_r($_SESSION, true));
error_log("penggunaID: " . (isset($_SESSION['penggunaID']) ? $_SESSION['penggunaID'] : 'TIDAK ADA'));

// TEMPORARY FIX: Ambil data user dari database jika session kosong
if (!isset($_SESSION['penggunaID']) && isset($_COOKIE['PHPSESSID'])) {
    // Coba cari user yang login dari cookies atau cara lain
    // Ini hanya untuk debugging, hapus nanti!
    error_log("WARNING: Session kosong, mencoba recovery...");
    
    // Cek apakah ada user login dari tabel lain atau cookies
    // GANTI 4 dengan ID user Anda untuk testing
    $_SESSION['penggunaID'] = 4; // UNTUK TESTING SAJA!
    $_SESSION['namaPengguna'] = 'Test User';
    $_SESSION['email'] = 'test@example.com';
    $_SESSION['noTelepon'] = '081234567890';
    
    error_log("Session di-set manual untuk testing");
}

// Include file koneksi dan midtrans
require_once 'config/koneksi.php';
require_once 'config/midtrans.php';

use Midtrans\Snap;

// Cek apakah koneksi database berhasil
if (!isset($conn) || $conn === null) {
    die("Error: Koneksi database gagal. Pastikan file config/koneksi.php sudah benar.");
}

/* ============================
   Ambil jadwal_id GET/POST
   ============================ */
$jadwal_id = $_POST['jadwal_id'] ?? $_GET['jadwal_id'] ?? null;

if (!$jadwal_id) {
    die("Jadwal tidak ditemukan. <a href='home.php'>Kembali</a>");
}

/* ===========================================
   Ambil data jadwal + lapangan
   =========================================== */
$qDetail = "
    SELECT j.*, l.namaLapangan, l.hargaPerJam 
    FROM jadwal j
    JOIN lapangan l ON j.lapanganID = l.lapanganID
    WHERE j.jadwalID = ?
";
$stmt = $conn->prepare($qDetail);
$stmt->bind_param("i", $jadwal_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("DATA JADWAL TIDAK DITEMUKAN UNTUK ID: $jadwal_id");
}

// Format tampilan
$mulai   = date('H:i', strtotime($data['waktuMulai']));
$selesai = date('H:i', strtotime($data['waktuSelesai']));
$harga   = number_format($data['hargaPerJam'], 0, ',', '.');

/* ============================
   Proses Booking + Midtrans
   ============================ */
if (isset($_POST['prosesBayar'])) {
    
    // Log untuk debugging
    error_log("POST data: " . print_r($_POST, true));
    error_log("SESSION data: " . print_r($_SESSION, true));

    if (!isset($_SESSION['penggunaID'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Anda harus login untuk melanjutkan.',
            'debug' => 'Session penggunaID tidak ditemukan'
        ]);
        exit;
    }

    $penggunaID = $_SESSION['penggunaID'];
    $metode = $_POST['metode'] ?? 'midtrans';

    if (!$metode) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Metode pembayaran belum dipilih.'
        ]);
        exit;
    }

    $totalBiaya = $data['hargaPerJam'];

    // Generate order ID unik
    $order_id = "ORDER-" . $penggunaID . "-" . time();

    try {
        // Mulai transaction
        $conn->begin_transaction();

        // Insert pemesanan (status pending)
        $q2 = "INSERT INTO pemesanan (penggunaID, jadwalID, totalBiaya, statusPemesanan, tanggalPemesanan)
               VALUES (?, ?, ?, 'dibayar', NOW())";
        $stmt2 = $conn->prepare($q2);
        if (!$stmt2) {
            throw new Exception("Prepare statement gagal: " . $conn->error);
        }
        $stmt2->bind_param("iii", $penggunaID, $jadwal_id, $totalBiaya);
        if (!$stmt2->execute()) {
            throw new Exception("Execute pemesanan gagal: " . $stmt2->error);
        }
        $pemesananID = $stmt2->insert_id;

        // Insert pembayaran - sesuaikan dengan struktur tabel
        $q3 = "INSERT INTO pembayaran (pemesananID, penggunaID, metodePembayaran, grossAmount, status, order_id, waktuDibuat)
               VALUES (?, ?, ?, ?, 'pending', ?, NOW())";
        $stmt3 = $conn->prepare($q3);
        if (!$stmt3) {
            throw new Exception("Prepare pembayaran gagal: " . $conn->error);
        }
        $stmt3->bind_param("iisis", $pemesananID, $penggunaID, $metode, $totalBiaya, $order_id);
        if (!$stmt3->execute()) {
            throw new Exception("Execute pembayaran gagal: " . $stmt3->error);
        }

        // Generate Snap Token
        $transaction = array(
            'transaction_details' => array(
                'order_id' => $order_id,
                'gross_amount' => (int)$totalBiaya,
            ),
            'customer_details' => array(
                'first_name' => $_SESSION['namaPengguna'] ?? 'Customer',
                'email' => $_SESSION['email'] ?? 'customer@example.com',
                'phone' => $_SESSION['noTelepon'] ?? '081234567890',
            ),
            'item_details' => array(
                array(
                    'id' => 'jadwal-' . $jadwal_id,
                    'price' => (int)$totalBiaya,
                    'quantity' => 1,
                    'name' => $data['namaLapangan'] . ' - ' . $mulai . ' s.d ' . $selesai
                )
            ),
        );

        $snapToken = Snap::getSnapToken($transaction);
        
        // Commit transaction
        $conn->commit();

        // Pastikan tidak ada output sebelum ini
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'snap_token' => $snapToken,
            'order_id' => $order_id,
            'pemesanan_id' => $pemesananID
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        
        error_log("Midtrans Error: " . $e->getMessage());
        
        // Pastikan tidak ada output sebelum ini
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
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
<title>Pembayaran Lapangan - Lapangin.Aja</title>

<!-- Midtrans Snap - GANTI dengan Client Key asli Anda -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-70ua3ZN798SZKqf5"></script>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');


  .search-box input {
    border: none;
    outline: none;
    padding: 5px;
  }

  .payment-card {
    background: #edf3f3;
    border-radius: 15px;
    width: 90%;
    max-width: 950px;
    margin: 40px auto;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
  }

  th {
    background: #c9d8de;
    padding: 12px;
    text-align: center;
    border-radius: 10px 10px 0 0;
    font-weight: 600;
  }

  td {
    padding: 15px;
    text-align: center;
    background: white;
  }

  td img {
    width: 120px;
    border-radius: 10px;
  }

  .booking-box {
    background: transparent;
    border-radius: 15px;
    padding: 20px;
    max-width: 400px;
    margin: 0 auto;
    box-shadow: none;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .booking-box h4 {
    margin-bottom: 15px;
    font-size: 18px;
    color: #333;
  }

  .btn-booking {
    background: #10b981;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    font-size: 16px;
  }

  .btn-booking:hover:not(:disabled) {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(16,185,129,0.3);
  }

  .btn-booking:disabled {
    background: #ccc;
    cursor: not-allowed;
  }

  .alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
  }

  .alert-error {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fca5a5;
  }

  .alert-info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 600px) {
    .payment-card {
      padding: 15px;
    }
    
    td img {
      width: 80px;
    }

    th, td {
      padding: 8px;
      font-size: 12px;
    }
  }
</style>

</head>
<body>

<header class="header">
<?php include 'includes/nav.php'; ?>
</header>

<div class="payment-card">
  
  

  <table>
    <tr>
      <th>Nama Lapangan</th>
      <th>Detail</th>
      <th>Status</th>
      <th>Jadwal</th>
      <th>Harga</th>
      <th>Jumlah</th>
    </tr>

    <tr>
      <td><img src="assets/image/lap_KG.png" alt="<?= htmlspecialchars($data['namaLapangan']) ?>"></td>
      <td><strong><?= htmlspecialchars($data['namaLapangan']) ?></strong><br>ID#<?= $data['jadwalID'] ?></td>
      <td><span style="color: <?= $data['status'] == 'tersedia' ? '#10b981' : '#ef4444' ?>;"><?= htmlspecialchars($data['status']) ?></span></td>
      <td><?= $mulai ?> - <?= $selesai ?></td>
      <td><strong>Rp <?= $harga ?></strong></td>
      <td>1x</td>
    </tr>
  </table>

  <div class="booking-box">
    <h4>Total Pembayaran: <span style="color: #10b981;">Rp <?= $harga ?></span></h4>
    <form id="paymentForm" method="POST">
      <input type="hidden" name="jadwal_id" value="<?= $jadwal_id ?>">
      <input type="hidden" name="metode" value="midtrans">
      <button type="button" class="btn-booking" id="btnBooking">
          Bayar Sekarang
      </button>
    </form>
  </div>

</div>

<script>
const btnBooking = document.getElementById('btnBooking');
const paymentForm = document.getElementById('paymentForm');

btnBooking.addEventListener("click", (e) => {
    e.preventDefault();

    // Disable button saat proses
    btnBooking.disabled = true;
    btnBooking.textContent = 'Memproses...';

    const formData = new FormData(paymentForm);
    formData.append('prosesBayar', '1');

    console.log('Mengirim request pembayaran...');

    // Fetch ke file yang sama (otomatis detect)
    fetch(window.location.pathname, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Clone response untuk bisa dibaca dua kali
        return response.text().then(text => {
            console.log('Raw response:', text);
            
            // Coba parse JSON
            try {
                const data = JSON.parse(text);
                return { ok: response.ok, data: data };
            } catch (e) {
                console.error('JSON Parse Error:', e);
                console.error('Response text:', text);
                throw new Error('Response bukan JSON valid: ' + text.substring(0, 100));
            }
        });
    })
    .then(result => {
        if (!result.ok) {
            throw new Error('HTTP Error: ' + result.data.message || 'Unknown error');
        }
        
        const data = result.data;
        console.log('Parsed data:', data);
        
        if (data.success) {
            console.log('Membuka Midtrans Snap...');
            // Snap popup default Midtrans
            snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    
                    // Update status ke database
                    fetch('update_status.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            order_id: data.order_id,
                            pemesanan_id: data.pemesanan_id,
                            status: "success"
                        })
                    })
                    .then(() => {
                        window.location.href = 'pembayaran_sukses.php?order_id=' + data.order_id;
                    });
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    
                    fetch('update_status.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            order_id: data.order_id,
                            pemesanan_id: data.pemesanan_id,
                            status: "pending"
                        })
                    })
                    .then(() => {
                        alert('Pembayaran pending, silakan selesaikan pembayaran Anda');
                        window.location.href = 'riwayat_pemesanan.php';
                    });
                },
                onError: function(result) {
                    console.error('Payment error:', result);
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    btnBooking.disabled = false;
                    btnBooking.textContent = 'Bayar Sekarang';
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    btnBooking.disabled = false;
                    btnBooking.textContent = 'Bayar Sekarang';
                }
            });
        } else {
            // Jika error karena belum login
            console.error('Error response:', data);
            alert('Error: ' + (data.message || 'Terjadi kesalahan'));
            btnBooking.disabled = false;
            btnBooking.textContent = 'Bayar Sekarang';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Terjadi kesalahan: ' + error.message);
        btnBooking.disabled = false;
        btnBooking.textContent = 'Bayar Sekarang';
    });
});
</script>

</body>
</html>
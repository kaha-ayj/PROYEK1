<?php 
session_start();
include 'config/koneksi.php';

/* ============================
   Ambil jadwal_id GET/POST
   ============================ */
$jadwal_id = $_POST['jadwal_id'] ?? $_GET['jadwal_id'] ?? null;

if (!$jadwal_id) {
    die("Jadwal tidak ditemukan. <a href='home.php'>Kembali</a>");
}

/* ===========================================
   AMBIL DATA JADWAL + LAPANGAN (HARUS DULUAN)
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
   PROSES BOOKING
   ============================ */
if (isset($_POST['prosesBayar'])) {

    if (!isset($_SESSION['penggunaID'])) {
        die("Anda harus login untuk melanjutkan.");
    }

    $penggunaID = $_SESSION['penggunaID'];
    $metode = $_POST['metode'] ?? null;

    if (!$metode) {
        die("Metode pembayaran belum dipilih.");
    }

    $totalBiaya = $data['hargaPerJam'];

    // Insert pemesanan
    $q2 = "INSERT INTO pemesanan (penggunaID, jadwalID, totalBiaya, statusPemesanan)
           VALUES (?, ?, ?, 'dibayar')";
    $stmt2 = $conn->prepare($q2);
    $stmt2->bind_param("iii", $penggunaID, $jadwal_id, $totalBiaya);
    $stmt2->execute();
    $pemesananID = $stmt2->insert_id;

    // Insert pembayaran
    $q3 = "INSERT INTO pembayaran (pemesananID, penggunaID, metodePembayaran, grossAmount, transactionStatus)
           VALUES (?, ?, ?, ?, 'berhasil')";
    $stmt3 = $conn->prepare($q3);
    $stmt3->bind_param("iisi", $pemesananID, $penggunaID, $metode, $totalBiaya);
    $stmt3->execute();

    // Update jadwal
    $conn->query("UPDATE jadwal SET status = 'Dipesan' WHERE jadwalID = $jadwal_id");

    header("Location: pembayaran_sukses.php");
    exit;
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
  }

  td {
    padding: 15px;
    text-align: center;
  }

  td img {
    width: 120px;
    border-radius: 10px;
  }

  .payment-header {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 20px;
    flex-wrap: wrap;
  }

  /* Box metode pembayaran */
  .payment-methods {
    background: transparent; /* warna putih dihapus */
    border-radius: 15px;
    padding: 20px;
    width: 280px;
    box-shadow: none; /* hilangkan bayangan putih */
    text-align: center;
  }

  .btn-metode {
    background: #5b6cff;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    width: 100%;
    transition: 0.3s;
  }

  .btn-metode:hover {
    background: #3f52d8;
  }

  .bank-list {
    display: none;
    margin-top: 15px;
    animation: fadeIn 0.4s ease forwards;
  }

  .bank-option {
    border: 1.5px solid #ddd;
    border-radius: 10px;
    padding: 8px;
    margin: 8px 0;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
  }

  .bank-option:hover {
    border-color: #5b6cff;
  }

  .bank-option.active {
    backdrop-filter: blur(15px);

    background: rgba(117, 165, 254, 0.7);
    border-color: #ffffff;
  }

  .bank-option img {
    width: 60px;
    height: 25px;
    object-fit: contain;
  }

  .booking-box {
    background: transparent; 
    border-radius: 15px;
    padding: 20px;
    width: 280px;
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
    background: #ccc;
    color: #333;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: not-allowed;
    transition: 0.3s;
  }

  .btn-booking.active {
    background: #10b981;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(16,185,129,0.3);
  }

  .btn-booking.active:hover {
    background: #059669;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 600px) {
    .payment-header {
      flex-direction: column;
      align-items: center;
    }
    .payment-methods, .booking-box {
      width: 90%;
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
      <td><?= htmlspecialchars($data['namaLapangan']) ?><br>ID#<?= $data['jadwalID'] ?></td>
      <td><?= htmlspecialchars($data['status']) ?></td>
      <td><?= $mulai ?> - <?= $selesai ?></td>
      <td>Rp <?= $harga ?></td>
      <td>1x</td>
    </tr>
  </table>

  <div class="payment-header">
    
    <!-- Metode Pembayaran -->
    <div class="payment-methods">
      <button class="btn-metode" id="toggleBtn">Pilih Metode Pembayaran</button>

      <div class="bank-list" id="bankList">
        <div class="bank-option"><img src="assets/image/dana.png" alt="DANA"></div>
        <div class="bank-option"><img src="assets/image/gopay.png" alt="GoPay"></div>
        <div class="bank-option"><img src="assets/image/bca.png" alt="BCA"></div>
        <div class="bank-option"><img src="assets/image/bri.png" alt="BRI"></div>
        <div class="bank-option"><img src="assets/image/bni.png" alt="BNI"></div>
        <div class="bank-option"><img src="assets/image/btn.png" alt="BTN"></div>
      </div>
    </div>

    <!-- Tombol Booking -->
    <div class="booking-box">
      <form method="POST">
        <input type="hidden" name="metode" id="metodePembayaran">
        <input type="hidden" name="jadwal_id" value="<?= $jadwal_id ?>">
        <button type="submit" name="prosesBayar" class="btn-booking" id="btnBooking">Booking Sekarang</button>
      </form>
    </div>

  </div>
</div>

<script>
const toggleBtn = document.getElementById('toggleBtn');
const bankList = document.getElementById('bankList');
const bankOptions = document.querySelectorAll('.bank-option');
const btnBooking = document.getElementById('btnBooking');
const metodeHidden = document.getElementById('metodePembayaran');

toggleBtn.addEventListener('click', () => {
    bankList.style.display = bankList.style.display === 'block' ? 'none' : 'block';
});

bankOptions.forEach(option => {
    option.addEventListener('click', () => {
        bankOptions.forEach(o => o.classList.remove('active'));
        option.classList.add('active');

        metodeHidden.value = option.querySelector("img").alt;

        btnBooking.classList.add('active');
        btnBooking.disabled = false;
    });
});

btnBooking.addEventListener("click", (e) => {
    if (metodeHidden.value === "") {
        e.preventDefault();
        alert("Pilih metode pembayaran terlebih dahulu");
    }
});
</script>

<?php include 'includes/footer.php'; ?>

</body>
</html>

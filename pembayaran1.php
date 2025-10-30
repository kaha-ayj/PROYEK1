<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran Lapangan - Lapangin.Aja</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
  }

  body {
    background: linear-gradient(180deg, #e4edf1 0%, #b8cfd3 100%);
    min-height: 100vh;
  }

  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 50px;
  }

  .logo {
    display: flex;
    align-items: center;
    font-size: 22px;
    font-weight: 700;
    color: #1a2a5c;
  }

  .logo span {
    color: #476cff;
  }

  nav a {
    margin: 0 15px;
    text-decoration: none;
    color: #75828e;
    font-weight: 500;
  }

  nav a.active {
    color: #5b6cff;
    font-weight: 600;
    border-bottom: 2px solid #5b6cff;
    padding-bottom: 3px;
  }

  .search-box {
    background: white;
    border-radius: 20px;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

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

<header>
  <div class="logo">Lapangin.<span>Aja</span></div>
  <nav>
    <a href="jadwal_lapangan1.php" class="active">Lapangan</a>
    <a href="homepage.php">Home</a>
    <a href="messege1.php">Message</a>
  </nav>
  <div class="search-box">
    <input type="text" placeholder="Cari lapangan">
    🔍
  </div>
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
      <td><img src="assets/image/lap_KG.png" alt="Lapangan"></td>
      <td>Lapangan A<br>ID#12345</td>
      <td>Belum bayar</td>
      <td>07.00 - 08.00</td>
      <td>Rp 15.000</td>
      <td>-</td>
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
      <button class="btn-booking" id="btnBooking">Booking Sekarang</button>
    </div>
  </div>
</div>

<script>
  const toggleBtn = document.getElementById('toggleBtn');
  const bankList = document.getElementById('bankList');
  const bankOptions = document.querySelectorAll('.bank-option');
  const btnBooking = document.getElementById('btnBooking');

  toggleBtn.addEventListener('click', () => {
    bankList.style.display = bankList.style.display === 'block' ? 'none' : 'block';
  });

  bankOptions.forEach(option => {
    option.addEventListener('click', () => {
      bankOptions.forEach(o => o.classList.remove('active'));
      option.classList.add('active');
      btnBooking.classList.add('active');
    });
  });

  btnBooking.addEventListener('click', () => {
    if (btnBooking.classList.contains('active')) {
      alert('Pembayaran sedang diproses...');
    }
  });
</script>

</body>
</html>

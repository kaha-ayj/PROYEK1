<?php
session_start();
require_once 'config/koneksi.php';

// --- AMBIL DATA DARI URL ---
$selectedDate = $_GET['tanggal'] ?? date('Y-m-d');
$venue_id = $_GET['venue_id'] ?? null;
$selectedLapanganID = $_GET['lapangan_id'] ?? null;
$selectedJadwalID = $_GET['jadwal_id'] ?? null;

// --- PROSES BOOKING JIKA ADA REQUEST ---
if (isset($_POST['booking'])) {
    $jadwal_id = $_POST['jadwal_id'];
    
    // Update status jadwal menjadi "Dipesan"
    $query_update = "UPDATE jadwal SET status = 'Dipesan' WHERE jadwalID = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "i", $jadwal_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['success_message'] = "Booking berhasil! Jadwal telah dipesan.";
        header("Location: pembayaran1.php?jadwal_id=" . $jadwal_id);
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal melakukan booking. Silakan coba lagi.";
    }
    mysqli_stmt_close($stmt_update);
}

// --- AMBIL DATA VENUE YANG DIPILIH ---
$venue_data = null;
if ($venue_id) {
    $query_venue = "SELECT venueID, namaVenue, alamat FROM venue WHERE venueID = ?";
    $stmt = mysqli_prepare($conn, $query_venue);
    mysqli_stmt_bind_param($stmt, "i", $venue_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $venue_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// --- AMBIL DAFTAR LAPANGAN DI VENUE INI ---
$lapangan_list = [];
if ($venue_id) {
    $query_lapangan = "SELECT * FROM lapangan WHERE venueID = ? ORDER BY namaLapangan ASC";
    $stmt = mysqli_prepare($conn, $query_lapangan);
    mysqli_stmt_bind_param($stmt, "i", $venue_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $lapangan_list[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// --- JIKA ADA LAPANGAN YANG DIPILIH, AMBIL DATA NYA ---
$data_lapangan_terpilih = null;
if ($selectedLapanganID) {
    $query_lap = "SELECT * FROM lapangan WHERE lapanganID = ?";
    $stmt = mysqli_prepare($conn, $query_lap);
    mysqli_stmt_bind_param($stmt, "i", $selectedLapanganID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data_lapangan_terpilih = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// --- LOGIKA KALENDER ---
$timestamp = strtotime($selectedDate);
$currentMonthName_Year = date('F Y', $timestamp);
$currentYear = date('Y', $timestamp);
$currentMonth = date('m', $timestamp);

// Link untuk navigasi bulan
$param = "";
if ($venue_id) $param .= "&venue_id=" . urlencode($venue_id);
if ($selectedLapanganID) $param .= "&lapangan_id=" . urlencode($selectedLapanganID);
if ($selectedJadwalID) $param .= "&jadwal_id=" . urlencode($selectedJadwalID);

$prevMonthDate = date('Y-m-d', strtotime('-1 month', $timestamp));
$nextMonthDate = date('Y-m-d', strtotime('+1 month', $timestamp));

// --- FUNGSI KALENDER ---
function buatKalender($year, $month, $selectedDate, $param) {
    $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
    $firstDayOfWeek = date('N', mktime(0, 0, 0, $month, 1, $year));

    $days = ['M', 'S', 'S', 'R', 'K', 'J', 'S'];
    foreach ($days as $day) {
        echo "<div class='calendar-day-header'>$day</div>";
    }

    for ($i = 1; $i < $firstDayOfWeek; $i++) {
        echo "<div class='calendar-day empty'></div>";
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dateString = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
        $class = "calendar-day";
        if ($dateString == $selectedDate) {
             $class .= " selected";
        }
        
        echo "<a href='jadwal_lapangan2.php?tanggal=$dateString$param' class='$class'>$day</a>";
    }
}

// --- AMBIL JADWAL BERDASARKAN LAPANGAN YANG DIPILIH ---
$jadwal_slots = [];
$jadwal_dipilih = null;

if ($selectedLapanganID) {
    // Ambil slot jadwal untuk lapangan & tanggal yang dipilih
    $query_jadwal = "SELECT * FROM jadwal 
                     WHERE lapanganID = ? AND DATE(waktuMulai) = ? 
                     ORDER BY waktuMulai ASC";
    $stmt_jadwal = mysqli_prepare($conn, $query_jadwal);
    mysqli_stmt_bind_param($stmt_jadwal, "is", $selectedLapanganID, $selectedDate);
    mysqli_stmt_execute($stmt_jadwal);
    $result_jadwal = mysqli_stmt_get_result($stmt_jadwal);
    while ($row = mysqli_fetch_assoc($result_jadwal)) {
        $jadwal_slots[] = $row;
    }
    mysqli_stmt_close($stmt_jadwal);
}

// Ambil detail jadwal yang dipilih
if ($selectedJadwalID) {
    foreach ($jadwal_slots as $slot) {
        if ($slot['jadwalID'] == $selectedJadwalID) {
            $jadwal_dipilih = $slot;
            break;
        }
    }
}

// Cek apakah semua syarat terpenuhi untuk mengaktifkan tombol
$syaratTerpenuhi = $venue_id && $selectedLapanganID && $selectedJadwalID && $jadwal_dipilih;

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/home.css">
    <link rel="stylesheet" href="assets/nav.css">
    <title>Lapangin.Aja | Pilih Lapangan</title>
    <style>

        .content {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 40px;
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 50px;
            margin-top: 20px;
        }

        /* LEFT SECTION */
        .left {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .venue-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #48cae4;
        }

        .venue-info h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.2em;
        }

        .venue-info p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .lapangan-image {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .lapangan-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .lapangan-btn {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            background: white;
            color: #666;
            font-weight: 600;
            font-size: 0.95em;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .lapangan-btn:hover {
            border-color: #48cae4;
            background: #f0f9ff;
        }

        .lapangan-btn.active {
            background: #48cae4;
            border-color: #48cae4;
            color: white;
        }

        /* RIGHT SECTION */
        .right {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .date-header {
            font-size: 1.3em;
            font-weight: 700;
            color: #333;
            margin-left: 10px;
            margin-top: 20px;
        }

        .jadwal-calendar-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        /* SLOT JAM */
        .slot-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .slot {
            padding: 18px 20px;
            border-radius: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95em;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: inherit;
        }

        .slot-status {
            font-size: 0.85em;
            font-weight: 500;
        }

        .slot-time {
            font-size: 1em;
            font-weight: 700;
        }

        .slot.tersedia {
            background: white;
            border: 2px solid #e0e0e0;
            color: #333;
        }

        .slot.tersedia:hover {
            border-color: #48cae4;
            background: #f0f9ff;
            transform: translateX(5px);
        }

        .slot.tersedia.selected {
            background: #48cae4;
            border-color: #48cae4;
            color: white;
        }

        .slot.dibooking {
            background: #ff4757;
            border: 2px solid #ff4757;
            color: white;
            cursor: not-allowed;
            opacity: 0.9;
        }

        .slot-empty {
            text-align: center;
            padding: 50px;
            color: #999;
            font-size: 0.95em;
        }

        /* KALENDER */
        .calendar-wrapper {
            display: flex;
            flex-direction: column;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header h3 {
            font-size: 1.1em;
            font-weight: 700;
            color: #333;
        }

        .calendar-nav {
            text-decoration: none;
            font-size: 1.5em;
            color: #999;
            font-weight: bold;
            transition: color 0.3s;
            padding: 0 10px;
        }

        .calendar-nav:hover {
            color: #48cae4;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 700;
            color: #999;
            font-size: 0.85em;
            padding: 10px 0;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #f5f5f5;
            color: #333;
            font-weight: 600;
            font-size: 0.9em;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .calendar-day:hover {
            background: #48cae4;
            color: white;
            transform: scale(1.1);
        }

        .calendar-day.selected {
            background: #48cae4;
            color: white;
            box-shadow: 0 4px 10px rgba(72, 202, 228, 0.4);
        }

        .calendar-day.empty {
            background: transparent;
            cursor: default;
        }

        .calendar-day.empty:hover {
            transform: none;
        }

        /* TOMBOL PILIH JADWAL */
        .btn-pilih-wrapper {
            margin-top: 20px;
            text-align: center;
        }
        
        .btn-pilih {
            padding: 15px 30px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #48cae4 0%, #0096c7 100%);
            color: white;
            font-weight: 700;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(72, 202, 228, 0.3);
            min-width: 200px;
        }

        .btn-pilih:hover:not(:disabled) {
            background: linear-gradient(135deg, #0096c7 0%, #023e8a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(72, 202, 228, 0.5);
        }

        .btn-pilih:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
            opacity: 0.6;
        }

        /* Notifikasi */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box p {
            margin: 0;
            color: #1976d2;
            font-size: 0.9em;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #48cae4;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 968px) {
            .content {
                grid-template-columns: 1fr;
            }
            
            .jadwal-calendar-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<header class="header">
<?php include 'includes/nav.php'; ?>
</header>

<main class="container">
    <!-- Link kembali ke halaman venue -->
    <a href="jadwal_lapangan1.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Venue
    </a>

    <div class="content">
        <!-- LEFT SECTION -->
        <div class="left">
            <?php if ($venue_data): ?>
                <div class="venue-info">
                    <h3><?php echo htmlspecialchars($venue_data['namaVenue']); ?></h3>
                    <p><?php echo htmlspecialchars($venue_data['alamat'] ?? 'Venue olahraga dengan fasilitas terbaik'); ?></p>
                </div>
            <?php else: ?>
                <div class="info-box">
                    <p>Venue tidak ditemukan.</p>
                </div>
            <?php endif; ?>

            <img src="assets/image/lapangan.png" alt="Lapangan" class="lapangan-image">
            
            <h4>Pilih Lapangan:</h4>
            
            <?php if (!empty($lapangan_list)): ?>
            <div class="lapangan-buttons">
                <?php foreach ($lapangan_list as $lap): ?>
                    <?php
                    $isActive = ($selectedLapanganID == $lap['lapanganID']) ? 'active' : '';
                    $linkParam = "venue_id=" . urlencode($venue_id);
                    $linkParam .= "&lapangan_id=" . urlencode($lap['lapanganID']);
                    if ($selectedDate) $linkParam .= "&tanggal=" . urlencode($selectedDate);
                    ?>
                    <a href="jadwal_lapangan2.php?<?php echo $linkParam; ?>" class="lapangan-btn <?php echo $isActive; ?>">
                        <?php echo htmlspecialchars($lap['namaLapangan']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div class="info-box">
                    <p>Tidak ada lapangan tersedia di venue ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT SECTION -->
        <div class="right">
            <!-- Notifikasi -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error_message']; ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (!$selectedLapanganID && !empty($lapangan_list)): ?>
                <div class="info-box">
                    <p>Silakan pilih lapangan terlebih dahulu untuk melihat jadwal yang tersedia.</p>
                </div>
            <?php endif; ?>

            <?php if ($selectedLapanganID): ?>
                <div class="date-header">Jadwal untuk <?php echo date("d F Y", $timestamp); ?></div>

                <div class="jadwal-calendar-wrapper">
                    <!-- SLOT JAM -->
                    <div class="slot-container">
                        <?php
                        if (empty($jadwal_slots)) {
                            echo "<div class='slot-empty'>Tidak ada jadwal tersedia untuk tanggal ini</div>";
                        } else {
                            foreach ($jadwal_slots as $slot) {
                                $jam = date('H.i', strtotime($slot['waktuMulai'])) . " - " . date('H.i', strtotime($slot['waktuSelesai']));
                                
                                if ($slot['status'] == 'Tersedia') {
                                    $selectedClass = ($selectedJadwalID == $slot['jadwalID']) ? ' selected' : '';
                                    
                                    $linkParam = "venue_id=" . urlencode($venue_id);
                                    $linkParam .= "&lapangan_id=" . urlencode($selectedLapanganID);
                                    $linkParam .= "&tanggal=" . urlencode($selectedDate);
                                    $linkParam .= "&jadwal_id=" . $slot['jadwalID'];
                                    
                                    echo "<a href='jadwal_lapangan2.php?$linkParam' class='slot tersedia$selectedClass'>";
                                    echo "<span class='slot-status'>Bisa Dibooking</span>";
                                    echo "<span class='slot-time'>$jam</span>";
                                    echo "</a>";
                                } else {
                                    echo "<div class='slot dibooking'>";
                                    echo "<span class='slot-status'>Sudah Dibooking</span>";
                                    echo "<span class='slot-time'>$jam</span>";
                                    echo "</div>";
                                }
                            }
                        }
                        ?>
                    </div>

                    <!-- KALENDER -->
                    <div class="calendar-wrapper">
                        <div class="calendar-header">
                            <a href="jadwal_lapangan2.php?tanggal=<?php echo $prevMonthDate; ?><?php echo $param; ?>" class="calendar-nav">&lt;</a>
                            <h3><?php echo $currentMonthName_Year; ?></h3>
                            <a href="jadwal_lapangan2.php?tanggal=<?php echo $nextMonthDate; ?><?php echo $param; ?>" class="calendar-nav">&gt;</a>
                        </div>
                        <div class="calendar">
                            <?php buatKalender($currentYear, $currentMonth, $selectedDate, $param); ?>
                        </div>
                    </div>
                </div>

                <div class="btn-pilih-wrapper">
                    <?php if ($syaratTerpenuhi): ?>
                        <!-- Form untuk proses booking -->
                        <form method="POST" action="">
                            <input type="hidden" name="jadwal_id" value="<?php echo $selectedJadwalID; ?>">
                            <button type="submit" name="booking" class="btn-pilih">Booking Sekarang</button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn-pilih" disabled>Pilih Jadwal Terlebih Dahulu</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

</body>
</html>
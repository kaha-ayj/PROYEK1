<?php
session_start();
include 'config/koneksi.php';

// Debug: Lihat struktur tabel
echo "<h2>Struktur Database</h2>";

// Cek tabel venue
echo "<h3>Struktur Tabel VENUE:</h3>";
$result_venue = mysqli_query($conn, "DESCRIBE venue");
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = mysqli_fetch_assoc($result_venue)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Cek tabel lapangan
echo "<h3>Struktur Tabel LAPANGAN:</h3>";
$result_lapangan = mysqli_query($conn, "DESCRIBE lapangan");
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = mysqli_fetch_assoc($result_lapangan)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Cek data venue yang ada
echo "<h3>Data VENUE yang ada:</h3>";
$result_data = mysqli_query($conn, "SELECT * FROM venue");
echo "<table border='1'>";
if ($result_data && mysqli_num_rows($result_data) > 0) {
    $first_row = true;
    while ($row = mysqli_fetch_assoc($result_data)) {
        if ($first_row) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<th>" . $key . "</th>";
            }
            echo "</tr>";
            $first_row = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . $value . "</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>Tidak ada data venue</td></tr>";
}
echo "</table>";

mysqli_close($conn);
?>
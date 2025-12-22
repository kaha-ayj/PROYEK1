<?php
echo "Current dir: " . __DIR__ . "<br>";
echo "Parent: " . dirname(__DIR__) . "<br>";
echo "Root: " . dirname(dirname(__DIR__)) . "<br>";

$koneksi_path = __DIR__ . "/../../config/koneksi.php";
echo "Koneksi path: " . $koneksi_path . "<br>";

if (file_exists($koneksi_path)) {
    echo "✅ File EXIST!";
} else {
    echo "❌ File NOT FOUND!";
}
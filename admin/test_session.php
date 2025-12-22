<?php
session_start();
echo "<h2>🔍 SESSION ADMIN:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>✅ CEK VALIDASI:</h2>";

// Test validasi
function isAdmin() {
    if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
        return true;
    }
    return false;
}

if (isAdmin()) {
    echo "✅ SESSION VALID - Admin terdeteksi!";
} else {
    echo "❌ SESSION INVALID - Admin tidak terdeteksi!";
}
?>
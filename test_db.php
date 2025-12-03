<?php
// Simple DB connection test
echo "Testing database connection...<br><br>";

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'hotel';

echo "Host: $host<br>";
echo "User: $user<br>";
echo "Database: $database<br><br>";

$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    die("❌ Connection FAILED: " . $koneksi->connect_error);
}

echo "✅ Connection successful!<br><br>";

// Test query
$result = $koneksi->query("SELECT DATABASE() as db, VERSION() as version");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Current database: " . $row['db'] . "<br>";
    echo "MySQL version: " . $row['version'] . "<br>";
} else {
    echo "Query failed: " . $koneksi->error;
}

$koneksi->close();
?>

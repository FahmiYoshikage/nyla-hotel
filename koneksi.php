<?php
// Read database credentials from environment with sensible defaults
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password_db = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'hotel';

$koneksi = new mysqli($host, $user, $password_db, $database);

if ($koneksi->connect_error) {
    die("Koneksi database GAGAL: " . $koneksi->connect_error);
}

?> 
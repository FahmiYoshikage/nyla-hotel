<?php
include "koneksi.php";

// Pastikan ada ID yang dikirim
if (!isset($_GET['id'])) {
    echo "<script>
            alert('ID pembayaran tidak ditemukan!');
            window.location='pembayaran.php';
          </script>";
    exit;
}

$id = $_GET['id'];

// Cek apakah data ada di database
$cek = $koneksi->query("SELECT * FROM pembayaran WHERE ID_PEMBAYARAN = '$id'");
if ($cek->num_rows == 0) {
    echo "<script>
            alert('Data pembayaran tidak ditemukan!');
            window.location='pembayaran.php';
          </script>";
    exit;
}

// Proses hapus data
$hapus = $koneksi->query("DELETE FROM pembayaran WHERE ID_PEMBAYARAN = '$id'");

if ($hapus) {
    echo "<script>
            alert('✅ Data pembayaran berhasil dihapus!');
            window.location='pembayaran.php';
          </script>";
} else {
    // Jika gagal karena foreign key
    echo "<script>
            alert('❌ Gagal menghapus data: " . addslashes($koneksi->error) . "');
            window.location='pembayaran.php';
          </script>";
}
?>

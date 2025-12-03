<?php
// Panggil koneksi ke database
include "koneksi.php";

// Pastikan ada ID pelanggan yang dikirim lewat URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data pelanggan
    $sql = "DELETE FROM pelanggan WHERE ID_PELANGGAN = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data pelanggan berhasil dihapus!');
                window.location='pelanggan.php';
              </script>";
        exit;
    } else {
        // Jika gagal (biasanya karena data masih digunakan di tabel lain)
        echo "<script>
                alert('❌ Gagal menghapus data pelanggan!\\nPastikan data tidak sedang digunakan di tabel lain.\\nError: " . addslashes($koneksi->error) . "');
                window.location='pelanggan.php';
              </script>";
        exit;
    }
} else {
    // Jika tidak ada ID di URL
    echo "<script>
            alert('ID pelanggan tidak ditemukan!');
            window.location='pelanggan.php';
          </script>";
    exit;
}
?>

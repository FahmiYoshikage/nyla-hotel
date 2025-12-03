<?php
// Panggil koneksi
include "koneksi.php";

// Pastikan ada ID yang dikirim lewat URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Coba hapus data Food & Beverage
    $sql = "DELETE FROM food_n_beverage WHERE ID_FNB = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data Food & Beverage berhasil dihapus!');
                window.location='food_n_beverage.php';
              </script>";
        exit;
    } else {
        // Jika gagal (misalnya data masih terhubung dengan tabel lain)
        echo "<script>
                alert('❌ Gagal menghapus data Food & Beverage!\\nPastikan data tidak sedang digunakan di tabel lain.\\nError: " . addslashes($koneksi->error) . "');
                window.location='food_n_beverage.php';
              </script>";
        exit;
    }
} else {
    // Jika tidak ada ID di URL
    echo "<script>
            alert('ID Food & Beverage tidak ditemukan!');
            window.location='food_n_beverage.php';
          </script>";
    exit;
}
?>

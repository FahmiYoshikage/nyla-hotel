<?php
// Panggil koneksi
include "koneksi.php";

// Pastikan ada ID yang dikirim lewat URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Coba hapus data pemesanan
    $sql = "DELETE FROM pemesanan WHERE ID_PEMESANAN = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data pemesanan berhasil dihapus!');
                window.location='pemesanan.php';
              </script>";
        exit;
    } else {
        // Jika gagal (biasanya karena masih digunakan di tabel lain)
        echo "<script>
                alert('❌ Gagal menghapus data pemesanan!\\nPastikan data tidak sedang digunakan di tabel lain.\\nError: " . addslashes($koneksi->error) . "');
                window.location='pemesanan.php';
              </script>";
        exit;
    }
} else {
    // Jika tidak ada ID di URL
    echo "<script>
            alert('ID pemesanan tidak ditemukan!');
            window.location='pemesananadmin.php';
          </script>";
    exit;
}
?>

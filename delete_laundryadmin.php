<?php
// Panggil koneksi
include "koneksi.php";

// Pastikan ada ID yang dikirim lewat URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Coba hapus data laundry
    $sql = "DELETE FROM laundry WHERE ID_LAUNDRY = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data laundry berhasil dihapus!');
                window.location.href='laundry.php';
              </script>";
    } else {
        echo "<script>
                alert('❌ Gagal menghapus data: " . addslashes($koneksi->error) . "');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('⚠️ ID laundry tidak ditemukan!');
            window.location.href='laundry.php';
          </script>";
}
?>

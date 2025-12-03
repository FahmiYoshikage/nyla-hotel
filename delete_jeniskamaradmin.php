<?php
// Panggil file koneksi
include "koneksi.php";

// Periksa apakah ada parameter ID di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query hapus data
    $sql = "DELETE FROM jenis_kamar WHERE ID_JENIS_KAMAR = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('🗑️ Data jenis kamar berhasil dihapus!');
                window.location='jenis-kamaradmin.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('❌ Gagal menghapus data: " . addslashes($koneksi->error) . "');
                window.history.back();
              </script>";
        exit;
    }
} else {
    // Jika tidak ada ID di URL
    echo "<script>
            alert('ID tidak ditemukan!');
            window.location='jenis-kamaradmin.php';
          </script>";
    exit;
}
?>

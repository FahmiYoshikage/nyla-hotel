<?php
// Panggil koneksi
include "koneksi.php";

// Pastikan ada ID yang dikirim lewat URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Coba hapus data kamar
    $sql = "DELETE FROM kamar WHERE ID_KAMAR = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data kamar berhasil dihapus!');
                window.location='kamaradmin.php';
              </script>";
        exit;
    } else {
        // Jika gagal (biasanya karena masih digunakan di tabel lain)
        echo "<script>
                alert('❌ Gagal menghapus data kamar!\\nPastikan data tidak sedang digunakan di tabel lain.\\nError: " . addslashes($koneksi->error) . "');
                window.location='kamaradmin.php';
              </script>";
        exit;
    }
} else {
    // Jika tidak ada ID di URL
    echo "<script>
            alert('ID kamar tidak ditemukan!');
            window.location='kamaradmin.php';
          </script>";
    exit;
}
?>

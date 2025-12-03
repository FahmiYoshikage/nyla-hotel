<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM checkin_checkout WHERE ID_CHECKIN_OUT = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data check-in/check-out berhasil dihapus!');
                window.location='checkinout_admin.php';
              </script>";
    } else {
        echo "<script>
                alert('❌ Gagal menghapus data!');
                window.location='checkinout_admin.php';
              </script>";
    }
}
$koneksi->close();
?>

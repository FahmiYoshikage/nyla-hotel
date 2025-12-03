<?php
session_start();
include "koneksi.php";

// Cek apakah user sudah login
if(isset($_SESSION['email'])) {

    $email = $_SESSION['email'];

    // Update status pelanggan menjadi tidak aktif
    $update = "UPDATE pelanggan 
               SET STATUS_PELANGGAN='Tidak Aktif', verify_status='0'
               WHERE EMAIL='$email'";
    mysqli_query($koneksi, $update);

    // Hapus session
    session_unset();
    session_destroy();

    echo "
    <script>
        alert('Anda telah logout. Status berubah menjadi tidak aktif dan verifikasi dinonaktifkan.');
        window.location='login.php';
    </script>";
    exit;
} else {
    // Jika tidak ada session
    header("Location: login.php");
    exit;
}
?>

<?php
include 'koneksi.php'; // Pastikan koneksi.php sudah terhubung ke database

// Cek apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan sanitasi data dari form
    $id_pemesanan = $_POST['id_pemesanan'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_jenis_kamar = $_POST['id_jenis_kamar'];
    $id_kamar = $_POST['id_kamar'];
    $tgl_check_in = $_POST['tgl_check_in'];
    $jam_check_in = $_POST['jam_check_in'];
    $lama_inap = $_POST['lama_inap'];
    $tgl_check_out = $_POST['tgl_check_out'];
    $status_pembayaran = $_POST['status_pembayaran'];
    $status_checkinout = $_POST['status_checkinout'];

    // Validasi dasar
    if (empty($id_pemesanan) || empty($id_pelanggan) || empty($id_kamar) || empty($tgl_check_in) || empty($tgl_check_out)) {
        die("Error: Semua field wajib diisi.");
    }
    
    // Mulai transaksi untuk memastikan konsistensi data
    $koneksi->begin_transaction();
    $success = true;

    // --- 2. Simpan Data ke Tabel checkin_checkout ---
    $sql_insert = "INSERT INTO checkin_checkout (
                        ID_PEMESANAN, ID_PELANGGAN, ID_JENIS_KAMAR, ID_KAMAR, 
                        TGL_CHECK_IN, JAM_CHECK_IN, LAMA_INAP, TGL_CHECK_OUT, 
                        STATUS_PEMBAYARAN, STATUS_CHECKINOUT
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = $koneksi->prepare($sql_insert);

    if ($stmt_insert) {
        $stmt_insert->bind_param("iiiissssss", 
            $id_pemesanan, 
            $id_pelanggan, 
            $id_jenis_kamar, 
            $id_kamar, 
            $tgl_check_in, 
            $jam_check_in, 
            $lama_inap, 
            $tgl_check_out, 
            $status_pembayaran, 
            $status_checkinout
        );

        if (!$stmt_insert->execute()) {
            $success = false;
            echo "Error saat menyimpan data Check-In/Check-Out: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    } else {
        $success = false;
        echo "Error saat prepare statement INSERT: " . $koneksi->error;
    }

    // --- 3. Update Status Kamar ---
    if ($success) {
        // Tentukan status kamar baru. Karena ini Check-In, statusnya menjadi 'Terisi'
        // Jika Anda ingin menggunakan 'Dipesan' (booked) atau 'Check-In' silakan disesuaikan.
        // Berdasarkan status Checkinout: jika 'Check-In', status kamar menjadi 'Terisi'.
        $new_room_status = ($status_checkinout == 'Check-In') ? 'Terisi' : 'Dipesan'; 
        
        $sql_update_kamar = "UPDATE kamar SET STATUS = ? WHERE ID_KAMAR = ?";
        $stmt_update = $koneksi->prepare($sql_update_kamar);

        if ($stmt_update) {
            $stmt_update->bind_param("si", $new_room_status, $id_kamar);
            
            if (!$stmt_update->execute()) {
                $success = false;
                echo "Error saat update status kamar: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $success = false;
            echo "Error saat prepare statement UPDATE kamar: " . $koneksi->error;
        }
    }
    
    // --- 4. Selesaikan Transaksi ---
    if ($success) {
        $koneksi->commit();
        // Redirect ke halaman daftar data setelah berhasil
        header("Location: checkinout_admin.php?status=success");
        exit();
    } else {
        $koneksi->rollback();
        // Redirect atau tampilkan pesan error
        // header("Location: tambahdata_checkinoutadmin.php?status=error&msg=" . urlencode("Gagal memproses data."));
        // Jika terjadi kegagalan, kode di atas akan menampilkan pesan error secara langsung.
    }

    $koneksi->close();

} else {
    // Jika diakses tanpa melalui form submission
    header("Location: tambahdata_checkinoutadmin.php");
    exit();
}
?>
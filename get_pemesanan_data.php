<?php
include 'koneksi.php'; 

header('Content-Type: application/json');

if (isset($_POST['id_pemesanan'])) {
    $id_pemesanan = $_POST['id_pemesanan'];

    // Query untuk mendapatkan detail Pemesanan, Pelanggan, dan Jenis Kamar
    // Asumsi TGL_CHECK_IN_PESANAN diambil dari tabel pemesanan
    $sql = "SELECT 
                p.ID_PEMESANAN, 
                p.TGL_CHECK_IN AS TGL_CHECK_IN_PESANAN, 
                p.ID_PELANGGAN,
                p.ID_JENIS_KAMAR,
                l.NAMA_PELANGGAN,
                jk.JENIS_KAMAR
            FROM pemesanan p
            INNER JOIN pelanggan l ON p.ID_PELANGGAN = l.ID_PELANGGAN
            INNER JOIN jenis_kamar jk ON p.ID_JENIS_KAMAR = jk.ID_JENIS_KAMAR
            WHERE p.ID_PEMESANAN = ?";

    $stmt = $koneksi->prepare($sql);
    // Cek apakah prepare berhasil
    if ($stmt) {
        $stmt->bind_param("i", $id_pemesanan);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data Pemesanan tidak ditemukan.']);
        }
        $stmt->close();
    } else {
         echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $koneksi->error]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'ID Pemesanan tidak diterima.']);
}

$koneksi->close();
?>
<?php
include 'koneksi.php'; 

header('Content-Type: application/json');

if (isset($_POST['id_jenis_kamar'])) {
    $id_jenis_kamar = $_POST['id_jenis_kamar'];
    $status_tersedia = 'Tersedia'; // Sesuaikan dengan nilai STATUS di tabel kamar Anda (image_deb239.png)

    // Query untuk mendapatkan Kamar yang Tersedia berdasarkan Jenis Kamar
    $sql = "SELECT ID_KAMAR, NO_KAMAR 
            FROM kamar 
            WHERE ID_JENIS_KAMAR = ? AND STATUS = ?";

    $stmt = $koneksi->prepare($sql);
    
    // Cek apakah prepare berhasil
    if ($stmt) {
        $stmt->bind_param("is", $id_jenis_kamar, $status_tersedia);
        $stmt->execute();
        $result = $stmt->get_result();

        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }

        echo json_encode(['success' => true, 'rooms' => $rooms]);
        $stmt->close();
    } else {
         echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $koneksi->error]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'ID Jenis Kamar tidak diterima.']);
}

$koneksi->close();
?>
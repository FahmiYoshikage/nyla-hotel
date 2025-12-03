<?php
session_start();
// Pastikan file koneksi.php ada dan berisi koneksi MySQLi ($koneksi)
include "koneksi.php";

// Set nama file saat ini untuk digunakan di AJAX URL
$current_file = basename(__FILE__);
header('Content-Type: text/html; charset=utf-8');

// Cek koneksi di awal
if ($koneksi->connect_error) {
    die("Koneksi Database Gagal Total: " . $koneksi->connect_error);
}

// =======================================================
// === LOGIKA GENERATE KODE FNB (NON-AJAX) ===
// Dijalankan sekali saat halaman dimuat.
// =======================================================
$kode_fnb_otomatis = 'FNB001'; // Nilai default jika tabel kosong

// Kueri untuk mencari angka maksimum dari kolom KODE_FNB (contoh: mengambil 2 dari FNB002)
// Berdasarkan data Anda, nama tabelnya adalah 'fnb'.
$sql_fnb = "SELECT MAX(CAST(SUBSTRING(KODE_FNB, 4) AS UNSIGNED)) AS max_num FROM food_n_beverage"; 
$result_fnb = $koneksi->query($sql_fnb);

if ($result_fnb && $row_fnb = $result_fnb->fetch_assoc()) {
    if ($row_fnb['max_num'] !== null) { 
        // Jika FNB002 ditemukan (max_num=2), maka berikutnya adalah 3.
        $next_id = $row_fnb['max_num'] + 1; 
        $kode_fnb_otomatis = 'FNB' . str_pad($next_id, 3, '0', STR_PAD_LEFT); // Menghasilkan FNB003
    }
}
// =======================================================

// ---------------------------------------------
//  ROUTER UNTUK AJAX (Hanya tersisa GET KAMAR)
// ---------------------------------------------
if (isset($_GET['ajax'])) {
    
    header('Content-Type: application/json');
    
    // =============================
    // 1) GET NOMOR KAMAR BY PELANGGAN
    // =============================
    if ($_GET['ajax'] == "get_kamar") {

        if (!isset($_POST['id_pelanggan']) || empty($_POST['id_pelanggan'])) {
            echo json_encode(['success' => false, 'message' => 'ID Pelanggan tidak valid.']);
            exit;
        }

        $id_pelanggan = (int)$_POST['id_pelanggan'];
        // Pastikan status ini SAMA PERSIS dengan database Anda!
        $status = "Check-In"; 

        $sql = "SELECT 
                    cc.ID_KAMAR,
                    k.NO_KAMAR
                FROM checkin_checkout cc
                INNER JOIN kamar k ON cc.ID_KAMAR = k.ID_KAMAR
                WHERE cc.ID_PELANGGAN = ?
                AND cc.STATUS_CHECKINOUT = ?
                ORDER BY cc.ID_CHECKIN_OUT DESC LIMIT 1";

        $stmt = $koneksi->prepare($sql);
        
        if ($stmt === false) {
             echo json_encode(['success' => false, 'message' => 'Error kueri kamar: ' . $koneksi->error]);
             exit;
        }

        $stmt->bind_param("is", $id_pelanggan, $status);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            // Pelanggan ada, tapi tidak ditemukan sedang Check-In
            echo json_encode(['success' => false, 'message' => 'Tidak sedang Check-In.']);
        }
        $stmt->close();
        exit;
    }

    exit; 
}


// ------------------------------------------------
//  PROSES SIMPAN FNB (INSERT KE DATABASE)
// ------------------------------------------------
if (isset($_POST['simpan'])) {
    
    // Gunakan prepared statement untuk keamanan
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_kamar     = $_POST['id_kamar'];
    $kode_fnb     = $_POST['kode_fnb'];
    
    // Perhatikan: kueri INSERT Anda sebelumnya menggunakan 'food_n_beverage'
    $sql = "INSERT INTO food_n_beverage(ID_PELANGGAN, ID_KAMAR, KODE_FNB)
            VALUES (?, ?, ?)";

    $stmt = $koneksi->prepare($sql);
    if ($stmt) {
        // Asumsi ID_PELANGGAN dan ID_KAMAR adalah integer, KODE_FNB string
        $stmt->bind_param("iis", $id_pelanggan, $id_kamar, $kode_fnb);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('Data berhasil ditambahkan!');
                    window.location='food_n_beverage.php'; // Diganti ke data_fnb.php
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menyimpan: " . addslashes($stmt->error) . "');
                    window.history.back();
                  </script>";
        }
        $stmt->close();
    } else {
        echo "<script>
                alert('Gagal menyiapkan statement: " . addslashes($koneksi->error) . "');
                window.history.back();
              </script>";
    }

    exit;
}


// ======================================================
//  QUERY UNTUK SELECT NAMA PELANGGAN DI DALAM FORM
// ======================================================
$sql_pelanggan = "SELECT ID_PELANGGAN, NAMA_PELANGGAN FROM pelanggan ORDER BY NAMA_PELANGGAN";
$result_pelanggan = $koneksi->query($sql_pelanggan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Data F&B</title>

    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">

<div class="wrapper">
<?php include 'header.php'; ?>
  <?php include 'navbar.php'; ?>
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <h1>TAMBAH DATA FOOD & BEVERAGE</h1>
            </div>
        </section>

        <section class="content">

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Food & Beverage</h3>
                </div>

                <form method="POST">
                    <div class="card-body">

                        <div class="form-group">
                            <label>Nama Pelanggan</label>
                            <select class="form-control" id="id_pelanggan" name="id_pelanggan" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php while ($row = $result_pelanggan->fetch_assoc()) { ?>
                                    <option value="<?= $row['ID_PELANGGAN']; ?>"><?= htmlspecialchars($row['NAMA_PELANGGAN']); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nomor Kamar</label>
                            <select class="form-control" id="id_kamar" name="id_kamar" required disabled>
                                <option value="">-- Pilih Nomor Kamar --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kode FNB</label>
                            <input type="text" class="form-control" id="kode_fnb" name="kode_fnb" 
                                   value="<?= $kode_fnb_otomatis ?>" readonly required>
                        </div>
                        
                        </div>

                    <div class="card-footer">
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                        <a href="data_fnb.php" class="btn btn-default">Batal</a>
                    </div>
                </form>

            </div>

        </section>

    </div>
  <?php include 'footer.php'; ?>
</div>

<?php 
if (isset($koneksi)) $koneksi->close(); 
?>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
const CURRENT_FILE = 'tambahdata_fnb.php'; 

$(document).ready(function(){

    // ----------------------------
    // HILANGKAN loadKodeFnb() KARENA SUDAH DI PHP
    // ----------------------------
    // loadKodeFnb(); // Dihapus!

    // ----------------------------
    // AMBIL KAMAR BERDASARKAN ID PELANGGAN (AJAX TETAP DIPAKAI DI SINI)
    // ----------------------------
    $("#id_pelanggan").change(function(){

        var id = $(this).val();

        $("#id_kamar").html('<option>Memuat...</option>');
        $("#id_kamar").prop("disabled", true);

        if (id === "") {
             $("#id_kamar").html('<option value="">-- Pilih Nomor Kamar --</option>');
             $("#id_kamar").prop("disabled", false);
             return;
        }

        $.ajax({
            url: CURRENT_FILE + "?ajax=get_kamar",
            type: "POST",
            data: { id_pelanggan: id },
            dataType: "json",
            success: function(res){

                $("#id_kamar").prop("disabled", false);
                $("#id_kamar").html("");

                if (res.success) {
                    var room = res.data;
                    $("#id_kamar").append(
                        "<option value='"+room.ID_KAMAR+"' selected>"+room.NO_KAMAR+"</option>"
                    );
                } else {
                    // Menampilkan pesan dari server (misalnya: Tidak sedang Check-In.)
                    $("#id_kamar").append("<option value=''>"+res.message+"</option>");
                }
            },
            error: function(xhr, status, error) {
                $("#id_kamar").prop("disabled", false);
                $("#id_kamar").html("<option value=''>Gagal memuat kamar (Cek Konsol)</option>");
                console.error("AJAX Error Kamar: " + status + ", " + error, xhr.responseText);
            }
        });

    });

});
</script>

</body>
</html>
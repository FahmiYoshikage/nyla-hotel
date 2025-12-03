<?php
// Panggil file koneksi
include "koneksi.php";

// Set nama file saat ini untuk digunakan di AJAX URL
$current_file = basename(__FILE__);

// Cek koneksi di awal
if ($koneksi->connect_error) {
    die("Koneksi Database Gagal Total: " . $koneksi->connect_error);
}

// =======================================================
// === LOGIKA GENERATE KODE LAUNDRY OTOMATIS (NON-AJAX) ===
// =======================================================
$kode_laundry_otomatis = 'LDR001'; // Nilai default awal

// Kueri untuk mencari angka maksimum dari kolom KODE_LAUNDRY (Sesuai Koreksi: KODE_LAUNDRY)
$sql_laundry = "SELECT MAX(CAST(SUBSTRING(KODE_LAUNDRY, 4) AS UNSIGNED)) AS max_num FROM laundry"; 
$result_laundry = $koneksi->query($sql_laundry);

if ($result_laundry && $row_laundry = $result_laundry->fetch_assoc()) {
    if ($row_laundry['max_num'] !== null) { 
        $next_id = $row_laundry['max_num'] + 1; 
        $kode_laundry_otomatis = 'LDR' . str_pad($next_id, 3, '0', STR_PAD_LEFT); 
    }
}
// =======================================================

// ---------------------------------------------
//  ROUTER UNTUK AJAX (GET KAMAR)
// ---------------------------------------------
if (isset($_GET['ajax'])) {
    
    header('Content-Type: application/json');
    
    // =============================
    // 1) GET NOMOR KAMAR BY PELANGGAN (AJAX)
    // =============================
    if ($_GET['ajax'] == "get_kamar") {

        if (!isset($_POST['id_pelanggan']) || empty($_POST['id_pelanggan'])) {
            echo json_encode(['success' => false, 'message' => 'ID Pelanggan tidak valid.']);
            exit;
        }

        $id_pelanggan = (int)$_POST['id_pelanggan'];
        $status = "Check-In"; // Ambil kamar yang sedang Check-In

        // Kueri untuk mengambil ID Kamar dan No Kamar yang sedang ditempati pelanggan
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
            echo json_encode(['success' => false, 'message' => 'Pelanggan tidak sedang Check-In.']);
        }
        $stmt->close();
        exit;
    }

    exit; 
}


// Proses penyimpanan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Gunakan Prepared Statement untuk keamanan!
    // Ambil ID Kamar dari hidden input (yang diisi oleh AJAX)
    $id_kamar = $_POST['hidden_id_kamar']; 
    $id_pelanggan = $_POST['id_pelanggan'];
    $kode_laundry = $_POST['kode_laundry']; 

    // Cek duplikasi kode
    $cek_stmt = $koneksi->prepare("SELECT KODE_LAUNDRY FROM laundry WHERE KODE_LAUNDRY = ?");
    $cek_stmt->bind_param("s", $kode_laundry);
    $cek_stmt->execute();
    $cek_stmt->store_result();
    
    if ($cek_stmt->num_rows > 0) {
        $cek_stmt->close();
        echo "<script>
                alert('⚠️ Kode laundry sudah digunakan, silakan masukkan kode lain! Kode yang terisi: {$kode_laundry}');
                window.history.back();
              </script>";
        exit;
    }
    $cek_stmt->close();

    // Query tambah data ke tabel laundry
    $sql_insert = "INSERT INTO laundry (ID_KAMAR, ID_PELANGGAN, KODE_LAUNDRY)
                   VALUES (?, ?, ?)";
    
    $insert_stmt = $koneksi->prepare($sql_insert);
    if ($insert_stmt) {
        $insert_stmt->bind_param("iis", $id_kamar, $id_pelanggan, $kode_laundry);

        if ($insert_stmt->execute()) {
            echo "<script>
                    alert('✅ Data laundry berhasil ditambahkan! Kode: {$kode_laundry}');
                    window.location='laundry.php';
                  </script>";
            exit;
        } else {
            echo "<script>
                    alert('❌ Gagal menambahkan data: " . addslashes($insert_stmt->error) . "');
                    window.history.back();
                  </script>";
            exit;
        }
        $insert_stmt->close();
    } else {
        echo "<script>
                alert('❌ Gagal menyiapkan statement INSERT: " . addslashes($koneksi->error) . "');
                window.history.back();
              </script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Data Laundry</title>

  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'header.php'; // Dikomentari karena file tidak disediakan ?>
  <?php include 'navbar.php'; // Dikomentari karena file tidak disediakan ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1>Tambah Data Laundry</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Tambah Laundry</h3>
          </div>

          <form action="" method="POST">
            <div class="card-body">

              <div class="form-group">
                <label for="id_pelanggan">Nama Pelanggan</label>
                <select class="form-control" id="id_pelanggan" name="id_pelanggan" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php
                    // Ambil data pelanggan
                    $queryPelanggan = $koneksi->query("SELECT ID_PELANGGAN, NAMA_PELANGGAN FROM pelanggan");
                    while ($data = $queryPelanggan->fetch_assoc()) {
                        echo "<option value='{$data['ID_PELANGGAN']}'>{$data['NAMA_PELANGGAN']}</option>";
                    }
                    ?>
                </select>
              </div>

              <div class="form-group">
                <label for="id_kamar">Nomor Kamar</label>
                <select class="form-control" id="id_kamar_display" required disabled> 
                    <option value="">-- Pilih Pelanggan Dahulu --</option>
                    <?php
                    // Ambil data kamar (Tidak digunakan jika AJAX berhasil, tapi tetap di sini jika form sederhana)
                    $queryKamar = $koneksi->query("SELECT ID_KAMAR, NO_KAMAR FROM kamar");
                    while ($data = $queryKamar->fetch_assoc()) {
                        // Tidak perlu di echo, karena akan digantikan AJAX
                    }
                    ?>
                </select>
                <input type="hidden" id="hidden_id_kamar" name="hidden_id_kamar" value="">
              </div>

              <div class="form-group">
                <label for="kode_laundry">Kode Laundry</label>
                <input type="text" class="form-control" id="kode_laundry" name="kode_laundry"
                        value="<?= $kode_laundry_otomatis; ?>" readonly required>
              </div>

            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
          </div>
      </div>
    </section>
  </div>

  <?php include 'footer.php'; // Dikomentari karena file tidak disediakan ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
// Nama file ini sendiri untuk endpoint AJAX
const CURRENT_FILE = 'tambahdata_laundryadmin.php'; 

$(document).ready(function(){
    
    // Hilangkan name dari select kamar agar tidak mengirim nilai 'disabled'
    $("#id_kamar_display").removeAttr("name"); 

    // ----------------------------
    // AMBIL KAMAR BERDASARKAN ID PELANGGAN (Via AJAX)
    // ----------------------------
    $("#id_pelanggan").change(function(){

        var id = $(this).val();

        // Tampilan loading
        $("#id_kamar_display").html('<option>Memuat...</option>');
        $("#id_kamar_display").prop("disabled", true);
        $("#hidden_id_kamar").val(''); // Reset hidden input

        if (id === "") {
             $("#id_kamar_display").html('<option value="">-- Pilih Pelanggan Dahulu --</option>');
             $("#id_kamar_display").prop("disabled", true); // Tetap disabled jika belum ada pilihan
             return;
        }

        $.ajax({
            url: CURRENT_FILE + "?ajax=get_kamar",
            type: "POST",
            data: { id_pelanggan: id },
            dataType: "json",
            success: function(res){

                $("#id_kamar_display").prop("disabled", false);
                $("#id_kamar_display").html(""); // Bersihkan pilihan sebelumnya

                if (res.success) {
                    var room = res.data;
                    
                    // Isi dropdown display dengan Nomor Kamar
                    $("#id_kamar_display").append(
                        "<option value='"+room.ID_KAMAR+"' selected>"+room.NO_KAMAR+"</option>"
                    );
                    
                    // PENTING: Set nilai ID Kamar ke input hidden agar terkirim saat form disubmit
                    $("#hidden_id_kamar").val(room.ID_KAMAR);
                    
                } else {
                    // Menampilkan pesan error dari server 
                    $("#id_kamar_display").append("<option value=''>Data tidak ditemukan.</option>");
                    $("#id_kamar_display").prop("disabled", true); // Disable jika gagal
                    console.warn("Peringatan Kamar:", res.message);
                }
            },
            error: function(xhr, status, error) {
                $("#id_kamar_display").prop("disabled", true);
                $("#id_kamar_display").html("<option value=''>Gagal memuat kamar (AJAX Error)</option>");
                $("#hidden_id_kamar").val(''); 
                console.error("AJAX Error Kamar: " + status + ", " + error, xhr.responseText);
            }
        });

    });

});
</script>
</body>
</html>
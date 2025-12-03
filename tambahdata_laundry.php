<?php
// Panggil file koneksi
include "koneksi.php";

// Cek koneksi di awal
if ($koneksi->connect_error) {
    die("Koneksi Database Gagal Total: " . $koneksi->connect_error);
}

// =======================================================
// === VARIABEL UNTUK MENGISI FORM SECARA OTOMATIS ===
// =======================================================
$selected_id_laundry = '';
$selected_id_pelanggan = '';
$selected_nama_pelanggan = '-- Isi Otomatis --';
$selected_no_kamar = '-- Isi Otomatis --';

// Cek apakah form di-submit untuk mendapatkan ID_LAUNDRY (Reload Form)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_laundry'])) {
    
    $selected_id_laundry = (int)$_POST['id_laundry'];

    if ($selected_id_laundry > 0) {
        
        // Kueri untuk mengambil ID_PELANGGAN, NAMA_PELANGGAN, dan NO_KAMAR
        $sql_lookup = "SELECT 
                            l.ID_PELANGGAN,
                            p.NAMA_PELANGGAN,
                            k.NO_KAMAR
                        FROM laundry l
                        INNER JOIN pelanggan p ON l.ID_PELANGGAN = p.ID_PELANGGAN
                        INNER JOIN kamar k ON l.ID_KAMAR = k.ID_KAMAR
                        WHERE l.ID_LAUNDRY = ? LIMIT 1";

        $stmt_lookup = $koneksi->prepare($sql_lookup);
        
        if ($stmt_lookup) {
            $stmt_lookup->bind_param("i", $selected_id_laundry);
            $stmt_lookup->execute();
            $result_lookup = $stmt_lookup->get_result();

            if ($row = $result_lookup->fetch_assoc()) {
                // Data ditemukan, isi variabel
                $selected_id_pelanggan = $row['ID_PELANGGAN'];
                $selected_nama_pelanggan = $row['NAMA_PELANGGAN'];
                $selected_no_kamar = $row['NO_KAMAR'];
            } else {
                // Data relasi tidak valid
                echo "<script>alert('Kode Laundry: Data pelanggan atau kamar terkait tidak valid di database. Periksa ID_PELANGGAN dan ID_KAMAR di tabel laundry.');</script>";
            }
            $stmt_lookup->close();
        } else {
            // Error SQL
            echo "<script>alert('Error: Gagal menyiapkan kueri data otomatis.');</script>";
        }
    }
}


// ------------------------------------------------
//  PROSES SIMPAN DETAIL LAUNDRY (INSERT KE DATABASE)
// ------------------------------------------------
// Kita bedakan dengan submit button 'simpan'
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan_data'])) {
    
    // Ambil data POST (ID_PELANGGAN sekarang diambil dari hidden input)
    $id_laundry      = $_POST['id_laundry'];
    $id_pelanggan    = $_POST['hidden_id_pelanggan']; // AMBIL DARI INPUT HIDDEN
    $tgl_laundry     = $_POST['tgl_laundry'];
    $jam_laundry     = $_POST['jam_laundry'];
    $jenis_laundry   = $_POST['jenis_laundry'];
    $harga_satuan    = $_POST['harga_satuan'];
    $jumlah_laundry  = $_POST['jumlah_laundry'];
    
    // Validasi dasar
    if (empty($id_pelanggan) || empty($id_laundry) || (int)$id_pelanggan === 0) {
         echo "<script>
                alert('❌ Gagal menyimpan: ID Pelanggan atau ID Laundry kosong/tidak valid. Pilih Kode Laundry dan tunggu form terisi otomatis.');
                window.history.back();
              </script>";
        exit;
    }

    // Hitung Total Laundry
    $harga_satuan_val = (int)$harga_satuan;
    $jumlah_laundry_val = (int)$jumlah_laundry;
    $total_laundry = $harga_satuan_val * $jumlah_laundry_val;
    
    // Query tambah data ke tabel detail_laundry
    $sql_insert = "INSERT INTO detail_laundry 
                   (ID_LAUNDRY, ID_PELANGGAN, TGL_LAUNDRY, JAM_LAUNDRY, JENIS_LAUNDRY, HARGA_SATUAN, JUMLAH_LAUNDRY, TOTAL_LAUNDRY)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $insert_stmt = $koneksi->prepare($sql_insert);
    
    if ($insert_stmt) {
        $insert_stmt->bind_param("iissisii", 
                                $id_laundry, 
                                $id_pelanggan, 
                                $tgl_laundry, 
                                $jam_laundry, 
                                $jenis_laundry, 
                                $harga_satuan_val, 
                                $jumlah_laundry_val, 
                                $total_laundry);

        if ($insert_stmt->execute()) {
            echo "<script>
                    alert('✅ Data Detail Laundry berhasil ditambahkan! Total: Rp " . number_format($total_laundry, 0, ',', '.') . "');
                    window.location='detail_laundry.php'; 
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


// ======================================================
//  QUERY UNTUK SELECT DATA DI DALAM FORM
// ======================================================

// Data Pelanggan (Hanya untuk mapping di bagian select pelanggan)
$sql_pelanggan = "SELECT ID_PELANGGAN, NAMA_PELANGGAN FROM pelanggan ORDER BY NAMA_PELANGGAN";
$result_pelanggan = $koneksi->query($sql_pelanggan);

// Data Laundry (untuk dropdown Kode Laundry)
$sql_laundry_codes = "SELECT ID_LAUNDRY, KODE_LAUNDRY FROM laundry ORDER BY ID_LAUNDRY DESC";
$result_laundry_codes = $koneksi->query($sql_laundry_codes);

// Set tanggal dan jam saat ini sebagai default
$tgl_sekarang = date('Y-m-d');
$jam_sekarang = date('H:i');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Data Detail Laundry</title>

  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php
  include 'header.php';
  include 'navbar.php';
  ?>
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1>Tambah Data Detail Laundry</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Tambah Detail Laundry</h3>
          </div>

          <form action="" method="POST" id="laundryForm">
            <div class="card-body">

              <div class="form-group">
                <label for="id_laundry">Kode Laundry (Transaksi Induk)</label>
                <select class="form-control" id="id_laundry" name="id_laundry" required 
                        onchange="document.getElementById('laundryForm').submit();">
                    <option value="">-- Pilih Kode Laundry --</option>
                    <?php 
                    while ($row = $result_laundry_codes->fetch_assoc()) { 
                        $selected = ($row['ID_LAUNDRY'] == $selected_id_laundry) ? 'selected' : '';
                    ?>
                        <option value="<?= $row['ID_LAUNDRY']; ?>" <?= $selected; ?>>
                            <?= htmlspecialchars($row['KODE_LAUNDRY']); ?>
                        </option>
                    <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label for="id_pelanggan">Nama Pelanggan</label>
                <select class="form-control" id="id_pelanggan" required disabled> 
                    <option value="">-- <?= htmlspecialchars($selected_nama_pelanggan); ?> --</option>
                    <?php 
                    // Tampilkan semua pelanggan untuk memastikan ID terpilih muncul
                    if ($result_pelanggan->num_rows > 0) $result_pelanggan->data_seek(0);
                    while ($data = $result_pelanggan->fetch_assoc()) {
                        $selected_option = ($data['ID_PELANGGAN'] == $selected_id_pelanggan) ? 'selected' : '';
                        echo "<option value='{$data['ID_PELANGGAN']}' {$selected_option}>{$data['NAMA_PELANGGAN']}</option>";
                    }
                    ?>
                </select>
                <input type="hidden" id="hidden_id_pelanggan" name="hidden_id_pelanggan" 
                       value="<?= htmlspecialchars($selected_id_pelanggan); ?>">
              </div>

              <div class="form-group">
                <label for="id_kamar">Nomor Kamar</label>
                <input type="text" class="form-control" id="no_kamar_display" 
                       value="<?= htmlspecialchars($selected_no_kamar); ?>" required readonly> 
              </div>
              
              <hr>
              
              <div class="form-group">
                <label for="tgl_laundry">Tanggal Laundry</label>
                <input type="date" class="form-control" id="tgl_laundry" name="tgl_laundry" 
                       value="<?= $tgl_sekarang; ?>" required>
              </div>

              <div class="form-group">
                <label for="jam_laundry">Jam Laundry</label>
                <input type="time" class="form-control" id="jam_laundry" name="jam_laundry" 
                       value="<?= $jam_sekarang; ?>" required>
              </div>

              <div class="form-group">
                <label for="jenis_laundry">Jenis Laundry</label>
                <input type="text" class="form-control" id="jenis_laundry" name="jenis_laundry" 
                       placeholder="Contoh: Baju, Seprai, Handuk" required>
              </div>

              <div class="form-group">
                <label for="harga_satuan">Harga Satuan (Rp)</label>
                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" 
                       value="0" min="0" required>
              </div>

              <div class="form-group">
                <label for="jumlah_laundry">Jumlah</label>
                <input type="number" class="form-control" id="jumlah_laundry" name="jumlah_laundry" 
                       value="1" min="1" required>
              </div>

              <div class="form-group">
                <label>Total Laundry (Rp)</label>
                <input type="text" class="form-control" id="total_laundry_display" value="Rp 0" readonly>
              </div>

            </div>
            <div class="card-footer">
              <button type="submit" name="simpan_data" class="btn btn-primary">Simpan</button>
            </div>
          </form>
          </div>
      </div>
    </section>
  </div>
<?php include 'footer.php'; ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function(){
    // Fungsi Menghitung Total Otomatis (HARGA * JUMLAH) - Tetap menggunakan JS karena ini interaksi di sisi klien
    function calculateTotal() {
        const harga = parseFloat($("#harga_satuan").val()) || 0;
        const jumlah = parseFloat($("#jumlah_laundry").val()) || 0;
        const total = harga * jumlah;
        
        $("#total_laundry_display").val('Rp ' + total.toLocaleString('id-ID'));
    }

    // Panggil fungsi saat input harga atau jumlah berubah
    $("#harga_satuan, #jumlah_laundry").on('input', calculateTotal);
    calculateTotal(); 
    
    // Perhatikan: Tidak ada lagi kode AJAX di sini.
});
</script>
</body>
</html>
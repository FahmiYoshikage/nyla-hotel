<?php
// Panggil file koneksi
include "koneksi.php";


// ===========================
// AUTO GENERATE KODE FNB
// ===========================
$queryKode = $koneksi->query("SELECT KODE_FNB FROM food_n_beverage ORDER BY KODE_FNB DESC LIMIT 1");

if ($queryKode->num_rows > 0) {
    $row = $queryKode->fetch_assoc();
    $lastKode = $row['KODE_FNB'];
    $num = intval(substr($lastKode, 3)) + 1;
    $kode_fnb_baru = "FNB" . str_pad($num, 3, "0", STR_PAD_LEFT);
} else {
    $kode_fnb_baru = "FNB001"; // Default jika belum ada data
}


// ===========================
// PROSES SIMPAN DATA
// ===========================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_kamar = $_POST['id_kamar'];
    $kode_fnb = $_POST['kode_fnb'];

    // Query tambah data
    $sql = "INSERT INTO food_n_beverage (ID_PELANGGAN, ID_KAMAR, KODE_FNB)
            VALUES ('$id_pelanggan', '$id_kamar', '$kode_fnb')";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data Food & Beverage berhasil ditambahkan!');
                window.location='food_n_beverage.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('❌ Gagal menambahkan data!');
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
  <title>Tambah Food & Beverage</title>

  <!-- CSS -->
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
        <h1>Tambah Data Food & Beverage</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Tambah Food & Beverage</h3>
          </div>

          <!-- Form -->
          <form action="" method="POST">
            <div class="card-body">

              <!-- Pilih Pelanggan -->
              <div class="form-group">
                <label for="id_pelanggan">Nama Pelanggan</label>
                <select class="form-control" id="id_pelanggan" name="id_pelanggan" required>
                  <option value="">-- Pilih Pelanggan --</option>
                  <?php
                  $pelanggan = $koneksi->query("SELECT ID_PELANGGAN, NAMA_PELANGGAN FROM pelanggan");
                  while ($p = $pelanggan->fetch_assoc()) {
                      echo "<option value='{$p['ID_PELANGGAN']}'>{$p['NAMA_PELANGGAN']}</option>";
                  }
                  ?>
                </select>
              </div>

              <!-- Pilih Kamar -->
              <div class="form-group">
                <label for="id_kamar">Nomor Kamar</label>
                <select class="form-control" id="id_kamar" name="id_kamar" required>
                  <option value="">-- Pilih Nomor Kamar --</option>
                  <?php
                  $kamar = $koneksi->query("SELECT ID_KAMAR, NO_KAMAR FROM kamar");
                  while ($k = $kamar->fetch_assoc()) {
                      echo "<option value='{$k['ID_KAMAR']}'>{$k['NO_KAMAR']}</option>";
                  }
                  ?>
                </select>
              </div>

              <!-- Kode FNB Otomatis -->
              <div class="form-group">
                <label for="kode_fnb">Kode FNB</label>
                <input type="text" class="form-control" id="kode_fnb" name="kode_fnb"
                       value="<?= $kode_fnb_baru ?>" readonly>
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

  <?php include 'footer.php'; ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

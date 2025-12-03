<?php
// Panggil file koneksi
include "koneksi.php";

// Proses penyimpanan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis_kamar = $_POST['jenis_kamar'];
    $fasilitas = $_POST['fasilitas'];
    $tarif = $_POST['tarif'];

    // Query tambah data
    $sql = "INSERT INTO jenis_kamar (JENIS_KAMAR, FASILITAS, TARIF)
            VALUES ('$jenis_kamar', '$fasilitas', '$tarif')";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data jenis kamar berhasil ditambahkan!');
                window.location='jenis-kamaradmin.php';
              </script>";
        exit; // pastikan tidak lanjut render HTML lagi
    } else {
        echo "<script>
                alert('❌ Gagal menambahkan data: " . addslashes($koneksi->error) . "');
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
  <title>Tambah Jenis Kamar</title>

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
        <h1>Tambah Jenis Kamar</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Tambah Jenis Kamar</h3>
          </div>

          <!-- ✅ Form dimulai di sini -->
          <form action="" method="POST">
            <div class="card-body">

              <div class="form-group">
                <label for="jenis_kamar">Jenis Kamar</label>
                <input type="text" class="form-control" id="jenis_kamar" name="jenis_kamar"
                       placeholder="Masukkan Jenis Kamar" required>
              </div>

              <div class="form-group">
                <label for="fasilitas">Fasilitas</label>
                <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3"
                          placeholder="Masukkan Fasilitas Kamar" required></textarea>
              </div>

              <div class="form-group">
                <label for="tarif">Tarif (Rp)</label>
                <input type="text" class="form-control" id="tarif" name="tarif"
                        placeholder="Masukkan tarif kamar, contoh: 250000" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>


            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
          <!-- ✅ Form berakhir di sini -->

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

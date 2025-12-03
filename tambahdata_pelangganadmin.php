<?php
// Panggil file koneksi
include "koneksi.php";

// Proses penyimpanan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $alamat         = $_POST['alamat'];
    $kota           = $_POST['kota'];
    $no_tlp         = $_POST['no_tlp'];
    $email          = $_POST['email'];

    // Query tambah data pelanggan
    $sql = "INSERT INTO pelanggan (NAMA_PELANGGAN, JENIS_KELAMIN, ALAMAT, KOTA, NO_TLP, EMAIL)
            VALUES ('$nama_pelanggan', '$jenis_kelamin', '$alamat', '$kota', '$no_tlp', '$email')";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data pelanggan berhasil ditambahkan!');
                window.location='pelanggan.php';
              </script>";
        exit;
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
  <title>Tambah Data Pelanggan</title>

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
        <h1>Tambah Data Pelanggan</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Tambah Pelanggan</h3>
          </div>

          <!-- ✅ Form dimulai di sini -->
          <form action="" method="POST">
            <div class="card-body">

              <div class="form-group">
                <label for="nama_pelanggan">Nama Pelanggan</label>
                <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                       placeholder="Masukkan Nama Pelanggan" required>
              </div>

              <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                  <option value="">-- Pilih Jenis Kelamin --</option>
                  <option value="Laki-Laki">Laki-Laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>

              <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3"
                          placeholder="Masukkan Alamat Lengkap" required></textarea>
              </div>

              <div class="form-group">
                <label for="kota">Kota</label>
                <input type="text" class="form-control" id="kota" name="kota"
                       placeholder="Masukkan Nama Kota" required>
              </div>

              <div class="form-group">
                <label for="no_tlp">No Telepon</label>
                <input type="text" class="form-control" id="no_tlp" name="no_tlp"
                       placeholder="Masukkan Nomor Telepon" required
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       placeholder="Masukkan Email Pelanggan" required>
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

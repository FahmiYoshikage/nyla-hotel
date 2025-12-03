<?php
// Panggil file koneksi
include "koneksi.php";

// Proses penyimpanan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis_kamar = $_POST['jenis_kamar'];
    $no_kamar = $_POST['no_kamar'];
    $status = $_POST['status'];
$cek = $koneksi->query("SELECT * FROM kamar WHERE NO_KAMAR = '$no_kamar'");
if ($cek->num_rows > 0) {
    echo "<script>
            alert('⚠️ Nomor kamar sudah ada, silakan gunakan nomor lain!');
            window.history.back();
          </script>";
    exit;
}
    // Query tambah data ke tabel kamar
    $sql = "INSERT INTO kamar (ID_JENIS_KAMAR, NO_KAMAR, STATUS)
            VALUES ('$jenis_kamar', '$no_kamar', '$status')";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data kamar berhasil ditambahkan!');
                window.location='kamaradmin.php';
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
  <title>Tambah Kamar</title>

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
        <h1>Tambah Data Kamar</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Tambah Kamar</h3>
          </div>

          <!-- ✅ Form dimulai di sini -->
          <form action="" method="POST">
            <div class="card-body">

              <div class="form-group">
                <label for="jenis_kamar">Jenis Kamar</label>
                <select class="form-control" id="jenis_kamar" name="jenis_kamar" required>
                    <option value="">-- Pilih Jenis Kamar --</option>
                    <?php
                    // Ambil data dari tabel jenis_kamar
                    $query = $koneksi->query("SELECT ID_JENIS_KAMAR, JENIS_KAMAR FROM jenis_kamar");
                    while ($data = $query->fetch_assoc()) {
                        echo "<option value='{$data['ID_JENIS_KAMAR']}'>{$data['JENIS_KAMAR']}</option>";
                    }
                    ?>
                </select>
                </div>

              <div class="form-group">
                <label for="no_kamar">Nomor Kamar</label>
                <input type="text" class="form-control" id="no_kamar" name="no_kamar"
                       placeholder="Masukkan Nomor Kamar" required>
              </div>

              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                  <option value="">-- Pilih Status --</option>
                  <option value="Tersedia">Tersedia</option>
                  <option value="Terisi">Terisi</option>
                </select>
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

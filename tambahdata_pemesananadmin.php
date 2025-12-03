<?php
// Panggil file koneksi
include "koneksi.php";

// Proses penyimpanan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_jenis_kamar = $_POST['id_jenis_kamar'];
    $id_pelanggan   = $_POST['id_pelanggan'];
    $tgl_pesan      = $_POST['tgl_pesan'];
    $jam_pesan      = $_POST['jam_pesan'];
    $uang_muka      = $_POST['uang_muka'];

    // Query tambah data ke tabel pemesanan
    $sql = "INSERT INTO pemesanan (ID_JENIS_KAMAR, ID_PELANGGAN, TGL_PESAN, JAM_PESAN, UANG_MUKA)
            VALUES ('$id_jenis_kamar', '$id_pelanggan', '$tgl_pesan', '$jam_pesan', '$uang_muka')";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data pemesanan berhasil ditambahkan!');
                window.location='pemesanan.php';
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

// Ambil data jenis kamar & pelanggan untuk combo box
$jenis_kamar = $koneksi->query("SELECT * FROM jenis_kamar");
$pelanggan = $koneksi->query("SELECT * FROM pelanggan");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Data Pemesanan</title>

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
        <h1>Tambah Data Pemesanan</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Tambah Pemesanan</h3>
          </div>

          <!-- ✅ Form dimulai di sini -->
          <form action="" method="POST">
            <div class="card-body">

              <div class="form-group">
                <label for="id_jenis_kamar">Jenis Kamar</label>
                <select class="form-control" id="id_jenis_kamar" name="id_jenis_kamar" required>
                  <option value="">-- Pilih Jenis Kamar --</option>
                  <?php while($row = $jenis_kamar->fetch_assoc()) { ?>
                    <option value="<?= $row['ID_JENIS_KAMAR']; ?>"><?= $row['JENIS_KAMAR']; ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label for="id_pelanggan">Nama Pelanggan</label>
                <select class="form-control" id="id_pelanggan" name="id_pelanggan" required>
                  <option value="">-- Pilih Pelanggan --</option>
                  <?php while($row = $pelanggan->fetch_assoc()) { ?>
                    <option value="<?= $row['ID_PELANGGAN']; ?>"><?= $row['NAMA_PELANGGAN']; ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label for="tgl_pesan">Tanggal Pesan</label>
                <input type="date" class="form-control" id="tgl_pesan" name="tgl_pesan" required>
              </div>

              <div class="form-group">
                <label for="jam_pesan">Jam Pesan</label>
                <input type="time" class="form-control" id="jam_pesan" name="jam_pesan" required>
              </div>

              <div class="form-group">
                <label for="uang_muka">Uang Muka (Rp)</label>
                <input type="text" class="form-control" id="uang_muka" name="uang_muka"
                       placeholder="Masukkan uang muka, contoh: 250000" required
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

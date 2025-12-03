<?php
include "koneksi.php";

// Pastikan ada parameter id
if (!isset($_GET['id'])) {
    echo "<script>
            alert('ID Pemesanan tidak ditemukan!');
            window.location='data_pemesananadmin.php';
          </script>";
    exit;
}

$id = $_GET['id'];

// Ambil data berdasarkan ID_PESANAN
$query = $koneksi->query("SELECT * FROM pemesanan WHERE ID_PESANAN = '$id'");
if ($query->num_rows == 0) {
    echo "<script>
            alert('Data tidak ditemukan!');
            window.location='pemesanan.php';
          </script>";
    exit;
}
$data = $query->fetch_assoc();

// Ambil data jenis kamar dan pelanggan untuk dropdown
$jenis_kamar = $koneksi->query("SELECT * FROM jenis_kamar");
$pelanggan = $koneksi->query("SELECT * FROM pelanggan");

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_jenis_kamar = $_POST['id_jenis_kamar'];
    $id_pelanggan   = $_POST['id_pelanggan'];
    $tgl_pesan      = $_POST['tgl_pesan'];
    $jam_pesan      = $_POST['jam_pesan'];
    $uang_muka      = $_POST['uang_muka'];

    $update = $koneksi->query("UPDATE pemesanan 
        SET ID_JENIS_KAMAR = '$id_jenis_kamar',
            ID_PELANGGAN   = '$id_pelanggan',
            TLG_PESAN      = '$tgl_pesan',
            JAM_PESAN      = '$jam_pesan',
            UANG_MUKA      = '$uang_muka'
        WHERE ID_PESANAN   = '$id'");

    if ($update) {
        echo "<script>
                alert('✅ Data pemesanan berhasil diperbarui!');
                window.location='pemesanan.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('❌ Gagal memperbarui data: " . addslashes($koneksi->error) . "');
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
  <title>Update Data Pemesanan</title>

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
        <h1>Update Data Pemesanan</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Update Pemesanan</h3>
          </div>

          <!-- ✅ Form dimulai di sini -->
          <form action="" method="POST">
            <div class="card-body">

              <div class="form-group">
                <label for="id_jenis_kamar">Jenis Kamar</label>
                <select class="form-control" id="id_jenis_kamar" name="id_jenis_kamar" required>
                  <option value="">-- Pilih Jenis Kamar --</option>
                  <?php while($row = $jenis_kamar->fetch_assoc()) { ?>
                    <option value="<?= $row['ID_JENIS_KAMAR']; ?>" 
                      <?= ($data['ID_JENIS_KAMAR'] == $row['ID_JENIS_KAMAR']) ? 'selected' : ''; ?>>
                      <?= $row['JENIS_KAMAR']; ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label for="id_pelanggan">Nama Pelanggan</label>
                <select class="form-control" id="id_pelanggan" name="id_pelanggan" required>
                  <option value="">-- Pilih Pelanggan --</option>
                  <?php while($row = $pelanggan->fetch_assoc()) { ?>
                    <option value="<?= $row['ID_PELANGGAN']; ?>"
                      <?= ($data['ID_PELANGGAN'] == $row['ID_PELANGGAN']) ? 'selected' : ''; ?>>
                      <?= $row['NAMA_PELANGGAN']; ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label for="tgl_pesan">Tanggal Pesan</label>
                <input type="date" class="form-control" id="tgl_pesan" name="tgl_pesan" 
                       value="<?= $data['TLG_PESAN']; ?>" required>
              </div>

              <div class="form-group">
                <label for="jam_pesan">Jam Pesan</label>
                <input type="time" class="form-control" id="jam_pesan" name="jam_pesan"
                       value="<?= $data['JAM_PESAN']; ?>" required>
              </div>

              <div class="form-group">
                <label for="uang_muka">Uang Muka (Rp)</label>
                <input type="text" class="form-control" id="uang_muka" name="uang_muka"
                       value="<?= $data['UANG_MUKA']; ?>" 
                       placeholder="Masukkan uang muka" required
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
              </div>

            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="data_pemesananadmin.php" class="btn btn-secondary">Batal</a>
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

<?php
include "koneksi.php";

// Pastikan ada ID yang dikirim
if (!isset($_GET['id'])) {
    echo "<script>
            alert('ID pembayaran tidak ditemukan!');
            window.location='pembayaran.php';
          </script>";
    exit;
}

$id = $_GET['id'];

// Ambil data pembayaran berdasarkan ID
$sql = "SELECT * FROM pembayaran WHERE ID_PEMBAYARAN = '$id'";
$result = $koneksi->query($sql);

if ($result->num_rows == 0) {
    echo "<script>
            alert('Data pembayaran tidak ditemukan!');
            window.location='pembayaran.php';
          </script>";
    exit;
}

$data = $result->fetch_assoc();

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_jenis_kamar = $_POST['id_jenis_kamar'];
    $id_pelanggan = $_POST['id_pelanggan'];

    $update = "UPDATE pembayaran 
               SET ID_JENIS_KAMAR = '$id_jenis_kamar',
                   ID_PELANGGAN = '$id_pelanggan'
               WHERE ID_PEMBAYARAN = '$id'";

    if ($koneksi->query($update) === TRUE) {
        echo "<script>
                alert('✅ Data pembayaran berhasil diperbarui!');
                window.location='pembayaranadmin.php';
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
  <title>Update Pembayaran</title>

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
        <h1>Update Data Pembayaran</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Update Pembayaran</h3>
          </div>

          <!-- ✅ Form Update -->
          <form action="" method="POST">
            <div class="card-body">

              <div class="form-group">
                <label for="id_jenis_kamar">Jenis Kamar</label>
                <select class="form-control" id="id_jenis_kamar" name="id_jenis_kamar" required>
                    <option value="">-- Pilih Jenis Kamar --</option>
                    <?php
                    $query = $koneksi->query("SELECT ID_JENIS_KAMAR, JENIS_KAMAR FROM jenis_kamar");
                    while ($row = $query->fetch_assoc()) {
                        $selected = ($row['ID_JENIS_KAMAR'] == $data['ID_JENIS_KAMAR']) ? 'selected' : '';
                        echo "<option value='{$row['ID_JENIS_KAMAR']}' $selected>{$row['JENIS_KAMAR']}</option>";
                    }
                    ?>
                </select>
              </div>

              <div class="form-group">
                <label for="id_pelanggan">Pelanggan</label>
                <select class="form-control" id="id_pelanggan" name="id_pelanggan" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php
                    $query = $koneksi->query("SELECT ID_PELANGGAN, NAMA_PELANGGAN FROM pelanggan");
                    while ($row = $query->fetch_assoc()) {
                        $selected = ($row['ID_PELANGGAN'] == $data['ID_PELANGGAN']) ? 'selected' : '';
                        echo "<option value='{$row['ID_PELANGGAN']}' $selected>{$row['NAMA_PELANGGAN']}</option>";
                    }
                    ?>
                </select>
              </div>

            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="pembayaran.php" class="btn btn-secondary">Batal</a>
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

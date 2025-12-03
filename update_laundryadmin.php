<?php
// Panggil file koneksi
include "koneksi.php";

// Ambil ID laundry dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data laundry berdasarkan ID
    $query = "SELECT laundry.*, kamar.NO_KAMAR, pelanggan.NAMA_PELANGGAN
              FROM laundry
              INNER JOIN kamar ON laundry.ID_KAMAR = kamar.ID_KAMAR
              INNER JOIN pelanggan ON laundry.ID_PELANGGAN = pelanggan.ID_PELANGGAN
              WHERE laundry.ID_LAUNDRY = '$id'";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('⚠️ Data laundry tidak ditemukan!');
                window.location='laundry.php';
              </script>";
        exit;
    }
}

// Proses update data laundry
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $id_kamar = $_POST['id_kamar'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $kode_laundry = $_POST['kode_laundry'];

    $sql = "UPDATE laundry 
            SET ID_KAMAR = '$id_kamar',
                ID_PELANGGAN = '$id_pelanggan',
                KODE_LAUNDRY = '$kode_laundry'
            WHERE ID_LAUNDRY = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data laundry berhasil diperbarui!');
                window.location='laundry.php';
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
  <title>Edit Data Laundry</title>

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
        <h1>Edit Data Laundry</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Edit Laundry</h3>
          </div>

          <!-- ✅ Form Edit Laundry -->
          <form action="" method="POST">
            <div class="card-body">

              <input type="hidden" name="id" value="<?php echo $row['ID_LAUNDRY']; ?>">

              <div class="form-group">
                <label for="id_kamar">Nomor Kamar</label>
                <select class="form-control" id="id_kamar" name="id_kamar" required>
                  <option value="">-- Pilih Nomor Kamar --</option>
                  <?php
                  $kamarQuery = $koneksi->query("SELECT * FROM kamar");
                  while ($k = $kamarQuery->fetch_assoc()) {
                      $selected = ($k['ID_KAMAR'] == $row['ID_KAMAR']) ? 'selected' : '';
                      echo "<option value='{$k['ID_KAMAR']}' $selected>{$k['NO_KAMAR']}</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="form-group">
                <label for="id_pelanggan">Nama Pelanggan</label>
                <select class="form-control" id="id_pelanggan" name="id_pelanggan" required>
                  <option value="">-- Pilih Pelanggan --</option>
                  <?php
                  $pelangganQuery = $koneksi->query("SELECT * FROM pelanggan");
                  while ($p = $pelangganQuery->fetch_assoc()) {
                      $selected = ($p['ID_PELANGGAN'] == $row['ID_PELANGGAN']) ? 'selected' : '';
                      echo "<option value='{$p['ID_PELANGGAN']}' $selected>{$p['NAMA_PELANGGAN']}</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="form-group">
                <label for="kode_laundry">Kode Laundry</label>
                <input type="text" class="form-control" id="kode_laundry" name="kode_laundry"
                       value="<?php echo $row['KODE_LAUNDRY']; ?>" required>
              </div>

            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="laundry.php" class="btn btn-secondary">Batal</a>
            </div>
          </form>
          <!-- ✅ Akhir Form Edit Laundry -->

        </div>
      </div>
    </section>
  </div>

  <?php include 'footer.php'; ?>
</div>

<!-- JS -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

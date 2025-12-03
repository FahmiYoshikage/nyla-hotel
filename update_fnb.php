<?php
// Panggil file koneksi
include "koneksi.php";

// Ambil ID Food & Beverage dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data berdasarkan ID
    $query = "SELECT food_n_beverage.*, kamar.NO_KAMAR, pelanggan.NAMA_PELANGGAN
              FROM food_n_beverage
              INNER JOIN kamar ON food_n_beverage.ID_KAMAR = kamar.ID_KAMAR
              INNER JOIN pelanggan ON food_n_beverage.ID_PELANGGAN = pelanggan.ID_PELANGGAN
              WHERE food_n_beverage.ID_FNB = '$id'";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('⚠️ Data Food & Beverage tidak ditemukan!');
                window.location='food_n_beverage.php';
              </script>";
        exit;
    }
}

// Proses update data Food & Beverage
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $id_kamar = $_POST['id_kamar'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $kode_fnb = $_POST['kode_fnb'];

    $sql = "UPDATE food_n_beverage 
            SET ID_KAMAR = '$id_kamar',
                ID_PELANGGAN = '$id_pelanggan',
                KODE_FNB = '$kode_fnb'
            WHERE ID_FNB = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data Food & Beverage berhasil diperbarui!');
                window.location='food_n_beverage.php';
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
  <title>Edit Data Food & Beverage</title>

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
        <h1>Edit Data Food & Beverage</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Edit Food & Beverage</h3>
          </div>

          <!-- ✅ Form Edit Food & Beverage -->
          <form action="" method="POST">
            <div class="card-body">

              <input type="hidden" name="id" value="<?php echo $row['ID_FNB']; ?>">

              <div class="form-group">
                <label for="id_kamar">Nomor Kamar</label>
                <select class="form-control" id="id_kamar" name="id_kamar" required>
                  <option value="">Pilih Nomor Kamar</option>
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
                <label for="kode_fnb">Kode F&B</label>
                <input type="text" class="form-control" id="kode_fnb" name="kode_fnb"
                       value="<?php echo $row['KODE_FNB']; ?>" required>
              </div>

            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="foodbeverage.php" class="btn btn-secondary">Batal</a>
            </div>
          </form>
          <!-- ✅ Akhir Form Edit Food & Beverage -->

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

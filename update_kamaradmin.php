<?php
// Panggil file koneksi
include "koneksi.php";

// Ambil ID kamar dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data kamar berdasarkan ID
    $query = "SELECT kamar.*, jenis_kamar.JENIS_KAMAR 
              FROM kamar 
              INNER JOIN jenis_kamar 
              ON kamar.ID_JENIS_KAMAR = jenis_kamar.ID_JENIS_KAMAR 
              WHERE kamar.ID_KAMAR = '$id'";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Data kamar tidak ditemukan!');
                window.location='kamaradmin.php';
              </script>";
        exit;
    }
}

// Proses update data kamar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $id_jenis_kamar = $_POST['id_jenis_kamar'];
    $no_kamar = $_POST['no_kamar'];
    $status = $_POST['status'];

    $sql = "UPDATE kamar 
            SET ID_JENIS_KAMAR = '$id_jenis_kamar', 
                NO_KAMAR = '$no_kamar', 
                STATUS = '$status'
            WHERE ID_KAMAR = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data kamar berhasil diperbarui!');
                window.location='kamaradmin.php';
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
  <title>Edit Data Kamar</title>

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
        <h1>Edit Data Kamar</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Edit Kamar</h3>
          </div>

          <!-- ✅ Form Edit Kamar -->
          <form action="" method="POST">
            <div class="card-body">

              <input type="hidden" name="id" value="<?php echo $row['ID_KAMAR']; ?>">

              <div class="form-group">
                <label for="id_jenis_kamar">Jenis Kamar</label>
                <select class="form-control" id="id_jenis_kamar" name="id_jenis_kamar" required>
                  <option value="">-- Pilih Jenis Kamar --</option>
                  <?php
                  $jenisQuery = $koneksi->query("SELECT * FROM jenis_kamar");
                  while ($jk = $jenisQuery->fetch_assoc()) {
                      $selected = ($jk['ID_JENIS_KAMAR'] == $row['ID_JENIS_KAMAR']) ? 'selected' : '';
                      echo "<option value='{$jk['ID_JENIS_KAMAR']}' $selected>{$jk['JENIS_KAMAR']}</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="form-group">
                <label for="no_kamar">Nomor Kamar</label>
                <input type="text" class="form-control" id="no_kamar" name="no_kamar"
                       value="<?php echo $row['NO_KAMAR']; ?>" required>
              </div>

              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                  <option value="Tersedia" <?php if ($row['STATUS'] == 'Tersedia') echo 'selected'; ?>>Tersedia</option>
                  <option value="Dipesan" <?php if ($row['STATUS'] == 'Dipesan') echo 'selected'; ?>>Dipesan</option>
                  <option value="Digunakan" <?php if ($row['STATUS'] == 'Digunakan') echo 'selected'; ?>>Digunakan</option>
                </select>
              </div>

            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="kadmin.php" class="btn btn-secondary">Batal</a>
            </div>
          </form>
          <!-- ✅ Akhir Form Edit Kamar -->

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

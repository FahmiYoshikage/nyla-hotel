<?php
// Panggil file koneksi
include "koneksi.php";

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data berdasarkan ID
    $query = "SELECT * FROM jenis_kamar WHERE ID_JENIS_KAMAR = '$id'";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Data tidak ditemukan!');
                window.location='jenis-kamaradmin.php';
              </script>";
        exit;
    }
}

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $jenis_kamar = $_POST['jenis_kamar'];
    $fasilitas = $_POST['fasilitas'];
    $tarif = $_POST['tarif'];

    $sql = "UPDATE jenis_kamar 
            SET JENIS_KAMAR = '$jenis_kamar', 
                FASILITAS = '$fasilitas', 
                TARIF = '$tarif' 
            WHERE ID_JENIS_KAMAR = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data jenis kamar berhasil diperbarui!');
                window.location='jenis-kamaradmin.php';
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
  <title>Edit Jenis Kamar</title>

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
        <h1>Edit Jenis Kamar</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Edit Jenis Kamar</h3>
          </div>

          <!-- ✅ Form dimulai di sini -->
          <form action="" method="POST">
            <div class="card-body">

              <input type="hidden" name="id" value="<?php echo $row['ID_JENIS_KAMAR']; ?>">

              <div class="form-group">
                <label for="jenis_kamar">Jenis Kamar</label>
                <input type="text" class="form-control" id="jenis_kamar" name="jenis_kamar"
                       value="<?php echo $row['JENIS_KAMAR']; ?>" required>
              </div>

              <div class="form-group">
                <label for="fasilitas">Fasilitas</label>
                <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3" required><?php echo $row['FASILITAS']; ?></textarea>
              </div>

              <div class="form-group">
                <label for="tarif">Tarif (Rp)</label>
                <input type="text" class="form-control" id="tarif" name="tarif"
                       value="<?php echo $row['TARIF']; ?>"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
              </div>

            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="jenis-kamaradmin.php" class="btn btn-secondary">Batal</a>
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

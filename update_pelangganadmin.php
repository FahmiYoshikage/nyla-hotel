<?php
// Panggil file koneksi
include "koneksi.php";

// Ambil ID pelanggan dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data pelanggan berdasarkan ID
    $query = "SELECT * FROM pelanggan WHERE ID_PELANGGAN = '$id'";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Data pelanggan tidak ditemukan!');
                window.location='pelanggan.php';
              </script>";
        exit;
    }
}

// Proses update data pelanggan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jk = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $kota = $_POST['kota'];
    $no_tlp = $_POST['no_tlp'];
    $email = $_POST['email'];

    $sql = "UPDATE pelanggan 
            SET NAMA_PELANGGAN = '$nama', 
                JENIS_KELAMIN = '$jk', 
                ALAMAT = '$alamat', 
                KOTA = '$kota', 
                NO_TLP = '$no_tlp', 
                EMAIL = '$email'
            WHERE ID_PELANGGAN = '$id'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data pelanggan berhasil diperbarui!');
                window.location='pelanggan.php';
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
  <title>Edit Data Pelanggan</title>

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
        <h1>Edit Data Pelanggan</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Form Edit Pelanggan</h3>
          </div>

          <!-- ✅ Form Edit Pelanggan -->
          <form action="" method="POST">
            <div class="card-body">

              <input type="hidden" name="id" value="<?php echo $row['ID_PELANGGAN']; ?>">

              <div class="form-group">
                <label for="nama">Nama Pelanggan</label>
                <input type="text" class="form-control" id="nama" name="nama"
                       value="<?php echo $row['NAMA_PELANGGAN']; ?>" required>
              </div>

              <div class="form-group">
                <label for="jk">Jenis Kelamin</label>
                <select class="form-control" id="jk" name="jk" required>
                  <option value="Laki-laki" <?php if ($row['JENIS_KELAMIN'] == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                  <option value="Perempuan" <?php if ($row['JENIS_KELAMIN'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                </select>
              </div>

              <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" class="form-control" id="alamat" name="alamat"
                       value="<?php echo $row['ALAMAT']; ?>" required>
              </div>

              <div class="form-group">
                <label for="kota">Kota</label>
                <input type="text" class="form-control" id="kota" name="kota"
                       value="<?php echo $row['KOTA']; ?>" required>
              </div>

              <div class="form-group">
                <label for="no_tlp">No Telepon</label>
                <input type="text" class="form-control" id="no_tlp" name="no_tlp"
                       value="<?php echo $row['NO_TLP']; ?>" required>
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?php echo $row['EMAIL']; ?>" required>
              </div>

            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Update</button>
              <a href="pelangganadmin.php" class="btn btn-secondary">Batal</a>
            </div>
          </form>
          <!-- ✅ Akhir Form Edit Pelanggan -->

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

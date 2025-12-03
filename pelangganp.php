<?php
session_start();
include "koneksi.php";

// Pastikan user sudah login
if (!isset($_SESSION['NAMA_PELANGGAN']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}
$email = $_SESSION['email'];
$cek = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE EMAIL='$email'");
$data = mysqli_fetch_assoc($cek);
$is_readonly = (
    !empty($data['JENIS_KELAMIN']) &&
    !empty($data['ALAMAT']) &&
    !empty($data['KOTA']) &&
    !empty($data['NO_TLP'])
);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = $_SESSION['NAMA_PELANGGAN'];
    $email          = $_SESSION['email'];
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $alamat         = $_POST['alamat'];
    $kota           = $_POST['kota'];
    $no_tlp         = $_POST['no_tlp'];

    // Update data pelanggan yang kolom lain masih NULL sesuai email
    $sql = "UPDATE pelanggan 
            SET JENIS_KELAMIN='$jenis_kelamin', ALAMAT='$alamat', KOTA='$kota', NO_TLP='$no_tlp'
            WHERE EMAIL='$email'";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Data pelanggan berhasil diperbarui!');
                window.location='pelangganp.php';
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
  <title>Tambah Data Pelanggan</title>
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include 'header.php'; ?>
    <?php include 'navbarp.php'; ?>

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

                    <<form action="" method="POST">
    <div class="card-body">

        <!-- Nama Pelanggan otomatis -->
        <div class="form-group">
            <label for="nama_pelanggan">Nama Pelanggan</label>
            <input type="text" class="form-control" id="nama_pelanggan"
                   name="nama_pelanggan"
                   value="<?php echo htmlspecialchars($_SESSION['NAMA_PELANGGAN']); ?>"
                   readonly>
        </div>

        <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin"
                <?php if ($is_readonly) echo "disabled"; ?> required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-Laki" <?php if($data['JENIS_KELAMIN']=="Laki-Laki") echo "selected"; ?>>Laki-Laki</option>
                <option value="Perempuan" <?php if($data['JENIS_KELAMIN']=="Perempuan") echo "selected"; ?>>Perempuan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3"
                placeholder="Masukkan Alamat Lengkap"
                <?php if ($is_readonly) echo "readonly"; ?> required><?php echo $data['ALAMAT']; ?></textarea>
        </div>

        <div class="form-group">
            <label for="kota">Kota</label>
            <input type="text" class="form-control" id="kota" name="kota"
                placeholder="Masukkan Nama Kota"
                value="<?php echo $data['KOTA']; ?>"
                <?php if ($is_readonly) echo "readonly"; ?> required>
        </div>

        <div class="form-group">
            <label for="no_tlp">No Telepon</label>
            <input type="text" class="form-control" id="no_tlp" name="no_tlp"
                placeholder="Masukkan Nomor Telepon"
                value="<?php echo $data['NO_TLP']; ?>"
                <?php if ($is_readonly) echo "readonly"; ?> required
                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
        </div>

    </div>

    <div class="card-footer">
        <?php if (!$is_readonly): ?>
            <button type="submit" class="btn btn-primary">Simpan</button>
        <?php endif; ?>
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

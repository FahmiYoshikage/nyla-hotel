<?php
session_start();
include "koneksi.php";

// Ambil data pelanggan yang login
$id_pelanggan_session = $_SESSION['ID_PELANGGAN']; 
$nama_pelanggan_session = $_SESSION['NAMA_PELANGGAN'];

$data = [
    'ID_JENIS_KAMAR' => '',
    'TGL_PESAN'      => '',
    'JAM_PESAN'      => '',
    'TGL_CHECK_IN'      => '',
    'UANG_MUKA'      => ''
];
$is_readonly = isset($_SESSION['PESANAN_READONLY']);
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $koneksi->query("SELECT * FROM pemesanan WHERE ID_PEMESANAN = '$id'");
    $data = $query->fetch_assoc();
}

// Proses penyimpanan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_jenis_kamar = $_POST['id_jenis_kamar'];
    $id_pelanggan   = $_POST['id_pelanggan']; // dari hidden input
    $tgl_pesan      = $_POST['tgl_pesan'];
    $tgl_checkin      = $_POST['tgl_checkin'];
    $jam_pesan      = $_POST['jam_pesan'];
    $uang_muka      = $_POST['uang_muka'];

    // Query tambah data ke tabel pemesanan
    $sql = "INSERT INTO pemesanan (ID_JENIS_KAMAR, ID_PELANGGAN, TGL_PESAN, JAM_PESAN, TGL_CHECK_IN, UANG_MUKA)
            VALUES ('$id_jenis_kamar', '$id_pelanggan', '$tgl_pesan', '$jam_pesan', '$tgl_checkin','$uang_muka')";

    if ($koneksi->query($sql) === TRUE) {
        $last_id = $koneksi->insert_id;
        echo "<script>
                alert('✅ Data pemesanan berhasil ditambahkan!');
                window.location = 'pemesananp.php?readonly=1&id=$last_id';
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

// Ambil data jenis kamar untuk combo box
$jenis_kamar = $koneksi->query("SELECT * FROM jenis_kamar");
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
  <?php include 'navbarp.php'; ?>

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
                <select class="form-control" id="id_jenis_kamar" name="id_jenis_kamar" 
        onchange="tampilkanHarga()" 
        <?php if ($is_readonly) echo "disabled"; ?>>

    <option value="">-- Pilih Jenis Kamar --</option>

    <?php while($row = $jenis_kamar->fetch_assoc()) { ?>
        <option value="<?= $row['ID_JENIS_KAMAR']; ?>"
                data-harga="<?= $row['TARIF']; ?>"
                <?php if ($row['ID_JENIS_KAMAR'] == $data['ID_JENIS_KAMAR']) echo "selected"; ?>>
            <?= $row['JENIS_KAMAR']; ?>
        </option>
    <?php } ?>

</select>


<!-- Tempat munculnya harga -->
<div id="harga_kamar" style="margin-top:10px; font-weight:bold; color:#007bff;"></div>

              </div>

              <div class="form-group">
    <label>Nama Pelanggan</label>
    <input type="text" class="form-control"
           value="<?= $nama_pelanggan_session; ?>" readonly>

    <!-- Tetap kirim ID pelanggan untuk INSERT -->
    <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan_session; ?>">
</div>

              <div class="form-group">
                <label for="tgl_pesan">Tanggal Pesan</label>
                <input type="date" class="form-control"
       id="tgl_pesan"
       name="tgl_pesan"
       value="<?= $data['TGL_PESAN']; ?>"
       <?php if ($is_readonly) echo "readonly"; ?> required>

              </div>

              <div class="form-group">
                <label for="jam_pesan">Jam Pesan</label>
                <input type="time" class="form-control"
       id="jam_pesan"
       name="jam_pesan"
       value="<?= $data['JAM_PESAN']; ?>"
       <?php if ($is_readonly) echo "readonly"; ?> required>

            <div class="form-group">
                            <label for="tgl_checkin">Tanggal Check In</label>
                            <input type="date" class="form-control"
                id="tgl_checkin"
                name="tgl_checkin"
                value="<?= $data['TGL_CHECK_IN']; ?>"
                <?php if ($is_readonly) echo "readonly"; ?> required>

                        </div>

            <div class="form-group">
    <label for="uang_muka">Uang Muka (Rp)</label>
    <input type="text" class="form-control"
           id="uang_muka"
           name="uang_muka"
           value="<?= isset($data['UANG_MUKA']) ? $data['UANG_MUKA'] : '' ?>"
           placeholder="Masukkan uang muka, contoh: 250000"
           <?php if ($is_readonly) echo "readonly"; ?>
           required
           oninput="this.value = this.value.replace(/[^0-9]/g, '');">
</div>


            </div>
            <div class="card-footer">
              <?php if (!$is_readonly): ?>
            <button type="submit" class="btn btn-primary">Simpan</button>
        <?php endif; ?>
            </div>
          </form>
          <!-- ✅ Form berakhir di sini -->

        </div>
      </div>
    </section>
  </div>

  <?php include 'footer.php'; ?>
</div>
<script>
function tampilkanHarga() {
    var select = document.getElementById("id_jenis_kamar");
    var harga  = select.options[select.selectedIndex].getAttribute("data-harga");

    if (harga) {
        document.getElementById("harga_kamar").innerHTML =
            "Harga Kamar: Rp " + new Intl.NumberFormat('id-ID').format(harga);
    } else {
        document.getElementById("harga_kamar").innerHTML = "";
    }
}
</script>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

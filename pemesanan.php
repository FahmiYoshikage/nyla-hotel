<?php
include 'koneksi.php';

// Query gabung tabel pemesanan + jenis_kamar + pelanggan
$sql = "SELECT 
            pemesanan.ID_PEMESANAN,
            jenis_kamar.JENIS_KAMAR,
            pelanggan.NAMA_PELANGGAN,
            pemesanan.TGL_PESAN,
            pemesanan.JAM_PESAN,
            pemesanan.TGL_CHECK_IN,
            pemesanan.UANG_MUKA
        FROM pemesanan
        INNER JOIN jenis_kamar 
            ON pemesanan.ID_JENIS_KAMAR = jenis_kamar.ID_JENIS_KAMAR
        INNER JOIN pelanggan 
            ON pemesanan.ID_PELANGGAN = pelanggan.ID_PELANGGAN";

$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Pemesanan | Admin Hotel</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'header.php'; ?>
  <?php include 'navbar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>DATA PEMESANAN</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Data Pemesanan</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <a href="tambahdata_pemesananadmin.php" 
        class="btn btn-primary btn-lg" 
        style="border-width: 1px; border-radius: 5px; margin-bottom:10px;">
        Tambahkan Data
      </a>

      <div class="card">
        <div class="card-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Jenis Kamar</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal Pesan</th>
                <th>Jam Pesan</th>
                <th>Tgl Check in</th>
                <th>Uang Muka</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['JENIS_KAMAR']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['NAMA_PELANGGAN']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['TGL_PESAN']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['JAM_PESAN']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['TGL_CHECK_IN']) . "</td>";
                      echo "<td>Rp " . number_format($row['UANG_MUKA'], 0, ',', '.') . "</td>";
                      echo "<td>
                              <a href='update_pemesananadmin.php?id=" . $row['ID_PEMESANAN'] . "' class='btn btn-success btn-sm'>Update</a>
                              <a href='delete_pemesananadmin.php?id=" . $row['ID_PEMESANAN'] . "' 
                                class='btn btn-danger btn-sm' 
                                onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Delete</a>
                            </td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='6' style='text-align:center;'>Tidak ada data pemesanan</td></tr>";
              }

              $koneksi->close();
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Jenis Kamar</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal Pesan</th>
                <th>Jam Pesan</th>
                <th>Uang Muka</th>
                <th>Aksi</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </section>
  </div>

  <?php include 'footer.php'; ?>
</div>

<!-- Script -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
</body>
</html>

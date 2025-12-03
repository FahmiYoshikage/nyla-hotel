<?php
include 'koneksi.php';

// Query gabung tabel detail_laundry dan pelanggan
$sql = "SELECT 
            detail_laundry.ID_DETAIL_LAUNDRY,
            laundry.KODE_LAUNDRY,
            pelanggan.NAMA_PELANGGAN,
            detail_laundry.TGL_LAUNDRY,
            detail_laundry.JAM_LAUNDRY,
            detail_laundry.JENIS_LAUNDRY,
            detail_laundry.HARGA_SATUAN,
            detail_laundry.JUMLAH_LAUNDRY,
            detail_laundry.TOTAL_LAUNDRY
        FROM detail_laundry
        INNER JOIN pelanggan 
            ON detail_laundry.ID_PELANGGAN = pelanggan.ID_PELANGGAN
        INNER JOIN laundry
        ON detail_laundry.ID_LAUNDRY = laundry.ID_LAUNDRY";

$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Laundry</title>

  <!-- Google Font -->
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

  <!-- Navbar -->
  <?php
  include 'header.php';
  include 'navbar.php';
  ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>DATA LAUNDRY</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Laundry</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <td>
        <a href="tambahdata_laundry.php" 
           class="btn btn-primary btn-lg" 
           style="border-width: 1px; border-radius: 5px;">
           Tambahkan Data
        </a>
      </td>

      <div class="card mt-3">
        <div class="card-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead class="text-center">
              <tr>
                <th>Kode Laundry</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Jenis Laundry</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['KODE_LAUNDRY']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['NAMA_PELANGGAN']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['TGL_LAUNDRY']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['JAM_LAUNDRY']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['JENIS_LAUNDRY']) . "</td>";
                      echo "<td>Rp " . number_format($row['HARGA_SATUAN'], 0, ',', '.') . "</td>";
                      echo "<td>" . htmlspecialchars($row['JUMLAH_LAUNDRY']) . "</td>";
                      echo "<td>Rp " . number_format($row['TOTAL_LAUNDRY'], 0, ',', '.') . "</td>";
                      echo "<td>
                              <a href='update_laundry.php?id=" . $row['ID_DETAIL_LAUNDRY'] . "' class='btn btn-success btn-sm'>Update</a>
                              <a href='delete_laundry.php?id=" . $row['ID_DETAIL_LAUNDRY'] . "' 
                                 class='btn btn-danger btn-sm' 
                                 onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Delete</a>
                            </td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='9' class='text-center'>Tidak ada data tersedia</td></tr>";
              }

              $koneksi->close();
              ?>
            </tbody>
            <tfoot class="text-center">
              <tr>
                <th>ID Detail Laundry</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Jenis Laundry</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Total</th>
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

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- DataTables Init -->
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

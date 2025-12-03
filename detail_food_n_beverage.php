<?php
include 'koneksi.php';

// Query gabung tabel detail_food_n_beverage, pelanggan, dan food_n_beverage (jika ada tabel FNB terpisah)
$sql = "SELECT 
            detail_food_n_beverage.ID_DETAIL_FNB,
            food_n_beverage.KODE_FNB,
            pelanggan.NAMA_PELANGGAN,
            detail_food_n_beverage.TGL_FNB,
            detail_food_n_beverage.JAM_FNB,
            detail_food_n_beverage.FOOD,
            detail_food_n_beverage.BEVERAGE,
            detail_food_n_beverage.JUMLAH_FNB,
            detail_food_n_beverage.SUBTOTAL_FNB,
            detail_food_n_beverage.TOTAL_FNB
        FROM detail_food_n_beverage
        INNER JOIN pelanggan 
            ON detail_food_n_beverage.ID_PELANGGAN = pelanggan.ID_PELANGGAN
        INNER JOIN food_n_beverage
        ON detail_food_n_beverage.ID_FNB = food_n_beverage.ID_FNB";

$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Food & Beverage</title>

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
            <h1>DATA FOOD & BEVERAGE</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Food & Beverage</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <td>
        <a href="tambahdata_detailfnb.php" 
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
                <th>Kode FNB</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Food</th>
                <th>Beverage</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Total</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['KODE_FNB']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['NAMA_PELANGGAN']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['TGL_FNB']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['JAM_FNB']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['FOOD']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['BEVERAGE']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['JUMLAH_FNB']) . "</td>";
                      echo "<td>Rp " . number_format($row['SUBTOTAL_FNB'], 0, ',', '.') . "</td>";
                      echo "<td>Rp " . number_format($row['TOTAL_FNB'], 0, ',', '.') . "</td>";
                      echo "<td>
                              <a href='update_foodbeverage.php?id=" . $row['ID_DETAIL_FNB'] . "' class='btn btn-success btn-sm'>Update</a>
                              <a href='delete_foodbeverage.php?id=" . $row['ID_DETAIL_FNB'] . "' 
                                 class='btn btn-danger btn-sm' 
                                 onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Delete</a>
                            </td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='10' class='text-center'>Tidak ada data tersedia</td></tr>";
              }

              $koneksi->close();
              ?>
            </tbody>
            <tfoot class="text-center">
              <tr>
                <th>ID Detail F&B</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Food</th>
                <th>Beverage</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
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

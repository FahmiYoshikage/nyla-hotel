<?php
include 'koneksi.php';

// Query gabung tabel laundry, kamar, dan pelanggan
$sql = "SELECT 
            laundry.ID_LAUNDRY,
            kamar.NO_KAMAR,
            pelanggan.NAMA_PELANGGAN,
            laundry.KODE_LAUNDRY
        FROM laundry
        INNER JOIN kamar 
            ON laundry.ID_KAMAR = kamar.ID_KAMAR
        INNER JOIN pelanggan 
            ON laundry.ID_PELANGGAN = pelanggan.ID_PELANGGAN";

$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Data Laundry</title>

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
        <a href="tambahdata_laundryadmin.php" 
           class="btn btn-primary btn-lg" 
           style="border-width: 1px; border-radius: 5px;">
           Tambahkan Data
        </a>
      </td>

      <div class="card">
        <div class="card-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                
                <th>Nama Pelanggan</th>
                <th>No Kamar</th>
                
                <th>Kode Laundry</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      
                      echo "<td>" . htmlspecialchars($row['NAMA_PELANGGAN']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['NO_KAMAR']) . "</td>";
                      
                      echo "<td>" . htmlspecialchars($row['KODE_LAUNDRY']) . "</td>";
                      echo "<td>
                              <a href='update_laundryadmin.php?id=" . $row['ID_LAUNDRY'] . "' class='btn btn-success btn-sm'>Update</a>
                              <a href='delete_laundryadmin.php?id=" . $row['ID_LAUNDRY'] . "' 
                                 class='btn btn-danger btn-sm' 
                                 onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Delete</a>
                            </td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data tersedia</td></tr>";
              }

              $koneksi->close();
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>ID Laundry</th>
                <th>Nama Pelanggan</th>
                <th>No Kamar</th>
                
                <th>Kode Laundry</th>
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
<!-- DataTables  & Plugins -->
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
<!-- Page specific script -->
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

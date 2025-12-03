<?php
include 'koneksi.php';

// Query gabungan data Check-In & Check-Out
$sql = "SELECT 
            checkin_checkout.ID_CHECKIN_OUT,
            pemesanan.TGL_PESAN,
            pelanggan.NAMA_PELANGGAN,
            jenis_kamar.JENIS_KAMAR,
            kamar.NO_KAMAR,
            checkin_checkout.TGL_CHECK_IN,
            checkin_checkout.JAM_CHECK_IN,
            checkin_checkout.LAMA_INAP,
            checkin_checkout.TGL_CHECK_OUT,
            checkin_checkout.STATUS_PEMBAYARAN,
            checkin_checkout.STATUS_CHECKINOUT
        FROM checkin_checkout
        INNER JOIN pemesanan 
            ON checkin_checkout.ID_PEMESANAN = pemesanan.ID_PEMESANAN
        INNER JOIN pelanggan 
            ON checkin_checkout.ID_PELANGGAN = pelanggan.ID_PELANGGAN
        INNER JOIN jenis_kamar
            ON checkin_checkout.ID_JENIS_KAMAR = jenis_kamar.ID_JENIS_KAMAR
        INNER JOIN kamar
            ON checkin_checkout.ID_KAMAR = kamar.ID_KAMAR";

$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Check-In & Check-Out | Admin Hotel</title>

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
  <?php include 'header.php'; ?>
  <?php include 'navbar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>DATA CHECK-IN & CHECK-OUT</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Data Check-In & Check-Out</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <a href="tambahdata_checkinoutadmin.php" 
         class="btn btn-primary btn-lg" 
         style="border-width: 1px; border-radius: 5px; margin-bottom:10px;">
        Tambahkan Data
      </a>

      <div class="card">
        <div class="card-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr class="text-center">
                <th>ID</th>
                <th>Tanggal Pemesanan</th>
                <th>Nama Pelanggan</th>
                <th>Jenis Kamar</th>
                <th>No Kamar</th>
                <th>Tgl Check-In</th>
                <th>Jam Check-In</th>
                <th>Lama Inap</th>
                <th>Tgl Check-Out</th>
                <th>Status Pembayaran</th>
                <th>Status Checkinout</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>{$row['ID_CHECKIN_OUT']}</td>";
                      echo "<td>{$row['TGL_PESAN']}</td>";
                      echo "<td>{$row['NAMA_PELANGGAN']}</td>";
                      echo "<td>{$row['JENIS_KAMAR']}</td>";
                      echo "<td>{$row['NO_KAMAR']}</td>";
                      echo "<td>{$row['TGL_CHECK_IN']}</td>";
                      echo "<td>{$row['JAM_CHECK_IN']}</td>";
                      echo "<td>{$row['LAMA_INAP']}</td>";
                      echo "<td>{$row['TGL_CHECK_OUT']}</td>";
                      echo "<td>{$row['STATUS_PEMBAYARAN']}</td>";
                      echo "<td>{$row['STATUS_CHECKINOUT']}</td>";
                      echo "<td class='text-center'>
                              <a href='update_checkinoutadmin.php?id={$row['ID_CHECKIN_OUT']}' class='btn btn-success btn-sm'>Update</a>
                              <a href='delete_checkinoutadmin.php?id={$row['ID_CHECKIN_OUT']}' 
                                 class='btn btn-danger btn-sm'
                                 onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Delete</a>
                            </td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='12' class='text-center'>Tidak ada data check-in/check-out</td></tr>";
              }

              $koneksi->close();
              ?>
            </tbody>
            <tfoot class="text-center">
              <tr>
                <th>ID</th>
                <th>ID Pemesanan</th>
                <th>Nama Pelanggan</th>
                <th>Jenis Kamar</th>
                <th>No Kamar</th>
                <th>Tgl Check-In</th>
                <th>Jam Check-In</th>
                <th>Lama Inap</th>
                <th>Tgl Check-Out</th>
                <th>Jenis Harga</th>
                <th>Total Bayar</th>
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
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
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

<?php
session_start();
include "koneksi.php";

// Pastikan role pelanggan atau admin
if (!isset($_SESSION['role'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Data Feedback Pelanggan</title>

<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="dist/css/adminlte.min.css">
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<style>
img.thumbnail {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 5px;
}
</style>

</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include "header.php"; ?>
<?php include "navbar.php"; ?>

<div class="content-wrapper">

<section class="content-header">
    <h1><i class="fas fa-comments"></i> Data Feedback Pelanggan</h1>
</section>

<section class="content">
<div class="card">
    <div class="card-header bg-info">
        <h3 class="card-title"><i class="fas fa-list"></i> Daftar Feedback</h3>
    </div>

    <div class="card-body">

<table id="example1" class="table table-bordered table-striped">
<thead>
<tr class="text-center">
    <th>No</th>
    <th>Pelanggan</th>
    <th>No Kamar</th>
    <th>Rating</th>
    <th>Kategori</th>
    <th>Pesan</th>
    <th>Foto</th>
    <th>Status</th>
</tr>
</thead>

<tbody>
<?php
$sql = "
    SELECT 
        f.ID_FEEDBACK,
        p.NAMA_PELANGGAN,
        (
            SELECT c.ID_KAMAR 
            FROM checkin_checkout c 
            WHERE c.ID_PELANGGAN = f.ID_PELANGGAN 
            ORDER BY c.ID_CHECKIN_OUT DESC 
            LIMIT 1
        ) AS NO_KAMAR,
        f.RATING,
        f.KATEGORI_FEEDBACK,
        f.PESAN,
        f.FOTO_FEEDBACK
    FROM feedback_pelanggan f
    LEFT JOIN pelanggan p ON f.ID_PELANGGAN = p.ID_PELANGGAN
    ORDER BY f.ID_FEEDBACK DESC
";

$result = $koneksi->query($sql);

$no = 1;
while ($row = $result->fetch_assoc()) {
?>
<tr>
    <td class="text-center"><?= $no++; ?></td>

    <td><?= htmlspecialchars($row['NAMA_PELANGGAN']); ?></td>

    <td class="text-center">
        <?= $row['NO_KAMAR'] ? $row['NO_KAMAR'] : "-"; ?>
    </td>

    <td class="text-center">
        <?php 
            for ($i=1; $i <= 5; $i++) {
                echo $i <= $row['RATING'] 
                ? "<i class='fas fa-star text-warning'></i>" 
                : "<i class='far fa-star text-warning'></i>";
            }
        ?>
    </td>

    <td class="text-center"><?= $row['KATEGORI_FEEDBACK']; ?></td>

    <td><?= nl2br(htmlspecialchars($row['PESAN'])); ?></td>

    <td class="text-center">
        <?php if ($row['FOTO_FEEDBACK']) { ?>
            <a href="uploads/feedback/<?= $row['FOTO_FEEDBACK']; ?>" target="_blank">
                <img src="uploads/feedback/<?= $row['FOTO_FEEDBACK']; ?>" class="thumbnail">
            </a>
        <?php } else { ?>
            <span class="text-muted">Tidak ada</span>
        <?php } ?>
    </td>

</tr>
<?php } ?>
</tbody>

</table>
</div>
</div>
</section>

</div>

<?php include "footer.php"; ?>

</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script>
$(function () {
    $("#example1").DataTable({
        "responsive": true,
        "autoWidth": false,
    });
});
</script>

</body>
</html>

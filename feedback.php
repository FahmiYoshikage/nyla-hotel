<?php
session_start();
include "koneksi.php";

// ===============================================
// CEK LOGIN
// ===============================================
if (!isset($_SESSION['ID_PELANGGAN'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$id_pelanggan   = $_SESSION['ID_PELANGGAN'];
$nama_pelanggan = $_SESSION['NAMA_PELANGGAN'];


// ===============================================
// AMBIL DATA CHECK-IN TERAKHIR PELANGGAN
// ===============================================
$sql_ci = "
    SELECT ID_CHECKIN_OUT, ID_KAMAR
    FROM checkin_checkout
    WHERE ID_PELANGGAN = ?
    ORDER BY ID_CHECKIN_OUT DESC
    LIMIT 1
";

$stmt_ci = $koneksi->prepare($sql_ci);
$stmt_ci->bind_param("i", $id_pelanggan);
$stmt_ci->execute();
$res_ci = $stmt_ci->get_result();
$data_ci = $res_ci->fetch_assoc();
$stmt_ci->close();

if (!$data_ci) {
    echo "<script>alert('Anda belum pernah check-in, sehingga tidak bisa mengirim feedback.'); 
          window.location='dashboard_pelanggan.php';</script>";
    exit;
}

$id_checkin_out = $data_ci['ID_CHECKIN_OUT'];
$no_kamar       = $data_ci['ID_KAMAR'];


// ===============================================
// PROSES SIMPAN FEEDBACK
// ===============================================
if (isset($_POST['simpan_feedback'])) {

    $rating   = (int)$_POST['rating'];
    $kategori = $_POST['kategori_feedback'];
    $pesan    = trim($_POST['pesan']);

    if ($rating == 0 || empty($kategori) || empty($pesan)) {
        echo "<script>alert('Harap isi semua field wajib!'); window.history.back();</script>";
        exit;
    }

    // =========================
    // UPLOAD FOTO (OPSIONAL)
    // =========================
    $foto_feedback = NULL;

    if (!empty($_FILES['foto_feedback']['name'])) {

        $allowed = ['image/jpeg','image/png','image/jpg'];
        $size_max = 5 * 1024 * 1024;

        if (!in_array($_FILES['foto_feedback']['type'], $allowed)) {
            echo "<script>alert('Format foto tidak valid!'); window.history.back();</script>";
            exit;
        }

        if ($_FILES['foto_feedback']['size'] > $size_max) {
            echo "<script>alert('Ukuran foto terlalu besar! (maks 5MB)'); window.history.back();</script>";
            exit;
        }

        $upload_dir = "uploads/feedback/";

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['foto_feedback']['name'], PATHINFO_EXTENSION);
        $filename_new = "feedback_" . time() . "_" . uniqid() . "." . $ext;

        if (move_uploaded_file($_FILES['foto_feedback']['tmp_name'], $upload_dir . $filename_new)) {
            $foto_feedback = $filename_new;
        }
    }

    // =========================
    // INSERT KE DATABASE
    // =========================
    $sql_insert = "
        INSERT INTO feedback_pelanggan
        (ID_PELANGGAN, ID_CHECKIN_OUT, RATING, KATEGORI_FEEDBACK, PESAN, FOTO_FEEDBACK)
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $koneksi->prepare($sql_insert);
    $stmt->bind_param("iiisss",
        $id_pelanggan,
        $id_checkin_out,
        $rating,
        $kategori,
        $pesan,
        $foto_feedback
    );

    if ($stmt->execute()) {
        echo "<script>alert('Feedback berhasil dikirim! Terima kasih.'); 
              window.location='feedback.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan feedback!'); window.history.back();</script>";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Feedback</title>

<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="dist/css/adminlte.min.css">

<style>
.rating i {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
}
.rating i.active {
    color: gold;
}
</style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include "header.php"; ?>
<?php include "navbarp.php"; ?>

<div class="content-wrapper">

<section class="content-header">
    <h1><i class="fas fa-comment-dots"></i> Tambah Feedback</h1>
</section>

<section class="content">
<div class="card card-info">
    <div class="card-header"><h3 class="card-title">Form Feedback</h3></div>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="card-body">

            <div class="form-group">
                <label>Nama Pelanggan</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($nama_pelanggan); ?>" readonly>
            </div>

            <div class="form-group">
                <label>No Kamar Terakhir</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($no_kamar); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Rating</label>
                <div class="rating">
                    <i class="far fa-star" data-value="1"></i>
                    <i class="far fa-star" data-value="2"></i>
                    <i class="far fa-star" data-value="3"></i>
                    <i class="far fa-star" data-value="4"></i>
                    <i class="far fa-star" data-value="5"></i>
                </div>
                <input type="hidden" name="rating" id="rating" value="0">
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_feedback" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="Pujian">Pujian</option>
                    <option value="Saran">Saran</option>
                    <option value="Keluhan">Keluhan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Pesan / Komentar</label>
                <textarea name="pesan" rows="5" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label>Upload Foto (Opsional)</label>
                <input type="file" name="foto_feedback" class="form-control-file" accept="image/*">
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" name="simpan_feedback" class="btn btn-info">
                <i class="fas fa-paper-plane"></i> Kirim Feedback
            </button>
        </div>
    </form>

</div>
</section>

</div>

<?php include "footer.php"; ?>

</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script>
$(".rating i").on("click", function(){
    let val = $(this).data("value");
    $("#rating").val(val);

    $(".rating i").removeClass("active fas").addClass("far");
    for (let i = 1; i <= val; i++) {
        $(".rating i[data-value='"+i+"']").removeClass("far").addClass("fas active");
    }
});
</script>

</body>
</html>

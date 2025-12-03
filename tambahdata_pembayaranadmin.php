<?php
// Panggil file koneksi
include "koneksi.php";

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi Database Gagal Total: " . $koneksi->connect_error);
}

// =======================================================
// === VARIABEL AWAL UNTUK MENGISI FORM ===
// =======================================================
$selected_id_checkin_out = '';
$selected_id_pelanggan = 0;
$selected_id_jenis_kamar = 0;
$selected_id_laundry = 'NULL';
$selected_id_fnb = 'NULL';

$nama_pelanggan_display = '-- Pilih Pemesanan --';
$jenis_kamar_display = 'N/A';
$tgl_checkin_display = 'N/A';
$lama_inap_display = 'N/A';
$lama_inap_value = 0;

$total_kamar = 0;
$uang_muka = 0;
$biaya_laundry = 0;
$biaya_fnb = 0;
$sisa_pembayaran_awal = 0;
$total_keseluruhan = 0;
$sisa_pembayaran_akhir = 0;

// =======================================================
// === AMBIL DAFTAR CHECK-IN YANG BELUM LUNAS ===
// =======================================================
$sql_checkin_codes = "SELECT 
        c.ID_CHECKIN_OUT, 
        c.ID_PEMESANAN,
        p.NAMA_PELANGGAN, 
        jk.JENIS_KAMAR, 
        c.STATUS_PEMBAYARAN
    FROM checkin_checkout c
    INNER JOIN pelanggan p ON c.ID_PELANGGAN = p.ID_PELANGGAN
    INNER JOIN jenis_kamar jk ON c.ID_JENIS_KAMAR = jk.ID_JENIS_KAMAR
    WHERE c.STATUS_PEMBAYARAN = 'Belum Lunas'
    AND c.ID_CHECKIN_OUT NOT IN (SELECT ID_CHECKIN_OUT FROM pembayaran)
    ORDER BY c.ID_CHECKIN_OUT DESC";

$result_checkin_codes = $koneksi->query($sql_checkin_codes);

// =======================================================
// === FORM RELOAD UNTUK AMBIL DATA OTOMATIS ===
// =======================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_checkin_out']) && !isset($_POST['simpan_pembayaran'])) {

    $selected_id_checkin_out = (int)$_POST['id_checkin_out'];

    if ($selected_id_checkin_out > 0) {

        // AMBIL DATA CHECK-IN
        $sql_ci_lookup = "SELECT 
            c.ID_PELANGGAN, 
            c.ID_JENIS_KAMAR, 
            c.TGL_CHECK_IN, 
            c.LAMA_INAP, 
            (jk.TARIF * c.LAMA_INAP) AS TOTAL,
            COALESCE(pm.UANG_MUKA, 0) AS UANG_MUKA,
            pel.NAMA_PELANGGAN,
            jk.JENIS_KAMAR
        FROM checkin_checkout c
        INNER JOIN pelanggan pel ON c.ID_PELANGGAN = pel.ID_PELANGGAN
        INNER JOIN jenis_kamar jk ON c.ID_JENIS_KAMAR = jk.ID_JENIS_KAMAR
        LEFT JOIN pemesanan pm ON c.ID_PEMESANAN = pm.ID_PEMESANAN
        WHERE c.ID_CHECKIN_OUT = ? LIMIT 1";

        $stmt_ci = $koneksi->prepare($sql_ci_lookup);

        if ($stmt_ci) {
            $stmt_ci->bind_param("i", $selected_id_checkin_out);
            $stmt_ci->execute();
            $result_ci = $stmt_ci->get_result();

            if ($row_ci = $result_ci->fetch_assoc()) {

                $selected_id_pelanggan = (int)$row_ci['ID_PELANGGAN'];
                $selected_id_jenis_kamar = (int)$row_ci['ID_JENIS_KAMAR'];

                $nama_pelanggan_display = $row_ci['NAMA_PELANGGAN'];
                $jenis_kamar_display = $row_ci['JENIS_KAMAR'];
                $tgl_checkin_display = $row_ci['TGL_CHECK_IN'];

                $lama_inap_value = (int)$row_ci['LAMA_INAP'];
                $lama_inap_display = $lama_inap_value . " Hari";

                $total_kamar = (int)$row_ci['TOTAL'];
                $uang_muka = (int)$row_ci['UANG_MUKA'];
                $sisa_pembayaran_awal = $total_kamar - $uang_muka;

                // AMBIL TOTAL LAUNDRY
                $sql_laundry_sum = "SELECT 
                        l.ID_LAUNDRY,
                        SUM(dl.TOTAL_LAUNDRY) AS TOTAL_LDR
                    FROM laundry l
                    LEFT JOIN detail_laundry dl ON l.ID_LAUNDRY = dl.ID_LAUNDRY
                    WHERE l.ID_PELANGGAN = ?
                    GROUP BY l.ID_LAUNDRY
                    ORDER BY l.ID_LAUNDRY DESC LIMIT 1";

                $stmt_ldr = $koneksi->prepare($sql_laundry_sum);
                if ($stmt_ldr) {
                    $stmt_ldr->bind_param("i", $selected_id_pelanggan);
                    $stmt_ldr->execute();
                    $result_ldr = $stmt_ldr->get_result();

                    if ($row_ldr = $result_ldr->fetch_assoc()) {
                        if ($row_ldr['ID_LAUNDRY'] !== NULL && (int)$row_ldr['TOTAL_LDR'] > 0) {
                            $selected_id_laundry = (int)$row_ldr['ID_LAUNDRY'];
                            $biaya_laundry = (int)$row_ldr['TOTAL_LDR'];
                        } else {
                            $selected_id_laundry = 'NULL';
                            $biaya_laundry = 0;
                        }
                    } else {
                         $selected_id_laundry = 'NULL';
                         $biaya_laundry = 0;
                    }
                    $stmt_ldr->close();
                }

                // AMBIL TOTAL FNB
                $sql_fnb_sum = "SELECT 
                        f.ID_FNB,
                        SUM(df.TOTAL_FNB) AS TOTAL_FNB
                    FROM food_n_beverage f
                    LEFT JOIN detail_food_n_beverage df ON f.ID_FNB = df.ID_FNB
                    WHERE f.ID_PELANGGAN = ?
                    GROUP BY f.ID_FNB
                    ORDER BY f.ID_FNB DESC LIMIT 1";

                $stmt_fnb = $koneksi->prepare($sql_fnb_sum);
                if ($stmt_fnb) {
                    $stmt_fnb->bind_param("i", $selected_id_pelanggan);
                    $stmt_fnb->execute();
                    $result_fnb = $stmt_fnb->get_result();

                    if ($row_fnb = $result_fnb->fetch_assoc()) {
                        if ($row_fnb['ID_FNB'] !== NULL && (int)$row_fnb['TOTAL_FNB'] > 0) {
                            $selected_id_fnb = (int)$row_fnb['ID_FNB'];
                            $biaya_fnb = (int)$row_fnb['TOTAL_FNB'];
                        } else {
                            $selected_id_fnb = 'NULL';
                            $biaya_fnb = 0;
                        }
                    } else {
                        $selected_id_fnb = 'NULL';
                        $biaya_fnb = 0;
                    }
                    $stmt_fnb->close();
                }

                // HITUNG TOTAL AKHIR
                $total_keseluruhan = $sisa_pembayaran_awal + $biaya_laundry + $biaya_fnb;
                $sisa_pembayaran_akhir = $total_keseluruhan;
            } else {
                echo "<script>alert('Data Check-in untuk ID tersebut tidak ditemukan.');</script>";
            }

            $stmt_ci->close();
        } else {
            echo "<script>alert('Error menyiapkan kueri data check-in: " . htmlspecialchars($koneksi->error) . "');</script>";
        }
    }

    // Reset pointer result checkin codes agar bisa digunakan lagi di HTML
    if ($result_checkin_codes && $result_checkin_codes->num_rows > 0)
        $result_checkin_codes->data_seek(0);
}

// =======================================================
// === SIMPAN PEMBAYARAN ===
// =======================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan_pembayaran'])) {

    $id_checkin_out = (int)$_POST['id_checkin_out'];
    $id_pelanggan = (int)$_POST['hidden_id_pelanggan'];
    $id_jenis_kamar = (int)$_POST['hidden_id_jenis_kamar'];

    // Ambil nilai ID, pastikan NULL atau integer
    $id_laundry = ($_POST['hidden_id_laundry'] == 'NULL' || $_POST['hidden_id_laundry'] == '') ? NULL : (int)$_POST['hidden_id_laundry'];
    $id_fnb = ($_POST['hidden_id_fnb'] == 'NULL' || $_POST['hidden_id_fnb'] == '') ? NULL : (int)$_POST['hidden_id_fnb'];

    $tgl_checkin = $_POST['hidden_tgl_checkin'];
    $lama_inap = (int)$_POST['hidden_lama_inap'];
    $total_kamar_val = (int)$_POST['hidden_total_kamar'];
    $uang_muka_val = (int)$_POST['hidden_uang_muka'];
    $biaya_laundry_val = (int)$_POST['hidden_biaya_laundry'];
    $biaya_fnb_val = (int)$_POST['hidden_biaya_fnb'];

    $bayar_sekarang = (int)$_POST['input_total_bayar_sekarang'];

    $total_tagihan_akhir = ($total_kamar_val - $uang_muka_val) + $biaya_laundry_val + $biaya_fnb_val;
    $sisa_pembayaran_akhir_val = $total_tagihan_akhir - $bayar_sekarang;

    if ($id_checkin_out <= 0 || $id_pelanggan <= 0 || $id_jenis_kamar <= 0) {
        echo "<script>alert('ID tidak valid. Silakan pilih Pemesanan yang Belum Lunas.');window.history.back();</script>";
        exit;
    }

    // INSERT PEMBAYARAN dengan handling NULL
    if ($id_laundry === NULL && $id_fnb === NULL) {
        // Kedua NULL
        $sql_insert = "INSERT INTO pembayaran 
            (ID_JENIS_KAMAR, ID_FNB, ID_LAUNDRY, ID_PELANGGAN, 
            TGL_CHECK_IN, LAMA_INAP, TOTAL, UANG_MUKA, 
            SISA_PEMBAYARAN, BIAYA_LAUNDRY, BIAYA_FNB, 
            TOTAL_BAYAR, ID_CHECKIN_OUT)
            VALUES (?, NULL, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $koneksi->prepare($sql_insert);
        $stmt->bind_param(
            "iisiiiiiiii",
            $id_jenis_kamar,
            $id_pelanggan,
            $tgl_checkin,
            $lama_inap,
            $total_kamar_val,
            $uang_muka_val,
            $sisa_pembayaran_akhir_val,
            $biaya_laundry_val,
            $biaya_fnb_val,
            $total_tagihan_akhir,
            $id_checkin_out
        );
    } elseif ($id_laundry === NULL) {
        // Laundry NULL, FNB ada
        $sql_insert = "INSERT INTO pembayaran 
            (ID_JENIS_KAMAR, ID_FNB, ID_LAUNDRY, ID_PELANGGAN, 
            TGL_CHECK_IN, LAMA_INAP, TOTAL, UANG_MUKA, 
            SISA_PEMBAYARAN, BIAYA_LAUNDRY, BIAYA_FNB, 
            TOTAL_BAYAR, ID_CHECKIN_OUT)
            VALUES (?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $koneksi->prepare($sql_insert);
        $stmt->bind_param(
            "iiisiiiiiiii",
            $id_jenis_kamar,
            $id_fnb,
            $id_pelanggan,
            $tgl_checkin,
            $lama_inap,
            $total_kamar_val,
            $uang_muka_val,
            $sisa_pembayaran_akhir_val,
            $biaya_laundry_val,
            $biaya_fnb_val,
            $total_tagihan_akhir,
            $id_checkin_out
        );
    } elseif ($id_fnb === NULL) {
        // FNB NULL, Laundry ada
        $sql_insert = "INSERT INTO pembayaran 
            (ID_JENIS_KAMAR, ID_FNB, ID_LAUNDRY, ID_PELANGGAN, 
            TGL_CHECK_IN, LAMA_INAP, TOTAL, UANG_MUKA, 
            SISA_PEMBAYARAN, BIAYA_LAUNDRY, BIAYA_FNB, 
            TOTAL_BAYAR, ID_CHECKIN_OUT)
            VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $koneksi->prepare($sql_insert);
        $stmt->bind_param(
            "iiisiiiiiiii",
            $id_jenis_kamar,
            $id_laundry,
            $id_pelanggan,
            $tgl_checkin,
            $lama_inap,
            $total_kamar_val,
            $uang_muka_val,
            $sisa_pembayaran_akhir_val,
            $biaya_laundry_val,
            $biaya_fnb_val,
            $total_tagihan_akhir,
            $id_checkin_out
        );
    } else {
        // Kedua ada nilai
        $sql_insert = "INSERT INTO pembayaran 
            (ID_JENIS_KAMAR, ID_FNB, ID_LAUNDRY, ID_PELANGGAN, 
            TGL_CHECK_IN, LAMA_INAP, TOTAL, UANG_MUKA, 
            SISA_PEMBAYARAN, BIAYA_LAUNDRY, BIAYA_FNB, 
            TOTAL_BAYAR, ID_CHECKIN_OUT)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $koneksi->prepare($sql_insert);
        $stmt->bind_param(
            "iiisiiiiiiii",
            $id_jenis_kamar,
            $id_fnb,
            $id_laundry,
            $id_pelanggan,
            $tgl_checkin,
            $lama_inap,
            $total_kamar_val,
            $uang_muka_val,
            $sisa_pembayaran_akhir_val,
            $biaya_laundry_val,
            $biaya_fnb_val,
            $total_tagihan_akhir,
            $id_checkin_out
        );
    }

    if ($stmt->execute()) {

        // UPDATE STATUS PEMBAYARAN di checkin_checkout
        $status = ($sisa_pembayaran_akhir_val <= 0) ? "Lunas" : "Belum Lunas";

        $up = $koneksi->prepare("UPDATE checkin_checkout 
                SET STATUS_PEMBAYARAN=? WHERE ID_CHECKIN_OUT=?");
        $up->bind_param("si", $status, $id_checkin_out);
        $up->execute();
        $up->close();

        echo "<script>
                alert('✅ Pembayaran berhasil ditambahkan! Status: " . $status . "');
                window.location='pembayaran.php';
            </script>";
        exit;
    } else {
        echo "<script>alert('❌ Gagal insert pembayaran: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Data Pembayaran</title>
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="dist/css/adminlte.min.css">

<style>
.text-right { text-align:right !important; }
.bg-info { background:#17a2b8 !important; color:white; }
.bg-warning { background-color: #ffc107 !important; color: black; }
.bg-success { background-color: #28a745 !important; color: white; }
</style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'header.php'; include 'navbar.php'; ?>

<div class="content-wrapper">
<section class="content-header">
    <div class="container-fluid">
        <h1>Tambah Data Pembayaran</h1>
    </div>
</section>

<section class="content">
<div class="container-fluid">

<div class="card card-primary">
<div class="card-header">
    <h3 class="card-title">Form Pembayaran</h3>
</div>

<form method="POST" id="pembayaranForm">
<div class="card-body">

<div class="form-group">
    <label>ID Check-in/Pemesanan (Belum Lunas)</label>
    <select class="form-control" name="id_checkin_out" id="id_checkin_out"
            onchange="document.getElementById('pembayaranForm').submit();" required>
        <option value="">-- Pilih --</option>
        <?php 
        if ($result_checkin_codes && $result_checkin_codes->num_rows > 0) {
            while ($row = $result_checkin_codes->fetch_assoc()) { ?>
                <option value="<?= $row['ID_CHECKIN_OUT']; ?>"
                    <?= ($row['ID_CHECKIN_OUT']==$selected_id_checkin_out)?'selected':''; ?>>
                    ID <?= $row['ID_CHECKIN_OUT']; ?> | <?= htmlspecialchars($row['NAMA_PELANGGAN']); ?> | <?= htmlspecialchars($row['JENIS_KAMAR']); ?> (<?= $row['STATUS_PEMBAYARAN']; ?>)
                </option>
            <?php }
        }
        ?>
    </select>
</div>

<fieldset disabled>
    <div class="form-group">
        <label>Nama Pelanggan</label>
        <input class="form-control" value="<?= htmlspecialchars($nama_pelanggan_display) ?>">
    </div>

    <div class="form-group">
        <label>Jenis Kamar</label>
        <input class="form-control" value="<?= htmlspecialchars($jenis_kamar_display) ?>">
    </div>

    <div class="row">
        <div class="col-sm-6">
            <label>Tgl Check-in</label>
            <input class="form-control" value="<?= htmlspecialchars($tgl_checkin_display) ?>">
        </div>

        <div class="col-sm-6">
            <label>Lama Inap</label>
            <input class="form-control" value="<?= htmlspecialchars($lama_inap_display) ?>">
        </div>
    </div>
</fieldset>

<hr>
<h4>Rincian Biaya</h4>

<div class="row">
    <div class="col-sm-6">
        <label>Total Biaya Kamar</label>
        <input class="form-control text-right" readonly
               value="Rp <?= number_format($total_kamar, 0, ',', '.') ?>">
    </div>

    <div class="col-sm-6">
        <label>Uang Muka</label>
        <input class="form-control text-right" readonly
               value="Rp <?= number_format($uang_muka, 0, ',', '.') ?>">
    </div>
</div>

<div class="row mt-2">
    <div class="col-sm-4">
        <label>Sisa Kamar (Awal)</label>
        <input class="form-control text-right" readonly
               value="Rp <?= number_format($sisa_pembayaran_awal, 0, ',', '.') ?>">
    </div>

    <div class="col-sm-4">
        <label>Biaya Laundry (ID: <?= $selected_id_laundry == 'NULL' ? 'N/A' : $selected_id_laundry ?>)</label>
        <input class="form-control text-right" readonly
               value="Rp <?= number_format($biaya_laundry, 0, ',', '.') ?>">
    </div>

    <div class="col-sm-4">
        <label>Biaya F&B (ID: <?= $selected_id_fnb == 'NULL' ? 'N/A' : $selected_id_fnb ?>)</label>
        <input class="form-control text-right" readonly
               value="Rp <?= number_format($biaya_fnb, 0, ',', '.') ?>">
    </div>
</div>

<div class="form-group mt-3">
    <label>TOTAL TAGIHAN AKHIR (Wajib Bayar)</label>
    <input class="form-control text-right bg-info" id="total_tagihan_display" readonly
           value="Rp <?= number_format($total_keseluruhan, 0, ',', '.') ?>">
</div>

<hr>

<div class="form-group">
    <label for="input_total_bayar_sekarang">Jumlah Bayar Sekarang</label>
    <input type="number" class="form-control" name="input_total_bayar_sekarang" id="input_total_bayar_sekarang"
           value="<?= $total_keseluruhan ?>" min="0" required 
           <?= $selected_id_checkin_out > 0 ? '' : 'disabled'; ?>>
</div>

<div class="form-group">
    <label>Sisa Pembayaran Akhir</label>
    <input class="form-control text-right" id="sisa_pembayaran_akhir_display" readonly
           value="Rp <?= number_format($sisa_pembayaran_akhir, 0, ',', '.') ?>">
</div>

<input type="hidden" name="hidden_id_pelanggan" value="<?= $selected_id_pelanggan ?>">
<input type="hidden" name="hidden_id_jenis_kamar" value="<?= $selected_id_jenis_kamar ?>">
<input type="hidden" name="hidden_id_laundry" value="<?= $selected_id_laundry ?>">
<input type="hidden" name="hidden_id_fnb" value="<?= $selected_id_fnb ?>">

<input type="hidden" name="hidden_tgl_checkin" value="<?= $tgl_checkin_display ?>">
<input type="hidden" name="hidden_lama_inap" value="<?= $lama_inap_value ?>">
<input type="hidden" name="hidden_total_kamar" value="<?= $total_kamar ?>">
<input type="hidden" name="hidden_uang_muka" value="<?= $uang_muka ?>">
<input type="hidden" name="hidden_biaya_laundry" value="<?= $biaya_laundry ?>">
<input type="hidden" name="hidden_biaya_fnb" value="<?= $biaya_fnb ?>">

</div>

<div class="card-footer">
    <button type="submit" name="simpan_pembayaran" class="btn btn-primary"
            <?= $selected_id_checkin_out > 0 ? '' : 'disabled'; ?>>
        Simpan Pembayaran
    </button>
</div>
</form>

</div>
</div>
</section>

</div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function(){
    // Nilai total yang harus dibayar (dari PHP)
    const totalKeseluruhan = parseFloat(<?= $total_keseluruhan; ?>);
    const sisaPembayaranAwal = parseFloat(<?= $sisa_pembayaran_awal; ?>);
    const biayaLaundry = parseFloat(<?= $biaya_laundry; ?>);
    const biayaFnb = parseFloat(<?= $biaya_fnb; ?>);

    // Fungsi Menghitung Sisa Pembayaran Otomatis
    function calculateSisaBayar() {
        const bayarSekarang = parseFloat($("#input_total_bayar_sekarang").val()) || 0;
        
        // Pastikan totalKeseluruhan dihitung ulang di sisi klien
        const totalTagihan = sisaPembayaranAwal + biayaLaundry + biayaFnb;
        
        const sisaAkhir = totalTagihan - bayarSekarang;
        
        // Format angka dengan pemisah ribuan
        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        // Update tampilan Sisa Pembayaran Akhir
        $("#sisa_pembayaran_akhir_display").val(formatter.format(sisaAkhir));
        
        // Visual feedback
        if (sisaAkhir <= 0) {
            $("#sisa_pembayaran_akhir_display").removeClass('bg-warning bg-danger').addClass('bg-success');
        } else {
            $("#sisa_pembayaran_akhir_display").removeClass('bg-success').addClass('bg-warning');
        }
    }

    // Panggil fungsi saat input Jumlah Bayar Sekarang berubah
    $("#input_total_bayar_sekarang").on('input', calculateSisaBayar);
    
    // Panggil saat halaman dimuat untuk inisialisasi
    calculateSisaBayar(); 

    // Tampilkan notifikasi jika ID Check-in belum dipilih
    $('#pembayaranForm').submit(function(e) {
        if ($('#id_checkin_out').val() === "" && !$(this).find('[name="simpan_pembayaran"]').is(':focus')) {
            alert('Silakan pilih ID Check-in/Pemesanan terlebih dahulu.');
            e.preventDefault();
        }
    });

});
</script>
</body>
</html>
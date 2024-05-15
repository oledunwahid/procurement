<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php
function formatRupiah($value)
{
    return "Rp. " . number_format($value, 0, ',', '.');
}

$id_proc_ch = $_GET['id']; // Pastikan nilai ini sesuai dengan konteks

//tambahin nama_admin cokk
$queryPR = "SELECT proc_purchase_requests.*, user_request.nama as nama_request, user_pic.nama as nama_pic, proc_category.nama_category as category_name  
            FROM proc_purchase_requests 
            JOIN user user_request ON proc_purchase_requests.nik_request = user_request.idnik
            JOIN user user_pic ON proc_purchase_requests.proc_pic = user_pic.idnik
            JOIN proc_category ON proc_purchase_requests.category = proc_category.id_category
            WHERE proc_purchase_requests.id_proc_ch = '$id_proc_ch'";
$resultPR = mysqli_query($koneksi, $queryPR);
$dataPR = mysqli_fetch_assoc($resultPR);

// Query untuk mengambil detail Purchase Request
$queryDetail = "SELECT * FROM proc_request_details WHERE id_proc_ch = '" . $dataPR['id_proc_ch'] . "'";
$resultDetail = mysqli_query($koneksi, $queryDetail);

// Menghitung total
$total = 0;
while ($row = mysqli_fetch_assoc($resultDetail)) {
    $totalPrice = $row['qty'] * $row['unit_price'];
    $total += $totalPrice;
}

$queryPR1 = "SELECT * FROM proc_purchase_requests WHERE id_proc_ch = '$id_proc_ch'";
$resultPR1 = mysqli_query($koneksi, $queryPR1);
$dataPR1 = mysqli_fetch_assoc($resultPR1);
?>

<title>Procurement - Price Request</title>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .company-info {
        text-align: center;
        margin-bottom: 20px;
    }

    .company-name {
        font-size: 18px;
        font-weight: bold;
    }

    .header {
        text-align: left;
        margin-bottom: 20px;
    }

    .document-title {
        font-size: 16px;
        font-weight: bold;
        margin: 0;
    }

    .pr-details {
        margin-top: 5px;
        /* Mengatur jarak atas */
    }

    .pr-details p {
        display: flex;
        align-items: center;
        /* Ini memastikan items di dalamnya vertikal sejajar */
        margin: 2px 0;
        /* Mengurangi margin atas dan bawah */
        line-height: 1.2;
        /* Mengatur line-height lebih kecil */
    }

    .pr-details p label {
        width: 120px;
        /* Atau lebar yang cukup untuk label terpanjang Anda, disesuaikan */
        min-width: 120px;
        /* Pastikan semua label memiliki lebar yang sama */
        margin-right: 8px;
        /* Menambahkan sedikit ruang antara label dan isi */
    }

    .pr-details p span {
        flex-grow: 1;
        /* Memastikan isi mengambil ruang yang tersisa */
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .totals {
        margin-top: 20px;
        text-align: right;
    }

    .totals table {
        border: none;
        margin-top: 10px;
        width: auto;
        margin-left: auto;
        line-height: 1.0;
    }

    .totals td {
        border: none;
        padding: 5px;
    }

    .remarks {
        margin-top: 20px;
        border: 1px solid black;
        padding: 10px;
    }

    .terms {
        font-size: 10px;
        /* Menjadikan font lebih kecil */
        margin-bottom: 5px;
        /* Mengurangi margin bawah */
    }

    .terms-list {
        list-style-type: none;
        /* Menghilangkan bullet points */
        padding-left: 0;
        /* Menghilangkan padding default */
        margin-top: 0;
        /* Mengurangi margin atas */
        font-size: 10px;
        /* Menyesuaikan ukuran font */
    }

    .terms-list li {
        margin-bottom: 2px;
        /* Mengurangi margin antar item */
        line-height: 1.0;
    }

    .approval {
        margin-top: 30px;
    }

    .approval table {
        width: 100%;
        table-layout: fixed;
    }

    .approval th,
    .approval td {
        border: none;
        text-align: center;
    }
</style>

<div class="company-info">
    <div class="company-name">Procurement Price Request</div>
</div>

<div class="header">
    <h2 class="document-title">Price Request - <?= $dataPR['id_proc_ch']; ?></h2>

    <div class="pr-details pt-4">
        <p><label>Date Request</label><span>: <?= date('j F Y', strtotime($dataPR['created_request'])); ?></span></p>
        <p><label>Title</label><span>: <?= $dataPR['title']; ?></span></p>
        <p><label>Request By</label><span>: <?= $dataPR['nama_request']; ?></span></p>
        <p><label>PIC</label><span>: <?= $dataPR['nama_pic']; ?></span></p>
        <p><label>Category</label><span>: <?= $dataPR['category_name']; ?></span></p>
        <p><label>Job Location</label><span>: <?= $dataPR['job_location']; ?></span></p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Name Product</th>
            <th width="20%">Detail Specification</th>
            <th width="3%">Qty</th>
            <th width="3%">Uom</th>
            <th width="15%">Price (per Unit)</th>
            <th width="20%">Total Price</th>
            <th width="10%">Detail Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php
        mysqli_data_seek($resultDetail, 0); // Mengulangi fetch pada result detail
        while ($row = mysqli_fetch_assoc($resultDetail)) : ?>
            <tr>
                <td><?= $row['nama_barang']; ?></td>
                <td><?= $row['detail_specification']; ?></td>
                <td><?= $row['qty']; ?></td>
                <td><?= $row['uom']; ?></td>
                <td><?= formatRupiah($row['unit_price']) ?></td>
                <td><?= formatRupiah($row['qty'] * $row['unit_price']) ?></td>
                <td><?= $row['detail_notes']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td><b>Total:</b></td>
            <td><b><?= formatRupiah($total) ?></b></td>
        </tr>
    </table>
</div>
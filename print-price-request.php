<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php
function formatRupiah($value)
{
    return "Rp. " . number_format($value, 0, ',', '.');
}

$id_proc_ch = $_GET['id']; // Pastikan nilai ini sesuai dengan konteks

// Tambahkan nama_admin
$queryPR = "SELECT
                pp.id_proc_ch,
                pp.title,
                pp.created_request,
                pp.status,
                pp.nik_request,
                pp.proc_pic,
                user1.nama AS nama_request,
                user1.divisi AS divisi_request,
                user2.nama AS nama_pic
            FROM
                proc_purchase_requests AS pp
                LEFT JOIN user AS user1 ON pp.nik_request = user1.idnik
                LEFT JOIN user AS user2 ON pp.proc_pic = user2.idnik
            WHERE
                pp.id_proc_ch = '$id_proc_ch'";
$resultPR = mysqli_query($koneksi, $queryPR);
$dataPR = mysqli_fetch_assoc($resultPR);

// Query untuk mengambil detail Purchase Request
$queryDetail = "SELECT prd.*, pc.nama_category 
                FROM proc_request_details prd
                LEFT JOIN proc_category pc ON prd.category = pc.id_category
                WHERE prd.id_proc_ch = '" . $dataPR['id_proc_ch'] . "'";
$resultDetail = mysqli_query($koneksi, $queryDetail);

// Menghitung total
$total = 0;
while ($row = mysqli_fetch_assoc($resultDetail)) {
    $totalPrice = $row['qty'] * $row['unit_price'];
    $total += $totalPrice;
}

// Reset resultDetail untuk digunakan kembali di bagian tabel
mysqli_data_seek($resultDetail, 0);
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
    }

    .pr-details p {
        display: flex;
        align-items: center;
        margin: 2px 0;
        line-height: 1.2;
    }

    .pr-details p label {
        width: 120px;
        min-width: 120px;
        margin-right: 8px;
    }

    .pr-details p span {
        flex-grow: 1;
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
        margin-bottom: 5px;
    }

    .terms-list {
        list-style-type: none;
        padding-left: 0;
        margin-top: 0;
        font-size: 10px;
    }

    .terms-list li {
        margin-bottom: 2px;
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
            <th width="10%">Category</th>
            <th width="10%">Detail Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($resultDetail)) : ?>
            <tr>
                <td><?= $row['nama_barang']; ?></td>
                <td><?= $row['detail_specification']; ?></td>
                <td><?= $row['qty']; ?></td>
                <td><?= $row['uom']; ?></td>
                <td><?= formatRupiah($row['unit_price']) ?></td>
                <td><?= formatRupiah($row['qty'] * $row['unit_price']) ?></td>
                <td><?= $row['nama_category']; ?></td>
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
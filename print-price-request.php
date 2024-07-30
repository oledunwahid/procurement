<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        line-height: 1.4;
        color: #333;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .company-name {
        font-size: 18px;
        font-weight: bold;
    }

    .document-title {
        font-size: 16px;
        font-weight: bold;
        margin-top: 10px;
    }

    .details {
        margin-bottom: 20px;
    }

    .details-row {
        display: flex;
        margin-bottom: 5px;
    }

    .details-label {
        width: 150px;
        font-weight: bold;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .total {
        text-align: right;
        font-weight: bold;
    }

    .approval {
        margin-top: 40px;
    }

    .approval-row {
        display: flex;
        justify-content: space-between;
    }

    .approval-column {
        text-align: center;
        width: 30%;
    }

    .signature-line {
        margin-top: 60px;
        border-top: 1px solid #333;
    }

    @media print {
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<?php
function formatRupiah($value)
{
    return "Rp. " . number_format($value, 0, ',', '.');
}

$id_proc_ch = $_GET['id'];

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

$queryDetail = "SELECT prd.*, pc.nama_category 
                    FROM proc_request_details prd
                    LEFT JOIN proc_category pc ON prd.category = pc.id_category
                    WHERE prd.id_proc_ch = '" . $dataPR['id_proc_ch'] . "'";
$resultDetail = mysqli_query($koneksi, $queryDetail);

$total = 0;
while ($row = mysqli_fetch_assoc($resultDetail)) {
    $totalPrice = $row['qty'] * $row['unit_price'];
    $total += $totalPrice;
}
mysqli_data_seek($resultDetail, 0);
?>

<div class="header">
    <div class="company-info">
        <div class="company-name">Procurement Price Request</div>
    </div>
</div>

<div class="header">
    <h2 class="document-title">Price Request - <?= htmlspecialchars($dataPR['id_proc_ch']); ?></h2>

    <div class="pr-details">
        <label>Date Request:</label>
        <span><?= date('j F Y', strtotime($dataPR['created_request'])); ?></span>
        <label>Title:</label>
        <span><?= htmlspecialchars($dataPR['title']); ?></span>
        <label>Request By:</label>
        <span><?= htmlspecialchars($dataPR['nama_request']); ?></span>
        <label>PIC:</label>
        <span><?= htmlspecialchars($dataPR['nama_pic']); ?></span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Name Product</th>
            <th>Detail Specification</th>
            <th>Qty</th>
            <th>Uom</th>
            <th>Price (per Unit)</th>
            <th>Total Price</th>
            <th>Category</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($resultDetail)) : ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                <td><?= htmlspecialchars($row['detail_specification']); ?></td>
                <td><?= htmlspecialchars($row['qty']); ?></td>
                <td><?= htmlspecialchars($row['uom']); ?></td>
                <td><?= formatRupiah($row['unit_price']) ?></td>
                <td><?= formatRupiah($row['qty'] * $row['unit_price']) ?></td>
                <td><?= htmlspecialchars($row['nama_category']); ?></td>
            </tr>
            <tr class="detail-notes mobile-full-width">
                <td colspan="7"><strong>Detail Notes:</strong> <?= htmlspecialchars($row['detail_notes']); ?></td>
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

<div class="approval">
    <table>
        <tr>
            <th>Requested by</th>
            <th>Approved by</th>
            <th>Procurement</th>
        </tr>
        <tr>
            <td style="height: 100px;"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><?= htmlspecialchars($dataPR['nama_request']); ?></td>
            <td>_________________</td>
            <td><?= htmlspecialchars($dataPR['nama_pic']); ?></td>
        </tr>
    </table>
</div>
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
        margin-bottom: 30px;
    }

    .company-name {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .document-title {
        font-size: 18px;
        font-weight: bold;
    }

    .pr-details {
        margin-bottom: 20px;
        display: grid;
        grid-template-columns: auto auto;
        gap: 10px;
    }

    .pr-details label {
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

    .detail-notes {
        background-color: #f9f9f9;
    }

    .totals {
        text-align: right;
        font-weight: bold;
        margin-top: 20px;
    }

    #printModal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 300px;
        text-align: center;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }


    @media print {
        #printModal {
            display: none !important;
        }
    }

    @media print {
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
<style>
    #printModal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #f8f9fa;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 400px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .print-icon {
        font-size: 48px;
        text-align: center;
        margin: 20px 0;
    }

    h2 {
        color: #333;
        text-align: center;
        margin-bottom: 15px;
    }

    ol {
        margin-left: 20px;
        margin-bottom: 20px;
    }

    kbd {
        background-color: #eee;
        border-radius: 3px;
        border: 1px solid #b4b4b4;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .2), 0 2px 0 0 rgba(255, 255, 255, .7) inset;
        color: #333;
        display: inline-block;
        font-size: .85em;
        font-weight: 700;
        line-height: 1;
        padding: 2px 4px;
        white-space: nowrap;
    }

    .note {
        font-style: italic;
        color: #666;
        margin-bottom: 15px;
    }

    button {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }

    @media print {
        #printModal {
            display: none !important;
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
    user2.nama AS nama_pic,
    (SELECT l.timestamp 
     FROM proc_admin_log l 
     WHERE l.table_name = 'proc_purchase_requests' 
     AND l.record_id = pp.id_proc_ch 
     AND l.new_value LIKE '%\"status\":\"Closed\"%'
     ORDER BY l.timestamp DESC 
     LIMIT 1) as closed_date
FROM
    proc_purchase_requests AS pp
    LEFT JOIN user AS user1 ON pp.nik_request = user1.idnik
    LEFT JOIN user AS user2 ON pp.proc_pic = user2.idnik
WHERE
    pp.id_proc_ch = ?";

$stmt = mysqli_prepare($koneksi, $queryPR);
mysqli_stmt_bind_param($stmt, "s", $id_proc_ch);
mysqli_stmt_execute($stmt);
$resultPR = mysqli_stmt_get_result($stmt);
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

<div id="printModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>

        </div>
        <div class="print-icon">🖨️</div>
        <h2>Print Justification Document</h2>
        <p>To print this justification document, please follow these steps:</p>
        <ol>
            <li>Press <kbd>Ctrl + P</kbd> (Windows) or <kbd>Cmd + P</kbd> (Mac)</li>
            <li>Select your printer or "Save as PDF"</li>
            <li>Click "Print" or "Save"</li>
        </ol>
        <div class="note">Note: This popup won't appear in the printed version.</div>
        <button onclick="document.getElementById('printModal').style.display='none';">Got it!</button>
    </div>
</div>

<div class="container">
    <div class="header">
        <div class="company-name">Procurement Price Request</div>
        <div class="document-title">Justification - <?= htmlspecialchars($dataPR['id_proc_ch']); ?></div>
    </div>

    <div class="pr-details">
        <label>Date Request:</label>
        <span><?= date('j F Y H:i:s', strtotime($dataPR['created_request'])); ?></span>

        <?php if (!empty($dataPR['closed_date'])): ?>
            <label>Date Closed:</label>
            <span><?= date('j F Y H:i:s', strtotime($dataPR['closed_date'])); ?></span>
        <?php endif; ?>

        <label>Title:</label>
        <span><?= htmlspecialchars($dataPR['title']); ?></span>

        <label>Request By:</label>
        <span><?= htmlspecialchars($dataPR['nama_request']); ?></span>

        <label>Division:</label>
        <span><?= htmlspecialchars($dataPR['divisi_request']); ?></span>

        <label>PIC:</label>
        <span><?= htmlspecialchars($dataPR['nama_pic']); ?></span>

        <label>Status:</label>
        <span><?= htmlspecialchars($dataPR['status']); ?></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Description</th>
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
                <tr class="detail-notes">
                    <td colspan="7"><strong>Detail Notes:</strong> <?= htmlspecialchars($row['detail_notes']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="totals">
        <p>Total: <?= formatRupiah($total) ?></p>
    </div>
</div>

<script>
    window.onload = function() {
        document.getElementById('printModal').style.display = "block";
    }

    document.getElementsByClassName("close")[0].onclick = function() {
        document.getElementById('printModal').style.display = "none";
    }

    window.onclick = function(event) {
        var modal = document.getElementById('printModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    window.addEventListener('beforeprint', function() {
        console.log('Print initiated');
    });

    window.addEventListener('afterprint', function() {
        console.log('Print completed or canceled');
    });
</script>
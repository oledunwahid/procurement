
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php 



function formatRupiah($value) {
    return "Rp. " . number_format($value, 0, ',', '.');
}

$id_mr = $_GET['id']; // Pastikan nilai ini sesuai dengan konteks

$queryMR = "SELECT budget_mr.*, user.nama as nama_pengguna, user.divisi FROM budget_mr 
            JOIN user ON budget_mr.id_request = user.idnik 
            WHERE budget_mr.id_mr = '$id_mr'";
$resultMR = mysqli_query($koneksi, $queryMR);
$dataMR = mysqli_fetch_assoc($resultMR);

// Query untuk mengambil detail MR
$queryDetail = "SELECT * FROM budget_detail_mr WHERE id_mr = '".$dataMR['id_mr']."'";
$resultDetail = mysqli_query($koneksi, $queryDetail);

// Menghitung total dan PPN
$subtotal = 0;
while($row = mysqli_fetch_assoc($resultDetail)) {
    $totalPrice = $row['qty_barang'] * $row['price'];
    $subtotal += $totalPrice;
}
$ppn = $subtotal * ($dataMR['ppn'] / 100);
$totalSetelahPPN = $subtotal + $ppn;

// Query untuk mengambil approval info (asumsi hanya mengambil satu row untuk contoh)
$queryApproval = "SELECT * FROM budget_approval_mr WHERE id_mr = '".$dataMR['id_mr']."' LIMIT 1";
$resultApproval = mysqli_query($koneksi, $queryApproval);
$dataApproval = mysqli_fetch_assoc($resultApproval);

$queryMR1 = "SELECT * FROM budget_mr WHERE id_mr = '$id_mr'";
$resultMR1 = mysqli_query($koneksi, $queryMR1);
$dataMR1 = mysqli_fetch_assoc($resultMR1);

// Query untuk mendapatkan data approval berdasarkan id_mr
$queryApproval1 = "SELECT bam.*, u.nama AS approver_name FROM budget_approval_mr bam
                  INNER JOIN user u ON bam.nik_approval = u.idnik
                  WHERE bam.id_mr = '$id_mr'
                  ORDER BY bam.id_approval ASC";
$resultApproval1 = mysqli_query($koneksi, $queryApproval1);
$approvals1 = [];
while ($row = mysqli_fetch_assoc($resultApproval1)) {
    $approvals1[] = $row;
}
$canApprove1 = false;
if (count($approvals1) > 0) {
    foreach ($approvals1 as $index => $approval1) {
        if ($approval1['nik_approval'] == $niklogin && $approval1['status'] == 'Pending') {
            // Cek apakah ini approval pertama atau approval sebelumnya sudah Approved
            if ($index == 0 || ($index > 0 && $approvals1[$index - 1]['status'] == 'Approved')) {
                $canApprove1 = true;
            }
            break;
        }
    }
}

?>
    <title>Material Request - Project</title>
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
        .mr-details {
    margin-top: 5px; /* Mengatur jarak atas */
}

.mr-details p {
    display: flex;
    align-items: center; /* Ini memastikan items di dalamnya vertikal sejajar */
    margin: 0; /* Menghilangkan margin */
    padding: 1px 0; /* Mengurangi padding untuk menjaga jarak antar baris minimum */
    line-height: 0.5; /* Mengatur line-height untuk lebih kecil jika perlu */
}

.mr-details p label {
    width: 120px; /* Atau lebar yang cukup untuk label terpanjang Anda, disesuaikan */
    min-width: 120px; /* Pastikan semua label memiliki lebar yang sama */
    margin-right: 8px; /* Menambahkan sedikit ruang antara label dan isi */
}

.mr-details p span {
    flex-grow: 1; /* Memastikan isi mengambil ruang yang tersisa */
}
		

		
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
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
            font-size: 10px; /* Menjadikan font lebih kecil */
            margin-bottom: 5px; /* Mengurangi margin bawah */
        }
        .terms-list {
            list-style-type: none; /* Menghilangkan bullet points */
            padding-left: 0; /* Menghilangkan padding default */
            margin-top: 0; /* Mengurangi margin atas */
            font-size: 10px; /* Menyesuaikan ukuran font */
        }
        .terms-list li {
            margin-bottom: 2px; /* Mengurangi margin antar item */
			 line-height: 1.0
        }
        .approval {
            margin-top: 30px;
        }
        .approval table {
            width: 100%;
            table-layout: fixed;
        }
        .approval th, .approval td {
            border: none;
            text-align: center;
        }
    </style>

     <div class="company-info">
        <div class="company-name"><?= $dataMR['pt_mr']; ?>
		
    </div>
		 	<?php if ($canApprove1): ?>
    <!-- Tampilkan tombol hanya jika $canApprove1 true -->
    <button onclick="processMR('<?= $approval1['id_approval']; ?>', 'Approved')">Approve</button>
<button onclick="processMR('<?= $approval1['id_approval']; ?>', 'Rejected')">Reject</button>
<?php endif; ?>
    <!-- Detail Material Request di sini -->
		 </div>
		  
        <div class="header">
            <h2 class="document-title">Material Request - <?= $dataMR['type_mr']; ?></h2>
			
            <div class="mr-details">
            <p><label>Date Request</label><span>: <?= date('j F Y', strtotime($dataMR['tgl_mr'])); ?></span></p>
			<p><label>Date Needed</label><span>: <?= date('j F Y', strtotime($dataMR['tgl_need_mr'])); ?></span></p>
            <p><label>Type</label><span>: <?= $dataMR['type_mr']; ?></span></p>
            <p><label>Request By</label><span>: <?= $dataMR['nama_pengguna']; ?> - <?= $dataMR['divisi']; ?></span></p>
            <p><label>Location</label><span>: <?= $dataMR['lokasi_mr']; ?></span></p>
            <p><label>Priority</label><span>: <?= $dataMR['priority_mr']; ?></span></p>
            <?php if ($dataMR['priority_mr'] == 'Urgent') { ?>
            <p><label>Reason for Urgency</label><span>: <?=$dataMR['dekripsi_priority_mr'] ?></span></p>
            <?php } ?>
        </div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Deskripsi Name Product</th>
                <th width="3%">Qty</th>
				<th width="3%">Uom</th>
                <th width="20%">Price (per Unit)</th>
                <th width="20%">Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php
            mysqli_data_seek($resultDetail, 0); // Mengulangi fetch pada result detail
            while($row = mysqli_fetch_assoc($resultDetail)): ?>
            <tr>
                <td><?= $row['nama_barang']; ?></td>
                <td><?= $row['qty_barang']; // Asumsi ini adalah unit ?></td>
				<td><?= $row['uom']; ?></td>
                <td><?= formatRupiah($row['price']) ?></td>
                <td><?= formatRupiah($row['qty_barang'] * $row['price']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="totals">
        <table>
            <tr>
                <td>Sub Total:</td>
                <td><?= formatRupiah($subtotal) ?></td>
            </tr>
            <tr>
                <td>PPN (<?=$dataMR['ppn']?>%):</td>
                <td><?= formatRupiah($ppn) ?></td>
            </tr>
            <tr>
                <td><b>Total Setelah PPN:</b></td>
                <td><b><?= formatRupiah($totalSetelahPPN) ?></b></td>
            </tr>
        </table>
    </div>
   <div class="remarks">
        <strong>REASON OF NEED:</strong> <?= $dataMR['deskripsi']; ?>
    </div>
    <div>
        <p class="terms">Terms & Conditions:</p>
        <ul class="terms-list">
            <!-- Contoh syarat & ketentuan, sesuaikan jika data bersumber dari database -->
            <li>Lead time pengadaan terhitung sejak RFM diterima oleh tim pengadaan hingga barang tiba di lokasi untuk masing-masing lokasi, dengan estimasi minimum lead time di bawah ini:</li>
            <li>a. Untuk Jakarta perkiraan normal 15 hari</li>
            <li>b. Untuk lapangan site Obi perkiraan normal 43 hari</li>
        </ul>
    </div>
  <?php
// Ambil id_mr yang diinginkan, bisa dari query parameter atau konteks halaman
$id_mr = $_GET['id']; // Pastikan nilai ini sesuai dengan konteks

// Query untuk mendapatkan data pengguna yang melakukan request
$query_mr = "SELECT bm.id_request, u.nama AS requested_by FROM budget_mr bm JOIN user u ON bm.id_request = u.idnik WHERE bm.id_mr = '$id_mr'";
$result_mr = mysqli_query($koneksi, $query_mr);
$requested_by = mysqli_fetch_assoc($result_mr);

// Query untuk mendapatkan data approval
$query_approval = "SELECT ba.nik_approval, ba.status, ba.date_approval, u.nama, u.position FROM budget_approval_mr ba JOIN user u ON ba.nik_approval = u.idnik WHERE ba.id_mr = '$id_mr' order by id_approval asc";
$result_approval = mysqli_query($koneksi, $query_approval);

$approvals = [];
if (mysqli_num_rows($result_approval) > 0) {
    while ($row = mysqli_fetch_assoc($result_approval)) {
        $approvals[] = $row;
    }
}
?>

<div class="approval">
    <table>
        <thead>
            <tr>
                <th>Requested By</th>
                <?php foreach ($approvals as $approval): ?>
                    <th><?=$approval['nama']; ?> <br><?=$approval['position']; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($requested_by['requested_by']); ?></td>
                <?php foreach ($approvals as $approval): ?>
                    <td>
                         <?=$approval['status']; ?><br>
                        Date: <?=$approval['date_approval']; ?><br>
                    </td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
</div>
<ul class="terms-list">
            <!-- Contoh syarat & ketentuan, sesuaikan jika data bersumber dari database -->
            <li><i>* This Material Request form does not require a signature as it has already been processed by the system.</i>
        </ul>

  <script>
   function processMR(idApproval, action) {
    // Menambahkan dialog konfirmasi
    var confirmAction = confirm("Apakah Anda yakin akan " + action.toLowerCase() + " dokumen ini?");
    if (confirmAction) {
        console.log("Sending AJAX request for ID Approval:", idApproval, "Action:", action); // Debug log
        $.ajax({
            url: 'function/process_mr.php',
            type: 'POST',
            data: {id_approval: idApproval, action: action},
            success: function(response) {
                // Log response dari server untuk debugging
                console.log("Response from server:", response);
                alert('MR ' + action);
                location.reload(); // Reload halaman untuk melihat perubahan
            },
            error: function(xhr, status, error) {
                // Log error jika AJAX request gagal
                console.error("AJAX request failed:", status, error);
                alert("Failed to process MR. Please try again.");
            }
        });
    } else {
        console.log(action + " cancelled by user."); // User membatalkan aksi
    }
}

</script>
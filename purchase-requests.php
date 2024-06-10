<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<?php
$isAdmin = false;
$sql = "SELECT * FROM user_roles WHERE idnik = '$niklogin' AND id_role = 5";
$result = mysqli_query($koneksi, $sql);
if (mysqli_num_rows($result) > 0) {
    $isAdmin = true;
}

// Query untuk mendapatkan data purchase requests
$sql = "
SELECT
pp.id_proc_ch,
pp.title,
pp.created_request,
pp.status,
pp.nik_request,
pp.proc_pic,
user1.nama AS nama_request,
user1.divisi AS divisi_request,
GROUP_CONCAT(DISTINCT prd.category SEPARATOR ', ') AS categories
FROM
proc_purchase_requests AS pp
LEFT JOIN user AS user1 ON pp.nik_request = user1.idnik
LEFT JOIN proc_request_details AS prd ON pp.id_proc_ch = prd.id_proc_ch
WHERE
'$niklogin' IN (SELECT idnik FROM user_roles WHERE id_role = 5) OR pp.nik_request = '$niklogin'
GROUP BY pp.id_proc_ch
";

// Eksekusi query dan cek apakah berhasil
$result = mysqli_query($koneksi, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($koneksi);
} else {
?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Request Price Forms</h5>
                        <div class="flex-shrink-0">
                            <?php
                            // Jika bukan admin, tampilkan tombol "Create Price Request"
                            if (!$isAdmin) {
                            ?>
                                <form action="function/insert_view_purchase_request.php" method="POST">
                                    <input type="text" value="<?= $niklogin ?>" name="nik_request" hidden />
                                    <button class="btn btn-danger add-btn" name="add-purchase-request" type="submit"><i class="ri-add-line align-bottom me-1"></i> Create Price Request</button>
                                </form>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="buttons-datatables" class="display table table-bordered dt-responsive" style="width:100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>ID Request</th>
                                <th>Title</th>
                                <th>Created Date</th>
                                <th>Requestor</th>
                                <th>Status</th>
                                <th>Division</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nomor = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                <tr>
                                    <td><?= $nomor++ ?></td>
                                    <td>
                                        <?php if ($isAdmin) { ?>
                                            <a href="index.php?page=DetailPurchase&id=<?= $row['id_proc_ch']; ?>"><?= $row['id_proc_ch'] ?></a>
                                        <?php } else { ?>
                                            <a href="index.php?page=ViewPriceReq&id=<?= $row['id_proc_ch']; ?>"><?= $row['id_proc_ch'] ?></a>
                                        <?php } ?>
                                    </td>
                                    <td><?= $row['title'] ?></td>
                                    <td><?= $row['created_request'] ?></td>
                                    <td><?= $row['nama_request'] ?></td>
                                    <td><?= $row['status'] ?></td>
                                    <td><?= $row['divisi_request'] ?></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="index.php?page=ViewPriceReq&id=<?= $row['id_proc_ch']; ?>" class="dropdown-item"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li>
                                                <?php if ($isAdmin) { ?>
                                                    <li><a class="dropdown-item" href="index.php?page=DetailPurchase&id=<?= $row['id_proc_ch']; ?>"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                    <li><a class="dropdown-item" href="index.php?page=DeletePurchase&id=<?= $row['id_proc_ch']; ?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--end col-->
    </div>
<?php
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!--datatable js-->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="../assets/js/pages/datatables.init.js"></script>
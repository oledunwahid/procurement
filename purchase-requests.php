<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Request Price Forms</h5>
                    <div class="flex-shrink-0">
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                            <form action="function/insert_purchase_request.php" method="POST">
                                <input type="text" value="<?= $niklogin ?>" name="nik_request" hidden />
                                <button class="btn btn-danger add-btn" name="add-purchase-request" type="submit"><i class="ri-add-line align-bottom me-1"></i> Create Price Request</button>
                            </form>
                        <?php } else { ?>
                            <a href="index.php?page=DetailPurchase" class="btn btn-danger add-btn"><i class="ri-add-line align-bottom me-1"></i> Create Price Request</a>
                        <?php } ?>
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
                            <th>Status</th>
                            <th>Category</th>
                            <th>Job Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = mysqli_query($koneksi, "SELECT pp.id_proc_ch, pp.title, pp.created_request, pp.status, pc.nama_category, pp.job_location 
                        FROM proc_purchase_requests AS pp 
                        INNER JOIN proc_category AS pc ON pp.category = pc.id_category");
                        $nomor = 1;
                        while ($row = mysqli_fetch_assoc($sql)) {
                        ?>
                            <tr>
                                <td><?= $nomor++ ?></td>
                                <td><a href="index.php?page=ViewPurchase&id=<?= $row['id_proc_ch']; ?>"><?= $row['id_proc_ch'] ?></a></td>
                                <td><?= $row['title'] ?></td>
                                <td><?= $row['created_request'] ?></td>
                                <td><?= $row['status'] ?></td>
                                <td><?= $row['nama_category'] ?></td>
                                <td><?= $row['job_location'] ?></td>
                                <td>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="index.php?page=ViewPurchase&id=<?= $row['id_proc_ch']; ?>" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                            <?php if ($_SESSION['role'] == 'admin') { ?>
                                                <li><a class="dropdown-item" href="index.php?page=DetailPurchase&id=<?= $row['id_proc_ch']; ?>"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li><a class="dropdown-item" href="index.php?page=DeletePurchase&id=<?= $row['id_proc_ch']; ?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!--end col-->
</div>





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
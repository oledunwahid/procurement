<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/2.2.0/css/searchPanes.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">


<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/searchpanes/2.2.0/js/dataTables.searchPanes.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['success_message'])) { ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $_SESSION['success_message']; ?>',
                showConfirmButton: false,
                timer: 3000
            });
            <?php unset($_SESSION['success_message']); ?>
        <?php } ?>
        <?php if (isset($_SESSION['error_message'])) { ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error_message']; ?>',
                showConfirmButton: false,
                timer: 3000
            });
            <?php unset($_SESSION['error_message']); ?>
        <?php } ?>
    });
</script>

<style>
    /* Ensure that the demo table scrolls */
    th,
    td {
        white-space: nowrap;
    }

    div.dataTables_wrapper {
        margin: 0 auto;
    }
</style>


<!-- start page title -->
<div class="row">
    <?php
    $sql7 = mysqli_query($koneksi, "SELECT * FROM access_level WHERE idnik = $niklogin");
    $row7 = mysqli_fetch_assoc($sql7);
    ?>
    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php
                        // Menghitung total permintaan pembelian
                        $sqlTotal = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM proc_request_details");
                        $totalProcRequests = mysqli_fetch_assoc($sqlTotal)['total'];
                        ?>
                        <p class="fw-medium text-muted mb-0">Total Purchase Requests</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $totalProcRequests ?>"></span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info text-info rounded-circle fs-4">
                                <i class="ri-shopping-cart-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div> <!-- end card-->
    </div>
    <!--end col-->

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php
                        $sqlTotalPurchase = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM proc_purchase_requests");
                        $totalPurchaseRequests = mysqli_fetch_assoc($sqlTotalPurchase)['total'];
                        ?>
                        <p class="fw-medium text-muted mb-0">Approved Purchase Orders</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $totalPurchaseRequests ?>"></span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info text-info rounded-circle fs-4">
                                <i class="ri-file-text-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <div class="card-title mb-0 flex-grow-1 flex">
                        <h5>Purchase Requests</h5>
                        <h6>Submit your purchase requests here.</h6>
                    </div>
                    <div class="flex-shrink-0">
                        <button class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                            <i class="ri-add-line align-bottom me-1"></i> Create Request
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <table id="procurementTable" class="stripe row-border order-column" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID Request</th>
                                            <th>Title</th>
                                            <th>Create Date</th>
                                            <th>Requestor</th>
                                            <th>PIC</th>
                                            <th>Status</th>
                                            <th>Category</th>
                                            <th>Urgencies</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal -->
<div class="modal fade zoomIn" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title" id="exampleModalLabel">Create Purchase Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form action="function/insert_purchase_request.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <input type="hidden" name="created_request" value="<?= date('Y-m-d H:i:s') ?>">
                            <input type="hidden" name="nik_request" value="<?= $niklogin ?>">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title:</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="lampiran" class="form-label">Attachment:</label>
                                <input type="file" class="form-control" id="lampiran" name="lampiran">
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category:</label>
                                <input type="text" class="form-control" id="category" name="category" required>
                            </div>
                            <div class="mb-3">
                                <label for="urgencies" class="form-label">Urgencies:</label>
                                <input type="text" class="form-control" id="urgencies" name="urgencies" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang:</label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                            </div>
                            <div class="mb-3">
                                <label for="qty" class="form-label">Quantity:</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="btnMinus">-</button>
                                    <input type="number" class="form-control" id="qty" name="qty" required>
                                    <button class="btn btn-outline-secondary" type="button" id="btnPlus">+</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="uom" class="form-label">Unit of Measure (UoM):</label>
                                <input type="text" class="form-control" id="uom" name="uom" required>
                            </div>
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks:</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="unit_price" class="form-label">Unit Price:</label>
                                <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-light" style="margin-right: 5px;" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_purchase_request">Submit Request</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/searchpanes/2.1.4/js/dataTables.searchPanes.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.2/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#procurementTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "procurement-data.php",
            "columns": [{
                    "data": "id_request"
                },
                {
                    "data": "title"
                },
                {
                    "data": "created_request"
                },
                {
                    "data": "proc_pic"
                },
                {
                    "data": "status"
                },
                {
                    "data": "category"
                }
            ],
            "order": [
                [2, 'desc'] // Sesuaikan dengan indeks kolom tanggal pembuatan
            ],
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength',
                'colvis',
                'searchPanes',
                'excelHtml5'
            ],
            "columnDefs": [{
                    "targets": [0, 3],
                    "visible": false
                } // Sembunyikan kolom ID dan qty dari searchPanes
            ]
        });
    });
</script>
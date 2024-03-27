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
    .input-step.light input[type="number"] {
        width: calc(100% - 110px);
        /* Sesuaikan lebar input di sini */
        margin: 0 5px;
    }

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

    <!-- (Created, Closed, Process) -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Pending Requests</p>
                        <?php if (isset($row7['admin']) && ($row7['admin'] == '1')) {
                            // Menghitung total permintaan yang masih dalam status tertentu
                            $sqlPending = mysqli_query($koneksi, "SELECT id_request FROM proc_request_details WHERE status = 'Pending'");
                            $pendingRequests = mysqli_fetch_assoc($sqlPending);
                        } elseif (isset($row7['proc_pic']) && ($row7['proc_pic'] == '1')) {
                            $sql1 = mysqli_query($koneksi, "SELECT proc_request_details.id_request, user.lokasi FROM proc_request_details INNER JOIN user ON proc_request_details.nik_request = user.idnik  WHERE status = 'Pending' AND user.lokasi IN ($lokasi) ");
                            $CreatedTiket = mysqli_num_rows($sql1);
                        } else {
                            // Menghitung total permintaan yang masih dalam status tertentu
                            $sqlPending = mysqli_query($koneksi, "SELECT id_request FROM proc_request_details WHERE status = 'Pending' AND nik_request ='$niklogin' ");
                            $pendingRequests = mysqli_fetch_assoc($sqlPending);
                        }
                        ?>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $pendingRequests ?>">0</span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info text-info rounded-circle fs-4">
                                <i class="mdi mdi-timer-sand"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Closed Requests</p>
                        <?php
                        if (isset($row7['admin']) && $row7['admin'] == '1') {
                            $sql2 = mysqli_query($koneksi, "SELECT id_request FROM proc_request_details WHERE status = 'Closed' ");
                            $closedRequests = mysqli_num_rows($sql2);
                        } elseif (isset($row7['proc_pic']) && $row7['proc_pic'] == '1') {
                            $sql2 = mysqli_query($koneksi, "SELECT proc_request_details.id_request, user.lokasi FROM proc_request_details INNER JOIN user ON proc_request_details.nik_request = user.idnik  WHERE status = 'Closed' AND user.lokasi IN ($lokasi) ");
                            $closedRequests = mysqli_num_rows($sql2);
                        } else {
                            $sql2 = mysqli_query($koneksi, "SELECT id_request FROM proc_request_details WHERE status = 'Closed' AND nik_request ='$niklogin' ");
                            $closedRequests = mysqli_num_rows($sql2);
                        }
                        ?>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $closedRequests ?>">0</span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info text-info rounded-circle fs-4">
                                <i class="ri-mail-close-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div>

    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Process Requests</p>
                        <?php
                        if (isset($row7['admin']) && $row7['admin'] == '1') {
                            $sql3 = mysqli_query($koneksi, "SELECT id_request FROM proc_request_details WHERE status = 'Process' ");
                            $processRequests = mysqli_num_rows($sql3);
                        } elseif (isset($row7['proc_pic']) && $row7['proc_pic'] == '1') {
                            $sql3 = mysqli_query($koneksi, "SELECT proc_request_details.id_request, user.lokasi FROM proc_request_details INNER JOIN user ON proc_request_details.nik_request = user.idnik  WHERE status = 'Process' AND user.lokasi IN ($lokasi) ");
                            $processRequests = mysqli_num_rows($sql3);
                        } else {
                            $sql3 = mysqli_query($koneksi, "SELECT id_request FROM proc_request_details WHERE status = 'Process' AND nik_request ='$niklogin' ");
                            $processRequests = mysqli_num_rows($sql3);
                        }
                        ?>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $processRequests ?>">0</span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info text-info rounded-circle fs-4">
                                <i class="ri-delete-bin-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
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
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Office Supplies">Office Supplies</option>
                                    <option value="Stationery">Stationery</option>
                                    <option value="Machinery">Machinery</option>
                                    <!-- Tambahkan opsi lainnya sesuai kebutuhan -->
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="urgencies" class="form-label">Urgencies:</label>
                                <select class="form-select" id="urgencies" name="urgencies" required>
                                    <option value="">Select Urgency</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
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
                                    <button type="button" class="btn btn-primary minus">–</button>
                                    <input type="number" class="form-control product-quantity" value="0" min="0" max="100" name="qty">
                                    <button type="button" class="btn btn-primary plus">+</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="uom" class="form-label">Unit of Measurement (UoM):</label>
                                <div class="input-group">
                                    <select class="form-select" id="uom" name="uom" required>
                                        <option value="">Select UoM</option>
                                        <option value="kg">Kilogram (kg)</option>
                                        <option value="cm">Centimeter (cm)</option>
                                        <option value="m">Meter (m)</option>
                                    </select>
                                    <div class="input-group-append uom-details" style="display: none;">
                                        <input type="number" class="form-control" id="kg" name="kg" placeholder="Kilograms(kg)">
                                        <input type="number" class="form-control" id="width" name="width" placeholder="Width (cm)">
                                        <input type="number" class="form-control" id="height" name="height" placeholder="Height (cm)">
                                        <input type="number" class="form-control" id="length" name="length" placeholder="Length (cm)">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="unit_price" class="form-label">Unit Price:</label>
                                <input type="text" class="form-control" id="unit_price" name="unit_price" step="0.01" oninput="formatInputAsRupiah(this)" required>
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks:</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Mendapatkan elemen input quantity
        var quantityInput = document.querySelector('.product-quantity');

        // Mendapatkan tombol plus dan minus
        var plusButton = document.querySelector('.plus');
        var minusButton = document.querySelector('.minus');

        // Menambahkan event listener untuk tombol plus
        plusButton.addEventListener('click', function() {
            var currentValue = parseInt(quantityInput.value);
            // Mengecek apakah nilai saat ini kurang dari 100
            if (currentValue < 100) {
                // Menambahkan 1 ke nilai saat ini
                quantityInput.value = currentValue + 1;
            }
        });

        // Menambahkan event listener untuk tombol minus
        minusButton.addEventListener('click', function() {
            var currentValue = parseInt(quantityInput.value);
            // Mengecek apakah nilai saat ini lebih dari 0
            if (currentValue > 0) {
                // Mengurangkan 1 dari nilai saat ini
                quantityInput.value = currentValue - 1;
            }
        });

        // Menambahkan event listener untuk mengontrol nilai yang dimasukkan secara manual
        quantityInput.addEventListener('change', function() {
            var currentValue = parseInt(quantityInput.value);
            // Memastikan nilai tetap di dalam rentang 0 hingga 100
            if (currentValue < 0 || isNaN(currentValue)) {
                quantityInput.value = 0;
            } else if (currentValue > 100) {
                quantityInput.value = 100;
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var uomSelect = document.getElementById('uom');
        var uomDetails = document.querySelector('.uom-details');
        var kgInput = document.getElementById('kg');

        // Tampilkan detail UoM yang sesuai saat memilih UoM dari dropdown
        uomSelect.addEventListener('change', function() {
            if (uomSelect.value === 'kg') {
                uomDetails.style.display = 'block';
                kgInput.placeholder = 'Kilograms (kg)';
            } else if (uomSelect.value === 'm') {
                uomDetails.style.display = 'block';
                kgInput.placeholder = 'Meters (m)';
            } else if (uomSelect.value === 'cm') {
                uomDetails.style.display = 'block';
                kgInput.placeholder = 'Centimeters (cm)';
            } else {
                uomDetails.style.display = 'none';
            }
        });
    });
</script>
<script>
    function formatRupiah(angka) {
        // Hapus karakter selain angka
        var clean = angka.replace(/\D/g, '');

        // Ubah ke format Rupiah
        var reverse = clean.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join('.').split('').reverse().join('');
        return 'Rp ' + formatted;
    }

    function formatInputAsRupiah(input) {
        input.value = formatRupiah(input.value);
    }
</script>
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
                }
            ]
        });
    });
</script>
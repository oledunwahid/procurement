<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Tambahkan library SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .card-enhanced {
        transition: transform .3s, box-shadow .3s;
        cursor: pointer;
    }

    .card-enhanced:hover {
        transform: scale(1.03);
        box-shadow: 0 10px 20px rgba(0, 0, 0, .12), 0 4px 8px rgba(0, 0, 0, .06);
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }


    .btn-enhanced {
        transition: background-color .3s, transform .3s;
    }

    .btn-enhanced:hover {
        transform: translateY(-2px);
    }

    /* Menyesuaikan lebar kolom pada tabel */


    /* Custom button styles */
    .btn-icon .ri {
        margin-right: 4px;
    }

    .action-buttons .btn {
        padding: 5px 10px;
        font-size: 14px;
    }

    /* Responsive card body padding */
    .card-body-enhanced {
        padding: 1.5rem;
    }

    /* Customizing the input fields for a better look */
    .form-control {
        border-radius: 0.375rem;
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
<style>
    @media screen and (max-width: 767px) {
        #detail-purchase-request thead {
            display: none;
        }

        #detail-purchase-request,
        #detail-purchase-request tbody,
        #detail-purchase-request tr,
        #detail-purchase-request td {
            display: block;
            width: 100%;
        }

        #detail-purchase-request tr {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        #detail-purchase-request td {
            text-align: left;
            padding: 8px 8px 8px 45%;
            position: relative;
            min-height: 30px;
            border: none;
        }

        #detail-purchase-request td::before {
            content: attr(data-label);
            position: absolute;
            left: 6px;
            width: 40%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #555;
        }

        #detail-purchase-request td input,
        #detail-purchase-request td select,
        #detail-purchase-request td textarea {
            width: 100% !important;
            margin-top: 5px;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            margin-top: 10px;
        }

        .action-buttons button {
            margin: 2px;
        }
    }
</style>
<!-- comment style -->
<style>
    #commentSection {
        max-height: 500px;
        overflow-y: auto;
        padding: 15px;
    }

    .comment-bubble {
        max-width: 100%;
        margin-bottom: 15px;
        clear: both;
    }

    .comment-bubble .card {
        border-radius: 20px;
    }

    .comment-bubble.left .card {
        background-color: #f0f0f0;
    }

    .comment-bubble.right .card {
        background-color: #dcf8c6;
        float: right;
    }

    .comment-bubble .card-body {
        padding: 10px 15px;
    }

    .comment-bubble .card-text {
        margin-bottom: 5px;
    }

    .comment-bubble .text-muted {
        font-size: 0.8em;
    }
</style>
<style>
    /* Add to your existing CSS section in detail-purchase-request.php */

    /* Price column styling */
    .price-column {
        min-width: 120px !important;
        /* Ensures minimum width for price columns */
        width: auto !important;
        /* Allows column to grow based on content */
        white-space: nowrap;
        /* Prevents wrapping of price values */
    }

    .total-price-column {
        min-width: 130px !important;
        /* Slightly wider for total price columns */
        width: auto !important;
        white-space: nowrap;
    }

    /* Price cell styling */
    .price-value {
        font-family: 'Roboto Mono', monospace, sans-serif;
        /* Monospace font for better number alignment */
        text-align: right;
        padding-right: 8px;
        font-weight: 500;
        white-space: nowrap;
        overflow: visible;
        /* Allow content to overflow */
    }

    /* Tooltip styling for price values */
    .price-tooltip {
        position: relative;
        display: inline-block;
        cursor: default;
    }

    .price-tooltip .tooltip-text {
        visibility: hidden;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 4px;
        padding: 5px 10px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 14px;
        white-space: nowrap;
        pointer-events: none;
    }

    .price-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    /* Additional responsive styles */
    @media screen and (max-width: 767px) {

        /* Improve mobile table layout for price columns */
        #detail-purchase-request td[data-label="Harga:"],
        #detail-purchase-request td[data-label="Total Harga:"] {
            text-align: right !important;
            font-weight: bold;
        }

        .price-value {
            width: 100%;
            text-align: right;
            display: inline-block;
        }

        /* Handle very large numbers on small screens */
        .price-value-mobile {
            font-size: 14px;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
    }

    /* Print-friendly styles */
    @media print {

        .price-column,
        .total-price-column {
            min-width: auto !important;
            width: auto !important;
        }

        .price-value {
            font-family: serif;
            /* Better for printing */
        }

        /* Ensure prices don't get cut off when printing */
        #detail-purchase-request th,
        #detail-purchase-request td {
            white-space: nowrap;
        }
    }
</style>

<?php
$id_proc_ch = $_GET['id'];
$sql = mysqli_query($koneksi, "SELECT * FROM proc_purchase_requests WHERE  id_proc_ch ='$id_proc_ch' "); // query jika filter dipilih
$row = mysqli_fetch_assoc($sql) // fetch query yang sesuai ke dalam array
?>

<div class="col">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Input Detail Price Request</h5>
                        <a href="index.php?page=PurchaseRequests" class="btn btn-close btn-lg">
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="sticky-top bg-white py-2 mb-3">
                        <button type="button" class="btn btn-primary" id="addRow">
                            <i class="ri-add-line icon-btn-space"></i>Add Row
                        </button>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover table-sm" id="detail-purchase-request">
                            <thead>
                                <tr>
                                    <th style="display:none;">ID Request</th>
                                    <th width="20%">Nama Barang</th>
                                    <th width="20%">Detail Spec</th>
                                    <th width="5%">Qty</th>
                                    <th width="15%">Category</th>
                                    <th width="10%">Uom</th>
                                    <th width="5%">Harga</th>
                                    <th width="5%">Total Harga</th>
                                    <th width="10%">Urgency Status</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan di-load menggunakan AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card card-enhanced">
                <div class="card-body card-body-enhanced">
                    <h5 class="card-title">Price Request - <?= $row['status'] ?></h5>
                    <form id="updatePurchaseRequestForm">
                        <input type="hidden" name="niklogin" value="<?= $niklogin ?>">
                        <div class="card-body border-bottom border-bottom-dashed">
                            <div class="row g-3">
                                <div class="col-lg-3 col-sm-6">
                                    <label for="invoicenoInput">No Price Request</label>
                                    <input type="text" name="id_proc_ch" class="form-control bg-light border-0" value="<?= $row['id_proc_ch'] ?>" readonly>
                                </div>
                                <?php
                                $currentDateTime = date("Y-m-d H:i:s");
                                ?>
                                <div class="col-lg-3 col-sm-6">
                                    <div>
                                        <label for="date-field">Current Datetime</label>
                                        <input type="text" name="created_request" class="form-control bg-light border-0" value="<?= $currentDateTime ?>" readonly>
                                    </div>
                                </div>
                                <?php
                                $idnik = $row['nik_request'];
                                $query = "SELECT nama FROM user WHERE idnik = ?";
                                $stmt = $koneksi->prepare($query);
                                $stmt->bind_param("s", $idnik);
                                $stmt->execute();
                                $stmt->bind_result($nama);
                                $stmt->fetch();
                                $stmt->close();
                                ?>

                                <div class="col-lg-3 col-sm-6">
                                    <label for="choices-payment-status">Nama Requester</label>
                                    <div class="input-light">
                                        <input type="text" name="requester_name_display" class="form-control bg-light border-0" value="<?= htmlspecialchars($nama) ?>" readonly>
                                        <input type="hidden" name="requester_name" value="<?= htmlspecialchars($idnik) ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <label for="choices-payment-status">Title</label>
                                    <div class="input-light">
                                        <input type="text" name="title" class="form-control" value="<?= $row['title'] ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div>
                                        <label for="totalamountInput">Total Amount</label>
                                        <input type="text" name="total_price" id="total_price" class="form-control bg-light border-0" value="<?php echo 'Rp ' . number_format($row['total_price'], 0, ',', '.'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div>
                                        <label for="totalamountInput">Attachment <strong>* MAX 2MB (PDF, JPG , PDF ,JPEG)</strong></label>
                                        <input type="file" name="lampiran" class="form-control" accept=".pdf,.jpg,.png,.jpeg">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3">
                            <button type="submit" name="updatePurchaseRequestForm" class="btn btn-primary">Submit</button>
                            <span id="submitMessage" class="text-danger ml-2" style="display: none;">
                                Please submit all detail rows before submitting the main form.
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--form comments -->
<div class="col-lg-12 mt-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Comments</h5>
            <div id="commentSection" class="mb-4">
                <!-- Comments will be loaded here dynamically -->
            </div>
            <form id="addCommentForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="commentText" class="form-label">Leave a Comment</label>
                    <textarea class="form-control" id="commentText" name="comment" rows="3" placeholder="Enter your comment"></textarea>
                </div>
                <div class="mb-3">
                    <label for="attachments" class="form-label">Attachments (Optional)</label>
                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                    <div id="filePreview" class="mt-2">
                        <!-- File previews will appear here -->
                    </div>
                    <small class="text-muted">
                        Allowed file types: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP (Max size: 5MB per file)
                    </small>
                </div>
                <input type="hidden" name="id_proc_ch" value="<?= htmlspecialchars($row['id_proc_ch']); ?>">
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const idProcCh = <?= json_encode($_GET['id']); ?>;
        let allDetailRowsSubmitted = true;
        const currentUser = {
            id: <?= json_encode($_SESSION['idnik'] ?? ''); ?>,
            name: <?= json_encode($current_user['nama'] ?? ''); ?>
        };

        function formatRibuan(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function applyDataLabels() {
            $('#detail-purchase-request tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    const label = $('#detail-purchase-request thead th').eq(index).text();
                    $(this).attr('data-label', label + ':');
                });
            });
        }

        function updateMainFormSubmitButton() {
            const $submitButton = $('#updatePurchaseRequestForm button[type="submit"]');
            const $lockIcon = $('#lockIcon');
            const $submitMessage = $('#submitMessage');

            if (allDetailRowsSubmitted) {
                $submitButton.prop('disabled', false);
                $lockIcon.hide();
                $submitMessage.hide();
            } else {
                $submitButton.prop('disabled', true);
                $lockIcon.show();
                $submitMessage.show();
            }
        }

        function loadData(callback) {
            $.ajax({
                url: 'function/fetch_view_detail_purchase_request.php',
                type: 'GET',
                data: {
                    id_proc_ch: idProcCh
                },
                success: function(data) {
                    $('#detail-purchase-request tbody').html(data);
                    allDetailRowsSubmitted = $('#detail-purchase-request tbody tr').length > 0;
                    updateMainFormSubmitButton();
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    console.error('Load Data Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load data. Please refresh the page.'
                    });
                }
            });
        }

        function submitDetailRow($row, url, method) {
            const formData = new FormData();

            // Store old values before changes (for update cases)
            if (method === 'update') {
                const oldData = {
                    nama_barang: $row.find("input[name='nama_barang[]']").data('original-value'),
                    qty: $row.find("input[name='qty[]']").data('original-value'),
                    category: $row.find("select[name='category[]']").data('original-value'),
                    uom: $row.find("select[name='uom[]']").data('original-value'),
                    detail_specification: $row.find("textarea[name='detail_specification[]']").data('original-value'),
                    urgency_status: $row.find("select[name='urgency_status[]']").data('original-value'),
                    unit_price: $row.find("[name='unit_price[]']").data('original-value'),
                };
                formData.append('old_data', JSON.stringify(oldData));
            }

            // Add user info for logging
            formData.append('user_id', currentUser.id);
            formData.append('user_name', currentUser.name);
            formData.append('action_timestamp', new Date().toISOString());

            const requiredFields = {
                'id_proc_ch': $row.find("input[name='id_proc_ch[]']").val(),
                'nama_barang': $row.find("input[name='nama_barang[]']").val(),
                'qty': $row.find("input[name='qty[]']").val(),
                'category': $row.find("select[name='category[]']").val(),
                'uom': $row.find("select[name='uom[]']").val(),
                'urgency_status': $row.find("select[name='urgency_status[]']").val()
            };

            for (const [key, value] of Object.entries(requiredFields)) {
                if (!value) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: `${key.replace('_', ' ')} is required`
                    });
                    return;
                }
                formData.append(key, value);
            }

            formData.append('detail_specification', $row.find("textarea[name='detail_specification[]']").val() || '');
            formData.append('detail_notes', '');
            formData.append('unit_price', $row.find("[name='unit_price[]']").text().replace(/\./g, '') || '0');

            if (method === 'update') {
                formData.append('id', $row.find('.saveRow').data('id'));
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: result.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                loadData(() => {
                                    applyDataLabels();
                                    allDetailRowsSubmitted = true;
                                    updateMainFormSubmitButton();
                                });
                            });
                        } else {
                            throw new Error(result.message || 'Unknown error occurred');
                        }
                    } catch (e) {
                        console.error('Response Error:', e, response);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: e.message
                        });
                        allDetailRowsSubmitted = false;
                        updateMainFormSubmitButton();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        xhr,
                        status,
                        error
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save data. Please try again.'
                    });
                    allDetailRowsSubmitted = false;
                    updateMainFormSubmitButton();
                }
            });
        }

        function addRow() {
            Promise.all([
                $.ajax({
                    url: 'function/get_uom.php',
                    type: 'GET',
                    dataType: 'json'
                }),
                $.ajax({
                    url: 'function/get_category.php',
                    type: 'GET',
                    dataType: 'json'
                }), // Ditambahkan koma
                $.ajax({
                    url: 'function/get_urgency_status.php',
                    type: 'GET',
                    dataType: 'json'
                })
            ]).then(([uomData, categoryData, urgencyData]) => {
                const uomOptions = uomData.map(uom =>
                    `<option value="${uom.uom_name}">${uom.uom_name}</option>`
                ).join('');

                const categoryOptions = categoryData.map(category =>
                    `<option value="${category.id_category}">${category.nama_category}</option>`
                ).join('');

                const urgencyOptions = urgencyData.map(urgency =>
                    `<option value="${urgency.id}">${urgency.name}</option>`
                ).join('');

                const newRow = `<tr>
                <td style="display:none;">
                    <input type="text" name="id_proc_ch[]" class="form-control" value="${idProcCh}" readonly />
                </td>
                <td data-label="Nama Barang">
                    <input type="text" name="nama_barang[]" class="form-control nama-barang" style="width: 100%;" required />
                </td>
                <td data-label="Detail Spec">
                    <textarea name="detail_specification[]" class="form-control" style="width: 100%;"></textarea>
                </td>
                <td data-label="Qty">
                    <input type="number" name="qty[]" class="form-control" maxlength="5" style="width: 80px;" required />
                </td>
                <td data-label="Category">
                    <select name='category[]' class='form-control category-dropdown' required>
                        <option value="">Select Category</option>
                        ${categoryOptions}
                    </select>
                </td>
                <td data-label="Uom">
                    <select name='uom[]' class='form-control uom-dropdown' required>
                        <option value="">Select UOM</option>
                        ${uomOptions}
                    </select>
                </td>
                <td data-label="Harga"><span type="text" name="unit_price[]">0</span></td>
                <td data-label="Total Harga"><span class="totalHarga">0</span></td>
                <td data-label="Urgency Status">
                <select name='urgency_status[]' class='form-control urgency-dropdown' required>
            <option value="">Select Status</option>
            <option value="normal">Normal</option>
            <option value="urgent">Urgent</option>
        </select>
    </td>
                <td data-label="Action">
                    <div class="action-buttons">
                        <button type="button" class="btn btn-success btn-sm saveNewRow">Save Now</button>
                        <button type="button" class="btn btn-success btn-sm saveRow" style="display: none;">Save</button>
                        <button type="button" class="btn btn-danger remove" style="display: none;" data-id="">Remove</button>
                    </div>
                </td>
            </tr>`;

                $('#detail-purchase-request tbody').append(newRow);
                applyDataLabels();

                $('.nama-barang:last').autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: 'function/get_suggestion.php',
                            data: {
                                query: request.term
                            },
                            dataType: 'json',
                            success: function(data) {
                                response(data);
                            }
                        });
                    }
                });

                allDetailRowsSubmitted = false;
                updateMainFormSubmitButton();
            }).catch(error => {
                console.error('Error loading dropdowns:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load form data. Please try again.'
                });
            });
        }

        // Initial load
        loadData(function() {
            applyDataLabels();
            updateMainFormSubmitButton();
        });

        // Event Handlers
        $('#addRow').click(addRow);

        $(document).on('click', '.saveNewRow', function() {
            submitDetailRow($(this).closest('tr'), 'function/insert_view_detail_purchase_request.php', 'insert');
        });

        $(document).on('click', '.edit', function() {
            // Ambil row yang sesuai
            const $row = $(this).closest('tr');

            // Simpan nilai original sebelum edit
            $row.find("input[name='nama_barang[]']").data('original-value', $row.find("input[name='nama_barang[]']").val());
            $row.find("input[name='qty[]']").data('original-value', $row.find("input[name='qty[]']").val());
            $row.find("select[name='category[]']").data('original-value', $row.find("select[name='category[]']").val());
            $row.find("select[name='uom[]']").data('original-value', $row.find("select[name='uom[]']").val());
            $row.find("textarea[name='detail_specification[]']").data('original-value',
                $row.find("textarea[name='detail_specification[]']").val());
            $row.find("select[name='urgency_status[]']").data('original-value',
                $row.find("select[name='urgency_status[]']").val());
            $row.find("[name='unit_price[]']").data('original-value',
                $row.find("[name='unit_price[]']").text().replace(/\./g, ''));

            // Enable editing
            $row.find('input, textarea, select').prop('readonly', false);
            $row.find('select[name="urgency_status[]"]').prop('readonly', false);
            $(this).hide();
            $row.find('.saveRow').show();
            allDetailRowsSubmitted = false;
            updateMainFormSubmitButton();
        });

        $(document).on('click', '.saveRow', function() {
            submitDetailRow($(this).closest('tr'), 'function/update_view_detail_purchase.php', 'update');
        });

        function autoResizeTextarea(element) {
            element.style.height = '60px';
            element.style.height = (element.scrollHeight) + 'px';
        }

        function initializeTextareaHandlers() {
            $('textarea.form-field-adjustable, textarea.desc-input').each(function() {
                autoResizeTextarea(this);
            }).on('input', function() {
                autoResizeTextarea(this);
            });
        }

        function validateRow($row) {
            const urgencyStatus = $row.find("select[name='urgency_status[]']").val();
            if (!urgencyStatus) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select urgency status'
                });
                return false;
            }
            return true;
        }

        // Comments handling
        function loadComments() {
            $.ajax({
                url: 'function/get_comments.php',
                type: 'GET',
                data: {
                    id_proc_ch: idProcCh
                },
                success: function(data) {
                    $('#commentSection').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error loading comments:", status, error);
                }
            });
        }

        loadComments();

        $('#addCommentForm').on('submit', function(e) {
            e.preventDefault();

            // Disable submit button to prevent double submission
            const $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: "function/add_comments.php",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Comment added successfully',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        loadComments();
                        $('#addCommentForm')[0].reset();
                        $('#filePreview').empty();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to add comment'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error submitting the comment: ' + error
                    });
                },
                complete: function() {
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        // Remove handler
        $(document).on('click', '.remove', function() {
            var id = $(this).data('id');
            if (!id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Cannot identify the item to delete'
                });
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "function/delete_view_detail_purchase.php",
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                loadData(function() {
                                    applyDataLabels();
                                    updateMainFormSubmitButton();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'There was an error deleting the row.'
                            });
                        }
                    });
                }
            });
        });

        // Main form submission
        $('#updatePurchaseRequestForm').on('submit', function(e) {
            e.preventDefault();

            console.log("Form submitted");

            if (!allDetailRowsSubmitted) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please submit all detail rows before submitting the main form.'
                });
                return;
            }

            var formData = new FormData(this);

            // Set id dari id_proc_ch untuk kebutuhan backend
            formData.append('id', formData.get('id_proc_ch'));

            // Debug: Log form data
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Validasi file
            var fileInput = $('input[name="lampiran"]')[0];
            var file = fileInput.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'File size must not exceed 2MB'
                    });
                    return;
                }
            }

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: "POST",
                url: "function/update_view_purchase.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log("Raw response:", response);
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        console.log("Parsed response:", result);

                        if (result.status === 'success') {
                            Swal.fire({
                                title: 'Sukses!',
                                text: result.message || 'Data berhasil di submit.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "index.php?page=PurchaseRequests";
                                }
                            });
                        } else {
                            throw new Error(result.message || 'Unknown error occurred');
                        }
                    } catch (error) {
                        console.error('Response Error:', error, response);
                        Swal.fire({
                            title: 'Error!',
                            text: error.message || 'Terjadi kesalahan saat memproses response.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        xhr,
                        status,
                        error
                    });
                    console.log("Response Text:", xhr.responseText);

                    let errorMessage = 'Terjadi kesalahan saat mengupdate data.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                        console.log("Debug logs:", response.debug_log);
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>
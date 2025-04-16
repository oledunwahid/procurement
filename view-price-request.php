<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Table Styles */
    .gdocs-style {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background-color: #ffffff;
    }

    .gdocs-style thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
        padding: 12px 8px;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .gdocs-style tbody td {
        padding: 8px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
        background-color: #fff;
        transition: all 0.2s;
    }

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

    .form-control {
        border-radius: 0.375rem;
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
<style>
    .form-field-adjustable {
        min-height: 60px;
        height: auto;
        resize: none;
        overflow: hidden;
        line-height: 1.5;
        padding: 8px;
        white-space: pre-wrap;
        word-wrap: break-word;
        transition: height 0.1s ease-in-out;
    }

    textarea.form-control {
        min-height: 60px;
        white-space: pre-wrap;
        word-wrap: break-word;
        resize: vertical;
    }

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
            text-align: right;
            padding-left: 50%;
            position: relative;
        }

        #detail-purchase-request td:before {
            content: attr(data-label);
            position: absolute;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
        }

        #detail-purchase-request td input,
        #detail-purchase-request td select,
        #detail-purchase-request td textarea {
            width: 100% !important;
        }

        .action-buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
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
<!-- desc input style -->
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
$sql = mysqli_query($koneksi, "SELECT * FROM proc_purchase_requests WHERE id_proc_ch ='$id_proc_ch'");
$row = mysqli_fetch_assoc($sql);
?>

<div class="row">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Price Request Details</h5>
                        <a href="index.php?page=PurchaseRequests" class="btn btn-close btn-lg"></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive mt-3">
                        <table class="table table-hover gdocs-style" id="detail-purchase-request">
                            <thead>
                                <tr>
                                    <th style="display:none;">ID Request</th>
                                    <th width="16%">Nama Barang</th>
                                    <th width="16%">Detail Spec</th>
                                    <th width="5%">Qty</th>
                                    <th width="16%">Category</th>
                                    <th width="9%">Uom</th>
                                    <th width="10%">Harga</th>
                                    <th width="5%">Total Harga</th>
                                    <th width="10%">Urgency Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via AJAX -->
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
                    <form id="updatePurchaseRequestForm" enctype="multipart/form-data">
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
                                $id_proc_ch = $_GET['id'];
                                $sql = mysqli_query($koneksi, "
                                    SELECT
                                        pp.*, user1.nama AS nama_request
                                    FROM
                                        proc_purchase_requests AS pp
                                        LEFT JOIN user AS user1 ON pp.nik_request = user1.idnik
                                    WHERE
                                        pp.id_proc_ch = '$id_proc_ch'
                                ");
                                $row = mysqli_fetch_assoc($sql);
                                ?>
                                <div class="col-lg-3 col-sm-6">
                                    <label for="choices-payment-status">Nama Requester</label>
                                    <div class="input-light">
                                        <input type="text" name="requester_name" class="form-control bg-light border-0" value="<?= htmlspecialchars($row['nama_request']) ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <label for="choices-payment-status">Title</label>
                                    <div class="input-light">
                                        <input type="text" name="title" class="form-control" readonly value="<?= $row['title'] ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div>
                                        <label for="totalamountInput">Total Amount</label>
                                        <input type="text" name="total_price" id="total_price" class="form-control bg-light border-0" value="<?php echo 'Rp ' . number_format($row['total_price'], 0, ',', '.'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <label for="totalamountInput">Attachment</label>
                                    <div class="d-flex align-items-center border border-dashed p-2 rounded">
                                        <?php
                                        if ($row['lampiran']) {
                                            $file = "file/procurement/" . $row['lampiran'];
                                            if (file_exists($file)) {
                                                $filesize = filesize($file);
                                                if ($filesize >= 1024 * 1024) {
                                                    $filesize = number_format($filesize / (1024 * 1024), 2) . ' MB';
                                                } elseif ($filesize >= 1024) {
                                                    $filesize = number_format($filesize / 1024, 2) . ' KB';
                                                } else {
                                                    $filesize = $filesize . ' bytes';
                                                }
                                        ?>
                                                <div class="flex-shrink-0 avatar-sm">
                                                    <div class="avatar-title bg-light rounded">
                                                        <?php
                                                        $file_extension = pathinfo($row['lampiran'], PATHINFO_EXTENSION);
                                                        if (in_array($file_extension, ['jpg', 'jpeg', 'png'])) {
                                                            echo '<i class="ri-image-line fs-20 text-primary"></i>';
                                                        } elseif ($file_extension === 'pdf') {
                                                            echo '<i class="ri-file-pdf-line fs-20 text-danger"></i>';
                                                        } else {
                                                            echo '<i class="ri-file-zip-line fs-20 text-primary"></i>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <a href="file/procurement/<?= $row['lampiran'] ?>" class="download-link" download>
                                                        <i class="mb-1 ri-download-2-line"></i> <?= $row['lampiran'] ?>
                                                        <small class="text-muted">(<?= $filesize ?>)</small>
                                                    </a>
                                                </div>
                                            <?php
                                            } else {
                                            ?>
                                                <div class="flex-grow-1 ms-3 mt-3 alert alert-warning" role="alert">
                                                    <i class="fa fa-exclamation-triangle me-2"></i> File not found.
                                                </div>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <div class="flex-grow-1 ms-3 mt-3 alert alert-info" role="alert">
                                                <i class="fa fa-info-circle me-2"></i> No files uploaded.
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
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
        var idProcCh = <?= json_encode($_GET['id']); ?>;
        var status = <?= json_encode($row['status']); ?>;
        console.log("Initial status:", status);

        function formatRibuan(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function loadData(callback) {
            const table = $('#detail-purchase-request');
            const tbody = table.find('tbody');

            $.ajax({
                url: 'function/fetch_view_price_request.php',
                type: 'GET',
                data: {
                    id_proc_ch: idProcCh
                },
                beforeSend: function() {
                    tbody.html('<tr><td colspan="11" class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                    table.closest('.table-responsive').addClass('loading-overlay');
                },
                success: function(data) {
                    tbody.html(data);
                    applyDataLabels();
                    initializeInputHandlers();
                    if (callback) callback();
                    checkStatusAndToggleButton();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    tbody.html('<tr><td colspan="11" class="text-center text-danger p-3"><i class="fas fa-exclamation-circle mr-2"></i>Error loading data</td></tr>');
                },
                complete: function() {
                    table.closest('.table-responsive').removeClass('loading-overlay');
                }
            });
        }

        function initializeInputHandlers() {
            function autoResizeTextarea(element) {
                element.style.height = '60px';
                element.style.height = (element.scrollHeight) + 'px';
            }

            // Handle textarea auto-resize
            $('textarea.form-field-adjustable, textarea.desc-input').each(function() {
                autoResizeTextarea(this);
            }).on('input', function() {
                autoResizeTextarea(this);
            });

            // Format price inputs
            $('input[name="unit_price[]"]').on('input', function() {
                let value = this.value.replace(/[^\d]/g, '');
                this.value = formatRibuan(value);
                calculateRowTotal($(this).closest('tr'));
            });

            // Handle quantity inputs
            $('input[name="qty[]"]').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value === '') this.value = 0;
                calculateRowTotal($(this).closest('tr'));
            });

            // Calculate row total
            function calculateRowTotal($row) {
                const qty = parseInt($row.find('input[name="qty[]"]').val()) || 0;
                const price = parseInt($row.find('input[name="unit_price[]"]').val().replace(/\./g, '')) || 0;
                const total = qty * price;
                $row.find('.total-price').text('Rp ' + formatRibuan(total.toString()));
                updateGrandTotal();
            }

            // Update grand total
            function updateGrandTotal() {
                let grandTotal = 0;
                $('.total-price').each(function() {
                    const value = $(this).text().replace('Rp ', '').replace(/\./g, '');
                    grandTotal += parseInt(value) || 0;
                });
                $('#total_price').val('Rp ' + formatRibuan(grandTotal.toString()));
            }

            // Initialize select2 if available
            if ($.fn.select2) {
                $('select[name="category[]"], select[name="uom[]"], select[name="urgency_status[]"]').select2({
                    width: '100%',
                    dropdownParent: $('.table-responsive')
                });
            }
        }

        function applyDataLabels() {
            $('#detail-purchase-request tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    var label = $('#detail-purchase-request thead th').eq(index).text();
                    $(this).attr('data-label', label + ':');
                });
            });
        }


        function autoResizeTextarea(element) {
            element.style.height = 'auto';
            element.style.height = (element.scrollHeight) + 'px';
        }

        // Initialize all textareas
        $('textarea.form-field-adjustable, textarea.desc-input').each(function() {
            autoResizeTextarea(this);
            $(this).on('input', function() {
                autoResizeTextarea(this);
            });
        });

        // Trigger resize on window resize for responsive behavior
        $(window).on('resize', function() {
            $('textarea.form-field-adjustable, textarea.desc-input').each(function() {
                autoResizeTextarea(this);
            });
        });

        function performUpdate(data) {
            $.ajax({
                type: "POST",
                url: "function/update_detail_purchase.php",
                data: data,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        loadData(function() {
                            applyDataLabels();
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
                        text: 'There was an error updating the row.'
                    });
                }
            });
        }

        $(document).on('click', '.edit', function() {
            var $row = $(this).closest('tr');
            $row.find('input, textarea, select').prop('readonly', false).prop('disabled', false);

            $row.find('input, textarea, select').each(function() {
                $(this).data('original-value', $(this).val());
            });

            $(this).hide();
            $row.find('.saveRow').show();
        });

        $(document).on('click', '.saveRow', function() {
            var $row = $(this).closest('tr');
            saveRowData($row);
        });

        function checkStatusAndToggleButton() {
            if (status && status.trim().toLowerCase() === 'closed') {
                $('#postCommentBtn').hide();
                $('#commentText').prop('disabled', true);
                $('#closedTicketInfo').show();
                console.log("Ticket is closed. Comment form disabled.");
            } else {
                $('#postCommentBtn').show();
                $('#commentText').prop('disabled', false);
                $('#closedTicketInfo').hide();
                console.log("Ticket is open. Comment form enabled.");
            }
        }

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

        loadData();
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

        $('#updateTicketBtn').on('click', function() {
            var formData = new FormData($('#updatePurchaseRequestForm')[0]);
            formData.append('status', 'Open');

            $.ajax({
                type: "POST",
                url: "function/update_purchase.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        response = typeof response === 'string' ? JSON.parse(response) : response;
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Sukses!',
                                text: 'Data berhasil diupdate.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "index.php?page=PurchaseRequests";
                                }
                            });
                        } else {
                            throw new Error(response.message || 'Unknown error occurred');
                        }
                    } catch (e) {
                        Swal.fire({
                            title: 'Error!',
                            text: e.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Update Error:", {
                        xhr,
                        status,
                        error
                    });
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengupdate data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        applyDataLabels();
        checkStatusAndToggleButton();

        $(document).on('change', '[name="status"]', function() {
            status = $(this).val();
            checkStatusAndToggleButton();
        });
        let resizeTimeout;
        $(window).on('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if ($.fn.select2) {
                    $('.custom-table select').select2('destroy').select2({
                        width: '100%',
                        dropdownParent: $('.table-responsive')
                    });
                }
            }, 250);
        });


    });
</script>
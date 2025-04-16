<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    textarea[name="detail_specification[]"] {
        min-height: 60px;
        overflow-y: hidden;
        resize: vertical;
        transition: height 0.1s ease-in-out;
        width: 100%;
    }

    #filePreview .alert {
        margin-bottom: 0.5rem;
        padding: 0.5rem 1rem;
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

<style>
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

    .gdocs-style tbody tr:hover td {
        background-color: #f8f9fa;
    }
</style>

<style>
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
// Di awal file detail-purchase-request.php
$id_proc_ch = $_GET['id'];

// Cek apakah user adalah admin (role 5) atau memiliki role 51 yang tidak dibatasi kategori
$isAdmin = in_array(5, $role);
$hasRole51 = in_array(51, $role);

if (!$isAdmin && !$hasRole51) {
    // Cek apakah PIC memiliki akses ke kategori dalam request ini
    $checkAccess = mysqli_query($koneksi, "
        SELECT COUNT(*) as count 
        FROM proc_request_details prd
        JOIN proc_admin_category pac ON prd.category = pac.id_category
        WHERE prd.id_proc_ch = '$id_proc_ch' 
        AND pac.idnik = '$niklogin'
    ");
    $hasAccess = mysqli_fetch_assoc($checkAccess)['count'] > 0;

    if (!$hasAccess) {
        // Jika PIC tidak memiliki akses, redirect ke halaman list dengan pesan error
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: 'You are not assigned to handle any items in this request.',
                confirmButtonText: 'OK'
            }).then((result) => {
                window.location.href = 'index.php?page=PurchaseRequests';
            });
        </script>";
        exit;
    }
}

// Jika memiliki akses, admin, atau memiliki role 51, lanjutkan dengan query normal
$sql = mysqli_query($koneksi, "SELECT * FROM proc_purchase_requests WHERE id_proc_ch ='$id_proc_ch'");
$row = mysqli_fetch_assoc($sql);

// Tambahkan handler jika data kosong setelah fetch
if (!$row) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Data Not Found',
            text: 'The requested data could not be found.',
            confirmButtonText: 'OK'
        }).then((result) => {
            window.location.href = 'index.php?page=PurchaseRequests';
        });
    </script>";
    exit;
}
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
                            <input type="hidden" name="niklogin" value="<?= $niklogin ?>">
                            <input type="hidden" name="isAdmin" value="<?= $isAdmin ? '1' : '0' ?>">
                            <input type="hidden" name="hasRole51" value="<?= $hasRole51 ? '1' : '0' ?>">
                            <thead>
                                <tr>
                                    <th style="display:none;">ID Request</th>
                                    <th width="16%">Nama Barang</th>
                                    <th width="16%">Detail Spec</th>
                                    <th width="6%">Qty</th>
                                    <th width="16%">Category</th>
                                    <th width="9%">Uom</th>
                                    <th class="price-column">Harga</th>
                                    <th class="total-price-column">Total Harga</th>
                                    <th width="5%">Urgency status</th>
                                    <th width="15%">Action</th>
                                    <th width="15%">Detail Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="no-data-row">
                                    <td colspan="10" class="text-center">Loading...</td>
                                </tr>
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
                                <input type="hidden" name="niklogin" value="<?= $niklogin ?>">
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
                        <div class="pt-3">
                            <button type="button" class="btn btn-primary" id="closedTicketBtn">Closed Ticket</button>
                            <button type="button" class="btn btn-secondary" id="updateTicketBtn">Update Ticket</button>
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
                    <div id="filePreview" class="mt-2"></div>
                    <small class="text-muted">
                        Max size: 5MB per file
                    </small>
                </div>
                <input type="hidden" name="id_proc_ch" value="<?= htmlspecialchars($row['id_proc_ch']); ?>">
                <button type="submit" id="postCommentBtn" class="btn btn-primary" <?php echo (strtolower(trim($row['status'])) === 'closed') ? 'style="display:none;"' : ''; ?>>Post Comment</button>
            </form>
            <div id="closedTicketInfo" class="alert alert-info mt-3" style="display: <?php echo (strtolower(trim($row['status'])) === 'closed') ? 'block' : 'none'; ?>;">
                <i class="fas fa-info-circle"></i> This ticket is closed. No new comments can be added.
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Define global variables
        var idProcCh = $('input[name="id_proc_ch"]').val();
        var status = $('input[name="status"]').val();
        var niklogin = $('input[name="niklogin"]').val();
        var idnik_pic = $('input[name="idnik_pic"]').val();
        var isAdmin = $('input[name="isAdmin"]').val() == '1';
        var hasRole51 = $('input[name="hasRole51"]').val() == '1';

        // Enhanced Utility Functions
        function formatRibuan(angka) {
            // Handle empty or non-numeric values
            if (!angka || isNaN(parseFloat(angka.toString().replace(/\./g, '')))) {
                return '0';
            }

            // Remove existing formatting first
            const cleanNumber = angka.toString().replace(/\./g, '');

            // Format with thousand separators
            return cleanNumber.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function showNoDataMessage() {
            var message = isAdmin || hasRole51 ?
                'No items found in this request.' :
                'No items assigned to you in this request.';
            return `<tr class="no-data-row"><td colspan="10" class="text-center">${message}</td></tr>`;
        }

        // Initialize price tooltips for better visibility of large values
        function initializePriceTooltips() {
            $('.price-tooltip').each(function() {
                var $this = $(this);
                var value = $this.find('.price-value').text() || $this.find('.price-value').val();

                // If tooltip doesn't exist, create it
                if ($this.find('.tooltip-text').length === 0) {
                    $this.append(`<span class="tooltip-text">Rp ${value}</span>`);
                }
            });
        }

        // Handle very large numbers with special styling
        function handleLargeNumbers() {
            $('.price-value').each(function() {
                const value = $(this).is('input') ? $(this).val() : $(this).text();
                const numericValue = parseInt(value.replace(/\./g, '')) || 0;

                // If value is very large (over 100 million), add special styling
                if (numericValue > 100000000) {
                    $(this).addClass('very-large-number');

                    // Update tooltip text to ensure it shows the full value
                    const tooltipElement = $(this).closest('.price-tooltip').find('.tooltip-text');
                    if (tooltipElement.length) {
                        tooltipElement.text('Rp ' + formatRibuan(numericValue));
                    }
                }
            });
        }

        // Data Loading & Table Management
        function loadData(callback) {
            console.log('Loading data...');
            $.ajax({
                url: 'function/fetch_detail_purchase_request.php',
                type: 'GET',
                data: {
                    id_proc_ch: idProcCh,
                    niklogin: niklogin,
                    hasRole51: hasRole51 ? '1' : '0'
                },
                beforeSend: function() {
                    $('#detail-purchase-request tbody').html('<tr><td colspan="10" class="text-center">Loading...</td></tr>');
                },
                success: function(data) {
                    console.log('Data received:', data);
                    if (data.trim()) {
                        $('#detail-purchase-request tbody').html(data);
                        applyDataLabels();
                        updateTotalPrice();
                        initializePriceTooltips();
                        handleLargeNumbers();
                    } else {
                        $('#detail-purchase-request tbody').html(showNoDataMessage());
                    }
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        xhr,
                        status,
                        error
                    });
                    $('#detail-purchase-request tbody').html('<tr><td colspan="10" class="text-center text-danger">Error loading data</td></tr>');
                }
            });
        }

        function applyDataLabels() {
            $('#detail-purchase-request tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    var label = $('#detail-purchase-request thead th').eq(index).text();
                    $(this).attr('data-label', label + ':');
                });
            });
        }

        function hasDetailRows() {
            var tableRows = $('#detail-purchase-request tbody tr').not('.no-data-row');
            return tableRows.length > 0;
        }

        // Enhanced Price Calculations & Updates
        function updateTotalPrice() {
            var total = 0;
            $("#detail-purchase-request tbody tr").each(function() {
                var qty = parseInt($(this).find("input[name='qty[]']").val()) || 0;
                var price = parseInt($(this).find("input[name='unit_price[]']").val().replace(/\./g, '')) || 0;
                var subtotal = qty * price;
                total += subtotal;
            });
            $("input[name='total_price']").val('Rp ' + formatRibuan(total));
            toggleClosedTicketButton();
        }

        // Enhanced row total calculation with tooltip update
        function updateRowTotal(row) {
            var qty = parseInt(row.find("input[name='qty[]']").val()) || 0;
            var price = parseInt(row.find("input[name='unit_price[]']").val().replace(/\./g, '')) || 0;
            var total = qty * price;

            // Update total display
            var totalElement = row.find('.totalHarga');
            totalElement.text(formatRibuan(total));

            // Update tooltip if it exists
            var tooltipElement = totalElement.closest('.price-tooltip').find('.tooltip-text');
            if (tooltipElement.length) {
                tooltipElement.text('Rp ' + formatRibuan(total));
            }

            updateTotalPrice();
        }

        $(document).on('input', "input[name='qty[]'], input[name='unit_price[]']", function() {
            var row = $(this).closest('tr');
            updateRowTotal(row);
        });

        // Enhanced price input handler with caret position preservation
        $(document).on('input', '.price-input', function() {
            // Store caret position
            const caret = this.selectionStart;
            const originalLength = this.value.length;

            // Remove non-numeric characters except dots
            var value = $(this).val().replace(/\./g, '');

            // Format with thousand separators
            $(this).val(formatRibuan(value));

            // Calculate new caret position
            const newPosition = caret + (this.value.length - originalLength);

            // Restore caret position
            this.setSelectionRange(Math.max(0, newPosition), Math.max(0, newPosition));

            // Update row total
            updateRowTotal($(this).closest('tr'));
        });

        // Category PIC Management
        function checkCategoryPIC(categoryId, callback) {
            console.log("Checking category:", categoryId);
            $.ajax({
                url: 'function/check_category_pic.php',
                type: 'GET',
                data: {
                    category_id: categoryId
                },
                success: function(response) {
                    console.log("Raw response:", response);
                    try {
                        let parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                        console.log("Parsed response:", parsedResponse);
                        callback(parsedResponse);
                    } catch (e) {
                        console.error("Error parsing response:", e);
                        Swal.fire({
                            title: 'Error',
                            text: 'Invalid response from server',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error checking category PIC:", {
                        xhr,
                        status,
                        error
                    });
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to check category PIC',
                        icon: 'error'
                    });
                }
            });
        }

        // Button State Management
        function toggleClosedTicketButton() {
            var hasRows = hasDetailRows();
            var totalAmount = parseFloat($("input[name='total_price']").val().replace(/[^\d]/g, '')) || 0;

            if (!hasRows || totalAmount === 0) {
                $('#closedTicketBtn').prop('disabled', true);
                $('#closedTicketBtn').html('<i class="ri-lock-line me-1"></i> Closed Ticket');
            } else {
                $('#closedTicketBtn').prop('disabled', false);
                $('#closedTicketBtn').html('Closed Ticket');
            }
        }

        function checkStatusAndToggleButton() {
            if (status && status.trim().toLowerCase() === 'closed') {
                $('#postCommentBtn').hide();
                $('#commentText').prop('disabled', true);
                $('#closedTicketInfo').show();
            } else {
                $('#postCommentBtn').show();
                $('#commentText').prop('disabled', false);
                $('#closedTicketInfo').hide();
            }
        }

        // Comment System
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

        // IMPORTANT - This is the only event handler for the comment form
        // All other handlers should be removed
        $('#addCommentForm').off('submit').on('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const $submitBtn = $(this).find('button[type="submit"]');
            const originalBtnText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Posting...');

            // Use FormData for file uploads
            var formData = new FormData(this);
            formData.append('niklogin', niklogin);
            formData.append('idnik_pic', idnik_pic);

            // Debug log
            console.log('Submitting comment, form data keys:', Array.from(formData.keys()));

            $.ajax({
                type: "POST",
                url: "function/add_comments.php",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Comment submission response:', response);

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
                    console.error("Response Text:", xhr.responseText);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error submitting the comment: ' + error
                    });
                },
                complete: function() {
                    // Reset button state
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        // Event handler for file uploads
        $('#attachments').on('change', function() {
            const files = Array.from(this.files);
            const filePreview = $('#filePreview');
            filePreview.empty();

            files.forEach(file => {
                const size = (file.size / 1024 / 1024).toFixed(2);
                if (size > 5) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File too large',
                        text: `${file.name} is larger than 5MB`
                    });
                    this.value = '';
                    return;
                }

                filePreview.append(`
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong>${file.name}</strong> (${size} MB)
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
            });
        });

        // CRUD Operations
        $(document).on('click', '.saveNewRow', function() {
            var row = $(this).closest('tr');
            var data = {
                id_proc_ch: row.find("input[name='id_proc_ch[]']").val(),
                nama_barang: row.find("input[name='nama_barang[]']").val(),
                detail_specification: row.find("textarea[name='detail_specification[]']").val(),
                qty: row.find("input[name='qty[]']").val(),
                category: row.find("select[name='category[]']").val(),
                uom: row.find("select[name='uom[]']").val(),
                unit_price: row.find("input[name='unit_price[]']").val().replace(/\./g, ''),
                detail_notes: row.find("textarea[name='detail_notes[]']").val(),
                niklogin: niklogin,
                idnik_pic: idnik_pic,
                hasRole51: hasRole51 ? '1' : '0'
            };

            $.ajax({
                type: "POST",
                url: "function/insert_detail_purchase_request.php",
                data: data,
                dataType: 'json',
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
                            text: response.message || 'Failed to save data'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error saving the row.'
                    });
                }
            });
        });

        $(document).on('click', '.edit', function() {
            var $row = $(this).closest('tr');
            $row.find('input, textarea, select').prop('readonly', false).prop('disabled', false);
            $row.find('select[name="category[]"]').data('original-value', $row.find('select[name="category[]"]').val());
            $(this).hide();
            $row.find('.saveRow').show();
        });

        $(document).on('click', '.saveRow', function() {
            var $row = $(this).closest('tr');
            saveRowData($row);
        });

        function saveRowData($row) {
            const originalValues = {
                nama_barang: $row.find("input[name='nama_barang[]']").val(),
                detail_specification: $row.find("textarea[name='detail_specification[]']").val(),
                qty: $row.find("input[name='qty[]']").val(),
                category: $row.find("select[name='category[]']").val(),
                uom: $row.find("select[name='uom[]']").val(),
                unit_price: $row.find("input[name='unit_price[]']").val(),
                urgency_status: $row.find("select[name='urgency_status[]']").val(),
                detail_notes: $row.find("textarea[name='detail_notes[]']").val()
            };

            var data = {
                id: $row.find('.saveRow').data('id'),
                id_proc_ch: $row.find("input[name='id_proc_ch[]']").val(),
                nama_barang: $row.find("input[name='nama_barang[]']").val(),
                detail_specification: $row.find("textarea[name='detail_specification[]']").val(),
                qty: $row.find("input[name='qty[]']").val(),
                category: $row.find("select[name='category[]']").val(),
                uom: $row.find("select[name='uom[]']").val(),
                unit_price: $row.find("input[name='unit_price[]']").val().replace(/\./g, ''),
                urgency_status: $row.find("select[name='urgency_status[]']").val(),
                detail_notes: $row.find("textarea[name='detail_notes[]']").val(),
                niklogin: niklogin,
                idnik_pic: idnik_pic,
                hasRole51: hasRole51 ? '1' : '0'
            };

            if (originalValues.urgency_status !== 'urgent' && data.urgency_status === 'urgent') {
                Swal.fire({
                    title: 'Confirm Urgent Status',
                    text: 'Are you sure you want to mark this item as urgent?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performUpdate(data);
                    } else {
                        $row.find("select[name='urgency_status[]']").val(originalValues.urgency_status);
                    }
                });
            } else {
                performUpdate(data);
            }
        }

        function performUpdate(data) {
            $.ajax({
                type: "POST",
                url: "function/update_detail_purchase.php",
                data: data,
                dataType: 'json',
                success: function(response) {
                    console.log("Server response:", response);
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
                            initializePriceTooltips();
                            handleLargeNumbers();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'An error occurred while updating the data'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", {
                        xhr: xhr,
                        status: status,
                        error: error
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error updating the row.'
                    });
                }
            });
        }

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
                        url: "function/delete_detail_purchase.php",
                        data: {
                            id: id,
                            niklogin: niklogin,
                            idnik_pic: idnik_pic,
                            hasRole51: hasRole51 ? '1' : '0'
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
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete item'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            console.log("Response:", xhr.responseText);
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

        // Ticket Management
        $('#closedTicketBtn').on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will close the ticket. Proceed?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, close it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    closeTicket();
                }
            });
        });

        function closeTicket() {
            var formData = new FormData($('#updatePurchaseRequestForm')[0]);
            formData.append('status', 'closed');
            formData.append('niklogin', niklogin);
            formData.append('idnik_pic', idnik_pic);
            formData.append('hasRole51', hasRole51 ? '1' : '0');

            $.ajax({
                type: "POST",
                url: "function/update_purchase.php",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Ticket has been closed successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "index.php?page=PurchaseRequests";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to close ticket',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error closing the ticket.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        $('#updateTicketBtn').on('click', function() {
            var formData = new FormData($('#updatePurchaseRequestForm')[0]);
            formData.append('status', 'Open');
            formData.append('niklogin', niklogin);
            formData.append('idnik_pic', idnik_pic);
            formData.append('hasRole51', hasRole51 ? '1' : '0');

            $.ajax({
                type: "POST",
                url: "function/update_purchase.php",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Ticket has been updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "index.php?page=PurchaseRequests";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to update ticket',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error updating the ticket.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Apply responsive styling based on screen size
        function applyResponsivePrice() {
            if (window.innerWidth <= 767) {
                $('.price-value').addClass('price-value-mobile');
            } else {
                $('.price-value').removeClass('price-value-mobile');
            }
        }

        // Initialization
        loadData();
        loadComments();
        toggleClosedTicketButton();
        applyDataLabels();
        checkStatusAndToggleButton();
        applyResponsivePrice();

        // Event listener for status changes
        $(document).on('change', '[name="status"]', function() {
            status = $(this).val();
            checkStatusAndToggleButton();
        });

        // Event listener for window resize
        $(window).on('resize', function() {
            applyResponsivePrice();
        });
    });
</script>
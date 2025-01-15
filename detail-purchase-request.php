<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

<?php
// Di awal file detail-purchase-request.php
$id_proc_ch = $_GET['id'];

// Cek apakah user adalah admin
$isAdmin = in_array(5, $role);

if (!$isAdmin) {
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

// Jika memiliki akses atau admin, lanjutkan dengan query normal
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
                            <thead>
                                <tr>
                                    <th style="display:none;">ID Request</th>
                                    <th width="16%">Nama Barang</th>
                                    <th width="16%">Detail Spec</th>
                                    <th width="6%">Qty</th>
                                    <th width="16%">Category</th>
                                    <th width="9%">Uom</th>
                                    <th width="6%">Harga</th>
                                    <th width="5%">Total Harga</th>
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
        // Constants and Initial Variables
        const idProcCh = <?= json_encode($_GET['id']); ?>;
        let status = <?= json_encode($row['status']); ?>;
        const niklogin = $('input[name="niklogin"]').val();
        const idnik_pic = $('input[name="idnik_pic"]').val();
        const isAdmin = $('input[name="isAdmin"]').val() === '1';

        // Utility Functions
        function formatRibuan(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function showNoDataMessage() {
            const message = isAdmin ? 'No items found in this request.' : 'No items assigned to you in this request.';
            return `<tr class="no-data-row"><td colspan="10" class="text-center">${message}</td></tr>`;
        }

        function showLoadingMessage() {
            return '<tr><td colspan="10" class="text-center">Loading...</td></tr>';
        }

        function showErrorMessage(message = 'Error loading data') {
            return `<tr><td colspan="10" class="text-center text-danger">${message}</td></tr>`;
        }

        // Auto-resize Functions
        function autoResizeTextarea(element) {
            element.style.height = '60px';
            element.style.height = (element.scrollHeight) + 'px';
        }

        function initializeInputHandlers() {
            const textareaSelectors = 'textarea.form-field-adjustable, textarea.desc-input';

            // Initial setup for all textareas
            $(textareaSelectors).each(function() {
                autoResizeTextarea(this);
            }).on('input', function() {
                autoResizeTextarea(this);
            });

            // Apply consistent styling
            $(textareaSelectors).css({
                'min-height': '60px',
                'height': 'auto',
                'white-space': 'pre-wrap',
                'word-wrap': 'break-word',
                'resize': 'none',
                'overflow': 'hidden'
            });

            // Handle window resize events
            $(window).off('resize.textarea').on('resize.textarea', function() {
                $(textareaSelectors).each(function() {
                    autoResizeTextarea(this);
                });
            });
        }

        // Data Loading and Handling Functions
        function loadData(callback) {
            console.log('Loading data...');
            $.ajax({
                url: 'function/fetch_detail_purchase_request.php',
                type: 'GET',
                data: {
                    id_proc_ch: idProcCh,
                    niklogin: niklogin
                },
                beforeSend: function() {
                    $('#detail-purchase-request tbody').html(showLoadingMessage());
                },
                success: function(data) {
                    console.log('Data received:', data);
                    const tbody = $('#detail-purchase-request tbody');

                    if (!data.trim()) {
                        tbody.html('<tr><td colspan="10" class="text-center">This Request Belongs to Other PIC</td></tr>');
                    } else {
                        tbody.html(data);
                        applyDataLabels();
                        initializeInputHandlers();
                        updateTotalPrice();
                    }

                    if (callback && typeof callback === 'function') {
                        callback();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        xhr,
                        status,
                        error
                    });
                    $('#detail-purchase-request tbody').html(showErrorMessage());
                }
            });
        }

        function applyDataLabels() {
            $('#detail-purchase-request tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    const label = $('#detail-purchase-request thead th').eq(index).text();
                    $(this).attr('data-label', label + ':');
                });
            });
        }

        function hasDetailRows() {
            return $('#detail-purchase-request tbody tr').not('.no-data-row').length > 0;
        }

        function toggleClosedTicketButton() {
            const hasRows = hasDetailRows();
            const totalAmount = parseFloat($("input[name='total_price']").val().replace(/\./g, '')) || 0;
            const $button = $('#closedTicketBtn');

            if (!hasRows || totalAmount === 0) {
                $button.prop('disabled', true)
                    .html('<i class="ri-lock-line me-1"></i> Closed Ticket');
            } else {
                $button.prop('disabled', false)
                    .html('Closed Ticket');
            }
        }

        function checkStatusAndToggleButton() {
            const isTicketClosed = status && status.trim().toLowerCase() === 'closed';
            $('#postCommentBtn').toggle(!isTicketClosed);
            $('#commentText').prop('disabled', isTicketClosed);
            $('#closedTicketInfo').toggle(isTicketClosed);
            console.log(`Ticket is ${isTicketClosed ? 'closed' : 'open'}. Comment form ${isTicketClosed ? 'disabled' : 'enabled'}.`);
        }

        function updateTotalPrice() {
            let total = 0;
            $("#detail-purchase-request tbody tr").each(function() {
                const qty = parseInt($(this).find("input[name='qty[]']").val()) || 0;
                const price = parseInt($(this).find("input[name='unit_price[]']").val().replace(/\./g, '')) || 0;
                total += (qty * price);
            });
            $("input[name='total_price']").val('Rp ' + formatRibuan(total));
            toggleClosedTicketButton();
        }

        // Category PIC Check Function
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
                        const parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
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

        // Save Row Function
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

            const data = {
                id: $row.find('.saveRow').data('id'),
                id_proc_ch: $row.find("input[name='id_proc_ch[]']").val(),
                ...originalValues,
                unit_price: originalValues.unit_price.replace(/\./g, ''),
                niklogin: niklogin,
                idnik_pic: idnik_pic
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
                        loadData(() => applyDataLabels());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'An error occurred while updating the data'
                        });
                        console.error("Error details:", response.debug_log);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", {
                        xhr,
                        status,
                        error
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error updating the row.'
                    });
                }
            });
        }

        // Comment Functions
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

        // Event Handlers
        $(document).on('input', "input[name='qty[]'], input[name='unit_price[]']", function() {
            const $row = $(this).closest('tr');
            const qty = parseInt($row.find("input[name='qty[]']").val()) || 0;
            const price = parseInt($row.find("input[name='unit_price[]']").val().replace(/\./g, '')) || 0;
            const total = qty * price;
            $row.find('.totalHarga').text(formatRibuan(total));
            updateTotalPrice();
        });

        $(document).on('input', '.price-input', function() {
            const value = $(this).val().replace(/\./g, '');
            $(this).val(formatRibuan(value));
        });

        $(document).on('change', 'select[name="category[]"]', function() {
            const $row = $(this).closest('tr');
            const selectedCategory = $(this).val();
            const currentPIC = niklogin;

            checkCategoryPIC(selectedCategory, function(response) {
                if (response.success) {
                    if (response.pic_list && !response.pic_list.includes(currentPIC)) {
                        const picNames = response.pic_names.join(', ');
                        Swal.fire({
                            title: 'Warning!',
                            html: `This category is assigned to: <br><b>${picNames}</b><br><br>After saving, this item will be handled by another PIC. Do you want to continue?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, change category',
                            cancelButtonText: 'No, keep current category'
                        }).then((result) => {
                            if (!result.isConfirmed) {
                                $row.find('select[name="category[]"]').val($row.find('select[name="category[]"]').data('original-value'));
                            } else {
                                $row.find('select[name="category[]"]').data('original-value', selectedCategory);
                            }
                        });
                    } else {
                        $row.find('select[name="category[]"]').data('original-value', selectedCategory);
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.error || 'Failed to check category PIC',
                        icon: 'error'
                    });
                }
            });
        });

        $(document).on('click', '.edit', function() {
            const $row = $(this).closest('tr');
            $row.find('input, textarea, select').prop('readonly', false);
            $row.find('select[name="category[]"]').data('original-value',
                $row.find('select[name="category[]"]').val());
            $(this).hide();
            $row.find('.saveRow').show();
        });

        $(document).on('click', '.saveRow', function() {
            const $row = $(this).closest('tr');
            const newCategory = $row.find('select[name="category[]"]').val();
            const originalCategory = $row.find('select[name="category[]"]').data('original-value');

            if (newCategory !== originalCategory) {
                checkCategoryPIC(newCategory, function(response) {
                    if (response.pic_list && !response.pic_list.includes(niklogin)) {
                        Swal.fire({
                            title: 'Confirm Category Change',
                            text: 'After saving, this item will be handled by another PIC. Are you sure you want to proceed?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, save changes',
                            cancelButtonText: 'No, cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                saveRowData($row);
                            }
                        });
                    } else {
                        saveRowData($row);
                    }
                });
            } else {
                saveRowData($row);
            }
        });

        $(document).on('click', '.remove', function() {
            const id = $(this).data('id');
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
                            idnik_pic: idnik_pic
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                loadData(() => applyDataLabels());
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

        // Handle form submissions
        $('#addCommentForm').on('submit', function(e) {
            e.preventDefault();
            const $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true);

            const formData = new FormData(this);

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
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        // Save New Row Handler
        $(document).on('click', '.saveNewRow', function() {
            const $row = $(this).closest('tr');
            const data = {
                id_proc_ch: $row.find("input[name='id_proc_ch[]']").val(),
                nama_barang: $row.find("input[name='nama_barang[]']").val(),
                detail_specification: $row.find("textarea[name='detail_specification[]']").val(),
                qty: $row.find("input[name='qty[]']").val(),
                category: $row.find("select[name='category[]']").val(),
                uom: $row.find("select[name='uom[]']").val(),
                unit_price: $row.find("input[name='unit_price[]']").val().replace(/\./g, ''),
                detail_notes: $row.find("textarea[name='detail_notes[]']").val(),
                niklogin: $(this).data('niklogin'),
                idnik_pic: $(this).data('idnik-pic')
            };

            $.ajax({
                type: "POST",
                url: "function/insert_detail_purchase_request.php",
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
                        loadData(() => applyDataLabels());
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
        });

        // Ticket Status Update Handlers
        $('#closedTicketBtn').on('click', function() {
            const formData = new FormData($('#updatePurchaseRequestForm')[0]);
            formData.append('status', 'closed');
            formData.append('niklogin', niklogin);
            formData.append('idnik_pic', idnik_pic);

            submitTicketUpdate(formData, 'Closed ticket successfully');
        });

        $('#updateTicketBtn').on('click', function() {
            const formData = new FormData($('#updatePurchaseRequestForm')[0]);
            formData.append('status', 'Open');
            formData.append('niklogin', niklogin);
            formData.append('idnik_pic', idnik_pic);

            submitTicketUpdate(formData, 'Data updated successfully');
        });

        function submitTicketUpdate(formData, successMessage) {
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
                            text: response.message || successMessage,
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
                            text: response.message || 'An error occurred while updating the ticket.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while updating the data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Status change handler
        $(document).on('change', '[name="status"]', function() {
            status = $(this).val();
            checkStatusAndToggleButton();
        });

        // Initialize everything
        loadData();
        toggleClosedTicketButton();
        applyDataLabels();
        checkStatusAndToggleButton();
        loadComments();
    });
</script>
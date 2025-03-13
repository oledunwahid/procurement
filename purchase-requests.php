<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">


<?php
$isAdmin = array_intersect([5], $role);
$isSuperadmin = array_intersect([51], $role);
// Query untuk mendapatkan data purchase requests

$sql = "SELECT DISTINCT
    pp.id_proc_ch,
    pp.title,
    pp.created_request,
    pp.closed_date,
    pp.status,
    pp.nik_request,
    pp.proc_pic,
    user1.nama AS nama_request,
    user1.divisi AS divisi_request,
    GROUP_CONCAT(DISTINCT prd.category SEPARATOR ', ') AS categories,
    GROUP_CONCAT(DISTINCT pac.idnik) as pic_list,
    (SELECT nama FROM user WHERE idnik = pp.proc_pic) AS pic_name
FROM proc_purchase_requests AS pp
LEFT JOIN user AS user1 ON pp.nik_request = user1.idnik
LEFT JOIN proc_request_details AS prd ON pp.id_proc_ch = prd.id_proc_ch
LEFT JOIN proc_admin_category pac ON prd.category = pac.id_category
WHERE (
    '$niklogin' IN (SELECT idnik FROM user_roles WHERE id_role = 51)
    OR pac.idnik = '$niklogin'
    OR pp.nik_request = '$niklogin'
)
GROUP BY pp.id_proc_ch
ORDER BY pp.created_request DESC";

function getTotal($koneksi, $condition, $niklogin)
{
    $sql = "SELECT DISTINCT pp.id_proc_ch 
            FROM proc_purchase_requests pp
            LEFT JOIN proc_request_details prd ON pp.id_proc_ch = prd.id_proc_ch
            LEFT JOIN proc_admin_category pac ON prd.category = pac.id_category
            WHERE ($condition)
            AND (
                '$niklogin' IN (SELECT idnik FROM user_roles WHERE id_role = 51)  -- Superadmin
                OR '$niklogin' IN (SELECT idnik FROM user_roles WHERE id_role = 5)  -- Admin
                OR pp.nik_request = '$niklogin'  -- User biasa
                OR pac.idnik = '$niklogin'  -- Admin kategori
            )";

    $result = mysqli_query($koneksi, $sql);
    return mysqli_num_rows($result);
}

// Penggunaan function
$total = ($isSuperadmin) ?
    getTotal($koneksi, "1=1", $niklogin) : ($isAdmin ?
        getTotal($koneksi, "1=1", $niklogin) :
        getTotal($koneksi, "nik_request='$niklogin'", $niklogin));

// Created Requests
$Created = getTotal($koneksi, "status = 'Created'", $niklogin);

// Open Requests
$Open = getTotal($koneksi, "status = 'Open'", $niklogin);

// Closed Requests
$Closed = getTotal($koneksi, "status = 'Closed'", $niklogin);
// Eksekusi query dan cek apakah berhasil
$result = mysqli_query($koneksi, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($koneksi);
} else {
?>

    <div class="container-fluid">
        <div class="row">
            <!-- Total Requests Card -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">Total Requests</p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="<?= htmlspecialchars($total) ?>"><?= htmlspecialchars($total) ?></span>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-info text-info rounded-circle fs-4">
                                        <i class="ri-shopping-cart-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Created Requests Card -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">Created Requests</p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="<?= htmlspecialchars($Created) ?>"><?= htmlspecialchars($Created) ?></span>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-warning text-warning rounded-circle fs-4">
                                        <i class="ri-time-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Open Requests Card -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">Open Requests</p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="<?= htmlspecialchars($Open) ?>"><?= htmlspecialchars($Open) ?></span>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success text-success rounded-circle fs-4">
                                        <i class="ri-check-double-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Closed Requests Card -->
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">Closed Requests</p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="<?= htmlspecialchars($Closed) ?>"><?= htmlspecialchars($Closed) ?></span>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-danger text-danger rounded-circle fs-4">
                                        <i class="ri-close-circle-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Issue List Table -->
    <div class="col-lg-12">
        <div class="card" id="List">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <div class="card-title mb-0 flex-grow-1">
                        <h5>Purchase Requests</h5>
                        <h6>List of all purchase requests.</h6>
                    </div>
                    <?php if (!$isAdmin) { ?>
                        <form action="function/insert_view_purchase_request.php" method="POST">
                            <input type="text" value="<?= $niklogin ?>" name="nik_request" hidden />
                            <button class="btn btn-danger add-btn" name="add-purchase-request" type="submit"><i class="ri-add-line align-bottom me-1"></i> Create Price Request</button>
                        </form>
                    <?php } ?>
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
                            <th>Closed Date</th>
                            <th>Requestor</th>
                            <th>Status</th>
                            <th>Division</th>
                            <?php if ($isSuperadmin) { ?>
                                <th>PIC</th>
                            <?php } ?>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $nomor = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $pic_list = explode(',', $row['pic_list']);

                            // Get responsible PIC name
                            $pic_name = null;
                            if ($row['proc_pic']) {
                                $pic_query = "SELECT nama FROM user WHERE idnik = '" . $row['proc_pic'] . "'";
                                $pic_result = mysqli_query($koneksi, $pic_query);
                                $pic = mysqli_fetch_assoc($pic_result);
                                $pic_name = $pic['nama'];
                            }
                        ?>
                            <tr>
                                <td><?= $nomor++ ?></td>
                                <td>
                                    <?php if ($isSuperadmin || in_array($niklogin, $pic_list)) { ?>
                                        <a href="index.php?page=DetailPurchase&id=<?= $row['id_proc_ch']; ?>"><?= $row['id_proc_ch'] ?></a>
                                    <?php } else { ?>
                                        <a href="index.php?page=ViewPriceReq&id=<?= $row['id_proc_ch']; ?>"><?= $row['id_proc_ch'] ?></a>
                                    <?php } ?>
                                </td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['created_request']) ?></td>
                                <td><?= $row['closed_date'] ? htmlspecialchars($row['closed_date']) : '-' ?></td>
                                <td><?= htmlspecialchars($row['nama_request']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($row['status']) {
                                        case 'Created':
                                            $statusClass = 'badge bg-warning';
                                            break;
                                        case 'Open':
                                            $statusClass = 'badge bg-success';
                                            break;
                                        case 'Closed':
                                            $statusClass = 'badge bg-danger';
                                            break;
                                        default:
                                            $statusClass = 'badge bg-secondary';
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= htmlspecialchars($row['status']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['divisi_request']) ?></td>
                                <?php if ($isSuperadmin) { ?>
                                    <td>
                                        <?php if ($pic_name) { ?>
                                            <span class="badge bg-info"><?= htmlspecialchars($pic_name) ?></span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">No PIC Assigned</span>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="index.php?page=PrintPriceReq&id=<?= $row['id_proc_ch']; ?>" class="dropdown-item">
                                                    <i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print
                                                </a>
                                            </li>
                                            <?php if ($isSuperadmin || in_array($niklogin, $pic_list)) { ?>
                                                <li>
                                                    <a class="dropdown-item" href="index.php?page=DetailPurchase&id=<?= $row['id_proc_ch']; ?>">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item remove" href="javascript:void(0);" data-id="<?= $row['id_proc_ch']; ?>">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
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


<script>
    $(document).ready(function() {
        // Initialize DataTable with custom empty message
        var isAdmin = <?php echo json_encode($isAdmin ? true : false); ?>;
        const currentUser = {
            id: <?= json_encode($_SESSION['idnik'] ?? ''); ?>,
            name: <?= json_encode($current_user['nama'] ?? ''); ?>
        };
        var table = $('#buttons-datatables').DataTable({
            // Basic DataTable configurations
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "pageLength": 10,
            "searching": true,
            "ordering": true,

            // Custom language for empty table message
            "language": {
                "emptyTable": isAdmin ?
                    `<div class="text-center p-3">
                        <i class="ri-information-line text-warning fs-4 mb-3 d-block"></i>
                        <p class="mb-1">No purchase requests found in your assigned categories.</p>
                        <small class="text-muted">You will see requests here once you are assigned to handle specific categories.</small>
                    </div>` : `<div class="text-center p-3">
                        <i class="ri-inbox-line text-muted fs-4 mb-3 d-block"></i>
                        <p class="mb-1">No purchase requests created yet.</p>
                        <small class="text-muted">Click "Create Price Request" button above to create your first request.</small>
                    </div>`,
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "lengthMenu": "Show _MENU_ entries",
                "loadingRecords": "Loading...",
                "processing": "Processing...",
                "search": "Search:",
                "zeroRecords": "No matching records found"
            }
        });

        // Function to initialize tooltips
        function initializeTooltips() {
            // Dispose existing tooltips first
            $('[data-bs-toggle="tooltip"]').each(function() {
                let tooltip = bootstrap.Tooltip.getInstance(this);
                if (tooltip) {
                    tooltip.dispose();
                }
            });

            // Initialize new tooltips
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(function(element) {
                new bootstrap.Tooltip(element, {
                    placement: 'right',
                    trigger: 'hover'
                });
            });
        }

        // Initialize tooltips on page load
        initializeTooltips();

        // Reinitialize tooltips after DataTable updates
        table.on('draw.dt', function() {
            setTimeout(initializeTooltips, 0);
        });

        // Handle delete functionality
        $(document).on('click', '.remove', function(e) {
            e.preventDefault();
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
                        url: "function/delete_purchase.php",
                        data: {
                            id: id,
                            user_id: '<?= $_SESSION['idnik'] ?? "0" ?>',
                            user_name: '<?= $_SESSION['nama'] ?? "System" ?>'
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
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete data'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'There was an error deleting the item.'
                            });
                        }
                    });
                }
            });
        });
        // Add responsive handling for table
        $(window).on('resize', function() {
            table.columns.adjust().responsive.recalc();
        });
    });
</script>
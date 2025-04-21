<!-- Enhanced CSS for dashboard -->
<link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.3/dist/apexcharts.min.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.3/dist/apexcharts.min.js"></script>

<style>
    .card-animate {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-animate:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-card {
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 600;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #af2a25 0%, #c73e39 100%);
        border-radius: 0.75rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 100%;
        background-image: url('assets/images/pattern.png');
        background-repeat: no-repeat;
        background-position: right;
        opacity: 0.1;
    }

    .chart-container {
        height: 300px;
    }

    .section-title {
        position: relative;
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background: #af2a25;
    }

    .activity-item {
        padding: 0.75rem;
        border-left: 3px solid #af2a25;
        margin-bottom: 0.75rem;
        background-color: rgba(175, 42, 37, 0.03);
        border-radius: 0 0.5rem 0.5rem 0;
    }

    .quick-tip {
        transition: all 0.3s ease;
    }

    .quick-tip:hover {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }

    .category-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        background-color: rgba(175, 42, 37, 0.1);
        color: #af2a25;
    }

    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .status-pending {
        background-color: #f7b84b;
    }

    .status-completed {
        background-color: #2ab57d;
    }

    .status-urgent {
        background-color: #f46a6a;
    }
</style>

<?php
// Safely get count from query result
function safeGetCount($query, $koneksi)
{
    $result = mysqli_query($koneksi, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return (int)$row['count'];
    }
    return 0;
}

// Error logging function
function logDashboardError($message)
{
    error_log("Dashboard Error [" . date('Y-m-d H:i:s') . "]: $message");
}

try {
    // User role checks
    $isAdmin = in_array(5, $role);
    $hasRole51 = in_array(51, $role);
    $userRoleText = $isAdmin ? "Administrator" : ($hasRole51 ? "Super PIC" : "Category PIC");

    // Fetch dashboard statistics with error handling
    $pendingRequests = safeGetCount("SELECT COUNT(*) as count FROM proc_purchase_requests WHERE status = 'Open'", $koneksi);
    $approvedRequests = safeGetCount("SELECT COUNT(*) as count FROM proc_purchase_requests WHERE status = 'Approved'", $koneksi);
    $completedRequests = safeGetCount("SELECT COUNT(*) as count FROM proc_purchase_requests WHERE status = 'Closed'", $koneksi);

    // For urgent requests, add proper error handling
    $urgentRequests = 0;
    $sqlUrgent = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM proc_request_details WHERE urgency_status = 'urgent'");
    if ($sqlUrgent) {
        $rowUrgent = mysqli_fetch_assoc($sqlUrgent);
        $urgentRequests = (int)$rowUrgent['count'];
    } else {
        logDashboardError("Failed to fetch urgent requests: " . mysqli_error($koneksi));
    }

    // Get recent activities with proper error handling
    $recentActivities = [];
    $sqlActivities = mysqli_query($koneksi, "SELECT al.*, u.nama, pr.title 
                                             FROM proc_admin_log al
                                             LEFT JOIN user u ON al.idnik = u.idnik
                                             LEFT JOIN proc_purchase_requests pr ON al.record_id = pr.id_proc_ch
                                             WHERE al.table_name IN ('proc_purchase_requests', 'proc_request_details')
                                             ORDER BY al.timestamp DESC LIMIT 5");
    if ($sqlActivities) {
        while ($activity = mysqli_fetch_assoc($sqlActivities)) {
            $recentActivities[] = $activity;
        }
    } else {
        logDashboardError("Failed to fetch recent activities: " . mysqli_error($koneksi));
    }

    // Get my assigned requests - MODIFIED for role 51 users
    $myAssignments = [];

    if ($hasRole51) {
        // For role 51 users - show all open requests without category filtering
        $sqlAssignments = mysqli_query($koneksi, "SELECT pr.id_proc_ch, pr.title, pr.created_request, pr.status, 
                                                 COUNT(prd.id) as item_count, 
                                                 SUM(CASE WHEN prd.urgency_status = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
                                                 pr.created_request as timestamp
                                                 FROM proc_purchase_requests pr
                                                 JOIN proc_request_details prd ON pr.id_proc_ch = prd.id_proc_ch
                                                 WHERE pr.status = 'Open'
                                                 GROUP BY pr.id_proc_ch
                                                 ORDER BY pr.created_request DESC LIMIT 5");
        if ($sqlAssignments) {
            while ($assignment = mysqli_fetch_assoc($sqlAssignments)) {
                $myAssignments[] = $assignment;
            }
        } else {
            logDashboardError("Failed to fetch role 51 assignments: " . mysqli_error($koneksi));
        }
    } elseif (!$isAdmin) {
        // For regular PICs - show only requests assigned to their categories
        $sqlAssignments = mysqli_query($koneksi, "SELECT pr.id_proc_ch, pr.title, pr.created_request, pr.status, 
                                                 COUNT(prd.id) as item_count, 
                                                 SUM(CASE WHEN prd.urgency_status = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
                                                 pr.created_request as timestamp
                                                 FROM proc_purchase_requests pr
                                                 JOIN proc_request_details prd ON pr.id_proc_ch = prd.id_proc_ch
                                                 JOIN proc_admin_category pac ON prd.category = pac.id_category
                                                 WHERE pac.idnik = '$niklogin' AND pr.status = 'Open'
                                                 GROUP BY pr.id_proc_ch
                                                 ORDER BY pr.created_request DESC LIMIT 5");
        if ($sqlAssignments) {
            while ($assignment = mysqli_fetch_assoc($sqlAssignments)) {
                $myAssignments[] = $assignment;
            }
        } else {
            logDashboardError("Failed to fetch regular PIC assignments: " . mysqli_error($koneksi));
        }
    }
    // Admins don't need assignments section (it won't be shown)

    // Get monthly data for chart (last 6 months) with proper error handling
    $chartLabels = [];
    $openData = [];
    $closedData = [];

    for ($i = 5; $i >= 0; $i--) {
        $month = date('M', strtotime("-$i month"));
        $startDate = date('Y-m-01', strtotime("-$i month"));
        $endDate = date('Y-m-t', strtotime("-$i month"));

        $chartLabels[] = $month;

        // Get open requests for this month
        $openCount = safeGetCount("SELECT COUNT(*) as count FROM proc_purchase_requests 
                                   WHERE created_request BETWEEN '$startDate' AND '$endDate'", $koneksi);
        $openData[] = $openCount;

        // Get closed requests for this month
        $closedCount = safeGetCount("SELECT COUNT(*) as count FROM proc_purchase_requests 
                                    WHERE status = 'Closed' AND created_request BETWEEN '$startDate' AND '$endDate'", $koneksi);
        $closedData[] = $closedCount;
    }
} catch (Exception $e) {
    logDashboardError("Exception: " . $e->getMessage());
    // Set default values in case of error
    $pendingRequests = $approvedRequests = $completedRequests = $urgentRequests = 0;
    $recentActivities = [];
    $myAssignments = [];
    $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $openData = $closedData = [0, 0, 0, 0, 0, 0];
}
?>

<!-- Welcome Banner -->
<div class="card welcome-banner" style="color: white;">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <h4 class="fs-20 mb-2" style="color:white">Hello, Welcome <?= htmlspecialchars($namalogin) ?></h4>
                <p class="fs-15 mb-2">
                    This system will help you manage procurement requests efficiently.
                    You're logged in as <span class="fw-bold"><?= htmlspecialchars($userRoleText) ?></span>.
                </p>
                <div class="d-flex align-items-center mb-3">
                    <div class="d-flex align-items-center me-3">
                        <i class="ri-calendar-2-line me-1"></i>
                        <span><?= date('l, d F Y') ?></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="ri-building-line me-1"></i>
                        <span><?= htmlspecialchars($divisilogin) ?></span>
                    </div>
                </div>
                <div class="language-container">
                    <p class="indonesian-text fs-14" style="color: white; font-style: italic;">
                        Sistem ini akan membantu Anda dalam mengelola permintaan procurement dengan efisien.
                        Dirancang untuk melacak pengajuan dengan cara yang efektif.
                    </p>
                </div>
            </div>
            <div class="col-lg-3 d-none d-lg-block text-end">
                <i class="ri-shopping-cart-2-line" style="font-size: 4rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Row -->
<div class="row mt-4">
    <!-- <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body p-0">
                <div class="p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary rounded-circle fs-3">
                                <i class="ri-inbox-line"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-1">Pending</p>
                            <h4 class="fs-4 mb-0"><span class="counter-value" data-target="<?= $pendingRequests ?>"><?= $pendingRequests ?></span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body p-0">
                <div class="p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success rounded-circle fs-3">
                                <i class="ri-check-line"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-1">Completed</p>
                            <h4 class="fs-4 mb-0"><span class="counter-value" data-target="<?= $completedRequests ?>"><?= $completedRequests ?></span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body p-0">
                <div class="p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning rounded-circle fs-3">
                                <i class="ri-file-list-3-line"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-1">Approved</p>
                            <h4 class="fs-4 mb-0"><span class="counter-value" data-target="<?= $approvedRequests ?>"><?= $approvedRequests ?></span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body p-0">
                <div class="p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger rounded-circle fs-3">
                                <i class="ri-alarm-warning-line"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-1">Urgent Items</p>
                            <h4 class="fs-4 mb-0"><span class="counter-value" data-target="<?= $urgentRequests ?>"><?= $urgentRequests ?></span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>

<div class="row mt-4">
    <!-- Main Content Column -->
    <div class="col-xl-8">
        <!-- Activity Chart -->
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Procurement Activity</h4>
                <div class="flex-shrink-0">
                    <ul class="nav nav-tabs-custom card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#overview" role="tab">
                                Monthly Overview
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="overview" role="tabpanel">
                        <div class="chart-container" id="procurementActivityChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Procurement Features Section -->
        <h4 class="section-title mb-4 mt-4">Procurement Features</h4>
        <div class="row">
            <!-- Price Requests Card -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card card-animate h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary text-primary rounded fs-3">
                                        <i class="ri-file-list-3-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Price Requests</h5>
                                <p class="text-muted mb-0">Explore our Price Requests section to view and manage procurement requests made by various departments.</p>
                            </div>
                        </div>
                        <?php if ($pendingRequests > 0): ?>
                            <div class="mt-3">
                                <span class="badge bg-soft-primary text-primary fs-12 fw-medium">
                                    <i class="ri-arrow-right-up-line fs-13 align-middle"></i> <?= $pendingRequests ?> Pending requests
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="mt-3">
                            <a href="index.php?page=PurchaseRequests" class="btn btn-primary">Open Price Requests</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Feature Card - Different based on role -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card card-animate h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success text-success rounded fs-3">
                                        <?php if ($isAdmin || $hasRole51): ?>
                                            <i class="ri-settings-4-line"></i>
                                        <?php else: ?>
                                            <i class="ri-eye-line"></i>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <?php if ($isAdmin || $hasRole51): ?>
                                    <h5 class="card-title mb-1">Admin Controls</h5>
                                    <p class="text-muted mb-0">Access administrative tools for managing categories, users, and system settings.</p>
                                <?php else: ?>
                                    <h5 class="card-title mb-1">View Price Requests</h5>
                                    <p class="text-muted mb-0">Browse through active price requests and check their current status.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-3">
                            <?php if ($isAdmin || $hasRole51): ?>
                                <a href="index.php?page=CategoryManagement" class="btn btn-success">Manage Categories</a>
                            <?php else: ?>
                                <a href="index.php?page=ViewPriceReq" class="btn btn-info">View Requests</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-xl-4">
        <!-- My Assignments Section (for both role 51 and regular PICs) -->
        <?php if (($hasRole51 || (!$isAdmin && !$hasRole51)) && count($myAssignments) > 0): ?>
            <div class="card mb-4">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">
                        <?php echo $hasRole51 ? 'All Open Requests' : 'My Assignments'; ?>
                    </h4>
                    <div class="flex-shrink-0">
                        <a href="index.php?page=PurchaseRequests" class="btn btn-sm btn-soft-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap mb-0">
                            <tbody>
                                <?php foreach ($myAssignments as $assignment): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-5">
                                                            <i class="ri-file-list-line"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h6 class="mb-0 text-truncate">
                                                        <a href="index.php?page=DetailPurchase&id=<?= htmlspecialchars($assignment['id_proc_ch']) ?>" class="text-body"><?= htmlspecialchars($assignment['title']) ?></a>
                                                    </h6>
                                                    <p class="mb-0 text-muted fs-12"><?= (int)$assignment['item_count'] ?> items</p>
                                                    <small class="text-muted"><?= date('d M Y', strtotime($assignment['timestamp'])) ?></small>

                                                    <?php if ((int)$assignment['urgent_count'] > 0): ?>
                                                        <span class="badge bg-soft-danger text-danger ms-2">
                                                            <?= (int)$assignment['urgent_count'] ?> urgent
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="index.php?page=DetailPurchase&id=<?= htmlspecialchars($assignment['id_proc_ch']) ?>" class="btn btn-sm btn-light">
                                                <i class="ri-arrow-right-line align-middle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Recent Activities -->
        <!-- <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                <?php if (count($recentActivities) > 0): ?>
                    <?php foreach ($recentActivities as $index => $activity):
                        $actionIcon = "ri-edit-line";
                        $actionColor = "primary";

                        if ($activity['action_type'] == 'INSERT') {
                            $actionIcon = "ri-add-line";
                            $actionColor = "success";
                        } elseif ($activity['action_type'] == 'DELETE') {
                            $actionIcon = "ri-delete-bin-line";
                            $actionColor = "danger";
                        }

                        $activityText = "performed an action";
                        switch ($activity['action_type']) {
                            case 'INSERT':
                                $activityText = "added a new item";
                                break;
                            case 'UPDATE':
                                $activityText = "updated an item";
                                break;
                            case 'DELETE':
                                $activityText = "deleted an item";
                                break;
                        }
                    ?>
                        <div class="activity-item d-flex">
                            <div class="flex-shrink-0">
                                <div class="avatar-xs">
                                    <div class="avatar-title rounded-circle bg-soft-<?= $actionColor ?> text-<?= $actionColor ?>">
                                        <i class="<?= $actionIcon ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= htmlspecialchars($activity['nama'] ?? 'System') ?></h6>
                                <p class="text-muted mb-1">
                                    <?= $activityText ?>
                                    <?php if (!empty($activity['title'])): ?>
                                        in "<?= htmlspecialchars($activity['title']) ?>"
                                    <?php endif; ?>
                                </p>
                                <small class="mb-0 text-muted"><?= date('d M Y, H:i', strtotime($activity['timestamp'])) ?></small>
                            </div>
                        </div>
                        <?php if ($index < count($recentActivities) - 1): ?>
                            <div class="mb-3"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="ri-inbox-archive-line fs-3 text-muted"></i>
                        <p class="mt-2 text-muted">No recent activities found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div> -->

        <!-- Quick Tips -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Tips</h5>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3 p-2 quick-tip">
                    <div class="flex-shrink-0">
                        <i class="ri-information-line text-primary fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fs-14 mb-1">Detailed Specifications</h6>
                        <p class="text-muted mb-0">Include clear details when creating requests for faster processing</p>
                    </div>
                </div>
                <div class="d-flex mb-3 p-2 quick-tip">
                    <div class="flex-shrink-0">
                        <i class="ri-error-warning-line text-warning fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fs-14 mb-1">Use Urgent Status Wisely</h6>
                        <p class="text-muted mb-0">Mark items as 'urgent' only when truly necessary</p>
                    </div>
                </div>
                <div class="d-flex p-2 quick-tip">
                    <div class="flex-shrink-0">
                        <i class="ri-file-list-3-line text-success fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fs-14 mb-1">Check Request Status</h6>
                        <p class="text-muted mb-0">Monitor your requests regularly for updates</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the activity chart
        const options = {
            series: [{
                name: 'Received',
                data: <?= json_encode($openData) ?>
            }, {
                name: 'Completed',
                data: <?= json_encode($closedData) ?>
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                },
                stacked: false
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 5
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            colors: ['#556ee6', '#34c38f'],
            xaxis: {
                categories: <?= json_encode($chartLabels) ?>,
            },
            yaxis: {
                title: {
                    text: 'Number of Requests'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " requests"
                    }
                }
            }
        };

        try {
            const chart = new ApexCharts(document.querySelector("#procurementActivityChart"), options);
            chart.render();
        } catch (e) {
            console.error("Error rendering chart:", e);
            document.querySelector("#procurementActivityChart").innerHTML =
                '<div class="alert alert-warning">Unable to load chart data. Please refresh the page.</div>';
        }

        // Counter animation for statistics
        const counterElements = document.querySelectorAll('.counter-value');
        counterElements.forEach(function(element) {
            const target = parseInt(element.getAttribute('data-target'));
            let count = 0;
            const increment = Math.ceil(target / 20);

            const interval = setInterval(function() {
                count += increment;
                if (count >= target) {
                    element.textContent = target;
                    clearInterval(interval);
                } else {
                    element.textContent = count;
                }
            }, 30);
        });
    });
</script>
<!-- admin-log.php -->
<?php include 'koneksi.php'; ?>

<!-- CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

<style>
    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .table th {
        background-color: #f8f9fa;
        white-space: nowrap;
    }

    .table td {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: pre-wrap;
    }

    .json-value {
        font-family: monospace;
        white-space: pre-wrap;
        max-height: 150px;
        overflow-y: auto;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .btn-export {
        margin: 2px;
    }
</style>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">System Activity Log</h4>
        <p class="text-muted mb-0">Track all system activities and changes</p>
    </div>

    <div class="card-body">
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="dateRange" placeholder="Select date range">
                        <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Action Type</label>
                    <select id="actionTypeFilter" class="form-select">
                        <option value="">All Activities</option>
                        <option value="INSERT">New Records</option>
                        <option value="UPDATE">Updates</option>
                        <option value="DELETE">Deletions</option>
                    </select>
                </div>


                <div class="col-md-3">
                    <button id="clearFilters" class="btn btn-light w-100">
                        <i class="ri-filter-off-line me-1"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="adminLogTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>User</th>
                        <th>Activity</th>
                        <th>Source</th>
                        <th>Record ID</th>
                        <th>Previous Data</th>
                        <th>New Data</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function() {
        // Initialize flatpickr
        $("#dateRange").flatpickr({
            mode: "range",
            dateFormat: "Y-m-d",
            maxDate: "today",
            rangeSeparator: " to ",
            placeholder: "Select date range",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    table.ajax.reload();
                }
            }
        });

        // Helper function untuk format JSON
        function formatJsonValue(data) {
            if (!data || data === '-') return '-';
            try {
                const obj = typeof data === 'string' ? JSON.parse(data) : data;
                let formattedHtml = '<div class="small bg-light p-2 rounded" style="max-height:200px;overflow-y:auto">';

                // Format key-value pairs
                Object.entries(obj).forEach(([key, value]) => {
                    if (key === 'logged_by' || key === 'logged_at') return;

                    // Format key label
                    const label = key.split('_')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');

                    // Format value based on type
                    let displayValue = value;
                    if (value === null || value === '') {
                        displayValue = '-';
                    } else if (typeof value === 'boolean') {
                        displayValue = value ? 'Yes' : 'No';
                    } else if (key.toLowerCase().includes('date') || key.toLowerCase().includes('created')) {
                        displayValue = moment(value).format('YYYY-MM-DD HH:mm:ss');
                    }

                    formattedHtml += `<div class="mb-1"><strong>${label}:</strong> ${displayValue}</div>`;
                });

                formattedHtml += '</div>';
                return formattedHtml;
            } catch (e) {
                return data || '-';
            }
        }

        var table = $('#adminLogTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'function/get_admin_log.php',
                type: 'POST',
                dataType: 'json',
                data: function(d) {
                    var dates = $('#dateRange').val().split(' to ');
                    return {
                        ...d,
                        date_start: dates[0] || '',
                        date_end: dates[1] || '',
                        action_type: $('#actionTypeFilter').val()
                    };
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', error, thrown);
                }
            },
            columns: [{
                    data: 'log_id',
                    width: '80px'
                },
                {
                    data: 'idnik',
                    render: function(data) {
                        return `<span class="text-primary">${data}</span>`;
                    }
                },
                {
                    data: 'action_type',
                    render: function(data) {
                        const badges = {
                            'INSERT': 'success',
                            'UPDATE': 'warning',
                            'DELETE': 'danger'
                        };
                        const text = {
                            'INSERT': 'New Entry',
                            'UPDATE': 'Updated',
                            'DELETE': 'Deleted'
                        };
                        return `<span class="badge bg-${badges[data] || 'secondary'}">${text[data] || data}</span>`;
                    }
                },
                {
                    data: 'table_name',
                    render: function(data) {
                        return data.replace('proc_', '').replace(/_/g, ' ');
                    }
                },
                {
                    data: 'record_id'
                },
                {
                    data: 'old_value',
                    render: formatJsonValue
                },
                {
                    data: 'new_value',
                    render: formatJsonValue
                },
                {
                    data: 'timestamp',
                    render: function(data) {
                        return `<span class="text-muted small">${moment(data).format('DD MMM YYYY HH:mm:ss')}</span>`;
                    }
                }
            ],
            order: [
                [0, 'desc']
            ],
            pageLength: 5, // Menampilkan 5 data per halaman
            lengthMenu: [
                [5, 10, 25, 50],
                [5, 10, 25, 50]
            ], // Opsi jumlah data per halaman
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                '<"row"<"col-12"tr>>' +
                '<"row mt-3"<"col-md-4"l><"col-md-4"i><"col-md-4"p>>', // Custom layout dengan pagination
            buttons: [{
                    extend: 'excel',
                    text: '<i class="ri-file-excel-line me-1"></i> Excel',
                    className: 'btn btn-sm btn-success me-2'
                },
                {
                    extend: 'pdf',
                    text: '<i class="ri-file-pdf-line me-1"></i> PDF',
                    className: 'btn btn-sm btn-danger me-2'
                },
                {
                    extend: 'print',
                    text: '<i class="ri-printer-line me-1"></i> Print',
                    className: 'btn btn-sm btn-info'
                }
            ],
            responsive: true,
            language: {
                search: "Search logs:",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: '<i class="ri-arrow-left-double-line"></i>',
                    last: '<i class="ri-arrow-right-double-line"></i>',
                    next: '<i class="ri-arrow-right-s-line"></i>',
                    previous: '<i class="ri-arrow-left-s-line"></i>'
                },
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                emptyTable: "No activity logs found",
                zeroRecords: "No matching logs found"
            }
        });

        // Event handlers untuk filter
        $('#dateRange, #actionTypeFilter').on('change', function() {
            table.ajax.reload();
        });

        $('#clearFilters').on('click', function() {
            $('#dateRange').val('');
            $('#actionTypeFilter').val('');
            table.ajax.reload();
        });
    });
</script>
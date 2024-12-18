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
        // Initialize date picker
        $('#dateRange').flatpickr({
            mode: "range",
            dateFormat: "Y-m-d",
            maxDate: "today"
        });

        // Initialize DataTable
        var table = $('#adminLogTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'function/get_admin_log.php',
                type: 'POST',
                data: function(d) {
                    var dates = $('#dateRange').val().split(' to ');
                    return {
                        ...d,
                        date_start: dates[0] || '',
                        date_end: dates[1] || '',
                        action_type: $('#actionTypeFilter').val(),
                        table_name: $('#tableNameFilter').val()
                    };
                }
            },
            columns: [{
                    data: 'log_id'
                },
                {
                    data: 'idnik',
                    render: function(data, type, row) {
                        return `<span class="text-primary">${data}</span>`;
                    }
                },
                {
                    data: 'action_type',
                    render: function(data) {
                        let badge = '';
                        switch (data) {
                            case 'INSERT':
                                badge = 'bg-success';
                                break;
                            case 'UPDATE':
                                badge = 'bg-warning';
                                break;
                            case 'DELETE':
                                badge = 'bg-danger';
                                break;
                        }
                        return `<span class="badge ${badge}">${data}</span>`;
                    }
                },
                {
                    data: 'table_name'
                },
                {
                    data: 'record_id'
                },
                {
                    data: 'old_value',
                    render: function(data) {
                        try {
                            if (!data) return '';
                            const obj = JSON.parse(data);
                            return `<div class="json-value">${JSON.stringify(obj, null, 2)}</div>`;
                        } catch {
                            return data || '';
                        }
                    }
                },
                {
                    data: 'new_value',
                    render: function(data) {
                        try {
                            if (!data) return '';
                            const obj = JSON.parse(data);
                            return `<div class="json-value">${JSON.stringify(obj, null, 2)}</div>`;
                        } catch {
                            return data || '';
                        }
                    }
                },
                {
                    data: 'timestamp',
                    render: function(data) {
                        return moment(data).format('DD MMM YYYY HH:mm:ss');
                    }
                }
            ],
            order: [
                [0, 'desc']
            ],
            pageLength: 25,
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            buttons: [{
                    extend: 'excel',
                    text: '<i class="ri-file-excel-line me-1"></i> Excel',
                    className: 'btn btn-sm btn-success btn-export'
                },
                {
                    extend: 'pdf',
                    text: '<i class="ri-file-pdf-line me-1"></i> PDF',
                    className: 'btn btn-sm btn-danger btn-export'
                },
                {
                    extend: 'print',
                    text: '<i class="ri-printer-line me-1"></i> Print',
                    className: 'btn btn-sm btn-info btn-export'
                }
            ],
            responsive: true,
            language: {
                search: "Search logs:",
                processing: '<div class="spinner-border text-primary" role="status"></div>',
                emptyTable: "No activity logs found",
                zeroRecords: "No matching logs found",
            }
        });

        // Load table names for filter
        $.get('function/get_table_names.php', function(data) {
            $('#tableNameFilter').append(
                data.map(name => `<option value="${name}">${name.replace('proc_', '').replace('_', ' ')}</option>`)
            );
        });

        // Handle filter changes
        $('#dateRange, #actionTypeFilter, #tableNameFilter').on('change', function() {
            table.ajax.reload();
        });

        // Handle clear filters
        $('#clearFilters').on('click', function() {
            $('#dateRange').val('');
            $('#actionTypeFilter').val('');
            $('#tableNameFilter').val('');
            table.ajax.reload();
        });
    });
</script>
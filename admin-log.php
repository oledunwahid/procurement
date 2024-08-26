<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Admin Log</h5>
                    <div class="mt-2">
                        <label for="dateRange">Filter by Date:</label>
                        <input type="text" id="dateRange" name="dateRange" class="form-control" />
                    </div>
                    <div class="mt-2">
                        <label for="actionTypeFilter">Action Type:</label>
                        <select id="actionTypeFilter" class="form-control">
                            <option value="">All</option>
                            <option value="INSERT">INSERT</option>
                            <option value="UPDATE">UPDATE</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div class="mt-2">
                        <label for="tableNameFilter">Table Name:</label>
                        <select id="tableNameFilter" class="form-control">
                            <option value="">All</option>
                            <!-- Populate this dynamically with table names from your database -->
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <table id="adminLogTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Log ID</th>
                                <th>Admin NIK</th>
                                <th>Action Type</th>
                                <th>Table Name</th>
                                <th>Record ID</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>
<script>
    $(document).ready(function() {
        $('#dateRange').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        var table = $('#adminLogTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "function/get_admin_log.php",
                "type": "POST",
                "data": function(d) {
                    d.dateRange = $('#dateRange').val();
                    d.actionType = $('#actionTypeFilter').val();
                    d.tableName = $('#tableNameFilter').val();
                },
                "error": function(xhr, error, thrown) {
                    console.error("DataTables AJAX error:", error);
                    console.log("Server response:", xhr.responseText);
                    alert("An error occurred while fetching data. Please check the console for more information.");
                },
                "dataSrc": function(json) {
                    if (!json || !json.data) {
                        console.error("Invalid JSON structure received:", json);
                        return [];
                    }
                    return json.data;
                }
            },
            "columns": [{
                    "data": 0
                },
                {
                    "data": 1
                },
                {
                    "data": 2
                },
                {
                    "data": 3
                },
                {
                    "data": 4
                },
                {
                    "data": 5
                },
                {
                    "data": 6
                },
                {
                    "data": 7
                }
            ],
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#adminLogTable_wrapper .col-md-6:eq(0)');

        $('#dateRange, #actionTypeFilter, #tableNameFilter').on('change', function() {
            table.ajax.reload();
        });

        $('<button>')
            .text('Clear Filters')
            .addClass('btn btn-secondary ml-2')
            .on('click', function() {
                $('#dateRange').val('');
                $('#actionTypeFilter').val('');
                $('#tableNameFilter').val('');
                table.ajax.reload();
            })
            .appendTo('.card-header');
    });
</script>
<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    #adminLogTable {
        width: 100% !important;
    }

    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .modal-dialog {
        max-width: 90%;
    }

    .modal-body {
        max-height: 80vh;
        overflow-y: auto;
    }
</style>
<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0 flex-grow-1">Admin Log</h4>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" class="form-control" data-provider="flatpickr" data-range-date="true" data-date-format="Y-m-d" data-deafult-date="01 Jan 2022 to 31 Jan 2022" id="dateRange">
                    <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <select id="actionTypeFilter" class="form-select">
                    <option value="">All Action Types</option>
                    <option value="INSERT">INSERT</option>
                    <option value="UPDATE">UPDATE</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="tableNameFilter" class="form-select">
                    <option value="">All Tables</option>
                    <!-- Table names will be populated dynamically -->
                </select>
            </div>
            <div class="col-md-3">
                <button id="clearFilters" class="btn btn-light w-100"><i class="ri-filter-off-line align-bottom me-1"></i> Clear Filters</button>
            </div>
        </div>
        <table id="adminLogTable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
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
                <!-- Data will be filled by DataTables -->
            </tbody>
        </table>
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
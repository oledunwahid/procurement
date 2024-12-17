<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">


<!-- CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0 flex-grow-1">Category Management</h4>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="ri-add-line align-bottom me-1"></i> Add Category Assignment
                </button>
            </div>
        </div>
        <table id="categoryTable" class="table table-bordered dt-responsive nowrap table-striped align-middle">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Assigned PIC (NIK)</th>
                    <th>PIC Name</th>
                    <th>WhatsApp Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be filled by DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Manage Category Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="action" name="action" value="add">
                    <input type="hidden" id="category_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <select class="form-select" id="location" name="location" required>
                            <option value="">Select Location</option>
                            <option value="HO">Head Office</option>
                            <option value="Site">Site</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="id_category" name="id_category" required>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign PIC</label>
                        <select class="form-select" id="idnik" name="idnik" required>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">WhatsApp Number</label>
                        <div class="input-group">
                            <span class="input-group-text">+62</span>
                            <input type="text" class="form-control" id="no_wa" name="no_wa"
                                placeholder="Example: 81234567890"
                                pattern="[0-9]{10,13}"
                                title="Please enter valid number without country code"
                                required>
                        </div>
                        <small class="text-muted">Enter number without leading zero</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCategory">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Include necessary JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        var categoryTable = $('#categoryTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'function/get_category_assignments.php',
                type: 'POST',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'id_category'
                },
                {
                    data: 'nama_category'
                },
                {
                    data: 'idnik'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'no_wa',
                    render: function(data, type, row) {
                        return data ? '+62' + data : '-';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div class="d-flex gap-2">
                    <button class="btn btn-sm btn-info edit-btn" data-id="${row.id}">
                        <i class="ri-edit-2-line"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>`;
                    }
                }
            ],
            responsive: true
        });


        // Load Categories
        $.ajax({
            url: 'function/get_category.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Categories loaded:', data);
                let options = '<option value="">Select Category</option>';
                data.forEach(function(category) {
                    options += `<option value="${category.id_category}">${category.nama_category}</option>`;
                });
                $('#id_category').html(options);
            }
        });

        // Handle location and category change
        $('#location, #id_category').on('change', function() {
            var location = $('#location').val();
            var id_category = $('#id_category').val();
            if (location && id_category) {
                $.ajax({
                    url: 'function/get_pic.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        location: location,
                        id_category: id_category
                    },
                    beforeSend: function() {
                        $('#idnik').html('<option value="">Loading...</option>');
                    },
                    success: function(data) {
                        let options = '<option value="">Select PIC</option>';
                        if (Array.isArray(data)) {
                            data.forEach(function(pic) {
                                options += `<option value="${pic.idnik}">${pic.nama}</option>`;
                            });
                            $('#idnik').html(options);
                        }
                    }
                });
            } else {
                $('#idnik').html('<option value="">Select PIC</option>');
            }
        });

        // Save Category Assignment
        $('#saveCategory').on('click', function() {
            if (!$('#categoryForm')[0].checkValidity()) {
                $('#categoryForm')[0].reportValidity();
                return;
            }

            var formData = $('#categoryForm').serialize();

            $.ajax({
                url: 'function/insert_category.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $('#saveCategory').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        $('#categoryModal').modal('hide');
                        $('.modal-backdrop').remove();
                        categoryTable.ajax.reload();

                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to save changes',
                            icon: 'error'
                        });
                    }
                },
                complete: function() {
                    $('#saveCategory').prop('disabled', false);
                }
            });
        });

        // Handle Delete
        $('#categoryTable').on('click', '.delete-btn', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'function/insert_category.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                categoryTable.ajax.reload();
                                Swal.fire('Deleted!', response.message, 'success');
                            }
                        }
                    });
                }
            });
        });

        // loadCategoryData function
        function loadCategoryData(id) {
            $.ajax({
                url: 'function/get_category_detail.php',
                type: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    $('#action').val('edit');
                    $('#category_id').val(id);
                    $('#location').val(data.location);
                    $('#id_category').val(data.id_category);
                    $('#no_wa').val(data.no_wa);

                    $('#location, #id_category').trigger('change');
                    setTimeout(function() {
                        $('#idnik').val(data.idnik);
                    }, 500);
                }
            });
        }


        // Handle Edit
        $('#categoryTable').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            loadCategoryData(id);
            $('#categoryModal').modal('show');
        });

        // Handle Add New
        $('[data-bs-toggle="modal"]').on('click', function() {
            $('#categoryForm')[0].reset();
            $('#action').val('add');
            $('#categoryModal').modal('show');
        });

    });
</script>
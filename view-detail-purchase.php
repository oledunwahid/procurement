<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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




    /* Menyesuaikan lebar kolom pada tabel */


    /* Custom button styles */
    .btn-icon .ri {
        margin-right: 4px;
    }

    .action-buttons .btn {
        padding: 5px 10px;
        font-size: 14px;
    }

    /* Responsive card body padding */
    .card-body-enhanced {
        padding: 1.5rem;
    }

    /* Customizing the input fields for a better look */
    .form-control {
        border-radius: 0.375rem;
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>


<?php
$id_proc_ch = $_GET['id'];
$sql = mysqli_query($koneksi, "SELECT * FROM proc_purchase_requests WHERE  id_proc_ch ='$id_proc_ch' "); // query jika filter dipilih
$row = mysqli_fetch_assoc($sql) // fetch query yang sesuai ke dalam array
?>

<div class="col">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="card-title mb-0">Input Detail Price Request</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary btn-enhanced" id="addRow">
                        <i class="ri-add-line icon-btn-space"></i>Add Row
                    </button>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover" id="detail-purchase-request">
                            <thead>
                                <tr>
                                    <th style="display:none;">ID Request</th>
                                    <th>Nama Barang</th>
                                    <th width="20%">Detail Spec</th>
                                    <th width="6%">Qty</th>
                                    <th width="7%">Uom</th>
                                    <th width="10%">Harga</th>
                                    <th width="13%">Total Harga</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan di-load menggunakan AJAX -->
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
                    <form id="updatePurchaseRequestForm">
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
                                <div class="col-lg-3 col-sm-6">
                                    <label for="choices-payment-status">ID NIK Requester</label>
                                    <div class="input-light">
                                        <input type="text" name="requester_name" class="form-control bg-light border-0" value="<?= $row['nik_request'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <label for="choices-payment-status">Title</label>
                                    <div class="input-light">
                                        <input type="text" name="title" class="form-control" value="<?= $row['title'] ?>">
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-control" data-choices name="category" id="kategori" required>
                                            <option value="">Select Category</option>
                                            <?php
                                            $sqlCategory = mysqli_query($koneksi, "SELECT * FROM proc_category");
                                            while ($rowCategory = mysqli_fetch_assoc($sqlCategory)) {
                                                $selected = ($rowCategory['id_category'] == $category) ? 'selected' : '';
                                                echo "<option value='" . $rowCategory['id_category'] . "' " . $selected . ">" . $rowCategory['nama_category'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <label for="choices-payment-status">Job Location</label>
                                    <div>
                                        <select class="form-control" name="jobLocation" id="lokasi" data-choices data-choices-search-false required>
                                            <option value="">Pilih Lokasi</option>
                                            <option value="HO">HO</option>
                                            <option value="OBI">OBI</option>
                                            <option value="LAR">LAR</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6">
                                    <label for="choices-payment-status">PIC Category Select</label>
                                    <div>
                                        <select class="form-control" name="proc_pic" id="pic">
                                            <option value="">Select PIC</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6">
                                    <div>
                                        <label for="totalamountInput">Total Amount</label>
                                        <input type="text" name="total_price" id="total_price" class="form-control bg-light border-0" value="<?= $row['total_price'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div>
                                        <label for="totalamountInput">Attachment</label>
                                        <input type="file" name="lampiran" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3">
                            <button type="submit" name="updatePurchaseRequestForm" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var idProcCh = <?= json_encode($_GET['id']); ?>;
        // Event handler ketika kategori atau lokasi berubah
        $('#kategori, #lokasi').change(function() {
            if ($('#kategori').val() && $('#lokasi').val()) {
                $.ajax({
                    url: "function/get_pic.php",
                    type: "POST",
                    data: {
                        id_category: $('#kategori').val(),
                        location: $('#lokasi').val()
                    },
                    success: function(data) {
                        $('#pic').html('<option value="">Pilih PIC</option>' + data);
                    }
                });
            } else {
                $('#pic').html('<option value="">Pilih PIC</option>');
            }
        });

        function formatRibuan(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function loadData(callback) {
            $.ajax({
                url: 'function/fetch_view_detail_purchase_request.php',
                type: 'GET',
                data: {
                    id_proc_ch: idProcCh
                },
                success: function(data) {
                    $('#detail-purchase-request tbody').html(data);
                    if (callback) callback();
                }
            });
        }

        loadData();

        function addRow() {
            var newRow = `<tr>
                <td style="display:none;"><input type="text" name="id_proc_ch[]" class="form-control" value="${idProcCh}" readonly /></td>
                <td><input type="text" name="nama_barang[]" class="form-control nama-barang" style="width: 100%;" /></td>
                <td><textarea name="detail_specification[]" class="form-control" style="width: 100%;"></textarea></td>
                <td><input type="number" name="qty[]" class="form-control" maxlength="5" style="width: 80px;" /></td>
                <td><input type="text" name="uom[]" class="form-control" style="width: 80px;" /></td>
                <td><span type="text" name="unit_price[]"</span>0</td>
                <td><span class="totalHarga">0</span></td>
                <td>
                    <button type="button" class="btn btn-success btn-sm saveNewRow">Save Now</button>
                    <button type="button" class="btn btn-success btn-sm saveRow" style="display: none;">Save</button>
                    <button type="button" class="btn btn-danger remove" style="display: none;" data-id="">Remove</button>
                </td>
            </tr>`;
            $('#detail-purchase-request tbody').append(newRow);

            // Inisialisasi auto-suggestion pada input nama barang yang baru ditambahkan
            $('.nama-barang:last').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: 'function/get_suggestion.php',
                        data: {
                            query: request.term
                        },
                        dataType: 'json',
                        success: function(data) {
                            response(data);
                        }
                    });
                }
            });
        }
        $('#addRow').click(function() {
            addRow();
        });

        $(document).on('click', '.saveNewRow', function() {
            var row = $(this).closest('tr');
            var data = {
                id_proc_ch: row.find("input[name='id_proc_ch[]']").val(),
                nama_barang: row.find("input[name='nama_barang[]']").val(),
                detail_specification: row.find("textarea[name='detail_specification[]']").val(),
                qty: row.find("input[name='qty[]']").val(),
                uom: row.find("input[name='uom[]']").val(),
            };

            $.ajax({
                type: "POST",
                url: "function/insert_view_detail_purchase_request.php",
                data: data,
                success: function(response) {
                    alert("Data berhasil disimpan");
                    loadData();
                },
                error: function() {
                    alert("Terjadi kesalahan saat menyimpan data");
                }
            });
        });

        $(document).on('click', '.edit', function() {
            var $row = $(this).closest('tr');
            $row.find('input').prop('readonly', false);
            $(this).hide();
            $row.find('.saveRow').show();
        });

        // Perbaikan pada fungsi saveRow
        $(document).on('click', '.saveRow', function() {
            var row = $(this).closest('tr');
            var data = {
                id: $(this).data('id'),
                id_proc_ch: row.find("input[name='id_proc_ch[]']").val(),
                nama_barang: row.find("input[name='nama_barang[]']").val(),
                detail_specification: row.find("textarea[name='detail_specification[]']").val(),
                qty: row.find("input[name='qty[]']").val(),
                uom: row.find("input[name='uom[]']").val(),
            };

            $.ajax({
                type: "POST",
                url: "function/update_view_detail_purchase.php",
                data: data,
                success: function(response) {
                    alert("Data berhasil diupdate");
                    loadData();
                },
                error: function() {
                    alert("Terjadi kesalahan saat menyimpan data");
                }
            });
        });

        // Perbaikan pada fungsi remove
        $(document).on('click', '.remove', function() {
            var id = $(this).data('id');
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                $.ajax({
                    type: "POST",
                    url: "function/delete_view_detail_purchase.php",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        alert("Data berhasil dihapus");
                        loadData(function() {
                            updateTotalPrice();
                        });
                    },
                    error: function() {
                        alert("Terjadi kesalahan saat menghapus data");
                    }
                });
            }
        });


        $('#updatePurchaseRequestForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: "function/update_view_purchase.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    window.location.href = "index.php?page=PurchaseRequests";
                },
                error: function() {
                    alert("Terjadi kesalahan saat mengupdate data");
                }
            });
        });

    });
</script>
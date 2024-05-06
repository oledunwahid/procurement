<?php
include '../koneksi.php'; // Sesuaikan dengan path file koneksi Anda

$id_proc_ch = $_GET['id_proc_ch'];

function nominal($angka)
{
    $hasil_nominal = number_format($angka, 0, ',', '.');
    return $hasil_nominal;
}
$query = "SELECT ppr.*, prd.id, prd.nama_barang, prd.qty, prd.uom, prd.unit_price, prd.detail_specification, prd.detail_notes
            FROM proc_request_details prd
            INNER JOIN proc_purchase_requests ppr ON prd.id_proc_ch = ppr.id_proc_ch
            WHERE ppr.id_proc_ch = '$id_proc_ch'";

$result = mysqli_query($koneksi, $query);
$output = '';

while ($row = mysqli_fetch_assoc($result)) {
    // Menghitung total harga (harga * jumlah)
    $totalHarga = $row['unit_price'] * $row['qty'];
    $output .= "<tr>
                    <td style='display:none;'><input type='text' name='id_proc_ch[]' class='form-control' value='" . $row['id_proc_ch'] . "' readonly /></td>
                    <td><input type='text' name='nama_barang[]' class='form-control' value='" . $row['nama_barang'] . "' readonly /></td>
                    <td><textarea name='detail_specification[]' class='form-control' readonly style='width: 100%;'>" . $row['detail_specification'] . "</textarea></td>
                    <td><input type='number' name='qty[]' class='form-control' value='" . $row['qty'] . "' readonly maxlength='5' /></td>
                    <td><input type='text' name='uom[]' class='form-control' value='" . $row['uom'] . "' readonly /></td>
                    <td><input type='text' name='unit_price[]' class='form-control' value='" . nominal($row['unit_price']) . "' readonly maxlength='11' /></td>
                    <td><span class='totalHarga'><b>" . nominal($totalHarga) . "</b></span></td>
                    <td>
                        <button type='button' class='btn btn-info btn-sm edit' data-id='" . $row['id'] . "'>Edit</button>
                        <button type='button' class='btn btn-success btn-sm saveRow' data-id='" . $row['id'] . "' style='display:none;'>Save</button>
                        <button type='button' class='btn btn-danger btn-sm remove' data-id='" . $row['id'] . "'>Remove</button>
                    </td>
                    <td><textarea name='detail_notes[]' class='form-control' readonly style='width: 100%;'>" . $row['detail_notes'] . "</textarea></td>
                </tr>";
}

echo $output;

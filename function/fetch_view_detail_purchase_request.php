<?php
include '../koneksi.php';

$id_proc_ch = $_GET['id_proc_ch'];

function nominal($angka)
{
    $hasil_nominal = number_format($angka, 0, ',', '.');
    return $hasil_nominal;
}

$query = "SELECT ppr.*, prd.id, prd.nama_barang, prd.qty, prd.uom, prd.unit_price, prd.detail_specification
          FROM proc_request_details prd
          INNER JOIN proc_purchase_requests ppr ON prd.id_proc_ch = ppr.id_proc_ch
          WHERE ppr.id_proc_ch = '$id_proc_ch'";

$result = mysqli_query($koneksi, $query);
$output = '';

while ($row = mysqli_fetch_assoc($result)) {
    $totalHarga = $row['unit_price'] * $row['qty'];
    $output .= "<tr>
                    <td style='display:none;'><input type='text' name='id_proc_ch[]' class='form-control' value='" . $row['id_proc_ch'] . "' readonly /></td>
                    <td><input type='text' name='nama_barang[]' class='form-control' value='" . $row['nama_barang'] . "' readonly /></td>
                    <td><textarea name='detail_specification[]' class='form-control' readonly style='width: 100%;'>" . $row['detail_specification'] . "</textarea></td>
                    <td><input type='number' name='qty[]' class='form-control' value='" . $row['qty'] . "' readonly maxlength='5' /></td>
                    <td>
                        <select name='uom[]' class='form-control' readonly>
                            <option value='Pcs' " . ($row['uom'] == 'Pcs' ? 'selected' : '') . ">Pcs</option>
                            <option value='Buah' " . ($row['uom'] == 'Buah' ? 'selected' : '') . ">Buah</option>
                            <option value='Unit' " . ($row['uom'] == 'Unit' ? 'selected' : '') . ">Unit</option>
                            <option value='Pack' " . ($row['uom'] == 'Pack' ? 'selected' : '') . ">Pack</option>
                            <option value='Dus' " . ($row['uom'] == 'Dus' ? 'selected' : '') . ">Dus</option>
                            <option value='M' " . ($row['uom'] == 'M' ? 'selected' : '') . ">M</option>
                            <option value='Btg' " . ($row['uom'] == 'Btg' ? 'selected' : '') . ">Btg</option>
                            <option value='CM' " . ($row['uom'] == 'CM' ? 'selected' : '') . ">CM</option>
                            <option value='KM' " . ($row['uom'] == 'KM' ? 'selected' : '') . ">KM</option>
                            <option value='Ich' " . ($row['uom'] == 'Ich' ? 'selected' : '') . ">Ich</option>
                            <option value='Kg' " . ($row['uom'] == 'Kg' ? 'selected' : '') . ">Kg</option>
                            <option value='Gram' " . ($row['uom'] == 'Gram' ? 'selected' : '') . ">Gram</option>
                            <option value='Lot' " . ($row['uom'] == 'Lot' ? 'selected' : '') . ">Lot</option>
                            <option value='ml' " . ($row['uom'] == 'ml' ? 'selected' : '') . ">ml</option>
                        </select>
                    </td>
                    <td><span name='unit_price[]' value='" . nominal($row['unit_price']) . "' readonly maxlength='11'></span>0</td>
                    <td><span class='totalHarga'><b>" . nominal($totalHarga) . "</b></span></td>
                    <td>
                        <button type='button' class='btn btn-info btn-sm edit' data-id='" . $row['id'] . "'>Edit</button>
                        <button type='button' class='btn btn-success btn-sm saveRow' data-id='" . $row['id'] . "' style='display:none;'>Save</button>
                        <button type='button' class='btn btn-danger btn-sm remove' data-id='" . $row['id'] . "'>Remove</button>
                    </td>
                </tr>";
}

echo $output;

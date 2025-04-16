<?php
include '../koneksi.php';

$id_proc_ch = $_GET['id_proc_ch'];

function nominal($angka)
{
    return number_format($angka, 0, ',', '.');
}

// Fetch UOM data
$uomQuery = "SELECT uom_name FROM uom";
$uomResult = mysqli_query($koneksi, $uomQuery);
$uomOptions = "";

while ($uomRow = mysqli_fetch_assoc($uomResult)) {
    $uomOptions .= "<option value='" . $uomRow['uom_name'] . "'>" . $uomRow['uom_name'] . "</option>";
}

// Fetch Category data
$categoryQuery = "SELECT id_category, nama_category FROM proc_category";
$categoryResult = mysqli_query($koneksi, $categoryQuery);
$categoryOptions = "";

while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
    $categoryOptions .= "<option value='" . $categoryRow['id_category'] . "'>" . $categoryRow['nama_category'] . "</option>";
}

// Define urgency options
$urgencyOptions = "
    <option value='normal'>Normal</option>
    <option value='urgent'>Urgent</option>
";

$query = "SELECT prd.*, pc.nama_category, prd.id, prd.nama_barang, prd.qty, prd.uom, prd.unit_price, 
          prd.detail_specification, prd.category, prd.urgency_status
          FROM proc_request_details prd
          LEFT JOIN proc_category pc ON prd.category = pc.id_category
          WHERE prd.id_proc_ch = '$id_proc_ch'";

$result = mysqli_query($koneksi, $query);
$output = '';

while ($row = mysqli_fetch_assoc($result)) {
    $totalHarga = $row['unit_price'] * $row['qty'];

    // Generate UOM options with selected attribute
    $uomSelectOptions = "";
    foreach (explode('</option>', $uomOptions) as $option) {
        $selected = strpos($option, "value='" . $row['uom'] . "'") !== false ? "selected" : "";
        $uomSelectOptions .= str_replace("<option", "<option $selected", $option) . "</option>";
    }

    // Generate Category options with selected attribute
    $categorySelectOptions = "";
    foreach (explode('</option>', $categoryOptions) as $option) {
        $selected = strpos($option, "value='" . $row['category'] . "'") !== false ? "selected" : "";
        $categorySelectOptions .= str_replace("<option", "<option $selected", $option) . "</option>";
    }

    // Generate Urgency options with selected attribute
    $urgencySelectOptions = "";
    foreach (explode('</option>', $urgencyOptions) as $option) {
        $selected = strpos($option, "value='" . ($row['urgency_status'] ?? 'normal') . "'") !== false ? "selected" : "";
        $urgencySelectOptions .= str_replace("<option", "<option $selected", $option) . "</option>";
    }

    // Format price and total price with tooltips for better visibility
    $formattedPrice = nominal($row['unit_price']);
    $formattedTotal = nominal($totalHarga);

    $output .= "<tr>
                    <td style='display:none;'><input type='text' name='id_proc_ch[]' class='form-control' value='" . $row['id_proc_ch'] . "' readonly /></td>
                    <td><input type='text' name='nama_barang[]' class='form-control' value='" . $row['nama_barang'] . "' readonly /></td>
                    <td><textarea name='detail_specification[]' class='form-control' readonly style='width: 100%;'>" . $row['detail_specification'] . "</textarea></td>
                    <td><input type='number' name='qty[]' class='form-control' value='" . $row['qty'] . "' readonly maxlength='5' /></td>
                    <td>
                        <select name='category[]' class='form-control' readonly>
                            $categorySelectOptions
                        </select>
                    </td>
                    <td>
                        <select name='uom[]' class='form-control' readonly>
                            $uomSelectOptions
                        </select>
                    </td>
                    <td class='price-cell'>
                        <div class='price-tooltip'>
                            <span name='unit_price[]' class='price-value' value='" . $formattedPrice . "' readonly maxlength='15'>" . $formattedPrice . "</span>
                            <span class='tooltip-text'>Rp " . $formattedPrice . "</span>
                        </div>
                    </td>
                    <td class='total-price-cell'>
                        <div class='price-tooltip'>
                            <span class='totalHarga price-value'><b>" . $formattedTotal . "</b></span>
                            <span class='tooltip-text'>Rp " . $formattedTotal . "</span>
                        </div>
                    </td>
                    <td>
                        <select name='urgency_status[]' class='form-control' readonly>
                            $urgencySelectOptions
                        </select>
                    </td>
                    <td>
                        <button type='button' class='btn btn-info btn-sm edit' data-id='" . $row['id'] . "'>Edit</button>
                        <button type='button' class='btn btn-success btn-sm saveRow' data-id='" . $row['id'] . "' style='display:none;'>Save</button>
                        <button type='button' class='btn btn-danger btn-sm remove' data-id='" . $row['id'] . "'>Remove</button>
                    </td>
                </tr>";
}

echo $output;

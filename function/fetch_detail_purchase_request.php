<?php
include '../koneksi.php';

$id_proc_ch = $_GET['id_proc_ch'];
$niklogin = $_GET['niklogin'];

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

// Fetch categories
$categoryQuery = "SELECT id_category, nama_category FROM proc_category ORDER BY nama_category";
$categoryResult = mysqli_query($koneksi, $categoryQuery);
$categoryOptions = "";

while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
    $categoryOptions .= "<option value='" . $categoryRow['id_category'] . "'>" . $categoryRow['nama_category'] . "</option>";
}

// Main query with urgency status
$query = "SELECT ppr.*, prd.id, prd.nama_barang, prd.qty, prd.uom, prd.unit_price, 
          prd.detail_specification, prd.detail_notes, prd.category, pc.nama_category,
          prd.urgency_status
          FROM proc_request_details prd
          INNER JOIN proc_purchase_requests ppr ON prd.id_proc_ch = ppr.id_proc_ch
          LEFT JOIN proc_category pc ON prd.category = pc.id_category
          WHERE ppr.id_proc_ch = ? 
          AND EXISTS (
              SELECT 1 
              FROM proc_admin_category pac 
              WHERE pac.id_category = prd.category 
              AND pac.idnik = ?
          )
          ORDER BY prd.id ASC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "ss", $id_proc_ch, $niklogin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$output = '';

while ($row = mysqli_fetch_assoc($result)) {
    $totalHarga = $row['unit_price'] * $row['qty'];

    // Generate UOM options
    $uomSelectOptions = "";
    foreach (explode('</option>', $uomOptions) as $option) {
        $selected = strpos($option, "value='" . $row['uom'] . "'") !== false ? "selected" : "";
        $uomSelectOptions .= str_replace("<option", "<option $selected", $option) . "</option>";
    }

    // Generate Category options
    $categorySelectOptions = "";
    foreach (explode('</option>', $categoryOptions) as $option) {
        $selected = strpos($option, "value='" . $row['category'] . "'") !== false ? "selected" : "";
        $categorySelectOptions .= str_replace("<option", "<option $selected", $option) . "</option>";
    }

    // Urgency status badge class
    $urgencyClass = $row['urgency_status'] === 'urgent' ? 'badge bg-danger' : 'badge bg-secondary';

    // Format price and total price with tooltips for better visibility
    $formattedPrice = nominal($row['unit_price']);
    $formattedTotal = nominal($totalHarga);

    $output .= "<tr>
        <td style='display:none;'><input type='text' name='id_proc_ch[]' class='form-control' value='" . $row['id_proc_ch'] . "' readonly /></td>
        <td data-label='Nama Barang:'><textarea name='nama_barang[]' class='form-control form-field-adjustable desc-input' readonly>" . htmlspecialchars($row['nama_barang']) . "</textarea></td>
        <td data-label='Detail Spec:'><textarea name='detail_specification[]' class='form-control' readonly>" . htmlspecialchars($row['detail_specification']) . "</textarea></td>
        <td data-label='Qty:'><input type='number' name='qty[]' class='form-control' value='" . $row['qty'] . "' readonly maxlength='5' /></td>
        <td data-label='Category:'>
            <select name='category[]' class='form-control' readonly>
                $categorySelectOptions
            </select>
        </td>
        <td data-label='Uom:'>
            <select name='uom[]' class='form-control' readonly>
                $uomSelectOptions
            </select>
        </td>
        <td data-label='Harga:' class='price-cell'>
            <div class='price-tooltip'>
                <input type='text' name='unit_price[]' class='form-control price-input price-value' value='" . $formattedPrice . "' readonly maxlength='15' />
                <span class='tooltip-text'>Rp " . $formattedPrice . "</span>
            </div>
        </td>
        <td data-label='Total Harga:' class='total-price-cell'>
            <div class='price-tooltip'>
                <span class='totalHarga price-value'><b>" . $formattedTotal . "</b></span>
                <span class='tooltip-text'>Rp " . $formattedTotal . "</span>
            </div>
        </td>
        <td data-label='Urgency Status:'>
            <select name='urgency_status[]' class='form-control' readonly>
                <option value='normal' " . ($row['urgency_status'] === 'normal' ? 'selected' : '') . ">Normal</option>
                <option value='urgent' " . ($row['urgency_status'] === 'urgent' ? 'selected' : '') . ">Urgent</option>
            </select>
        </td>
        <td data-label='Action:'>
            <div class='action-buttons'>
                <button type='button' class='btn btn-info btn-sm edit' data-id='" . $row['id'] . "'>Edit</button>
                <button type='button' class='btn btn-success btn-sm saveRow' data-id='" . $row['id'] . "' style='display:none;'>Save</button>
                <button type='button' class='btn btn-danger btn-sm remove' data-id='" . $row['id'] . "'>Remove</button>
            </div>
        </td>
        <td><textarea name='detail_notes[]' class='form-control' readonly style='width: 100%;'>" . htmlspecialchars($row['detail_notes']) . "</textarea></td>
    </tr>";
}

echo $output;

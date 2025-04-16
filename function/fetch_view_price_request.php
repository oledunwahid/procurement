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

$query = "SELECT 
            prd.*,
            pc.nama_category,
            prd.id,
            prd.nama_barang,
            prd.qty,
            prd.uom,
            prd.unit_price,
            prd.detail_specification,
            prd.category,
            prd.urgency_status
          FROM proc_request_details prd
          LEFT JOIN proc_category pc ON prd.category = pc.id_category
          WHERE prd.id_proc_ch = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $id_proc_ch);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$output = '';

while ($row = mysqli_fetch_assoc($result)) {
    $totalHarga = $row['unit_price'] * $row['qty'];

    // Get category name
    $categoryQuery = "SELECT nama_category FROM proc_category WHERE id_category = ?";
    $stmtCategory = mysqli_prepare($koneksi, $categoryQuery);
    mysqli_stmt_bind_param($stmtCategory, "s", $row['category']);
    mysqli_stmt_execute($stmtCategory);
    $categoryResult = mysqli_stmt_get_result($stmtCategory);
    $categoryName = mysqli_fetch_assoc($categoryResult)['nama_category'];
    mysqli_stmt_close($stmtCategory);

    // Format urgency status badge
    $urgencyBadgeClass = $row['urgency_status'] === 'urgent' ? 'badge bg-danger' : 'badge bg-primary';
    $urgencyLabel = ucfirst($row['urgency_status'] ?? 'normal');

    // Format price and total price with tooltips for better visibility
    $formattedPrice = nominal($row['unit_price']);
    $formattedTotal = nominal($totalHarga);

    $output .= "<tr>
        <td style='display:none;'>
            <input type='text' name='id_proc_ch[]' class='form-control' value='" . htmlspecialchars($row['id_proc_ch']) . "' readonly />
        </td>
        <td>
            <textarea name='nama_barang[]' class='form-control form-field-adjustable desc-input' rows='auto' style='resize:none;overflow:hidden;min-height:60px;' readonly>" . htmlspecialchars($row['nama_barang']) . "</textarea>
        </td>
        <td>
            <textarea name='detail_specification[]' class='form-control form-field-adjustable desc-input' rows='auto' style='resize:none;overflow:hidden;min-height:60px;' readonly>" . htmlspecialchars($row['detail_specification']) . "</textarea>
        </td>
        <td>
            <input type='number' name='qty[]' class='form-control' value='" . htmlspecialchars($row['qty']) . "' readonly maxlength='5' />
        </td>
        <td>
            <input type='hidden' name='category[]' value='" . htmlspecialchars($row['category']) . "' />
            <span class='form-control-plaintext'>" . htmlspecialchars($categoryName) . "</span>
        </td>
        <td>
            <input type='hidden' name='uom[]' value='" . htmlspecialchars($row['uom']) . "' />
            <span class='form-control-plaintext'>" . htmlspecialchars($row['uom']) . "</span>
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
            <input type='hidden' name='urgency_status[]' value='" . htmlspecialchars($row['urgency_status']) . "' />
            <span class='" . $urgencyBadgeClass . "'>" . $urgencyLabel . "</span>
        </td>
    </tr>";
}

echo $output;
mysqli_close($koneksi);

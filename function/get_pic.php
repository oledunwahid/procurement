<?php
include '../koneksi.php';

if (isset($_GET['category'])) {
    $category = $_GET['category'];

    $query = "SELECT u.idnik, u.nama
              FROM proc_admin_category pac
              INNER JOIN user u ON pac.idnik = u.idnik
              WHERE pac.id_category = '$category'";

    $result = mysqli_query($koneksi, $query);

    $options = '<option value="">Select PIC</option>';
    echo "Mulai membangun opsi PIC\n";
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= '<option value="' . $row['idnik'] . '">' . $row['nama'] . '</option>';
        echo "Menambahkan opsi PIC: " . $row['nama'] . "\n";
    }
    echo "Selesai membangun opsi PIC\n";

    echo $options;
}

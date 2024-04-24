<?php
include '../koneksi.php'; // Sesuaikan dengan path koneksi database Anda

$dataReceived = print_r($_POST, true); // Mengubah data array ke string
file_put_contents('debug_data.txt', $dataReceived);


if(isset($_POST['id_mr'])) {
    $id_mr = $_POST['id_mr'];
    $deskripsi = $_POST['deskripsi'];
    $ppn = $_POST['ppn'];
	$tgl_need_mr = $_POST['tgl_need_mr'];
	$tgl_mr = $_POST['tgl_mr'];
	$pt_mr = $_POST['pt_mr'];
	$lokasi_mr = $_POST['lokasi_mr'];
    $total_price_mr = $_POST['total_price_mr'];
	$total_price_mr = str_replace('.', '', $total_price_mr);
	$type_mr = $_POST['type_mr'];
	$priority_mr = $_POST['priority_mr'];
	$dekripsi_priority_mr = $_POST['dekripsi_priority_mr'];

    $query = "UPDATE proc_purchase_requests SET  status_mr = 'Procesed',  tgl_need_mr ='$tgl_need_mr' ,tgl_mr ='$tgl_mr' ,pt_mr = '$pt_mr', lokasi_mr = '$lokasi_mr', deskripsi = '$deskripsi', ppn = '$ppn', total_price_mr = '$total_price_mr', type_mr ='$type_mr', priority_mr = '$priority_mr',  dekripsi_priority_mr = '$dekripsi_priority_mr' WHERE id_mr = '$id_mr'";
    if(mysqli_query($koneksi, $query)) {
        echo "Data berhasil diupdate";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
} else {
    echo "ID Material Request tidak ditemukan";
}
?>

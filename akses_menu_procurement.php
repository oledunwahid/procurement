<?php
$page = isset($_GET['page']) ? $_GET['page'] : '';

$sql7 = mysqli_query($koneksi, "SELECT * FROM access_level WHERE idnik = $niklogin");
$row7 = mysqli_fetch_assoc($sql7);


if (isset($row7['admin']) && ($row7['admin'] == '1')) {
	switch ($page) {
		case 'Dashboard':
			include "home-facility.php";
			break;
		case 'PurchaseRequests':
			include "purchase-requests.php";
			break;
		default:
			include "pages-404.php";
			break;
	}
} else {
	switch ($page) {
		case 'Dashboard':
			include "home-facility.php";
			break;
		case 'PurchaseRequests':
			include "purchase-requests.php";
			break;
		default:
			include "pages-404.php";
			break;
	}
}

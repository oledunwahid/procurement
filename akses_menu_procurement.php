<?php
$pageFiles = [
	'Dashboard' => 'home-facility.php',
	'PurchaseRequests' => 'purchase-requests.php',
	'DetailPurchase' => 'detail-purchase-request.php',
	'UserDetailPurchase' => 'view-detail-purchase.php',
	'AccessAdministrator' => (in_array('1', $role) || in_array('5', $role)) ?  'akses_admin.php' : 'pages-404.php',
	'MenuAdministrator' => (in_array('1', $role) || in_array('5', $role)) ?  'menu_admin.php' : 'pages-404.php',
	'PrintPriceReq' => 'print-price-request.php',
	'ViewPriceReq' => 'view-price-request.php',
	'CategoryManagement' => (in_array('1', $role) || in_array('5', $role)) ? 'category-management.php' : 'pages-404.php',
	'AdminLog' => (in_array('1', $role) || in_array('5', $role)) ?  'admin-log.php' : 'pages-404.php',
];


if (isset($_SESSION['idnik'])) {
	if (array_key_exists($page, $pageFiles)) {
		include $pageFiles[$page];
	} else {
		include 'pages-404.php';
	}
} else {
	include 'login.php';
}

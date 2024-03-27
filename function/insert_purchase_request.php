<?php
session_start();
require_once("../koneksi.php");

function redirectToFacilitiesPage($message, $icon)
{
    $_SESSION["Messages"] = $message;
    $_SESSION["Icon"] = $icon;
    header('Location: ../index.php?page=PurchaseRequests');
    exit();
}

function generateRequestId()
{
    $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $timestamp = $currentDateTime->format('ymdHis');
    return "PR" . $timestamp . str_pad(1, '0', STR_PAD_LEFT);
}

if (isset($_POST['add_purchase_request'])) {
    // Get form data
    $id_request = generateRequestId();
    $title = $_POST["title"];
    $created_request = $_POST["created_request"];
    $nik_request = $_POST["nik_request"];
    $category = $_POST["category"];
    $urgencies = $_POST["urgencies"];
    $lampiran = $_FILES["lampiran"]["name"];

    // Insert data into proc_purchase_requests
    $sql1 = "INSERT INTO proc_request_details (id_request, title, created_request, nik_request, category, urgencies, lampiran) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt1 = $koneksi->prepare($sql1);
    $stmt1->bind_param("sssssss", $id_request, $title, $created_request, $nik_request, $category, $urgencies, $lampiran);

    // Insert data into proc_request_details
    $nama_barang = $_POST["nama_barang"];
    $qty = $_POST["qty"];
    $uom = $_POST["uom"];
    $remarks = $_POST["remarks"];
    $unit_price = $_POST["unit_price"];

    $sql2 = "INSERT INTO proc_purchase_requests(id_request, nama_barang, qty, uom, remarks, unit_price) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt2 = $koneksi->prepare($sql2);
    $stmt2->bind_param("ssssss", $id_request, $nama_barang, $qty, $uom, $remarks, $unit_price);

    // Perform double insertion
    $success = $stmt1->execute() && $stmt2->execute();

    if ($success) {
        // If the insertion is successful, redirect with success message
        redirectToFacilitiesPage("Purchase request added successfully!", "success");
    } else {
        // If there's an error, redirect with an error message
        redirectToFacilitiesPage("Error adding purchase request. Please try again.", "error");
    }

    // Close the statements
    $stmt1->close();
    $stmt2->close();
}

// Close the database connection
$koneksi->close();

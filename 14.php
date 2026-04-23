<?php
// Assuming invoices are stored in a specific directory
$invoice_dir = "invoices/";

// Getting the filename from the URL parameter
$filename = $_GET['file'];

// Constructing the path following the readfile($_GET['path']) logic
$path = $invoice_dir . $filename;

// Setting headers to force a PDF download
header('Content-Type: application/json'); // Defaulting to JSON for errors
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Reading the file directly to the output buffer
readfile($path);
?>
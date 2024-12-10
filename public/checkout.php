<?php
session_start();
require_once "../utils/db_connection.php";
require_once "../classes/Cart.php";
require_once "../utils/fpdf/fpdf.php";

$conn = getDBConnection();
$cart = new Cart($conn);

$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    $_SESSION['error_message'] = "Please log in to proceed to checkout.";
    header('Location: login.php');
    exit();
}

// Fetch cart items
$cartItems = $cart->getCartItems($userId);

if (empty($cartItems)) {
    $_SESSION['error_message'] = "Your cart is empty.";
    header('Location: cart.php');
    exit();
}

// Invoice details
$invoiceNumber = rand(1000, 9999);
$issueDate = date('Y-m-d');
$dueDate = date('Y-m-d', strtotime('+7 days'));
$customerName = $_SESSION['username'] ?? 'Guest User';
$customerAddress = 'N/A'; // Replace with actual customer address if available

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$discount = 50; 
$taxRate = 5;
$tax = ($subtotal - $discount) * ($taxRate / 100);
$total = $subtotal - $discount + $tax;

// Generate PDF Invoice
$pdf = new FPDF();
$pdf->AddPage();

// Header Section (Blue background with company info)
$pdf->SetFillColor(23, 56, 106); // Navy Blue
$pdf->Rect(0, 0, 210, 50, 'F'); // Full-width rectangle
$pdf->SetTextColor(255, 255, 255); // White text
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetXY(10, 10);
$pdf->Cell(0, 10, 'Invoice', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(150, 10);
$pdf->Cell(0, 5, 'Business Name', 0, 1, 'R');
$pdf->SetX(150);
$pdf->Cell(0, 5, 'Street Address Line 01', 0, 1, 'R');
$pdf->SetX(150);
$pdf->Cell(0, 5, 'Street Address Line 02', 0, 1, 'R');
$pdf->SetX(150);
$pdf->Cell(0, 5, '+1 (999) 999-9999', 0, 1, 'R');
$pdf->SetX(150);
$pdf->Cell(0, 5, 'Email Address', 0, 1, 'R');
$pdf->SetX(150);
$pdf->Cell(0, 5, 'Website', 0, 1, 'R');

// Invoice and Billing Info
$pdf->SetY(60);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(100, 7, "INVOICE DETAILS:", 0, 0);
$pdf->Cell(90, 7, "BILL TO:", 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 7, "Invoice #: $invoiceNumber", 0, 0);
$pdf->Cell(90, 7, $customerName, 0, 1);
$pdf->Cell(100, 7, "Date of Issue: $issueDate", 0, 0);
$pdf->Cell(90, 7, $customerAddress, 0, 1);
$pdf->Cell(100, 7, "Due Date: $dueDate", 0, 1);

// Table Header
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230); // Light Gray
$pdf->Cell(80, 7, "ITEM/SERVICE", 1, 0, 'C', true);
$pdf->Cell(50, 7, "DESCRIPTION", 1, 0, 'C', true);
$pdf->Cell(20, 7, "QTY", 1, 0, 'C', true);
$pdf->Cell(20, 7, "RATE", 1, 0, 'C', true);
$pdf->Cell(20, 7, "AMOUNT", 1, 1, 'C', true);

// Table Content
$pdf->SetFont('Arial', '', 10);
foreach ($cartItems as $item) {
    $amount = $item['price'] * $item['quantity'];
    $pdf->Cell(80, 7, $item['name'], 1);
    $pdf->Cell(50, 7, 'Laptop', 1); // Example description, replace with actual description
    $pdf->Cell(20, 7, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(20, 7, "$" . $item['price'], 1, 0, 'C');
    $pdf->Cell(20, 7, "$" . $amount, 1, 1, 'C');
}

// Summary Section
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(150, 7, "Subtotal:", 0, 0, 'R');
$pdf->Cell(20, 7, "$" . $subtotal, 0, 1, 'R');
$pdf->Cell(150, 7, "Discount:", 0, 0, 'R');
$pdf->Cell(20, 7, "- $" . $discount, 0, 1, 'R');
$pdf->Cell(150, 7, "Tax Rate:", 0, 0, 'R');
$pdf->Cell(20, 7, $taxRate . "%", 0, 1, 'R');
$pdf->Cell(150, 7, "Tax:", 0, 0, 'R');
$pdf->Cell(20, 7, "$" . $tax, 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(150, 7, "TOTAL:", 0, 0, 'R');
$pdf->Cell(20, 7, "$" . $total, 0, 1, 'R');

// Footer Terms Section
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, "Terms and Conditions: Please pay by the due date.", 0, 1, 'C');

// Save the file in 'invoices' directory
$pdfFileName = "Invoice_" . time() . ".pdf";
$pdf->Output('F', "../invoices/" . $pdfFileName);

// Clear Cart
$cart->clearCart($userId);

// Redirect to success page with invoice link
header("Location: success.php?invoice=../invoices/$pdfFileName");
exit();
?>

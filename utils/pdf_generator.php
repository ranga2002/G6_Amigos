<?php
require_once "../vendor/fpdf/fpdf.php";

function generatePDFInvoice($orderDetails, $totalPrice) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);

    // Title
    $pdf->Cell(0, 10, 'Order Invoice', 0, 1, 'C');
    $pdf->Ln(10);

    // Table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(60, 10, 'Product', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Price (per unit)', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Total', 1, 1, 'C');

    // Table rows
    $pdf->SetFont('Arial', '', 12);
    foreach ($orderDetails as $item) {
        $pdf->Cell(60, 10, $item['name'], 1);
        $pdf->Cell(30, 10, $item['quantity'], 1, 0, 'C');
        $pdf->Cell(40, 10, number_format($item['price_per_unit'], 2), 1, 0, 'C');
        $pdf->Cell(40, 10, number_format($item['quantity'] * $item['price_per_unit'], 2), 1, 1, 'C');
    }

    // Total row
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(130, 10, 'Total Amount', 1, 0, 'R');
    $pdf->Cell(40, 10, number_format($totalPrice, 2), 1, 1, 'C');

    // Save or Output
    $filename = 'invoice.pdf';
    $pdf->Output('D', $filename); // Download the file
}
?>

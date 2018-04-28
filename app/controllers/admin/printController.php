<?php
// Include the main TCPDF library (search for installation path).
require_once('../../../assets/admin/TCPDF-master/tcpdf.php');
include '../../services/connection.php';
include '../../services/printService.php';
include '../../services/orderService.php';

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$printService = new PrintService($pdf);
$orderService = new OrderService($conn);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 006');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(null, null, 'Danh sách đơn hàng', 'Công ty TNHH Elite Sport');

// set header and footer fonts
$pdf->setHeaderFont(Array('freeserif', '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// ---------------------------------------------------------

// set font
$pdf->SetFont('freeserif', '', 10);

$function = $_POST["function"];
$redirectUrl = $_POST["p"];

switch ($function) {
    case 'orders':
        $condition = array();
        parse_str($redirectUrl, $condition);

        $orders = $orderService->search($condition);
        $printService->orders($orders);
        break;
}

?>
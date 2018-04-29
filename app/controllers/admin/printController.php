<?php
// Include the main TCPDF library (search for installation path).
require_once('../../../assets/admin/TCPDF-master/tcpdf.php');
include '../../services/connection.php';
include '../../services/printService.php';
include '../../services/productService.php';
include '../../services/shippingService.php';
include '../../services/imageService.php';
include '../../viewModels/cartViewModel.php';
include '../../viewModels/orderInfoViewModel.php';
include '../../services/orderService.php';

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$printService = new PrintService($pdf);
$orderService = new OrderService($conn);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Quan Tran');
$pdf->SetTitle('Danh sách đơn hàng');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(null, null, 'Danh sách đơn hàng', 'Công ty cổ phần Elite Sport');

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

switch ($function) {
    case 'orders':
        $redirectUrl = $_POST["p"];
        $condition = array();
        parse_str($redirectUrl, $condition);

        $orders = $orderService->search($condition);
        $printService->orders($orders);
        break;
    case 'invoices':
        $invoices = explode(",", $_POST["orders"]);

        $printedOrders = array();
        foreach ($invoices as $invoice) {
            $printOrder = array(
                "basicInfo" => $orderService->get($invoice),
                "productInfo" => $orderService->getWithProduct($invoice)
            );

            array_push($printedOrders, $printOrder);
        }

        $printService->invoices($printedOrders);
        break;
    case 'invoicesAll':
        $redirectUrl = $_POST["p"];
        $condition = array();
        parse_str($redirectUrl, $condition);

        $orders = $orderService->search($condition);
        $printedOrders = array();
        foreach ($orders as $order) {
            $printOrder = array(
                "basicInfo" => $order,
                "productInfo" => $orderService->getWithProduct($order["code"])
            );

            array_push($printedOrders, $printOrder);
        }

        $printService->invoices($printedOrders);
        break;
}

?>
<?php

class PrintService
{
    public $pdf;

    public function __construct($pdf)
    {
        $this->pdf = $pdf;
    }

    public function orders($orders)
    {
        $this->pdf->AddPage();

        $html = '
            <style>
               table.orders {
               border-collapse: collapse;
               }
               table.orders, table.orders td, table.orders th {
                border: 1px solid #000;
               }
               table, table td, table th {
                padding: 5px;
               }
               table.orders td.last {
                    width: 20%;
               }
              
            </style>
            <table class="orders">
             <thead>
             <tr style="background-color: #d9d9d9">
             <th style="width: 7%">STT</th>
             <th style="width: 18%">Mã đơn hàng</th>
             <th style="width: 20%">Họ tên</th>
             <th style="width: 26%">Địa chỉ</th>
             <th style="width: 15%">Số điện thoại</th>
             <th style="width: 13%">Ký tên</th>
            </tr>
            </thead>
            <tbody>';

        $index = 0;
        foreach ($orders as $order) {
            $html .= '
            <tr>
                <td style="width: 7%">' . (++$index) . '</td>
                <td style="width: 18%">' . $order["code"] . '</td>
                <td style="width: 20%">' . $order["customerName"] . '</td>
                <td style="width: 26%">' . $order["customerAddress"] . '</td>
                <td style="width: 15%">' . $order["customerMobile"] . '</td>
                <td style="width: 13%"></td>
            </tr>
            ';
        }
        $html .= '</tbody>
        </table>
      ';

        $this->pdf->writeHTML($html, true, false, true, false, '');

        $this->pdf->lastPage();

        $this->pdf->Output('I');
    }

    public function invoices($invoices)
    {
        // create columns content
        $mutualPart = '
        <style>
        table, table td, table th {
            padding: 3px;
            text-align: left;
        }
        table.customerSummary td.last {
        text-align: right;
        }
        </style>
        ';

        $this->pdf->AddPage();
        foreach ($invoices as $invoice) {
            $html = $mutualPart . '';
            $html .= '
            <table>
            <tr>
            <td><h2 style="text-align: center; text-transform: uppercase">Hóa đơn thanh toán</h2></td>
            </tr>
            <tr><td><p style="text-align: center;">Ngày '.date('d').' tháng '.date('m').', năm '.date('Y').'</p></td></tr>
            </table>
            <table class="customerSummary">
                <tr>
                    <td>Mã hóa đơn <span style="font-style: italic">(No.)</span></td>
                    <td class="last">' . $invoice["basicInfo"]["code"] . '</td>    
                </tr>
                <tr>
                    <td>Họ tên khách hàng <span style="font-style: italic">(Customer\'s Name)</span></td>
                    <td class="last">' . $invoice["basicInfo"]["customerName"] . '</td>
                </tr>
                <tr>
                    <td>Địa chỉ <span style="font-style: italic">(Customer\'s Address)</span></td>
                    <td class="last">' . $invoice["basicInfo"]["customerAddress"] . '</td>
                </tr>
                <tr>
                    <td>Số điện thoại <span style="font-style: italic">(Customer\'s Mobile)</span></td>
                    <td class="last">' . $invoice["basicInfo"]["customerMobile"] . '</td>
                </tr>
            </table>
            ';

            $html .= '
            <table class="detail">
           <thead>
             <tr style="background-color: #d9d9d9">
                 <th style="width: 10%">STT</th>
                 <th style="width: 52%">Sản phẩm</th>
                 <th style="width: 10%">SL</th>
                 <th style="width: 28%">Đơn giá (VNĐ)</th>
            </tr>
            </thead>
            <tbody>';

            for ($k = 0; $k < count($invoice["productInfo"]->items); $k++) {
                $html .= '
                <tr>
                    <td style="width: 10%">' . ($k + 1) . '</td>
                    <td style="width: 52%">' . $invoice["productInfo"]->items[$k]->name . '</td>
                    <td style="width: 10%">' . $invoice["productInfo"]->items[$k]->quantity . '</td>
                    <td style="width: 28%">' . number_format($invoice["productInfo"]->items[$k]->price) . '</td>
                </tr>
               ';
            }

            $html .= '</tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="border-top: 1px dashed #d9d9d9;">Tổng tiền sán phẩm (VNĐ)</th>
                    <th style="border-top: 1px dashed #d9d9d9;">350,000,000</th>
                </tr>
                <tr>
                    <th colspan="3">Phí vận chuyển (VNĐ)</th>
                    <th>50,000</th>
                </tr>
                <tr>
                    <th colspan="3">Tổng tiền hóa đơn (VNĐ)</th>
                    <th>350,050,000</th>
                </tr>
            </tfoot>
            </table>
            <br pagebreak="true"/>.
            ';

            $this->pdf->SetFillColor(255, 255, 200);

            $this->pdf->SetTextColor(0, 63, 127);

            $this->pdf->writeHTML($html, true, false, true, false, '');
        }

        $this->pdf->Output('I');
    }
}

?>
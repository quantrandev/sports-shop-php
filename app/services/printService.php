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
}

?>
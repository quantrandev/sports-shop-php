<?php

class OrderInfoViewModel
{
    public $code;
    public $items;
    public $subtotal;
    public $shippingMethod;
    public $shippingStatus;
    public $total;

    public function __construct($args = array())
    {
        $this->code = empty($args["code"]) ? null : $args["code"];
        $this->items = empty($args["items"]) ? null : $args["items"];
        $this->subtotal = empty($args["subtotal"]) ? null : $args["subtotal"];
        $this->shippingMethod = empty($args["shippingMethod"]) ? null : $args["shippingMethod"];
        $this->shippingStatus = empty($args["shippingStatus"]) ? null : $args["shippingStatus"];
        $this->total = empty($args["total"]) ? null : $args["total"];
    }

    public function getSubtotal()
    {
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += intval($item->quantity) * intval($item->price);
        }

        return $this->subtotal;
    }
}

?>
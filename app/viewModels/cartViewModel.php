<?php

class CartViewModel
{
    public $items;
    public $quantity;
    public $subtotal;
    public $shippingMethod;
    public $payment;

    public function __construct()
    {
        $this->items = array();
    }

    public function add($cartItem)
    {
        //check duplicate
        foreach ($this->items as $item) {
            if ($cartItem->id == $item->id)
                return null;
        }

        array_push($this->items, $cartItem);
        return $cartItem;
    }

    public function update($id, $quantity)
    {
        foreach ($this->items as $item) {
            if ($item->id == $id) {
                $item->quantity = $quantity;
                return true;
            }
        }

        return false;
    }

    public function delete($id)
    {
        $deletedIndex = -1;
        for ($i = 0; $i < count($this->items); $i++) {
            if ($this->items[$i]->id == $id) {
                $deletedIndex = $i;
                break;
            }
        }

        if ($deletedIndex != -1) {
            $deletedItem = $this->items[$deletedIndex];
            array_splice($this->items, $deletedIndex, 1);

            return $deletedItem;
        }

        return null;
    }

    public function find($id)
    {
        foreach ($this->items as $item) {
            if ($item->id == $id)
                return $item;
        }

        return null;
    }

    public function getSubtotal()
    {
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += intval($item->quantity) * intval($item->price);
        }

        return $this->subtotal;
    }

    public function getQuantity()
    {
        $this->quantity = 0;
        foreach ($this->items as $item) {
            $this->quantity += intval($item->quantity);
        }

        return $this->quantity;
    }
}

class CartItemViewModel
{
    public $id;
    public $image;
    public $name;
    public $price;
    public $quantity;

    public function __construct($id, $image, $name, $price, $quantity)
    {
        $this->id = $id;
        $this->image = $image;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getTotal()
    {
        return $this->quantity * $this->price;
    }
}

?>
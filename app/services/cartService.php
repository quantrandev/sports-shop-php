<?php
include '../viewModels/cartViewModel.php';
include 'productService.php';
include 'imageService.php';
include 'shippingService.php';
include 'paymentService.php';

class CartService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function add($data)
    {
        $error = false;
        $message = null;

        $productService = new ProductService($this->db);
        $product = $productService->get($data["id"]);
        $quantity = $data["quantity"];

        //cart
        $cart = empty($_SESSION["cart"]) ? null : unserialize($_SESSION["cart"]);

        $cartItem = new CartItemViewModel(
            $product->id,
            $product->images[0]["source"],
            $product->name,
            ($product->isSale() ? $product->getSalePrice() : $product->basicPrice),
            (intval($quantity) < 0) ? 1 : intval($quantity)
        );

        if (empty($cart)) {
            $cart = new CartViewModel();
            $cart->add($cartItem);
            $cart->shippingMethod = (new ShippingMethodService($this->db))->all()[0];
            $cart->payment = (new PaymentService($this->db))->all()[0];
            $message = "Đã thêm thành công vào giỏ hàng";
        } else {
            $result = $cart->add($cartItem);

            if (empty($result)) {
                $message = "Sản phẩm đã có trong giỏ hàng";
                $error = true;
            } else {
                $message = "Đã thêm thành công vào giỏ hàng";
            }
        }

        $totalQuantity = $cart->getQuantity();
        $total = $cart->getSubtotal();

        $_SESSION["cart"] = serialize($cart);

        return array(
            "error" => $error,
            "message" => $message,
            "quantity" => $totalQuantity,
            "total" => $total
        );
    }

    public function update($id, $data)
    {
        $cart = unserialize($_SESSION["cart"]);
        $quantity = $data["quantity"];

        $error = !$cart->update($id, $quantity);
        if ($error || intval($quantity) <= 0)
            return array(
                "error" => true
            );

        $subtotal = $cart->getSubtotal();
        $totalQuantity = $cart->getQuantity();
        $_SESSION["cart"] = serialize($cart);

        return array(
            "error" => $error,
            "subtotal" => $subtotal,
            "total" => $cart->getSubtotal() + $cart->shippingMethod["cost"],
            "quantity" => $totalQuantity,
            "singleTotal" => $cart->find($id)->getTotal()
        );
    }

    public function delete($id)
    {
        $cart = unserialize($_SESSION["cart"]);

        $deletedItem = $cart->delete($id);
        $error = empty($deletedItem);

        if (count($cart->items) == 0) {
            unset($_SESSION["cart"]);
            return array(
                "error" => $error,
                "subtotal" => 0,
                "total" => 0,
                "quantity" => 0
            );
        }

        $subtotal = $cart->getSubtotal();
        $totalQuantity = $cart->getQuantity();
        $_SESSION["cart"] = serialize($cart);

        return array(
            "error" => $error,
            "subtotal" => $subtotal,
            "total" => $cart->getSubtotal() + $cart->shippingMethod["cost"],
            "quantity" => $totalQuantity
        );
    }

    public function setShippingMethod($data)
    {
        $shippingMethod = (new ShippingMethodService($this->db))->get($data["shippingMethodId"]);
        //cart
        $cart = unserialize($_SESSION["cart"]);

        $cart->shippingMethod = $shippingMethod;

        $subtotal = $cart->getSubtotal();
        $totalQuantity = $cart->getQuantity();
        $_SESSION["cart"] = serialize($cart);

        return array(
            "shippingMethod" => $shippingMethod,
            "subtotal" => $subtotal,
            "total" => $subtotal + $cart->shippingMethod["cost"],
            "quantity" => $totalQuantity
        );
    }

    public function like($productId)
    {
        $selectQuery = "select * from products where id = " . $productId;
        $stmt = $this->db->prepare($selectQuery);
        $stmt->execute();

        $product = $stmt->fetch();

        $newLikesCount = intval($product["likes"]) + 1;

        $updateQuery = "update products set likes = " . $newLikesCount . " where id = " . $productId;
        $this->db->exec($updateQuery);

        return $newLikesCount;
    }

    public function view($productId)
    {
        $selectQuery = "select * from products where id = " . $productId;
        $stmt = $this->db->prepare($selectQuery);
        $stmt->execute();

        $product = $stmt->fetch();

        $newViewsCount = intval($product["views"]) + 1;

        $updateQuery = "update products set views = " . $newViewsCount . " where id = " . $productId;
        $this->db->exec($updateQuery);

        return $newViewsCount;
    }
}

?>
<?php

class ProductViewModel
{
    public $id;
    public $name;
    public $description;
    public $images;
    public $currentPrice;
    public $oldPrice;
    public $quantity;
    public $createdDate;
    public $categoryId;

    public function __construct($args)
    {
        $this->id = $args["id"];
        $this->name = $args["name"];
        $this->description = $args["description"];
        $this->currentPrice = $args["currentPrice"];
        $this->oldPrice = $args["oldPrice"];
        $this->quantity = $args["quantity"];
        $this->createdDate = $args["createdDate"];
        $this->categoryId = $args["categoryId"];
    }

    public function setImages($image)
    {
        $this->images = $image;
    }
}

class ProductService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($id)
    {
        $stmt = $this->db
            ->prepare("select * from products where id = " . $id);
        $stmt->execute();

        $product = new ProductViewModel($stmt->fetch());
        $image = new ImageService($this->db);
        $product->setImages($image->getMany($product->id));

        return $product;
    }

    public function sales($offset, $take)
    {
        $stmt = $this->db
            ->prepare("select * from products "
                . " where oldPrice != 0 limit "
                . $take
                . " offset "
                . $offset);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $product = new ProductViewModel($row);
            $image = new ImageService($this->db);
            $product->setImages($image->getMany($row["id"]));
            array_push($result, $product);
        }

        return $result;
    }

    public function newComings($offset, $take)
    {
        $stmt = $this->db
            ->prepare("select * from products "
                . "order by createdDate desc "
                . "limit "
                . $take
                . " offset "
                . $offset);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $product = new ProductViewModel($row);
            $image = new ImageService($this->db);
            $product->setImages($image->getMany($row["id"]));
            array_push($result, $product);
        }

        return $result;
    }
}
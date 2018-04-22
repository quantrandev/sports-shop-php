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

    public function search($page, $pageSize, $condition)
    {
        $query = "";

        $nameQuery = $this->buildNameQuery($condition);
        $categoriesQuery = $this->buildCategoryQuery($condition);
        $priceQuery = $this->buildPriceQuery($condition);

        $condition = "";
        if (empty($nameQuery) && empty($categoriesQuery) && empty($priceQuery))
            $condition .= '';
        else {
            $condition .= " where "
                . (empty($nameQuery) ? 'true' : $nameQuery)
                . " and "
                . (empty($categoriesQuery) ? 'true' : $categoriesQuery)
                . " and "
                . (empty($priceQuery) ? 'true' : $priceQuery);
        }

        $query .= "select * from products" . $condition
            . " order by currentPrice desc"
            . " limit " . $pageSize . " offset " . (($page - 1) * $pageSize);

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $products = array();
        while ($row = $stmt->fetch()) {
            $product = new ProductViewModel($row);
            $image = new ImageService($this->db);
            $product->setImages($image->getMany($row["id"]));
            array_push($products, $product);
        }

        //get count
        $query = "select count(*) as count from products" . $condition;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $countResult = $stmt->fetch();

        return array(
            "products" => $products,
            "count" => intval($countResult["count"])
        );
    }

    public function add($data)
    {
        return $data;
    }

    //helpers
    public function buildNameQuery($condition)
    {
        if (empty($condition["name"]))
            return '';

        //name
        $nameQuery = "";
        $nameParts = explode(" ", $condition["name"]);
        if (count($nameParts) != 0) {
            foreach ($nameParts as $part) {
                $nameQuery .= "name like N'%" . $part . "%' and ";
            }
            $nameQuery = substr($nameQuery, 0, strlen($nameQuery) - 4);
        }

        return $nameQuery;
    }

    public function buildCategoryQuery($condition)
    {
        if (empty($condition["category"]))
            return '';

        $categoriesToQuery = array();
        $categoryService = new CategoryService($this->db);
        $categories = $categoryService::menus($categoryService->all());
        if (!is_array($condition["category"])) {
            $isParent = false;
            foreach ($categories as $category) {
                if ($category->id == $condition["category"]) {
                    if (count($category->children) == 0)
                        array_push($categoriesToQuery, $category->id);
                    else
                        $categoriesToQuery = array_merge($categoriesToQuery,
                            array_map(function ($value) {
                                return $value->id;
                            }, $category->children));
                    $isParent = true;
                    break;
                }
            }
            if (!$isParent)
                array_push($categoriesToQuery, $condition["category"]);
        } else {
            foreach ($categories as $parent) {
                if (in_array($parent->id, $condition["category"])) {
                    if (count($parent->children) == 0)
                        array_push($categoriesToQuery, $parent->id);
                    else
                        $categoriesToQuery = array_merge($categoriesToQuery,
                            array_map(function ($value) {
                                return $value->id;
                            }, $parent->children));
                }
            }
        }

        $categoryQuery = "categoryId in (";
        foreach ($categoriesToQuery as $category) {
            $categoryQuery .= $category . ",";
        }
        $categoryQuery = substr($categoryQuery, 0, strlen($categoryQuery) - 1) . ")";
        return $categoryQuery;
    }

    public function buildPriceQuery($condition)
    {
        if (empty($condition["price-from"]) && empty($condition["price-to"]))
            return '';

        $priceQuery = "";
        if (empty($condition["price-from"])) {
            $priceQuery .= "currentPrice < " . $condition["price-to"];
            return $priceQuery;
        }
        if (empty($condition["price-to"])) {
            $priceQuery .= "currentPrice > " . $condition["price-from"];
            return $priceQuery;
        }

        $priceQuery .= "currentPrice < " . $condition["price-to"] . " and currentPrice > " . $condition["price-from"];
        return $priceQuery;
    }
}
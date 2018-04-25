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
    public $likes;
    public $views;

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
        $this->likes = $args["likes"];
        $this->views = $args["views"];
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

    public function getImages($id)
    {
        $image = new ImageService($this->db);
        $result = $image->getMany($id);
        return $result;
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
        $name = $data["name"];
        $quantity = $data["quantity"];
        if (empty($name) || empty($quantity) || empty($data["oldPrice"]))
            return false;
        $oldPrice = empty($data["currentPrice"]) ? 0 : $data["oldPrice"];
        $currentPrice = empty($data["currentPrice"]) ? $data["oldPrice"] : $data["currentPrice"];
        $categoryId = $data["categoryId"];
        $description = $data["description"];

        $query = $this->buildInsertQuery(array(
            "name" => "N'" . $name . "'",
            "oldPrice" => $oldPrice,
            "quantity" => $quantity,
            "currentPrice" => $currentPrice,
            "categoryId" => $categoryId,
            "description" => "'" . $description . "'"
        ));

        $result = $this->db->exec($query);
        $last_id = $this->db->lastInsertId();

        $error = empty($result) ? true : false;
        //process images
        $images = $data["images"];
        if (empty($images))
            return $error;

        $images = explode(",", $images);
        $queries = $this->buildImagesQuery($last_id, $images);

        foreach ($queries as $query) {
            $result = $this->db->exec($query);
            $error = empty($result) ? true : $error;
        }

        return $error;
    }

    public function updateImages($id, $data)
    {
        $addedImages = $data["addedImages"];
        $deletedImages = empty($data["deletedImages"]) ? array() : $data["deletedImages"];

        $error = false;
        if (!empty($addedImages)) {
            $query = "insert into images (source, productId) values ";
            $images = explode(",", $addedImages);
            foreach ($images as $image) {
                $query .= "('/images/products/new/" . $image . "', " . $id . "),";
            }
            $query = substr(trim($query), 0, strlen($query) - 1);
            $result = $this->db->exec($query);
            $error = empty($result) ? true : $error;
        }

        if (!empty($deletedImages)) {
            $query = "delete from images where id in (";
            foreach ($deletedImages as $image) {
                $query .= $image . ",";
            }
            $query = substr(trim($query), 0, strlen($query) - 1) . ")";
            $result = $this->db->exec($query);
            $error = empty($result) ? true : $error;
        }

        return $error;
    }

    public function update($id, $data)
    {
        $columns = $data["data"];

        $sql = "update products set ";

        if (!empty($columns["categoryId"]))
            $sql .= "categoryId = " . $columns["categoryId"] . ",";
        if (!empty($columns["name"]))
            $sql .= "name = N'" . $columns["name"] . "',";
        if (!empty($columns["oldPrice"]))
            $sql .= "oldPrice = " . $columns["oldPrice"] . ",";
        if (!empty($columns["currentPrice"]))
            $sql .= "currentPrice = " . $columns["currentPrice"] . ",";
        if (!empty($columns["description"]))
            $sql .= "description = '" . $columns["description"] . "',";

        $sql = substr(trim($sql), 0, strlen($sql) - 1) . " where id = '" . $id . "'";
        $result = $this->db->exec($sql);
        return empty($result) ? false : true;
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

        $categoriesFromClient = is_array($condition["category"]) ? $condition["category"] : array($condition["category"]);
        //process categoriesToQuery
        foreach ($categories as $category) {
            if (in_array($category->id, $categoriesFromClient)) {
                if (count($category->children) > 0) {
                    $categoriesToQuery = array_merge($categoriesToQuery, array_map(function ($value) {
                        return $value->id;
                    }, $category->children));
                } else {
                    array_push($categoriesToQuery, $category->id);
                }
            } else {
                foreach ($category->children as $child) {
                    if (in_array($child->id, $categoriesFromClient)) {
                        array_push($categoriesToQuery, $child->id);
                    }
                }
            }
        }

        $categoryQuery = "categoryId in (";
        foreach ($categoriesToQuery as $category) {
            $categoryQuery .= $category . ",";
        }
        $categoryQuery = substr(trim($categoryQuery), 0, strlen($categoryQuery) - 1) . ")";
        return $categoryQuery;
    }

    public function buildPriceQuery($condition)
    {
        if (empty($condition["price-from"]) && empty($condition["price-to"]))
            return '';

        $priceQuery = "";
        if (empty($condition["price-from"])) {
            $priceQuery .= "currentPrice <= " . $condition["price-to"];
            return $priceQuery;
        }
        if (empty($condition["price-to"])) {
            $priceQuery .= "currentPrice >= " . $condition["price-from"];
            return $priceQuery;
        }

        $priceQuery .= "currentPrice <= " . $condition["price-to"] . " and currentPrice >= " . $condition["price-from"];
        return $priceQuery;
    }

    //insert helpers
    public function buildInsertQuery($columns)
    {
        $query = "insert into products";
        $insertedColumns = "(";
        $insertedData = "(";
        foreach ($columns as $key => $value) {
            $insertedColumns .= $key . ",";
            $insertedData .= $value . ",";
        }
        $insertedColumns = substr(trim($insertedColumns), 0, strlen($insertedColumns) - 1) . ")";
        $insertedData = substr(trim($insertedData), 0, strlen($insertedData) - 1) . ")";

        $query .= " " . $insertedColumns . " values " . $insertedData;

        return $query;
    }

    public function buildImagesQuery($productId, $images)
    {
        $queries = array();
        foreach ($images as $image) {
            $query = "insert into images (source, productId) values ('/images/products/new/" . $image . "', " . $productId . ")";
            array_push($queries, $query);
        }

        return $queries;
    }
}
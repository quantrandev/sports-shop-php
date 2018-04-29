<?php

class ProductViewModel
{
    public $id;
    public $name;
    public $description;
    public $images;
    public $basicPrice;
    public $saleFrom;
    public $saleTo;
    public $salePercentage;

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
        $this->basicPrice = $args["basicPrice"];
        $this->saleFrom = $args["saleFrom"];
        $this->saleTo = $args["saleTo"];
        $this->salePercentage = $args["salePercentage"];
        $this->isSale = $args["isSale"];
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

    public function isSale()
    {
        if ($this->isSale == 1 && $this->salePercentage != 0 && strtotime($this->saleTo) >= strtotime(date('Y-m-d', time())) && strtotime($this->saleFrom) <= strtotime(date('Y-m-d', time())))
            return true;

        return false;
    }

    public function getSalePrice()
    {
        return intval($this->basicPrice) - ((intval($this->basicPrice) * intval($this->salePercentage)) / 100);
    }
}

class ProductService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function all($condition)
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

        $query .= "select * from products" . $condition;

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $products = array();
        while ($row = $stmt->fetch()) {
            $product = new ProductViewModel($row);
            $image = new ImageService($this->db);
            $product->setImages($image->getMany($row["id"]));
            array_push($products, $product);
        }


        return $products;
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
                . " where isSale = 1 and salePercentage != 0 and saleFrom <= CURDATE() and saleTo >= CURDATE() order by views desc limit "
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
                . "order by id desc "
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

    public function favorites($offset, $take)
    {
        $stmt = $this->db
            ->prepare("select * from products "
                . "order by likes desc "
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

    public function bestSellers($offset, $take)
    {
        $stmt = $this->db
            ->prepare("SELECT *, count(order_details.orderId) as count FROM `products` LEFT JOIN order_details on products.id = order_details.productId GROUP BY products.id ORDER BY count DESC "
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
        $isSaleQuery = $this->buildIsSaleQuery($condition);

        $condition = "";
        if (empty($nameQuery) && empty($categoriesQuery) && empty($priceQuery) && empty($isSaleQuery))
            $condition .= '';
        else {
            $condition .= " where "
                . (empty($nameQuery) ? 'true' : $nameQuery)
                . " and "
                . (empty($categoriesQuery) ? 'true' : $categoriesQuery)
                . " and "
                . (empty($priceQuery) ? 'true' : $priceQuery)
                . " and "
                . (empty($isSaleQuery) ? 'true' : $isSaleQuery);
        }

        $query .= "select * from products" . $condition
            . " order by basicPrice desc"
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
        $basicPrice = $data["basicPrice"];
        if (empty($name) || empty($data["basicPrice"]))
            return true;
        $categoryId = $data["categoryId"];
        $description = $data["description"];

        $query = $this->buildInsertQuery(array(
            "name" => "N'" . $name . "'",
            "basicPrice" => $basicPrice,
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
        if (!empty($columns["basicPrice"]))
            $sql .= "basicPrice = " . $columns["basicPrice"] . ",";
        if (!empty($columns["description"]))
            $sql .= "description = '" . $columns["description"] . "',";

        $sql = substr(trim($sql), 0, strlen($sql) - 1) . " where id = '" . $id . "'";
        $result = $this->db->exec($sql);
        return empty($result) ? false : true;
    }

    public function updateSale($data)
    {
        $postRange = $data["range"];
        $dateRangeArr = explode("-", $postRange);
        $dateFrom = getdate(strtotime(trim($dateRangeArr[0])));
        $dateTo = getdate(strtotime(trim($dateRangeArr[1])));
        $range = array(
            "from" => $dateFrom["year"] . "-" . $dateFrom["mon"] . "-" . $dateFrom["mday"],
            "to" => $dateTo["year"] . "-" . $dateTo["mon"] . "-" . $dateTo["mday"],
        );

        $salePercentage = $data["salePercentage"];
        $products = $data["products"];

        $query = $this->buildSaleQuery(array(
            "percentage" => $salePercentage,
            "products" => $products,
            "range" => $range
        ));

        $affectedRows = $this->db->exec($query);

        return empty($affectedRows) ? false : true;
    }

    public function unsale($data)
    {
        $query = $this->buildUnsaleQuery($data["products"]);

        $affectedRows = $this->db->exec($query);

        return empty($affectedRows) ? false : true;
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
            $priceQuery .= "basicPrice <= " . $condition["price-to"];
            return $priceQuery;
        }
        if (empty($condition["price-to"])) {
            $priceQuery .= "basicPrice >= " . $condition["price-from"];
            return $priceQuery;
        }

        $priceQuery .= "basicPrice <= " . $condition["price-to"] . " and basicPrice >= " . $condition["price-from"];
        return $priceQuery;
    }

    public function buildIsSaleQuery($condition)
    {
        if (!isset($condition["isSale"]))
            return '';
        if ($condition["isSale"] == null)
            return '';

        //never ever on sale
        if ($condition["isSale"] == 0) {
            $query = "salePercentage = 0 and saleFrom IS NULL and saleTo IS NULL";
        } else if ($condition["isSale"] == 1) {
            $query = "isSale = 1 and salePercentage != 0 and saleFrom <= CURDATE() and saleTo >= CURDATE()";
        } else if ($condition["isSale"] == 2) {
            $query = "salePercentage != 0 and saleFrom IS NOT NULL and saleTo IS NOT NULL and id not in (select id from products where isSale = 1 and salePercentage != 0 and saleFrom <= CURDATE() and saleTo >= CURDATE())";
        } else
            $query = '';

        return $query;
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

    public function buildSaleQuery($data)
    {
        $percentage = $data["percentage"];
        $range = $data["range"];
        $products = $data["products"];
        $query = "update products set salePercentage = " . $percentage . ", saleFrom = '" . $range["from"] . "', saleTo = '" . $range["to"] . "', isSale = 1 where id in (";

        foreach ($products as $product) {
            $query .= $product . ",";
        }
        $query = substr(trim($query), 0, strlen($query) - 1) . ")";

        return $query;
    }

    public function buildUnsaleQuery($products)
    {
        $query = "update products set isSale = 0 where id in (";

        foreach ($products as $product) {
            $query .= $product . ",";
        }
        $query = substr(trim($query), 0, strlen($query) - 1) . ")";

        return $query;
    }
}
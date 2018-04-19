<?php

class MenuViewModel
{
    public $id;
    public $name;
    public $position;
    public $isActive;
    public $children;

    public function __construct()
    {
        $this->children = array();
    }
}

class CategoryService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($id)
    {
        $stmt = $this->db
            ->prepare("select * from categories where id = " . $id);
        $stmt->execute();

        $category = $stmt->fetch();
        return $category;
    }

    public function update($id, $data)
    {
        $putData = $data["data"];
        $sql = "update categories set name = '" . $putData["name"] . "', isActive = " . $putData["isActive"] . ",parentId = " . $putData["parentId"] . " where id = " . $id;
        $result = $this->db->exec($sql);
        return empty($result) ? false : true;
    }

    public function all()
    {
        $stmt = $this->db->prepare("select * from categories where isActive = 1");
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            array_push($result, $row);
        }
        return $result;
    }

    public function allIncludedInactive()
    {
        $stmt = $this->db->prepare("select * from categories");
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            array_push($result, $row);
        }
        return $result;
    }

    //helpers
    public static function menus($categories)
    {
        $menus = array();
        $parents = array_filter($categories, function ($value) {
            return $value["parentId"] == 0;
        });

        foreach ($parents as $parent) {
            $parentViewModel = new MenuViewModel();
            $parentViewModel->id = $parent["id"];
            $parentViewModel->name = $parent["name"];
            $parentViewModel->position = $parent["position"];
            $parentViewModel->isActive = $parent["isActive"];

            foreach ($categories as $category) {
                if ($category["parentId"] == $parent["id"]) {
                    $chidlViewModel = new MenuViewModel();
                    $chidlViewModel->id = $category["id"];
                    $chidlViewModel->name = $category["name"];
                    $chidlViewModel->position = $category["position"];
                    $chidlViewModel->isActive = $category["isActive"];

                    array_push($parentViewModel->children, $chidlViewModel);
                }
            }
            array_push($menus, $parentViewModel);
        }
        return $menus;
    }
}

?>
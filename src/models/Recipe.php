<?php

class Recipe extends Model {

    private $id;
    private $categoryId;
    private $title;
    private $urlRecipe;
    private $urlImage;
    private $status;

    const TABLE = 'recipes';
    const PRIMARY_KEY = 'id';
    public $pdo;

    public function __construct()
    {
        parent::__construct(self::TABLE,self::PRIMARY_KEY);
        $this->pdo = parent::connection();
    }

    public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function save() {
        
        $data = array (
            'category_id' => $this->categoryId,
            'title' => $this->title,
            'url_recipe' => $this->urlRecipe,
            'url_image' => $this->urlImage,
        );

        return $this->insert($data);
    }
    
}

?>
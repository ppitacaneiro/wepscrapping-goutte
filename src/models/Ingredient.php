<?php

class Ingredient extends Model 
{
    private $id;
    private $name;
    private $recipeId;
    private $status;

    const TABLE = 'ingredients';
    const PRIMARY_KEY = 'id';
    public $pdo;

    public function __construct()
    {
        parent::__construct(self::TABLE,self::PRIMARY_KEY);
        $this->pdo = parent::connection();
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value) 
    {
        $this->$name = $value;
    }

    public function save()
    {
        $data = array(
            'name' => $this->name,
            'recipe_id' => $this->recipeId
        );

        return $this->insert($data);
    }
}

?>
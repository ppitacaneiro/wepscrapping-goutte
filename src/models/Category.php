<?php

class Category extends Model {
    
    private $id;
    private $name;
    private $status;

    const TABLE = 'categories';
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

    public function __set($name,$value) 
    {
        $this->$name = $value;
    }

    public function save() {

        $data = array(
            'name' => $this->name
        );

        return $this->insert($data);
    }

}

?>
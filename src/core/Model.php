<?php

require './src/core/Connection.php';

abstract class Model extends Connection 
{    
    private $table;
    private $primaryKey;
    private $fields = array();
    public $pdo;

    public function __construct($table,$primaryKey) 
    {
        $this->table = (string) $table;
        $this->primaryKey = $primaryKey;
        $this->pdo = parent::connection();
        $this->fields = $this->getFields();
    }

    public function getFields()
    {
        $fields = array();
        
        try
        {
            $sql = 
            "
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = '$this->table'
            ";

            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            $rows = $stm->fetchAll(PDO::FETCH_OBJ);
            
            foreach($rows as $row) 
            {
                array_push($fields,$row->COLUMN_NAME);
            }

            return $fields;
        }
        catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function getAll()
    {
        try
        {
            $stm = $this->pdo->prepare("SELECT * FROM $this->table");
            $stm->execute();
            return $stm->fetch(PDO::FETCH_OBJ);
        }
        catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function getById($id)
    {
        try
        {
            $stm = $this->pdo->prepare("SELECT * FROM $this->table WHERE $this->primaryKey = ?");
            $stm->execute(array($id));
            return $stm->fetchAll(PDO::FETCH_OBJ);
        }
        catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function getByField($field,$value)
    {
        try
        {
            $stm = $this->pdo->prepare("SELECT * FROM $this->table WHERE $field = ?");
            $stm->execute(array($value));
            return $stm->fetchAll(PDO::FETCH_OBJ);
        }
        catch(PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function delete($id)
    {
        try
        {
            $stm = $this->pdo->prepare("DELETE FROM $this->table WHERE $this->primaryKey = ?");
            return $stm->execute(array($id));
        }
        catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function insert($list) 
    {
        $fieldList = '';
        $paramsList = '';
        $valuesList = array();

        foreach($list as $field => $value) 
        {
            if (in_array($field,$this->fields))
            {
                if ($field != $this->primaryKey)
                {
                    $fieldList .= $field . ',';
                    $param = ':' . $field;
                    $paramsList .=  $param . ',';
                    $valuesList[$param] = $value;
                }
            }
        }
        
        $fieldList = rtrim($fieldList,',');
        $paramsList = rtrim($paramsList,',');

        $sql = "INSERT INTO $this->table ($fieldList) VALUES ($paramsList);";

        try 
        {
            $stm = $this->pdo->prepare($sql);
            if ($stm->execute($valuesList)) 
            {
                return $this->pdo->lastInsertId();
            }
            
            return false;
        }
        catch(PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function update($list)
    {
        $setFieldsValues = '';
        $valuesList = array();

        foreach($list as $field => $value)
        {
            if (in_array($field,$this->fields))
            {
                if ($field != $this->primaryKey)
                {
                    $setFieldsValues .= $field . ' = :' .  $field . ','; 
                }
                $valuesList[':' . $field] = $value;
            }
        }
        
        $setFieldsValues = rtrim($setFieldsValues,',');
        
        $sql = "UPDATE $this->table SET $setFieldsValues WHERE $this->primaryKey = :$this->primaryKey";

        try 
        {
            $stm = $this->pdo->prepare($sql);
            if (!$stm->execute($valuesList))
            {
                return false;
            }

            return true;
        }
        catch(PDOException $e)
        {
            die($e->getMessage());
        }
    }
}

?>
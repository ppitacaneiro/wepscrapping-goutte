<?php

require './src/core/Config.php';

class Connection 
{
    private $driver = DRIVER_DATABASE;
    private $host = HOST;
    private $user = USER_DATABASE;
    private $pass = PASSWORD_DATABASE;
    private $dbName = NAME_DATABASE;
    private $charset = CHARSET_DATABASE;
    private $pdo;

    protected function connection()
    {
        try 
        {

            $this->pdo = new PDO("{$this->driver}:host={$this->host};dbname={$this->dbName};charset={$this->charset}", $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $this->pdo;

        } 
        catch (PDOException $e) 
        {
            die($e->getMessage());
        }
    }

    protected function disconect()
    {
        $this->pdo = null;
    }
}

?>
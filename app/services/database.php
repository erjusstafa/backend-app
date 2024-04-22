<?php
class Database
{
    private $host = 'localhost';
    private $dbname = 'test5';
    private $username = 'root';
    private $password = '';
    protected $conn;


    public function __construct()
    {
        $this->conn = null;
    }

    public function connect()
    {
        try {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Successfuly: ";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $conn;
    }
}

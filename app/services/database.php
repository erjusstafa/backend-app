<?php

namespace App\Controller;

use PDO;
use PDOException;

abstract class Database
{
    protected $conn;
    public function __construct($host, $dbname, $username, $password)
    {
        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed 😰: " . $e->getMessage());
        }
    }

    abstract public function executeData($query, $params = []);
}

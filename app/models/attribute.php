<?php

namespace App\Controller;

use App\Controller\Database;
use PDOException;
use Product;

class Atribute 
{

    public function encodeAttributes($attributesData)
    {
        echo '😊'.json_encode($attributesData ?? []);
    }
}

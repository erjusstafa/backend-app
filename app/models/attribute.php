<?php

namespace App\Controller;
class Atribute
{

    public function encodeAttributes($attributesData)
    {
        return json_encode($attributesData ?? []);
    }
}

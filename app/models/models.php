<?php

abstract class Models
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    abstract public function display();
}

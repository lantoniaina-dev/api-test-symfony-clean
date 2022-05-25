<?php

namespace App\Data;

use Symfony\Component\Routing\Annotation\Route;



class TestData
{
    
    public function getData()
    {
        $array = array(
            "foo" => "bar",
            42    => 24,
            "multi" => array(
                 "dimensional" => array(
                     "array" => "foo"
                 )
            )
        );
        return $array;
    }
}
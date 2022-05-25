<?php

namespace App\Service;

use Symfony\Component\Routing\Annotation\Route;

class CalculeService
{
    
    public function taxe(float $prix , float $taxe)
    {
        return $prix-$taxe;
    }
}
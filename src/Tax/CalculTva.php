<?php

namespace App\Tax;

class CalculTva
{
    public function CalculTTC(float $prixHt) : float
    {
        return $prixHt * 1.2;
    
    }
}
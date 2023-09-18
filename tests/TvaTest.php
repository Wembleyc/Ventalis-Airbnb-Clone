<?php

namespace App\Tests;
use App\Tax\CalculTva;
use PHPUnit\Framework\TestCase;

class TvaTest extends TestCase
{
    public function testSomething(): void
    {
        $calcul = new CalculTva();

        $result = $calcul->CalculTTC(10.0);

        $this->assertEquals(12.0, $result);
        
    }
}

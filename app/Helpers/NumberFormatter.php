<?php

namespace App\Helpers;

class NumberFormatter
{
    /**
     * @param type $number
     * @param type $significance
     * @return type
     */
    public static function ceiling($number, $significance = 100)
    {
        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
    }
    
    /**
     * @param type $number
     * @param type $significance
     * @return type
     */
    public static function currencyIDR($number, $decimal = 0)
    {
        return number_format($number, $decimal, ',', '.');
    }
    
    
}
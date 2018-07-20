<?php

namespace App\Helpers;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DateTimeHelper
{
    /**
     * 
     * @param type $startDate date
     * @param type $endDate date
     * @param type $returnFormat [Y-m] by default
     */
    public static function getArrayDateRange($startDate, $endDate, $returnFormat = 'Y-m')
    {
        $start    = (new \DateTime($startDate))->modify('first day of this month');
        $end      = (new \DateTime($endDate))->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);
        
        $result = [];
        foreach ($period as $dt) {
            $result[] = $dt->format($returnFormat);
        }
        return $result;
    }
}
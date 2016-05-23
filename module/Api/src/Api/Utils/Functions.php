<?php

namespace Api\Utils\Functions;

function age($birthDate)
{
    $date = new \DateTime($birthDate);
    $now = new \DateTime();
    $interval = $now->diff($date);

    return $interval->y;
}

function dateDiffInSecs($from, $to)
{
    $date = new \DateTime($from);
    $now = new \DateTime($to);

    $diffInSeconds = $now->getTimestamp() - $date->getTimestamp();

    return $diffInSeconds;
}

function convertSecsToDecimal($seconds)
{
    $hours = floor($seconds / (60 * 60));
 
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);
  
    $minutesInDecimal = $minutes/60;
    
    
    return $hours + $minutesInDecimal;
}

function convertStringToDateTime($string){
    $timestamp = strtotime($string);
     return date("Y-m-d H:i:s", $timestamp);
}
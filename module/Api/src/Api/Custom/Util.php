<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Api\Custom;

Class Util
{

    public static $baseUrl;

    public function getInGMT($date, $tz)
    {
        if ($tz == "") {
            $tz = "UTC";
        }
        $exDate = $this->dateExplode($date);
        $obj = new \DateTime();
        $obj->setTimezone(new \DateTimeZone($tz));
        $obj->setDate($exDate['y'], $exDate['m'], $exDate['d']);
        $obj->setTime($exDate['h'], $exDate['i'], $exDate['s']);
        $obj->setTimezone(new \DateTimeZone("UTC"));
        return $obj->format("Y-m-d H:i:s");
    }

    public function getFromGMT($date, $tz)
    {
        if ($tz == "") {
            $tz = "UTC";
        }
        $exDate = $this->dateExplode($date);
        $obj = new \DateTime();
        $obj->setTimezone(new \DateTimeZone("UTC"));
        $obj->setDate($exDate['y'], $exDate['m'], $exDate['d']);
        $obj->setTime($exDate['h'], $exDate['i'], $exDate['s']);
        $obj->setTimezone(new \DateTimeZone($tz));
        return $obj->format("Y-m-d H:i:s");
    }

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

        $minutesInDecimal = $minutes / 60;


        return $hours + $minutesInDecimal;
    }

}

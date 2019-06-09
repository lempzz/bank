<?php

if (!function_exists('between')) {
    function between($number, $from, $to)
    {
        return $number > $from && $number < $to;
    }
}

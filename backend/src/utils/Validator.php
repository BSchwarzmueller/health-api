<?php

namespace App\utils;

class Validator
{
    public static function sanitizeString($string)
    {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    public static function validateString($string, $maxLength)
    {
        return is_string($string) && strlen($string) <= $maxLength;
    }

    public static function sanitizeInt($int)
    {
        return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function validateInt($int)
    {
        return filter_var($int, FILTER_VALIDATE_INT) !== false;
    }

    public static function sanitizeDateTime($dateTime)
    {
        return filter_var($dateTime, FILTER_SANITIZE_STRING);
    }

    public static function validateDateTime($dateTime)
    {
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return $d && $d->format('Y-m-d H:i:s') === $dateTime;
    }
}
<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class StringHelper extends Str
{
    private const PREPOSITONS = ['da', 'de', 'do', 'e'];

    public static function nameComplete(string $str)
    {
        $str = explode(' ', trim($str));
        $str = array_map(function ($value) {
            $value = parent::lower($value);
            return in_array($value, self::PREPOSITONS) ? $value : ucfirst($value);
        }, $str);
        return implode(' ', $str);
    }

    public static function nameAbbreviated(string $str)
    {
        $str = explode(' ', self::nameComplete($str));
        $name = [
            array_shift($str),
            sizeof($str) > 2 && in_array($str[sizeof($str) - 2], self::PREPOSITONS) ? $str[sizeof($str) - 2] : null,
            array_pop($str)
        ];
        return implode(' ', $name);
    }
}

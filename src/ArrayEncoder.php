<?php

namespace HercDotTech\Stateless;

class ArrayEncoder
{
    public static function encode(array $array): string
    {
        if (empty($array)) {
            return '';
        }

        return base64_encode(json_encode($array));
    }

    public static function decode(string $string): array
    {
        if (empty($string)) {
            return [];
        }

        return json_decode(base64_decode($string), true);
    }
}

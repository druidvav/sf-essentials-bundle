<?php
namespace Druidvav\EssentialsBundle;

class SerializerForUrl
{
    public static function serialize($data, $compact = true): array|string
    {
        $data = serialize($data);
        if ($compact) $data = gzencode($data);
        return self::encode($data);
    }

    public static function unserialize($data, $compact = true)
    {
        $data = self::decode($data);
        if ($compact) $data = gzdecode($data);
        return unserialize($data);
    }

    public static function encode($string): array|string
    {
        return str_replace('/', '-', base64_encode($string));
    }

    public static function decode($string): bool|string
    {
        return base64_decode(str_replace([ ' ', '-' ], [ '+', '/' ], $string));
    }
}
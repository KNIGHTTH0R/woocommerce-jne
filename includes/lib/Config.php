<?php

class Neokurir_Config
{

    public static $debug        = false;
    public static $api_version  = 'v1';

    const API_ENDPOINT = 'https://api.neokurir.com';

    public static function api_endpoint()
    {
        return self::API_ENDPOINT . '/' . self::$api_version;
    }

    public static function api_token()
    {
        return self::api_endpoint() . '/auth/token';
    }

    public static function api_city()
    {
        return self::api_endpoint() . '/city';
    }

    public static function api_price()
    {
        return self::api_endpoint() . '/price';
    }

    public static function api_conote()
    {
        return self::api_endpoint() . '/conote';
    }
}

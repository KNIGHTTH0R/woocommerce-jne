<?php

class Neokurir_Config
{

    public static $debug        = false;
    public static $api_version  = 'v1';
    public static $access_token = '';

    const API_ENDPOINT = 'https://api.neokurir.com';

    public static function api_endpoint()
    {
        return self::API_ENDPOINT . '/' . self::$api_version;
    }

    public static function endpoint_token()
    {
        return self::api_endpoint() . '/auth/token';
    }

    public static function endpoint_province($access_token = '')
    {
        if (!empty($access_token)) {
            self::$access_token = $access_token;
        }

        return self::api_endpoint() . '/province';
    }

    public static function endpoint_city($access_token = '')
    {
        if (!empty($access_token)) {
            self::$access_token = $access_token;
        }

        return self::api_endpoint() . '/city';
    }

    public static function endpoint_price($access_token = '')
    {
        if (!empty($access_token)) {
            self::$access_token = $access_token;
        }

        return self::api_endpoint() . '/price';
    }

    public static function endpoint_conote($access_token = '')
    {
        if (!empty($access_token)) {
            self::$access_token = $access_token;
        }

        return self::api_endpoint() . '/conote';
    }
}

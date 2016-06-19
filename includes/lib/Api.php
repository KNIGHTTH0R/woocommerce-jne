<?php

class Neokurir_Api
{

    public static function access_token($params = array())
    {
        try {
            $request = Neokurir_Request::post(Neokurir_Config::api_token(), $params);
            return $request->results->access_token;
        } catch (Exception $e) {
            if (Neokurir_Config::$debug) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        return false;
    }

    public static function get_city($access_token, $query = array())
    {
        try {
            Neokurir_Request::set_access_token($access_token);
            $request = Neokurir_Request::get(Neokurir_Config::api_city(), $query);
            return $request->results;
        } catch (Exception $e) {
            if (Neokurir_Config::$debug) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        return false;
    }

    public static function get_price($access_token, $params = array())
    {
        try {
            Neokurir_Request::set_access_token($access_token);
            $request = Neokurir_Request::post(Neokurir_Config::api_price(), $params);
            return $request->results;
        } catch (Exception $e) {
            if (Neokurir_Config::$debug) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        return false;
    }

    public static function get_conote($access_token, $params = array())
    {
        try {
            Neokurir_Request::set_access_token($access_token);
            $request = Neokurir_Request::post(Neokurir_Config::api_conote(), $params);
            return $request->results;
        } catch (Exception $e) {
            if (Neokurir_Config::$debug) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        return false;
    }
}

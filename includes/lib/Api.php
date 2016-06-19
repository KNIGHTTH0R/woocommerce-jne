<?php

class Neokurir_Api
{

    public static function access_token($params = array())
    {
        try {
            $endpoint = Neokurir_Config::endpoint_token();
            $request  = Neokurir_Request::post($endpoint, $params);
            return $request->results;
        } catch (Exception $e) {
            if (Neokurir_Config::$debug) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        return false;
    }

    public static function get_province($access_token, $query = array())
    {
        try {
            $endpoint = Neokurir_Config::endpoint_province($access_token);
            $request  = Neokurir_Request::get($endpoint, $query);
            return $request->results;
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
            $endpoint = Neokurir_Config::endpoint_city($access_token);
            $request  = Neokurir_Request::get($endpoint, $query);
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
            $endpoint = Neokurir_Config::endpoint_price($access_token);
            $request  = Neokurir_Request::post($endpoint, $params);
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
            $endpoint = Neokurir_Config::endpoint_conote($access_token);
            $request  = Neokurir_Request::post($endpoint, $params);
            return $request->results;
        } catch (Exception $e) {
            if (Neokurir_Config::$debug) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        return false;
    }

    public static function is_token_expired($time)
    {
        if (empty($time) || !$time) {
            return true;
        }

        $date = DateTime::createFromFormat('Y-m-d', date('Y-m-d', $time));
        if ($date && $date->format('Y-m-d') === date('Y-m-d', $time)) {
            $date1 = new DateTime(date('Y-m-d'));
            $date2 = new DateTime(date('Y-m-d', $time));
            $date2->sub(new DateInterval('P2D'));
            $diff = $date1->diff($date2)->format("%r%a");
            return $diff < 0;
        }

        return true;
    }
}

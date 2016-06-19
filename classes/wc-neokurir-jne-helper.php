<?php
/**
 * WooCommerce Neo Kurir JNE Helper
 *
 * Helper File
 *
 * @author Neo Kurir
 * @package WooCommerce Neo Kurir JNE Shipping
 */

// Exit if accessed directly
if (!defined('WC_NEOKURIR_JNE')) {
    exit;
}

if (!function_exists('nk_save_option')) {
    function nk_save_option($key, $value)
    {
        if (empty(get_option($key))) {
            add_option($key, $value);
        } else {
            update_option($key, $value);
        }
    }
}

if (!function_exists('nk_default_services')) {
    function nk_default_services()
    {
        return array(
            array(
                'id'      => 'reg',
                'name'    => 'REG (Regular)',
                'add_fee' => 0,
                'enable'  => 1,
            ),
            array(
                'id'      => 'oke',
                'name'    => 'OKE (Ongkos Kirim Ekonomis)',
                'add_fee' => 0,
                'enable'  => 1,
            ),
            array(
                'id'      => 'yes',
                'name'    => 'YES (Yakin Esok Sampai)',
                'add_fee' => 0,
                'enable'  => 1,
            ),
        );
    }
}

if (!function_exists('nk_get_cities')) {
    function nk_get_cities()
    {
        $cities = file_get_contents(WC_NEOKURIR_JNE()->get_cities_path());
        $cities = json_decode($cities);
        $data   = [];
        foreach ($cities as $city) {
            $data[$city->id] = $city->name;
        }
        return $data;
    }
}

if (!function_exists('nk_admin_url')) {
    function nk_admin_url()
    {
        return admin_url('admin.php?page=wc-settings&tab=shipping&section=wc_neokurir_jne_shipping');
    }
}

if (!function_exists('nk_ajax_url')) {
    function nk_ajax_url()
    {
        return admin_url('admin-ajax.php');
    }
}

if (!function_exists('nk_woocommerce_version')) {
    function nk_woocommerce_version()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugin_folder = get_plugins('/' . 'woocommerce');
        $plugin_file   = 'woocommerce.php';

        if (isset($plugin_folder[$plugin_file]['Version'])) {
            return $plugin_folder[$plugin_file]['Version'];
        } else {
            return null;
        }
    }
}

if (!function_exists('nk_get_page')) {
    function nk_get_page()
    {
        $permalink  = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $permalinks = explode('/', $permalink);
        end($permalinks);
        $key         = key($permalinks);
        $currentPage = $permalinks[$key - 1];

        if (is_cart()) {
            $page = 'cart';
        } elseif (is_checkout()) {
            $page = 'checkout';
        } elseif ($currentPage == 'billing') {
            $page = 'billing';
        } elseif ($currentPage == 'shipping') {
            $page = 'shipping';
        } else {
            $page = '';
        }

        return $page;
    }
}

if (!function_exists('nk_localize_script')) {
    function nk_localize_script()
    {
        return array(
            'plugin_id'  => WC_NEOKURIR_JNE_PLUGIN_ID,
            'wc_version' => nk_woocommerce_version(),
            'ajax_url'   => nk_ajax_url(),
            'page'       => nk_get_page(),
            '_wpnonce'   => wp_create_nonce(WC_NeoKurir_JNE_AJAX::$nonce),
        );
    }
}

if (!function_exists('nk_find_city_id')) {
    function nk_find_city_id($name)
    {
        $cities = file_get_contents(WC_NEOKURIR_JNE()->get_cities_path());
        $cities = json_decode($cities);
        foreach ($cities as $city) {
            if($city->name == $name) {
                return $city->id;
            }
        }
        return false;
    }
}

<?php
/**
 * WooCommerce Neo Kurir JNE Ajax
 *
 * Main file for ajax request
 *
 * @author Neo Kurir
 * @package WooCommerce Neo Kurir JNE Shipping
 */

// Exit if accessed directly
if (!defined('WC_NEOKURIR_JNE')) {
    exit;
}

class WC_NeoKurir_JNE_AJAX
{

    /**
     * @var string
     */
    public static $nonce_admin = '_wc_neokurir_jne_ajax_admin';

    /**
     * @var string
     */
    public static $nonce = '_wc_neokurir_jne_ajax';

    /**
     * Cunstructor
     */
    public function __construct()
    {
        add_action('wp_ajax_nopriv_' . WC_NEOKURIR_JNE_PLUGIN_ID . '_post_cities', array(__CLASS__, 'post_cities'));
        add_action('wp_ajax_' . WC_NEOKURIR_JNE_PLUGIN_ID . '_post_cities', array(__CLASS__, 'post_cities'));
    }

    /**
     * AJAX Get cities
     **/
    public static function post_cities()
    {

        check_ajax_referer(self::$nonce);

        if (isset($_POST['province']) && !empty($_POST['province'])) {
            $provinces = file_get_contents(WC_NEOKURIR_JNE()->get_provinces_path());
            $provinces = json_decode($provinces);
            $prov_id   = false;
            foreach ($provinces as $province) {
                if ($province->code == $_POST['province']) {
                    $prov_id = $province->id;
                    break;
                }
            }

            $data = [];
            if ($prov_id) {
                $cities = file_get_contents(WC_NEOKURIR_JNE()->get_cities_path());
                $cities = json_decode($cities);
                foreach ($cities as $city) {
                    if ($city->province == $prov_id) {
                        $temp         = [];
                        $temp['id']   = $city->id;
                        $temp['name'] = $city->name;
                        array_push($data, $temp);
                    }
                }
            }

            wp_send_json($data);
        }

        wp_die();
    }
}

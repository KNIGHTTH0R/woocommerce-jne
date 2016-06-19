<?php
/**
 * WooCommerce Neo Kurir JNE Shipping
 *
 * Main file for the calculation and settings shipping
 *
 * @author Neo Kurir
 * @package WooCommerce Neo Kurir JNE Shipping
 */

// Exit if accessed directly
if (!defined('WC_NEOKURIR_JNE')) {
    exit;
}

class WC_NeoKurir_JNE_Shipping extends WC_Shipping_Method
{

    /**
     * Main class instance
     * @var null
     */
    protected static $_instance = null;

    /**
     * Generate instance
     * @return WC_NeoKurir_JNE_Shipping
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     **/
    public function __construct()
    {
        $this->id                 = 'wc_neokurir_jne';
        $this->method_title       = __('Neo Kurir JNE Shipping', 'wc-neokurir-jne-shipping');
        $this->method_description = __('Plugin cek ongkos kirim JNE untuk woocommerce shipping menggunakan layanan API dari Neo Kurir secara realtime.', 'wc-neokurir-jne-shipping');

        // Get Settings
        $this->init_form_fields();
        $this->init_settings();

        $this->title         = $this->settings['title'];
        $this->client_id     = $this->settings['client_id'];
        $this->client_secret = $this->settings['client_secret'];
        $this->weight        = $this->settings['weight'];
        $this->add_fee       = $this->settings['add_fee'];
        $this->logger        = new WC_Logger();

        // Save settings in admin
        add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_admin_options'));
    }

    /**
     * Form fields for admin
     */
    public function init_form_fields()
    {

        $this->form_fields = array(
            'enabled'       => array(
                'title'   => __('Enable/Disable', 'wc-neokurir-jne-shipping'),
                'type'    => 'checkbox',
                'label'   => __('Enable WooCommerce Neo Kurir JNE Shipping', 'wc-neokurir-jne-shipping'),
                'default' => 'no',
            ),
            'title'         => array(
                'title'   => __('Method Title', 'wc-neokurir-jne-shipping'),
                'type'    => 'text',
                'default' => __('JNE Shipping', 'wc-neokurir-jne-shipping'),
            ),
            'client_id'     => array(
                'title'       => __('Client ID', 'wc-neokurir-jne-shipping'),
                'type'        => 'text',
                'description' => __('Client ID yang diberikan oleh Neo Kurir.', 'wc-neokurir-jne-shipping'),
            ),
            'client_secret' => array(
                'title'       => __('Client Secret', 'wc-neokurir-jne-shipping'),
                'type'        => 'text',
                'description' => __('Client Secret yang diberikan oleh Neo Kurir.', 'wc-neokurir-jne-shipping'),
            ),
            'weight'        => array(
                'title'             => __('Default Weight ( kg )', 'wc-neokurir-jne-shipping'),
                'description'       => __('Berat default setiap produk jika tidak mempunyai informasi berat.', 'wc-neokurir-jne-shipping'),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0',
                ),
                'placeholder'       => '0.00',
                'default'           => '1',
            ),
            'add_fee'       => array(
                'title'             => __('Additional Fee', 'wc-neokurir-jne-shipping'),
                'description'       => __('Biaya tambahan untuk setiap ongkos kirim.', 'wc-neokurir-jne-shipping'),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0',
                ),
                'placeholder'       => '0.00',
                'default'           => '0',
            ),
        );
    }

    /**
     * Check if this gateway is enabled
     */
    public function is_available($package)
    {
        if ($this->enabled == "yes") {

            // Required fields check
            if (!$this->client_id) {
                return false;
            }

            // Required fields check
            if (!$this->client_secret) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Calculate shipping cost
     */
    public function calculate_shipping($package)
    {

        $country = WC()->customer->get_shipping_country();
        $state   = WC()->customer->get_shipping_state();
        $city    = WC()->customer->get_shipping_city();

        if ($country != 'ID' || sizeof($package) == 0) {
            return false;
        }

        $weight = $this->calculate_weight($package['contents']);
        if (is_numeric($weight) && floor($weight) != $weight) {
            $desimal    = explode('.', $weight);
            $jne_weight = ($desimal[0] == 0 || substr($desimal[1], 0, 1) > 3 || substr($desimal[1], 0, 2) > 30) ? ceil($weight) : floor($weight);
            $weight     = ($jne_weight == 0) ? 1 : $jne_weight;
        }

        $access_token = Neokurir_Api::access_token([
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
        ]);

        if ($access_token) {
            echo $access_token;
        }

        //get the total weight and dimensions
        // $weight     = 0;
        // $dimensions = 0;
        // foreach ($package['contents'] as $item_id => $values) {
        //     $_product   = $values['data'];
        //     $weight     = $weight + $_product->get_weight() * $values['quantity'];
        //     $dimensions = $dimensions + (($_product->length * $values['quantity']) * $_product->width * $_product->height);

        // }

        // //calculate the cost according to the table
        // switch ($weight) {
        //     case ($weight < 1):
        //         switch ($dimensions) {
        //             case ($dimensions <= 1000):
        //                 $cost = 3;
        //                 break;
        //             case ($dimensions > 1000):
        //                 $cost = 4;
        //                 break;
        //         }
        //         break;
        //     case ($weight >= 1 && $weight < 3):
        //         switch ($dimensions) {
        //             case ($dimensions <= 3000):
        //                 $cost = 10;
        //                 break;
        //         }
        //         break;
        //     case ($weight >= 3 && $weight < 10):
        //         switch ($dimensions) {
        //             case ($dimensions <= 5000):
        //                 $cost = 25;
        //                 break;
        //             case ($dimensions > 5000):
        //                 $cost = 50;
        //                 break;
        //         }
        //         break;

        // }

        // // send the final rate to the user.
        // $this->add_rate(array(
        //     'id'    => $this->id,
        //     'label' => $this->title,
        //     'cost'  => $cost,
        // ));
    }

    /**
     * Calculate Total Weight
     * This function will calculated total weight for all product
     **/
    private function calculate_weight($products)
    {
        $weight         = 0;
        $weight_unit    = get_option('woocommerce_weight_unit');
        $default_weight = $this->weight;

        // Default weight JNE settings is Kilogram
        // Change default weight settings to gram if woocommerce unit is gram
        if ($weight_unit == 'g') {
            $default_weight = $default_weight * 1000;
        }

        foreach ($products as $item_id => $item) {
            $product = $item['data'];

            if ($product->is_downloadable() == false && $product->is_virtual() == false) {
                $product_weight = $product->get_weight() ? $product->get_weight() : $default_weight;
                $product_weight = ($product_weight == 0) ? $default_weight : $product_weight;

                $product_weight = $product_weight * $item['quantity'];

                // Change product weight to kilograms
                if ($weight_unit == 'g') {
                    $product_weight = $product_weight / 1000;
                }

                $weight += $product_weight;
            }
        }

        $weight = number_format((float) $weight, 2, '.', '');

        return $weight;
    }
}

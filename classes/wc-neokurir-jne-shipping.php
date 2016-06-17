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
        $this->id                 = WC_NEOKURIR_JNE_PLUGIN_ID;
        $this->method_title       = __('Neo Kurir JNE Shipping', 'wc-neokurir-jne-shipping');
        $this->method_description = __('Plugin cek ongkos kirim JNE untuk woocommerce shipping menggunakan layanan API dari Neo Kurir secara realtime.', 'wc-neokurir-jne-shipping');

        // Option key
        $this->option_access_token  = $this->id . '_access_token';
        $this->option_expired_token = $this->id . '_access_token_expired';
        $this->option_services      = $this->id . '_services';

        // Get Settings
        $this->init_form_fields();
        $this->init_settings();
        $this->init_default_services();

        $this->title          = $this->settings['title'];
        $this->client_id      = $this->settings['client_id'];
        $this->client_secret  = $this->settings['client_secret'];
        $this->weight         = $this->settings['weight'];
        $this->store_location = $this->settings['store_location'];
        $this->logger         = new WC_Logger();

        // Save settings in admin
        add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_admin_options'));
        add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_admin_custom_options'));
    }

    /**
     * Form fields for admin
     */
    public function init_form_fields()
    {

        $this->form_fields = array(
            'enabled'        => array(
                'title'   => __('Enable/Disable', 'wc-neokurir-jne-shipping'),
                'type'    => 'checkbox',
                'label'   => __('Enable WooCommerce Neo Kurir JNE Shipping', 'wc-neokurir-jne-shipping'),
                'default' => 'no',
            ),
            'title'          => array(
                'title'   => __('Method Title', 'wc-neokurir-jne-shipping'),
                'type'    => 'text',
                'default' => __('JNE Shipping', 'wc-neokurir-jne-shipping'),
            ),
            'client_id'      => array(
                'title'       => __('Client ID', 'wc-neokurir-jne-shipping'),
                'type'        => 'text',
                'description' => __('Client ID yang diberikan oleh Neo Kurir.', 'wc-neokurir-jne-shipping'),
            ),
            'client_secret'  => array(
                'title'       => __('Client Secret', 'wc-neokurir-jne-shipping'),
                'type'        => 'text',
                'description' => __('Client Secret yang diberikan oleh Neo Kurir.', 'wc-neokurir-jne-shipping'),
            ),
            'store_location' => array(
                'title'       => __('Store Location', 'wc-neokurir-jne-shipping'),
                'description' => __('Pilih lokasi toko Anda berada.', 'wc-neokurir-jne-shipping'),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'options'     => nk_get_cities(),
            ),
            'weight'         => array(
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
            'services'       => array(
                'type'        => 'jne_service',
                'title'       => __('JNE Services', 'wc-neokurir-jne-shipping'),
                'description' => __('Additional fee digunakan untuk biaya tambahan pada setiap pengiriman.', 'wc-neokurir-jne-shipping'),
            ),
        );
    }

    /**
     * Load default JNE Services
     */
    private function init_default_services()
    {
        $servives_options = get_option($this->option_services);
        if (empty($servives_options)) {
            nk_save_option($this->option_services, nk_default_services());
        }
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
            if (!$this->store_location) {
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

        $country     = WC()->customer->get_shipping_country();
        $state       = WC()->customer->get_shipping_state();
        $city        = WC()->customer->get_shipping_city();
        $destination = nk_find_city_id($city);

        if ($country != 'ID' || sizeof($package) == 0 || !$destination) {
            return false;
        }


        $weight = $this->calculate_weight($package['contents']);
        if (is_numeric($weight) && floor($weight) != $weight) {
            $desimal    = explode('.', $weight);
            $jne_weight = ($desimal[0] == 0 || substr($desimal[1], 0, 1) > 3 || substr($desimal[1], 0, 2) > 30) ? ceil($weight) : floor($weight);
            $weight     = ($jne_weight == 0) ? 1 : $jne_weight;
        }

        $expired_stamp = get_option($this->option_expired_token);
        if (Neokurir_Api::is_token_expired($expired_stamp)) {
            $access_token = Neokurir_Api::access_token([
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
            ]);
            if ($access_token) {
                nk_save_option($this->option_access_token, $access_token->access_token);
                nk_save_option($this->option_expired_token, time() + $access_token->expires_in);
            }
        }

        $access_token = get_option($this->option_access_token);
        $prices       = Neokurir_Api::get_price($access_token, [
            'origin'      => $this->store_location,
            'destination' => $destination,
            'weight'      => round($weight),
            'courier'     => "JNE",
        ]);

        if ($prices) {
            foreach ($prices as $price) {
                // send the final rate to the user.
                $this->add_rate(array(
                    'id'    => $this->id . '_' . strtolower($price->service->name),
                    'label' => $price->courier->code . ' ' . $price->service->name,
                    'cost'  => $price->price,
                ));
            }
        }

        return false;
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

    /**
     * Process custom field for services
     **/
    public function process_admin_custom_options()
    {
        // Save custom option JNE services
        $services  = get_option($this->option_services);
        $field_key = $this->get_field_key('services');

        for ($i = 0; $i < count($services); $i++) {
            foreach ($services[$i] as $k => $v) {

                if ($k == 'enable') {
                    $services[$i][$k] = 0;
                }

                $key = $field_key . '_' . $i . '_' . $k;
                if (isset($_POST[$key]) && !empty($_POST[$key])) {
                    $services[$i][$k] = $_POST[$key];
                }
            }
        }

        nk_save_option($this->option_services, $services);
    }

    /**
     * Field for services
     **/
    public function generate_jne_service_html($key, $data)
    {
        $field_key = $this->get_field_key($key);
        $defaults  = array(
            'title'             => '',
            'disabled'          => false,
            'class'             => '',
            'css'               => '',
            'placeholder'       => '',
            'type'              => 'text',
            'desc_tip'          => false,
            'description'       => '',
            'custom_attributes' => array(),
        );

        $data     = wp_parse_args($data, $defaults);
        $services = get_option($this->option_services);

        ob_start();
        include WC_NEOKURIR_JNE_TEMPLATE_PATH . '/admin-jne-services.php';

        return ob_get_clean();
    }
}

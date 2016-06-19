<?php
/**
 * Plugin Name: WooCommerce Neokurir JNE Shipping
 * Plugin URI: https://neokurir.com/addons/woocommerce-jne-shipping
 * Description: Plugin cek ongkos kirim JNE untuk woocommerce shipping menggunakan layanan API dari Neo Kurir secara realtime.
 * Version: 1.0.0
 * Author: Neo Kurir
 * Author URI: http://neokurir.com
 *
 *
 * Copyright 2016 Neo Kurir. All Rights Reserved.
 * This Software should not be used or changed without the permission of Neo Kurir.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WC_NeoKurir_JNE
{
    /**
     * Plugin version
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Plugin id
     * @var string
     */
    public $plugin_id = 'wc_neokurir_jne';

    /**
     * Main class instance
     * @var null
     */
    protected static $_instance = null;

    /**
     * Various links
     * @var string
     */
    public $url_dokumen = 'https://doc.neokurir.com';
    public $url_support = 'https://neokurir.com/contact';

    /**
     * Various path
     * @var string
     */
    public $path_cities    = '/assets/json/cities.json';
    public $path_provinces = '/assets/json/provinces.json';

    /**
     * Generate instance
     * @return WC_NeoKurir_JNE
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Cunstructor
     */
    public function __construct()
    {
        // Constant
        define('WC_NEOKURIR_JNE', true);
        define('WC_NEOKURIR_JNE_VERSION', $this->version);
        define('WC_NEOKURIR_JNE_PLUGIN_ID', $this->plugin_id);
        define('WC_NEOKURIR_JNE_MAIN_FILE', __FILE__);
        define('WC_NEOKURIR_JNE_MAIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
        define('WC_NEOKURIR_JNE_TEMPLATE_PATH', WC_NEOKURIR_JNE_MAIN_PATH . '/templates/');
        define('WC_NEOKURIR_JNE_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));

        // Actions
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
        add_action('woocommerce_shipping_init', array(&$this, 'init'));
        add_filter('woocommerce_shipping_methods', array(&$this, 'register_shipping_method'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        // add_action('admin_notices', array(&$this, 'wc_admin_notice'));

        // Load Ajax
        $this->init_ajax();
    }

    /**
     * Initialize before install
     */
    public function init()
    {
        // Requires Files
        require_once 'includes/autoload.php';
        require_once 'classes/wc-neokurir-jne-helper.php';
        require_once 'classes/wc-neokurir-jne-frontend.php';
        require_once 'classes/wc-neokurir-jne-shipping.php';

        if ($this->is_enable()) {
            new WC_NeoKurir_JNE_Frontend();
        }

        load_plugin_textdomain('wc-neokurir-jne-shipping', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Initialize and load ajax, should be load directly
     */
    public function init_ajax()
    {
        require_once 'classes/wc-neokurir-jne-ajax.php';
        new WC_NeoKurir_JNE_AJAX();
    }

    /**
     * Check whether plugin is enabled or not
     */
    public function is_enable()
    {
        $settings = get_option('woocommerce_' . $this->plugin_id . '_settings');
        return (is_array($settings) && array_key_exists('enabled', $settings) && $settings['enabled'] == 'yes') ? true : false;
    }

    /**
     * Add setting link to plugin list
     * @param array $links existing links
     */
    public function plugin_action_links($links)
    {
        $plugin_links = array(
            '<a href="' . nk_admin_url() . '">' . __('Settings', 'neokurir') . '</a>',
            '<a href="' . $this->url_dokumen . '" target="new">' . __('Docs', 'neokurir') . '</a>',
        );

        return array_merge($plugin_links, $links);
    }

    /**
     * Register shipping method
     */
    public function register_shipping_method($methods)
    {
        $methods[] = 'WC_NeoKurir_JNE_Shipping';
        return $methods;
    }

    /**
     * Check if is enabled and notify the user
     */
    // public function wc_admin_notice()
    // {
    //     $settings = WC_NEOKURIR_JNE_SHIPPING()->settings;
    //     if ($settings['enabled'] == 'no') {
    //         return;
    //     }

    //     // Check required fields
    //     if (!$settings['client_id']) {
    //         echo '<div class="error"><p>' . sprintf(__('Neo Kurir JNE Error: Please enter your Client ID <a href="%s">here</a>', 'woocommerce-gateway-kredivo'), nk_admin_url()) . '</p></div>';
    //         return;
    //     }

    //     // Check required fields
    //     if (!$settings['client_secret']) {
    //         echo '<div class="error"><p>' . sprintf(__('Neo Kurir JNE Error: Please enter your Client Secret <a href="%s">here</a>', 'woocommerce-gateway-kredivo'), nk_admin_url()) . '</p></div>';
    //         return;
    //     }
    // }

    /**
     * Load scripts at frontend
     */
    public function enqueue_scripts()
    {
        $suffix      = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        $assets_path = str_replace(array('http:', 'https:'), '', WC()->plugin_url()) . '/assets/';

        // select2
        $select2_js_path  = $assets_path . 'js/select2/select2' . $suffix . '.js';
        $select2_css_path = $assets_path . 'css/select2.css';
        if (!wp_script_is('select2', 'registered')) {
            wp_register_script('select2', $select2_js_path, array('jquery'), '3.5.2');
        }

        if (!wp_style_is('select2', 'registered')) {
            wp_register_style('select2', $select2_css_path);
        }

        // chosen
        $chosen_js_path  = $assets_path . 'js/chosen/chosen.jquery' . $suffix . '.js';
        $chosen_css_path = $assets_path . 'css/chosen.css';
        if (!wp_script_is('chosen', 'registered')) {
            wp_register_script('chosen', $chosen_js_path, array('jquery'), '1.0.0', true);
        }

        if (!wp_style_is('chosen', 'registered')) {
            wp_enqueue_style('woocommerce_chosen_styles', $chosen_css_path);
        }

        // Load frontend script
        $identifier = $this->plugin_id . '_shipping';
        wp_register_script($identifier, WC_NEOKURIR_JNE_PLUGIN_URL . '/assets/js/shipping' . $suffix . '.js', array('jquery'), '1.0.0', true);

        // shipping
        if ($this->is_enable()) {
            if (is_checkout() || is_wc_endpoint_url('edit-address')) {
                wp_enqueue_script($identifier);
                wp_localize_script($identifier, $this->plugin_id . '_js_params', nk_localize_script());
            }
        }

    }

    /**
     * Get cities file path
     */
    public function get_cities_path() {
        return WC_NEOKURIR_JNE_MAIN_PATH . $this->path_cities;
    }

    /**
     * Get provinces file path
     */
    public function get_provinces_path() {
        return WC_NEOKURIR_JNE_MAIN_PATH . $this->path_provinces;
    }
}

/**
 * Check if WooCommerce is active
 **/
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    // Let's fucking rock n roll! Yeah!
    WC_NeoKurir_JNE::instance();

    /**
     * Returns the main instance
     *
     * @return WC_NeoKurir_JNE
     */
    function WC_NEOKURIR_JNE()
    {
        return WC_NeoKurir_JNE::instance();
    }

}

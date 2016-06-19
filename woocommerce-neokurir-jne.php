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
        define('WC_NEOKURIR_JNE_TEMPLATE_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/templates/');
        define('WC_NEOKURIR_JNE_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));
        define('WC_NEOKURIR_JNE_MAIN_FILE', __FILE__);

        // Actions
        register_activation_hook(__FILE__, array('WC_NeoKurir_JNE', 'install'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
        add_action('woocommerce_shipping_init', array(&$this, 'init'));
        add_filter('woocommerce_shipping_methods', array(&$this, 'register_shipping_method'));
        // add_action('admin_notices', array(&$this, 'wc_admin_notice'));
    }

    /**
     * Triger when installing
     */
    public static function install()
    {

        if (!empty(get_option('wc_neokurir_access_token'))) {
            delete_option('wc_neokurir_access_token');
            delete_option('wc_neokurir_access_token_expired');
        }

        add_option('wc_neokurir_access_token', '');
        add_option('wc_neokurir_access_token_expired', '');
    }

    /**
     * Initialize before install
     */
    public function init()
    {
        // Requires Files
        require_once 'includes/autoload.php';
        require_once 'classes/wc-neokurir-jne-shipping.php';

        load_plugin_textdomain('wc-neokurir-jne-shipping', false, dirname(plugin_basename(__FILE__)) . '/languages');

    }

    /**
     * Add setting link to plugin list
     * @param array $links existing links
     */
    public function plugin_action_links($links)
    {
        $plugin_links = array(
            '<a href="' . $this->admin_url() . '">' . __('Settings', 'neokurir') . '</a>',
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
     * Get admin url
     */
    public function admin_url()
    {
        return admin_url('admin.php?page=wc-settings&tab=shipping&section=wc_neokurir_jne_shipping');
    }

    /**
     * Check if is enabled and notify the user
     */
    public function wc_admin_notice()
    {
        $settings = WC_NEOKURIR_JNE_SHIPPING()->settings;
        if ($settings['enabled'] == 'no') {
            return;
        }

        // Check required fields
        if (!$settings['client_id']) {
            echo '<div class="error"><p>' . sprintf(__('Neo Kurir JNE Error: Please enter your Client ID <a href="%s">here</a>', 'woocommerce-gateway-kredivo'), $this->admin_url()) . '</p></div>';
            return;
        }

        // Check required fields
        if (!$settings['client_secret']) {
            echo '<div class="error"><p>' . sprintf(__('Neo Kurir JNE Error: Please enter your Client Secret <a href="%s">here</a>', 'woocommerce-gateway-kredivo'), $this->admin_url()) . '</p></div>';
            return;
        }
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

    /**
     * Returns the main instance
     *
     * @return WC_NeoKurir_JNE_Shipping
     */
    function WC_NeoKurir_JNE_Shipping()
    {
        return WC_NeoKurir_JNE_Shipping::instance();
    }

}

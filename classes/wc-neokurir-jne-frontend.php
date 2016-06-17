<?php
/**
 * WooCommerce Neo Kurir JNE Frontedn
 *
 * Main file for frontend process
 *
 * @author Neo Kurir
 * @package WooCommerce Neo Kurir JNE Shipping
 */

// Exit if accessed directly
if (!defined('WC_NEOKURIR_JNE')) {
    exit;
}

class WC_NeoKurir_JNE_Frontend
{
    /**
     * Constructor
     **/
    public function __construct()
    {
        // JNE reorder checkout fields option
        // add_filter('woocommerce_checkout_fields', array(&$this, 'wc_checkout_fields'), 15);

        // JNE reorder billing address
        add_filter('woocommerce_billing_fields', array(&$this, 'wc_billing_fields'), 15);

        // JNE reorder shipping address
        add_filter('woocommerce_shipping_fields', array(&$this, 'wc_shipping_fields'), 15);

        // Enable city shipping calculator
        add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');

        // Show total weight in review order table
        // add_action( 'woocommerce_review_order_before_shipping', array( &$this, 'show_total_weight' ) );

    }

    /**
     * JNE reorder fields option billing
     **/
    public function wc_billing_fields($fields)
    {
        $wc_fields['billing_country']    = $fields['billing_country'];
        $wc_fields['billing_first_name'] = $fields['billing_first_name'];
        $wc_fields['billing_last_name']  = $fields['billing_last_name'];
        $wc_fields['billing_company']    = $fields['billing_company'];
        $wc_fields['billing_address_1']  = $fields['billing_address_1'];
        $wc_fields['billing_address_2']  = $fields['billing_address_2'];
        $wc_fields['billing_state']      = $fields['billing_state'];
        $wc_fields['billing_postcode']   = $fields['billing_postcode'];

        $wc_fields['billing_nk_city']                = array();
        $wc_fields['billing_nk_city']['type']        = 'select';
        $wc_fields['billing_nk_city']['label']       = __('Town / City', 'wc-neokurir-jne-shipping');
        $wc_fields['billing_nk_city']['class']       = array('form-row-wide', 'address-field', 'update_totals_on_change');
        $wc_fields['billing_nk_city']['options']     = array('0' => 'Select Town / City');
        $wc_fields['billing_nk_city']['required']    = true;
        $wc_fields['billing_nk_city']['placeholder'] = __('Select Town / City', 'wc-neokurir-jne-shipping');

        $wc_fields['billing_city']          = $fields['billing_city'];
        $wc_fields['billing_city']['class'] = array('form-row-wide', 'address-field', 'update_totals_on_change');

        $wc_fields['billing_email'] = $fields['billing_email'];
        $wc_fields['billing_phone'] = $fields['billing_phone'];

        return $wc_fields;
    }

    /**
     * JNE reorder fields option shipping
     **/
    public function wc_shipping_fields($fields)
    {
        $wc_fields['shipping_country']    = $fields['shipping_country'];
        $wc_fields['shipping_first_name'] = $fields['shipping_first_name'];
        $wc_fields['shipping_last_name']  = $fields['shipping_last_name'];
        $wc_fields['shipping_company']    = $fields['shipping_company'];
        $wc_fields['shipping_address_1']  = $fields['shipping_address_1'];
        $wc_fields['shipping_address_2']  = $fields['shipping_address_2'];
        $wc_fields['shipping_state']      = $fields['shipping_state'];
        $wc_fields['shipping_postcode']   = $fields['shipping_postcode'];

        $wc_fields['shipping_nk_city']                = array();
        $wc_fields['shipping_nk_city']['type']        = 'select';
        $wc_fields['shipping_nk_city']['label']       = __('Town / City', 'wc-neokurir-jne-shipping');
        $wc_fields['shipping_nk_city']['class']       = array('form-row-wide', 'address-field');
        $wc_fields['shipping_nk_city']['options']     = array('0' => 'Select Town / City');
        $wc_fields['shipping_nk_city']['required']    = true;
        $wc_fields['shipping_nk_city']['placeholder'] = __('Select Town / City', 'wc-neokurir-jne-shipping');

        $wc_fields['shipping_city']          = $fields['shipping_city'];
        $wc_fields['shipping_city']['class'] = array('form-row-wide', 'address-field', 'update_totals_on_change');

        return $wc_fields;
    }

    /**
     * JNE reorder fields option
     **/
    public function wc_checkout_fields($fields)
    {
        $wc_fields['billing']['billing_country']    = $fields['billing']['billing_country'];
        $wc_fields['billing']['billing_first_name'] = $fields['billing']['billing_first_name'];
        $wc_fields['billing']['billing_last_name']  = $fields['billing']['billing_last_name'];
        $wc_fields['billing']['billing_company']    = $fields['billing']['billing_company'];
        $wc_fields['billing']['billing_address_1']  = $fields['billing']['billing_address_1'];
        $wc_fields['billing']['billing_address_2']  = $fields['billing']['billing_address_2'];
        $wc_fields['billing']['billing_state']      = $fields['billing']['billing_state'];
        $wc_fields['billing']['billing_postcode']   = $fields['billing']['billing_postcode'];

        $wc_fields['billing_nk_city']                = array();
        $wc_fields['billing_nk_city']['type']        = 'select';
        $wc_fields['billing_nk_city']['label']       = __('Town / City', 'wc-neokurir-jne-shipping');
        $wc_fields['billing_nk_city']['class']       = array('form-row-wide', 'address-field', 'update_totals_on_change');
        $wc_fields['billing_nk_city']['options']     = array('0' => 'Select Town / City');
        $wc_fields['billing_nk_city']['required']    = true;
        $wc_fields['billing_nk_city']['placeholder'] = __('Select Town / City', 'wc-neokurir-jne-shipping');

        $wc_fields['billing']['billing_city']          = $fields['billing']['billing_city'];
        $wc_fields['billing']['billing_city']['class'] = array('form-row-wide', 'address-field', 'update_totals_on_change');

        $wc_fields['billing']['billing_email'] = $fields['billing']['billing_email'];
        $wc_fields['billing']['billing_phone'] = $fields['billing']['billing_phone'];

        // =======================================

        $wc_fields['shipping']['shipping_country']    = $fields['shipping']['shipping_country'];
        $wc_fields['shipping']['shipping_first_name'] = $fields['shipping']['shipping_first_name'];
        $wc_fields['shipping']['shipping_last_name']  = $fields['shipping']['shipping_last_name'];
        $wc_fields['shipping']['shipping_company']    = $fields['shipping']['shipping_company'];
        $wc_fields['shipping']['shipping_address_1']  = $fields['shipping']['shipping_address_1'];
        $wc_fields['shipping']['shipping_address_2']  = $fields['shipping']['shipping_address_2'];
        $wc_fields['shipping']['shipping_state']      = $fields['shipping']['shipping_state'];
        $wc_fields['shipping']['shipping_postcode']   = $fields['shipping']['shipping_postcode'];

        $wc_fields['shipping_nk_city']                = array();
        $wc_fields['shipping_nk_city']['type']        = 'select';
        $wc_fields['shipping_nk_city']['label']       = __('Town / City', 'wc-neokurir-jne-shipping');
        $wc_fields['shipping_nk_city']['class']       = array('form-row-wide', 'address-field');
        $wc_fields['shipping_nk_city']['options']     = array('0' => 'Pilih Kota');
        $wc_fields['shipping_nk_city']['required']    = true;
        $wc_fields['shipping_nk_city']['placeholder'] = __('Select Town / City', 'wc-neokurir-jne-shipping');

        $wc_fields['shipping']['shipping_city']          = $fields['shipping']['shipping_city'];
        $wc_fields['shipping']['shipping_city']['class'] = array('form-row-wide', 'address-field', 'update_totals_on_change');

        $wc_fields['account'] = $fields['account'];
        $wc_fields['order']   = $fields['order'];

        return $wc_fields;
    }
}

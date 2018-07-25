<?php
/*
Plugin Name: Qwertycoin - WooCommerce Gateway
Plugin URI: http://qwertycoin.org
Description: Extends WooCommerce by adding the Qwertycoin Gateway
Version: 0.3
Author: Alex
*/
if(!defined('ABSPATH')) {
	exit;
}

//Load Plugin
add_action('plugins_loaded', 'qwertycoin_init', 0 );

function qwertycoin_init() {
	if(!class_exists('WC_Payment_Gateway')) return;
	
	include_once('include/qwertycoin_payments.php');
	require_once('library.php');

    add_filter( 'woocommerce_payment_gateways', 'qwertycoin_gateway');
	function qwertycoin_gateway( $methods ) {
		$methods[] = 'qwertycoin_gateway';
		return $methods;
	}
}

//Add action link
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'qwertycoin_payment');

function qwertycoin_payment($links) {
	$plugin_links = array('<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout') . '">' . __('Settings', 'qwertycoin_payment') . '</a>',);
	return array_merge($plugin_links, $links);	
}

//Configure currency
add_filter('woocommerce_currencies','add_my_currency');
add_filter('woocommerce_currency_symbol','add_my_currency_symbol', 10, 2);

function add_my_currency($currencies) {
     $currencies['QWC'] = __('Qwertycoin','woocommerce');
     return $currencies;
}

function add_my_currency_symbol($currency_symbol, $currency) {
    switch($currency) {
        case 'QWC': $currency_symbol = 'QWC'; break;
    }
    return $currency_symbol;
}

//Create Database
register_activation_hook(__FILE__,'createDatabase');

function createDatabase() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'woocommerce_wooqwerty';
    
	$sql = "CREATE TABLE $table_name (
       `id` INT(32) NOT NULL AUTO_INCREMENT,
	   `oid` INT(32) NOT NULL,
       `pid` VARCHAR(64) NOT NULL,
       `hash` VARCHAR(120) NOT NULL,
       `amount` DECIMAL(12, 2) NOT NULL,
	   `conversion` DECIMAL(12,2) NOT NULL,
       `paid` INT(1) NOT NULL,
       UNIQUE KEY id (id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

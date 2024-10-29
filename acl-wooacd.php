<?php
/*
 * Plugin Name: ACL - Woo Advanced Customer Dashboard
 * Version: 0.8.0
 * Plugin URI: https://amadercode.com/premium-products/acl-wooacd
 * Description: Customer Dashboard Plugin is the most Advanced Award Wining WooCommerce plugin that lets you create the decorative users end dashboard with many interactive features.
 * Author: AmaderCode Lab
 * Author URI: http://www.amadercode.com/
 * Requires at least: 4.0
 * Tested up to: 5.2
 * Stable tag: 2.2.0
 * Text Domain: acl-wooacd
 * Domain Path: /lang/
 * @package WordPress
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('ACL_WOOACD_PLUGIN_FILE')) {
    define('ACL_WOOACD_PLUGIN_FILE', __FILE__);
}
if (!defined('ACL_WOOACD_ABSPATH')) {
    define('ACL_WOOACD_ABSPATH', dirname(ACL_WOOACD_PLUGIN_FILE) . '/');
}
if (!defined('ACL_WOOACD_VERSION')) {
    define('ACL_WOOACD_VERSION', '1.0.0');
}
if (!defined('ACL_WOOACD_URL')) {
    define('ACL_WOOACD_URL', plugin_dir_url(__FILE__));
}
// Load plugin basic class files
include_once ABSPATH . 'wp-admin/includes/plugin.php';
include_once 'includes/class-acl-wooacd-plugin.php';
include_once 'includes/class-acl-wooacd-install.php';
/**
 * Returns the main instance of WOOACD_PLUGIN to prevent the need to use global.
 *@since  1.0.0
 * @return object Wooacd_plugin
 *
 */
function wooacd_woocommerce_is_active()
{
    return is_plugin_active('woocommerce/woocommerce.php');
}
function acl_wooacd()
{
    if (!wooacd_woocommerce_is_active()) {
        return;
    }
    // Load dependencies.
    $instance = Acl_Wooacd::get_instance(__FILE__, ACL_WOOACD_VERSION);
    if (is_null($instance->settings)) {
        $instance->settings = ACL_Wooacd_Settings::instance($instance);
    }
    return $instance;
}
add_action('plugins_loaded', 'acl_wooacd');
function acl_wooacd_woocommerce_activation_checking()
{
    if (!wooacd_woocommerce_is_active()) {
        deactivate_plugins(plugin_basename(__FILE__));
        unset($_GET['activate']); // Input variable okay.
        //showing error message.
        add_action('admin_notices', 'acl_wooacd_admin_notice__error');
    }
}
add_action('admin_init', 'acl_wooacd_woocommerce_activation_checking');
function acl_wooacd_admin_notice__error()
{
    $class = 'notice notice-error';
    $message = __('Please install Woocommerce, before install this plugin!!!', 'acl-wooacd');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

// function to create the DB / Options / Defaults
function wooacd_create_table()
{
    global $wpdb;
    $sql = array();
    $db_notification = $wpdb->prefix . 'wooacd_notifications';
    if ($wpdb->get_var("show tables like '$db_notification'") != $db_notification) {
        $sql[] = "CREATE TABLE " . $db_notification . " (
            `id` int(9) NOT NULL AUTO_INCREMENT,
            `notification_message` varchar(255) NOT NULL,
            `sender_id` int(9) NOT NULL,
            `sender_type` tinyint(2) NOT NULL,
            `receiver_id` int(9),
            `receiver_type` tinyint(2) NOT NULL,
            `order_id` int(9),
            `custom_cart_id` int(9),
            `notification_for` tinyint(2) NOT NULL,
            `notificated_at` datetime NOT NULL,
            `status` tinyint(2) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        );";
    }
    $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
    if ($wpdb->get_var("show tables like '$db_cart'") != $db_cart) {
        $sql[] = "CREATE TABLE " . $db_cart . " (
            `id` int(9) NOT NULL AUTO_INCREMENT,
            `product_name` text NULL,
            `product_link` text NOT NULL,
            `product_id` int(9) NULL,
            `notes` text NULL,
            `quantity` int(9) NULL,
            `user_id` int(9) NOT NULL,
            `admin_note` text NULL,
            `status` tinyint(2) default 0,            
            `cart_type` tinyint(2) NOT NULL,
            `product_type` tinyint(2) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        );";
    }
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    //echo "ssdd";exit;

}

// Creating tables for all blogs in a WordPress Multisite installation
function wooacd_on_activate($network_wide)
{
    global $wpdb;
    if (is_multisite() && $network_wide) {
        // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            wooacd_create_table();
            restore_current_blog();
        }
    } else {
        wooacd_create_table();
    }
    if (!get_option('acl_wooacd_custom_request')) {
        update_option('acl_wooacd_custom_request', ['shop', 'product']);
    }
    if (!get_option('acl_wooacd_traveller')) {
        update_option('acl_wooacd_traveller', 'enable');
    }
    if (!get_option('acl_wooacd_estimated_delivery_time')) {
        update_option('acl_wooacd_estimated_delivery_time', 'enable');
    }
    if (!get_option('acl_wooacd_menus')) {
        update_option('acl_wooacd_menus', ['dashboard', 'orders', 'notifications', 'custom_cart', 'edit-account', 'downloads', 'edit-addresses', 'payment-methods', 'customer-logout']);
    }
    if (!get_option('acl_wooacd_enable_notifications')) {
        update_option('acl_wooacd_enable_notifications', ['order', 'custom-request']);
    }
    if (!get_option('acl_wooacd_set_estimated_delivery_time')) {
        update_option('acl_wooacd_set_estimated_delivery_time', '21');
    }
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'wooacd_on_activate');

<?php

defined('ABSPATH') || exit;

class Wooacd_Cart
{
    public function __construct()
    {
        $this->init();
        $this->includes();
    }

    public function init()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function includes()
    {
        include_once ACL_WOOACD_ABSPATH . 'includes/cart/class-wooacd-cart-frontend.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/cart/class-wooacd-cart-admin.php';
    }

    // enqueue scripts
    public function enqueue_scripts()
    {
        if (is_admin()) {
             wp_enqueue_script('wooacd_cart_admin_scripts', plugins_url('acl-woo-advanced-customer-dashboard/includes/cart/assets/js/wooacd_cart_admin_scripts.js'), array('jquery'),'1.0');
            wp_localize_script('wooacd_cart_admin_scripts', 'wooacd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        } else {
            wp_enqueue_script('wooacd_cart_forntend_scripts', plugins_url('acl-woo-advanced-customer-dashboard/includes/cart/assets/js/wooacd_cart_frontend_scripts.js'), array('jquery'), '1.0');
            wp_localize_script('wooacd_cart_forntend_scripts', 'wooacd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        }
    }

    public function enqueue_styles()
    {
        if (is_admin()) {
            wp_enqueue_style('wooacd_cart_admin_styles', plugins_url('acl-woo-advanced-customer-dashboard/includes/cart/assets/css/wooacd_cart_admin_styles.css'));
        } else {
            wp_enqueue_style('wooacd_cart_frontend_styles', plugins_url('acl-woo-advanced-customer-dashboard/includes/cart/assets/css/wooacd_cart_frontend_styles.css'));
        }
    }

}
new Wooacd_Cart();

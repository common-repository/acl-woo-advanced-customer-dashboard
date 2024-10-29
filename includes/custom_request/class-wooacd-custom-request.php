<?php

defined('ABSPATH') || exit;

class Wooacd_Custom_Request
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
        include_once ACL_WOOACD_ABSPATH . 'includes/custom_request/class-wooacd-custom-request-frontend.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/custom_request/class-wooacd-custom-request-admin.php';
    }

    // enqueue scripts
    public function enqueue_scripts()
    {
        if (is_admin()) {
            wp_enqueue_script('wooacd_custom_request_admin_scripts', plugins_url('acl-woo-advanced-customer-dashboard/includes/custom_request/assets/js/wooacd_custom_request_admin_scripts.js'), array('jquery', 'jquery-ui-autocomplete'), null, true);
            wp_localize_script('wooacd_custom_request_admin_scripts', 'wooacd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        } else {
            wp_enqueue_script('wooacd_custom_request_forntend_scripts', plugins_url('acl-woo-advanced-customer-dashboard/includes/custom_request/assets/js/wooacd_custom_request_frontend_scripts.js'), array('jquery'), '1.0');
            wp_localize_script('wooacd_custom_request_forntend_scripts', 'wooacd_custom_request_ajax_object',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'phrases' => get_option('acl_wooacd_phrase'),
                )
            );
        }
    }

    public function enqueue_styles()
    {
        if (is_admin()) {
            wp_enqueue_style('wooacd_custom_request_admin_styles', plugins_url('acl-woo-advanced-customer-dashboard/includes/custom_request/assets/css/wooacd_custom_request_admin_styles.css'));
            wp_register_style('myprefix-jquery-ui', plugins_url('acl-woo-advanced-customer-dashboard/includes/custom_request/assets/css/jquery-ui.css'));
        } else {
            wp_enqueue_style('wooacd_custom_request_frontend_styles', plugins_url('acl-woo-advanced-customer-dashboard/includes/custom_request/assets/css/wooacd_custom_request_frontend_styles.css'));
        }
    }

}
new Wooacd_Custom_Request();
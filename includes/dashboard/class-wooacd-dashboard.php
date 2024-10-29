<?php

defined('ABSPATH') || exit;

class Wooacd_Dashboard
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
        include_once ACL_WOOACD_ABSPATH . 'includes/dashboard/class-wooacd-dashboard-admin.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/dashboard/class-wooacd-dashboard-frontend.php';
    }

    // enqueue scripts
    public function enqueue_scripts()
    {
        if (is_admin()) {
             wp_enqueue_script('wooacd_dashboard_admin_scripts', plugins_url('acl-woo-advanced-customer-dashboard/includes/dashboard/assets/js/wooacd_dashboard_admin_scripts.js'), array('jquery'),'1.0');
            wp_localize_script('wooacd_dashboard_admin_scripts', 'wooacd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        } else {
            wp_enqueue_script('wooacd_dashboard_forntend_scripts', plugins_url('acl-woo-advanced-customer-dashboard/includes/dashboard/assets/js/wooacd_dashboard_frontend_scripts.js'), array('jquery'), '1.0');
            wp_localize_script('wooacd_dashboard_forntend_scripts', 'wooacd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        }
    }

    public function enqueue_styles()
    {
        if (is_admin()) {
            wp_enqueue_style('wooacd_dashboard_admin_styles', plugins_url('acl-woo-advanced-customer-dashboard/includes/dashboard/assets/css/wooacd_dashboard_admin_styles.css'));
        } else {
            wp_enqueue_style('wooacd_dashboard_frontend_styles', plugins_url('acl-woo-advanced-customer-dashboard/includes/dashboard/assets/css/wooacd_dashboard_frontend_styles.css'));
        }
    }

}
new Wooacd_Dashboard();

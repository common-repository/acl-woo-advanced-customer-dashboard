<?php

if (!defined('ABSPATH')) {
    exit;
}

class Wooacd_Notifications
{
    private $admin;
    private $frontend;

    public function __construct()
    {
        // echo "no";exit;
        $this->init();
        $this->includes();
        // if (is_admin()) {
        //     $this->admin = new Wooacd_Notifications_Admin();
        // }else{
        //     $this->frontend = new Wooacd_Notifications_Frontend();
        // }
    }

    public function init()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_ajax_change_notification_status', array($this, 'change_notification_status')); // Call when user logged in
        add_action('wp_ajax_nopriv_change_notification_status', array($this, 'change_notification_status')); // Call when user in not
    }

    public function includes()
    {
        include_once ACL_WOOACD_ABSPATH . 'includes/notifications/class-wooacd-notifications-admin.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/notifications/class-wooacd-notifications-frontend.php';
    }

    // enqueue scripts
    public function enqueue_scripts()
    {
        if (is_admin()) {
            wp_enqueue_script('wooacd_notifications_admin_scripts', plugins_url('acl-woo-advanced-customer-dashboard/includes/notifications/assets/js/wooacd_notifications_admin_scripts.js'), array('jquery'), '1.0');
            wp_localize_script('wooacd_notifications_admin_scripts', 'wooacd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

        } else {
            wp_enqueue_script('wooacd_notifications_forntend_scripts', plugins_url('acl-woo-advanced-customer-dashboard/includes/notifications/assets/js/wooacd_notifications_frontend_scripts.js'), array('jquery'), '1.0');
            wp_localize_script('wooacd_notifications_forntend_scripts', 'wooacd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        }
    }

    public function enqueue_styles()
    {
        if (is_admin()) {
            wp_enqueue_style('wooacd_notifications_admin_styles', plugins_url('acl-woo-advanced-customer-dashboard/includes/notifications/assets/css/wooacd_notifications_admin_styles.css'));
        } else {
            wp_enqueue_style('wooacd_notifications_frontend_styles', plugins_url('acl-woo-advanced-customer-dashboard/includes/notifications/assets/css/wooacd_notifications_frontend_styles.css'));
        }
    }
    
    public function change_notification_status()
    {
        global $wpdb;
        $id = wc_clean($_POST['nid']);
        $wpdb->update('wp_wooacd_notifications', array(
            'status' => 1,
        ), array(
            'id' => $id,
        ));
    }
    
}
new Wooacd_Notifications();

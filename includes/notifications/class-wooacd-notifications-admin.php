<?php

if (!defined('ABSPATH')) {
    exit;
}

class Wooacd_Notifications_Admin
{
    public function __construct()
    {
        //echo "asas";exit;
        $this->init();
    }

    public function init()
    {        
        add_action('admin_menu', array($this, 'add_menu_item'));        
        add_action('woocommerce_order_status_changed', array($this, 'get_notification_on_order_status_change'), 99, 3);

    }

    public function add_menu_item()
    {
        global $wpdb;
        $db_notifications = $wpdb->prefix . 'wooacd_notifications';
        $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $db_notifications WHERE `receiver_type` = 1 AND `status`= 0");
        add_submenu_page(
            'acl-wooacd',
            'Notifications',
            $rowcount ? sprintf('Notifications <span class="awaiting-mod">%d</span>', $rowcount) :'Notifications',
            'manage_options',
            'wooacd-notifications',
            array($this, 'notification_page')
        );
    }
    public function notification_page()
    {
        include ACL_WOOACD_ABSPATH . 'includes/notifications/templates/wooacd-notifications-page.php';
    }

    public function get_notification_on_order_status_change($order_id, $old_status, $new_status)
    {
        global $wpdb;
        // Get an instance of the WC_Order object
        $order = wc_get_order($order_id);
        // Get the user ID from WC_Order methods
        $customer_id = $order->get_user_id();
        $db_notifications = $wpdb->prefix . 'wooacd_notifications';
        if ($new_status === "cancelled") {                        
            $wpdb->insert($db_notifications, array(
                'notification_message' => 'Your order <a href="' . $order->get_view_order_url() . '">#'.$order_id.'</a> is Cancelled.',
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $customer_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => $order_id,
                'custom_cart_id' => null,
                'notification_for' => 2, //1=order-message, 2=order-status
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        } elseif ($new_status === "completed") {
            $wpdb->insert($db_notifications, array(
                'notification_message' => 'Your order <a href="' . $order->get_view_order_url() . '">#'.$order_id.'</a> is Completed.',
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $customer_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => $order_id,
                'custom_cart_id' => null,
                'notification_for' => 2, //1=order-message, 2=order-status
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        } elseif ($new_status === "failed") {
            $wpdb->insert($db_notifications, array(
                'notification_message' => 'Your order <a href="' . $order->get_view_order_url() . '">#'.$order_id.'</a> is Failed.',
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $customer_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => $order_id,
                'custom_cart_id' => null,
                'notification_for' => 2, //1=order-message, 2=order-status
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        } elseif ($new_status === "on-hold") {
            $wpdb->insert($db_notifications, array(
                'notification_message' => 'Your order <a href="' . $order->get_view_order_url() . '">#'.$order_id.'</a> is On Hold.',
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $customer_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => $order_id,
                'custom_cart_id' => null,
                'notification_for' => 2, //1=order-message, 2=order-status
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        } elseif ($new_status === "pending") {
            $wpdb->insert($db_notifications, array(
                'notification_message' => 'Your order <a href="' . $order->get_view_order_url() . '">#'.$order_id.'</a> is Pending Payment.',
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $customer_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => $order_id,
                'custom_cart_id' => null,
                'notification_for' => 2, //1=order-message, 2=order-status
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        } elseif ($new_status === "processing") {
            $wpdb->insert($db_notifications, array(
                'notification_message' => 'Your order <a href="' . $order->get_view_order_url() . '">#'.$order_id.'</a> is Processing.',
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $customer_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => $order_id,
                'custom_cart_id' => null,
                'notification_for' => 2, //1=order-message, 2=order-status
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        } elseif ($new_status === "refunded") {
            $wpdb->insert($db_notifications, array(
                'notification_message' => 'Your order <a href="' . $order->get_view_order_url() . '">#'.$order_id.'</a> is Refunded.',
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $customer_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => $order_id,
                'custom_cart_id' => null,
                'notification_for' => 2, //1=order-message, 2=order-status
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        }
    }
}
new Wooacd_Notifications_Admin();

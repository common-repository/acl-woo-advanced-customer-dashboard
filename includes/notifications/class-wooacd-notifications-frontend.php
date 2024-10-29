<?php

if (!defined('ABSPATH')) {
    exit;
}

class Wooacd_Notifications_Frontend
{
    public function __construct()
    {
        $this->init();
    }
    public function init()
    {
        add_action('init', array($this, 'add_notifications_endpoint'));
        add_filter('woocommerce_account_menu_items', array($this, 'add_notifications_link_my_account'));
        add_action('woocommerce_account_notifications_endpoint', array($this, 'notifications_content'));
        add_filter('woocommerce_account_menu_items', array($this, 'my_account_order_with_unread_notification'));
        $enableSettings = get_option('acl_wooacd_enable_notifications');
        if (!empty($enableSettings) && in_array('order', $enableSettings)) {
            add_action('woocommerce_new_order', array($this, 'get_notification_on_create_order'));
        }

    }
    public function add_notifications_endpoint()
    {
        add_rewrite_endpoint('notifications', EP_ROOT | EP_PAGES);
        flush_rewrite_rules();
    }
    public function add_notifications_link_my_account($items)
    {        
         $items['notifications'] = 'Notifications';
        //print_r($items);exit;
        // global $wpdb;
        // $db_notifications = $wpdb->prefix . 'wooacd_notifications';
        // $user_id = get_current_user_id();
        // $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $db_notifications WHERE receiver_id = $user_id AND status = 0 ");
        // $items['notifications'] = __('Notifications  (' . $rowcount . ')', 'woocommerce');
        return $items;
    }
    public function notifications_content()
    {
        //echo 'present sir';exit;
        include ACL_WOOACD_ABSPATH . 'includes/notifications/templates/wooacd-notifications.php';

    }
    public function my_account_order_with_unread_notification()
    {
        global $wpdb;
        $db_notifications = $wpdb->prefix . 'wooacd_notifications';
        $user_id = get_current_user_id();
        $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $db_notifications WHERE receiver_id = $user_id AND status = 0 ");

        // $myorder = array(
        //     'dashboard' => __('Dashboard', 'woocommerce'),
        //     'orders' => __('Orders', 'woocommerce'),
        //     'messages' => __('Messages', 'woocommerce'),
        //     'notifications' => __('Notifications  (' . $rowcount . ')', 'woocommerce'),
        //     'custom_cart' => __('Cart', 'woocommerce'),
        //     'edit-account' => __('Account Details', 'woocommerce'),
        //     'downloads' => __('Downloads', 'woocommerce'),
        //     'edit-address' => __('Addresses', 'woocommerce'),
        //     'payment-methods' => __('Payment Methods', 'woocommerce'),
        //     'customer-logout' => __('Logout', 'woocommerce'),
        // );
        $db_messages = $wpdb->prefix . 'wooacd_messages';
        $messageCount = $wpdb->get_var("SELECT COUNT(*) FROM $db_messages WHERE `customer_seen_status` = 1");
        $selected_menus = get_option('acl_wooacd_menus');
        //var_dump($selected_menus);
        // exit();
        $orders_count = count(wc_get_orders( array(
            //'status' => 'processing',
            'customer_id'  => get_current_user_id(),
            'return' => 'ids',
            'limit' => -1,
        )));
        //var_dump(orders_count);exit;
        if (!empty($selected_menus)) {
            $my_menus = array();
            if (in_array('dashboard', $selected_menus)) {
                $my_menus['dashboard'] = __('Dashboard', 'woocommerce');
            }
            if (in_array('orders', $selected_menus)) {
                $my_menus['orders'] = __('Orders (' . $orders_count . ')', 'woocommerce');
            }
            if (in_array('messages', $selected_menus)) {
                $my_menus['messages'] = __('Messages (' . $messageCount . ')', 'acl-wooacd');
            }
            if (in_array('notifications', $selected_menus)) {
                $my_menus['notifications'] = __('Notifications (' . $rowcount . ')', 'acl-wooacd');
            }
            if (in_array('custom_cart', $selected_menus)) {
                $my_menus['custom_cart'] = __('Cart', 'acl-wooacd');
            }
            if (in_array('edit-account', $selected_menus)) {
                $my_menus['edit-account'] = __('Account Details', 'woocommerce');
            }
            if (in_array('downloads', $selected_menus)) {
                $my_menus['downloads'] = __('Downloads', 'woocommerce');
            }
            if (in_array('edit-address', $selected_menus)) {
                $my_menus['edit-address'] = __('Addresses', 'woocommerce');
            }
            if (in_array('payment-methods', $selected_menus)) {
                $my_menus['payment-methods'] = __('Payment Methods', 'woocommerce');
            }
            if (in_array('customer-logout', $selected_menus)) {
                $my_menus['customer-logout'] = __('Logout', 'woocommerce');
            }
        }
        return $my_menus;
    }
    public function get_notification_on_create_order($order_id)
    {
        global $wpdb;
        $db_notifications = $wpdb->prefix . 'wooacd_notifications';
        $order_url = admin_url('post.php?post=' . $order_id . '&action=edit');
        $wpdb->insert($db_notifications, array(
            'notification_message' => 'A new order has been created<a href="' . $order_url . '"> #' . $order_id . '</a> ',
            'sender_id' => get_current_user_id(),
            'sender_type' => 2, //  admin = 1, customer = 2
            'receiver_id' => null,
            'receiver_type' => 1, //  admin = 1, customer = 2
            'order_id' => $order_id,
            'custom_cart_id' => null,
            'notification_for' => 2, //1=order-message, 2=order-status
            'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
            'status' => 0, //0 = unread, 1 = read
        ));
    }

}
new Wooacd_Notifications_Frontend();

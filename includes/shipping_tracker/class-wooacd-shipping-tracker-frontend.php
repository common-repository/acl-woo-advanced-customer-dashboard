<?php

defined('ABSPATH') || exit;

class Wooacd_Shipping_Tracker_Frontend
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_action('woocommerce_order_details_before_order_table', array($this, 'wooacd_shipping_tracker'), 1, 1);
        if (get_option('acl_wooacd_traveller' == 'enable')) {
            add_filter('woocommerce_account_orders_columns', array($this, 'add_account_orders_traveler_column'), 10, 1);
            add_action('woocommerce_my_account_my_orders_column_traveler-column', array($this, 'add_account_orders_traveler_column_rows'));
        }
        if (get_option('acl_wooacd_estimated_delivery_time') == 'enable') {
            add_filter('woocommerce_account_orders_columns', array($this, 'add_account_orders_delivery_column'), 10, 1);
            add_action('woocommerce_my_account_my_orders_column_delivery-column', array($this, 'add_account_orders_delivery_column_rows'));
            add_action('woocommerce_checkout_create_order', array($this, 'add_estimated_delivery_time'));
        }
        

    }

    public function wooacd_shipping_tracker($order_id)
    {
        $order = wc_get_order($order_id);
        $trackSteps = $order->get_meta('_wooacd_shipping_tracker');
        ?>

        <?php
if (!empty($trackSteps)) {
            ?>
            <div class="wooacd-shipping-tracker-step">
                <ul>
                    <?php
foreach ($trackSteps as $trackStep) {
                $active_stage = "";
                if (isset($trackStep["value"]) && $trackStep["value"] == 1) {
                    $active_stage = "completed";
                }
                ?>
                        <li class='<?php echo $active_stage; ?>'>
                            <span><?php echo $trackStep["label"] ?></span>
                        </li>
                        <?php
}
            ?>
                </ul>
            </div>
            <?php

        }
        ?>

        <?php

    }

    public function add_account_orders_traveler_column($columns)
    {
        $columns['traveler-column'] = __('Traveler', 'woocommerce');
        return $columns;
    }

    public function add_account_orders_traveler_column_rows($order)
    {
        $traveler = $order->get_meta('_wooacd_traveler_name');
        echo $traveler;
    }

    public function add_estimated_delivery_time($order)
    {
        $days = (int)get_option('acl_wooacd_set_estimated_delivery_time');
        $deliveryDate = date('F j, Y', strtotime("+ $days days"));
        $order->update_meta_data('_wooacd_delivery_date', $deliveryDate);
    }

    public function add_account_orders_delivery_column($columns)
    {
        $columns['delivery-column'] = __('Estimated Delivery Time', 'woocommerce');
        return $columns;
    }

    public function add_account_orders_delivery_column_rows($order)
    {
        $deliveryDate = $order->get_meta('_wooacd_delivery_date');
        echo $deliveryDate;
    }
}

new Wooacd_Shipping_Tracker_Frontend();
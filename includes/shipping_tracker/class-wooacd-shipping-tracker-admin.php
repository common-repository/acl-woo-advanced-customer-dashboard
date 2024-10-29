<?php

defined('ABSPATH') || exit;

class Wooacd_Shipping_Tracker_Admin
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {

        add_action('admin_init', array($this, 'wooacd_add_meta_boxes'));
        if (get_option('acl_wooacd_traveller') == 'enable') {
            add_action('woocommerce_process_shop_order_meta', array($this, 'wooacd_traveler_save'));
        }
        if (get_option('acl_wooacd_estimated_delivery_time') == 'enable') {
            add_action('woocommerce_process_shop_order_meta', array($this, 'wooacd_delivery_time_save'));
        }
        add_action('woocommerce_process_shop_order_meta', array($this, 'wooacd_shipping_tracking_steps_save'));
    }

    public function wooacd_add_meta_boxes()
    {

        add_meta_box('wooacd_shipping_tracker_box', __('Custom Request Order Shipping Tracker', 'woocommerce'), array($this, 'wooacd_shipping_tracker_box'), 'shop_order', 'side', 'high');


        if (get_option('acl_wooacd_traveller') == 'enable') {
            add_meta_box('wooacd_set_traveler_box', __('Set Traveler', 'woocommerce'), array($this, 'wooacd_set_traveler_box'), 'shop_order', 'side', 'high');
        }
        if (get_option('acl_wooacd_estimated_delivery_time') == 'enable') {
            add_meta_box('wooacd_set_estimated_delivery_box', __('Estimated Delivery Date', 'woocommerce'), array($this, 'wooacd_set_estimated_delivery_box'), 'shop_order', 'side', 'high');
        }
    }

    public function wooacd_shipping_tracker_box()
    {
        global $post;

?>
        <div class="wrap wooacd-shipping-tracker-container">
            <?php $ship_trucking_steps = get_post_meta($post->ID, '_wooacd_shipping_tracker', true);
            $counter = 0;
            if (!empty($ship_trucking_steps)) {
                foreach ($ship_trucking_steps as $ship_trucking_steps) {
            ?>
                    <div class="wooacd-shipping-tracker-pair">
                        <label for=""><?php if (isset($ship_trucking_steps['label'])) {
                                            echo $ship_trucking_steps['label'];
                                        } ?></label>
                        <input type="hidden" class="" name="wooacd_shipping_tracker_step[<?php echo $counter; ?>][label]" value="<?php if (isset($ship_trucking_steps['label'])) {
                                                                                                                                        echo $ship_trucking_steps['label'];
                                                                                                                                    } ?>" />
                        <input type="checkbox" class="wooacd-checkbox" name="wooacd_shipping_tracker_step[<?php echo $counter; ?>][value]" value="<?php if (isset($ship_trucking_steps['value'])) {
                                                                                                                                                        echo $ship_trucking_steps['value'];
                                                                                                                                                    } ?>" <?php if (isset($ship_trucking_steps['value'])) {
                                                                                                                                                                echo "checked";
                                                                                                                                                            } ?> />
                    </div>
                    <?php
                    $counter++;
                }
            } else {
                $date = date("Y-m-d");
                $date_string = "> '$date'";
                global $wpdb;
                $result = $wpdb->get_var("
                SELECT DISTINCT count(p.ID) FROM {$wpdb->prefix}posts as p LEFT JOIN {$wpdb->prefix}postmeta as pm on pm.post_id = p.ID WHERE p.post_type = 'shop_order' AND p.post_date $date_string AND pm.meta_key = '_wooacd_shipping_tracker'
            ");

                    $trackers = get_option('acl_wooacd_tracker_steps');

                    if (!empty($trackers)) {
                        foreach ($trackers as $tracker) {
                    ?>
                            <div class="wooacd-shipping-tracker-pair">
                                <label for=""><?php echo $tracker; ?> </label>
                                <input type="hidden" class="" name="wooacd_shipping_tracker_step[<?php echo $counter; ?>][label]" value="<?php echo $tracker; ?>" <?php if($result >= 3){echo "disabled";} ?> />
                                <input type="checkbox" name="wooacd_shipping_tracker_step[<?php echo $counter; ?>][value]" value="1" <?php if($result >= 3){echo "disabled";} ?>/>
                            </div>
            <?php
                            $counter++;
                        }
                    }
            }
            ?>
        </div><br>
        <button type="submit" class="button save_order button-primary" name="save" value="Update">Update</button>


    <?php
    }

    public function wooacd_shipping_tracking_steps_save($post_id)
    {
        update_post_meta($post_id, '_wooacd_shipping_tracker', wc_clean($_POST['wooacd_shipping_tracker_step']));
    }

    public function wooacd_set_traveler_box()
    {
        global $post;
        $traveler = get_post_meta($post->ID, '_wooacd_traveler_name', true);
    ?>
        <div class="wooacd-traveler-name-container">
            <input type="text" class="" name="wooacd_traveler_name" value="<?php if (!empty($traveler)) {
                                                                                echo $traveler;
                                                                            } ?>" placeholder="<?php _e('Name', 'woocommerce'); ?>" />
            <button type="submit" class="button save_order button-primary" name="save">Update</button>
        </div>

    <?php
    }

    public function wooacd_traveler_save($post_id)
    {
        update_post_meta($post_id, '_wooacd_traveler_name', wc_clean($_POST['wooacd_traveler_name']));
    }

    public function wooacd_set_estimated_delivery_box()
    {
        global $post;
        $delivery = get_post_meta($post->ID, '_wooacd_delivery_date', true);
        $wcDate = date("m/d/Y", strtotime($delivery));
    ?>
        <div class="wooacd-delivery-time-container">
            <input type="date" class="" name="wooacd_estimated_delivery_time" value="<?php if (!empty($wcDate)) {
                                                                                            echo $wcDate;
                                                                                        } ?>" />
            <button type="submit" class="button save_order button-primary" name="save">Update</button>
        </div>

<?php
    }

    public function wooacd_delivery_time_save($post_id)
    {
        $deliveryDate = wc_clean($_POST['wooacd_estimated_delivery_time']);
        if ($deliveryDate != 0) {
            $wcDate = date("F j, Y", strtotime($deliveryDate));
            update_post_meta($post_id, '_wooacd_delivery_date', $wcDate);
        }
    }
}

new Wooacd_Shipping_Tracker_Admin();

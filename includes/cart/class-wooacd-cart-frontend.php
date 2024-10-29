<?php

defined('ABSPATH') || exit;

class Wooacd_Cart_Frontend
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_action('init', array($this, 'add_cart_endpoint'));
        add_filter('woocommerce_account_menu_items', array($this, 'add_cart_link_my_account'));
        add_action('woocommerce_account_custom_cart_endpoint', array($this, 'cart_content'));
        add_action('wp_ajax_wooacd_show_cart', array($this, 'show_cart')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_show_cart', array($this, 'show_cart')); // Call when user in not
        add_action('wp_ajax_wooacd_show_pending', array($this, 'show_pending')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_show_pending', array($this, 'show_pending')); // Call when user in not
        add_action('wp_ajax_wooacd_rejected_request', array($this, 'rejected_request')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_rejected_request', array($this, 'rejected_request')); // Call when user in not
        add_action('wp_ajax_wooacd_remove_product', array($this, 'remove_product')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_remove_product', array($this, 'remove_product')); // Call when user in not
        add_action('wp_ajax_wooacd_update_cart_quantity', array($this, 'update_cart_quantity')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_update_cart_quantity', array($this, 'update_cart_quantity')); // Call when user in not
    }

    // 1. Register new endpoint to use for My Account page
    // Note: Resave Permalinks or it will give 404 error
    public function add_cart_endpoint()
    {
        add_rewrite_endpoint('custom_cart', EP_ROOT | EP_PAGES);
        flush_rewrite_rules();
    }

    // 2. Insert the new endpoint into the My Account menu
    public function add_cart_link_my_account($items)
    {
        $items['custom_cart'] = 'Cart';
        //print_r($items);exit;
        return $items;
    }

    // 3. Add content to the new endpoint
    public function cart_content()
    {
        //echo"fdgfd";exit;
        include ACL_WOOACD_ABSPATH . 'includes/cart/templates/wooacd_cart.php';
    }

    public function show_cart()
    {
        ob_start();
        if (!WC()->cart->get_cart_contents_count() == 0) {
            // Do something fun

?>
            <div class="wooacd-table wooacd-cart-table">

                <div>
                    <div class="product-remove">&nbsp;</div>
                    <div class="product-details"><?php esc_html_e('Product', 'acl-wooacd'); ?></div>
                    <div class="product-price"><?php esc_html_e('Price', 'acl-wooacd'); ?></div>
                    <div class="product-quantity"><?php esc_html_e('Quantity', 'acl-wooacd'); ?></div>
                    <div class="product-subtotal"><?php esc_html_e('Total', 'acl-wooacd'); ?></div>

                </div>


                <?php do_action('woocommerce_before_cart_contents'); ?>

                <?php
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                ?>
                        <div class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                            <div class="product-remove">
                                <button type="button" class="wooacd-remove" wooacd-cart-pid="<?php echo esc_attr($cart_item_key); ?>">
                                    <?php //esc_html_e('Remove', 'acl-wooacd'); 
                                    ?>
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <div class="product-details">
                                <div class="product-details-inner">
                                    <div class="product-thumbnail">
                                        <?php
                                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                                        if (!$product_permalink) {
                                            echo $thumbnail; // PHPCS: XSS ok.
                                        } else {
                                            printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                                        }
                                        ?>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-name" data-title="<?php esc_attr_e('Product', 'acl-wooacd'); ?>">
                                            <?php
                                            if (!$product_permalink) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                            } else {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                            }

                                            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                            // Meta data.
                                            echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                                            // Backorder notification.
                                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                            }
                                            ?>

                                        </div>
                                        <?php
                                        $breakdowns = get_post_meta($product_id, '_wooacd_cost_breakdown');
                                        //print_r($breakdowns);
                                        //var_dump($breakdown);exit;'
                                        if (!empty($breakdowns)) {
                                        ?>
                                            <div class="product-breakdown">
                                                <div class="wooacd_toggle" style="cursor:pointer; color: white; margin-top: 20px; background: black; padding: 10px; overflow: hidden; display: inline-block;">
                                                    <?php _e("Cost Breakdown", "acl-wooacd") ?></div>
                                                <div class="wooacd_breakdown" style="display: none;">
                                                    <?php
                                                    foreach ($breakdowns as $breakdown) {
                                                        foreach ($breakdown as $cost) {
                                                            echo '<div class="wooacd-breakdown-item"> <div>' . $cost['label'] . ' :</div> <div>' . get_woocommerce_currency_symbol(), sprintf('%0.2f', $cost['value']) . '</div></div>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                        <?php }
                                        ?>
                                        <div class="product-transfer">
                                            <button type="button" class="save_later" pid="<?php echo esc_attr($cart_item_key); ?>">
                                                <i class="far fa-bookmark"></i>
                                                <?php esc_html_e('Save for Later', 'acl-wooacd'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                <?php
                                echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                ?>
                            </div>
                            <div class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'acl-wooacd'); ?>">
                                <input type="number" cart-item-id="<?php echo $cart_item_key; ?>" class="input-text qty text" step="1" min="0" max="" value="<?php echo $cart_item['quantity']; ?>" title="Qty" size="4">
                            </div>

                            <div class="product-subtotal" data-title="<?php esc_attr_e('Total', 'acl-wooacd'); ?>">
                                <?php
                                echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                ?>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
            <?php do_action('woocommerce_cart_contents'); ?>
            <?php if (wc_coupons_enabled()) { ?>
                <div class="actions wooacd-cart-coupon">
                    <div class="coupon">
                        <div>
                            <label for="coupon_code"><?php esc_html_e('Coupon:', 'acl-wooacd'); ?></label>
                        </div>
                        <div><input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'acl-wooacd'); ?>" />
                        </div>
                        <div>
                            <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'acl-wooacd'); ?>"><?php esc_attr_e('Apply coupon', 'acl-wooacd'); ?></button>
                        </div>
                        <?php do_action('woocommerce_cart_coupon'); ?>
                    </div>
                </div>
            <?php } ?>
            <?php do_action('woocommerce_cart_actions'); ?>
            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            <?php do_action('woocommerce_after_cart_contents'); ?>
            </div>
            <?php do_action('woocommerce_after_cart_table'); ?>
            </form>
            <?php do_action('woocommerce_before_cart_collaterals'); ?>
            <div class="cart-collaterals">
                <?php
                /**
                 * Cart collaterals hook.
                 *
                 * @hooked woocommerce_cross_sell_display
                 * @hooked woocommerce_cart_totals - 10
                 */
                do_action('woocommerce_cart_collaterals');
                ?>
            </div>

            <?php do_action('woocommerce_after_cart'); ?>
        <?php
        } else {
            echo 'Your Cart is empty!!';
        }
        $content = ob_get_clean();
        echo wp_send_json(array('html' => $content));
        wp_die();
    }


    public function show_pending()
    {
        ob_clean();
        global $wpdb;
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $user_id = get_current_user_id();
        $get_saved_item = $wpdb->get_results("SELECT * FROM $db_cart WHERE `cart_type` = 2 AND `user_id`= $user_id AND `admin_note` IS NULL AND `status` IN (0,2)");
        if (!$wpdb->num_rows == 0) {
        ?>
            <div class="wooacd-table wooacd-cart-table wooacd-pending-cart-table">
                <div>
                    <div class="product-remove">&nbsp;</div>
                    <div class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></div>
                    <div class="product-quantity"><?php esc_html_e('Quantity', 'woocommerce'); ?></div>
                </div>
                <?php do_action('woocommerce_before_cart_contents');
                //var_dump($get_saved_item);exit;
                foreach ($get_saved_item as $row) {
                    //var_dump($_product);exit;
                ?>
                    <div>
                        <div class="product-remove">
                            <button type="button" class="wooacd-remove" wooacd-request-pid="<?php echo $row->id; ?>">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <div class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                            <a class="wooacd-text-small" href="<?php
                                                                echo $row->product_link;
                                                                ?>"><?php
                    if (!empty($row->product_name)) {
                        echo $row->product_name;
                    } else {
                        echo $row->product_link;
                    } ?>
                            </a>
                        </div>
                        <div class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                            <p class="wooacd-custom-cart-quantity" pid="<?php echo $row->id ?>"><?php
                                                                                                echo $row->quantity;
                                                                                                ?></p>
                        </div>
                    </div>
                <?php }
                do_action('woocommerce_cart_contents');
                do_action('woocommerce_after_cart_contents');
                ?>

            </div>
        <?php
        } else {
            echo 'You have no pending items!';
        }
        $content = ob_get_clean();
        echo wp_send_json(array('html' => $content));
        wp_die();
    }
    public function rejected_request()
    {
        ob_clean();
        //
        global $wpdb;
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $user_id = get_current_user_id();
        $get_saved_item = $wpdb->get_results("SELECT * FROM $db_cart WHERE `cart_type` = 2 AND `user_id`= $user_id AND `admin_note` IS NOT NULL AND `status` IN (0,2)");
        //print_r($get_saved_item);

        if (!$wpdb->num_rows == 0) {
        ?>
            <div class="wooacd-table wooacd-cart-table wooacd-pending-cart-table">

                <div>
                    <div class="product-remove">&nbsp;</div>
                    <div class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></div>
                    <div class="product-quantity"><?php esc_html_e('Quantity', 'woocommerce'); ?></div>
                </div>


                <?php do_action('woocommerce_before_cart_contents');

                //var_dump($get_saved_item);exit;
                foreach ($get_saved_item as $row) {
                    //var_dump($_product);exit;
                ?>
                    <div>
                        <div class="product-remove">
                            <button type="button" class="wooacd-remove" wooacd-request-pid="<?php echo $row->id; ?>">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>

                        <div class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                            <a class="wooacd-text-small" href="<?php echo $row->product_link; ?>" target="_blank"><?php
                                                                                                                    if (!empty($row->product_name)) {
                                                                                                                        echo $row->product_name;
                                                                                                                    } else {
                                                                                                                        echo $row->product_link;
                                                                                                                    } ?>

                            </a>
                            <P class="rejected_reason" style="font-weight: bold;
    margin: 10px 0 0px !important;
    padding: 0 !important;">Rejected Reason</P>
                            <p><?php echo $row->admin_note; ?></p>
                        </div>
                        <div class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                            <p type="number" class="wooacd-custom-cart-quantity" pid="<?php echo $row->id ?>"><?php
                                                                                                                echo $row->quantity;
                                                                                                                ?></p>
                        </div>
                    </div>
                <?php
                }

                do_action('woocommerce_cart_contents');
                do_action('woocommerce_after_cart_contents');
                ?>

            </div>
<?php
        } else {
            echo 'You have no pending items!';
        }
        $content = ob_get_clean();
        echo wp_send_json(array('html' => $content));
        wp_die();
    }

    public function remove_product()
    {
        global $wpdb;
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';

        $custom_cart_id = wc_clean($_POST['cart_id']);
        $request_id = wc_clean($_POST['request_id']);

        if ($custom_cart_id) {
            $cart = WC()->instance()->cart;
            $cart_item_id = $cart->find_product_in_cart($custom_cart_id);
            if ($cart_item_id) {
                $cart->set_quantity($cart_item_id, 0);
            }
        } elseif ($request_id) {
            
            $wpdb->delete($db_cart, array('id' => $request_id));

        }
        wp_send_json($custom_cart_id);
    }

    public function update_cart_quantity()
    {
        $cart_item_key = wc_clean($_POST['cart_item_key']);
        $quantity = wc_clean($_POST['qty']);
        // getting cart items
        // // //   //$qty           = $_POST['qty'];
        $qty = empty(wc_clean($_POST['qty'])) ? 1 : apply_filters('woocommerce_stock_amount', $quantity);
        global $woocommerce;
        $result = $woocommerce->cart->set_quantity($cart_item_key, $qty);
        echo wp_send_json($result);
        wp_die();
    }
}

new Wooacd_Cart_Frontend();

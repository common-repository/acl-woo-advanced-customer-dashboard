<?php

defined('ABSPATH') || exit;

class Wooacd_Custom_Request_Frontend
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        global $wpdb;
        add_shortcode('wooacd-custom-request', array($this, 'custom_request_shortcode'));
        add_action('wp_ajax_wooacd_grab_local_url', array($this, 'grab_local_url')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_grab_local_url', array($this, 'grab_local_url')); // Call when user in not

        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $rowCount = $wpdb->get_var("SELECT COUNT(DATE_FORMAT(`created_at`,'%YYYY-%m-%d')) FROM $db_cart WHERE DATE(`created_at`) = CURDATE()");
        if( $rowCount < 5 ){
            add_action('wp_ajax_wooacd_submit_request', array($this, 'submit_request')); // Call when user logged in
            add_action('wp_ajax_nopriv_wooacd_submit_request', array($this, 'submit_request')); // Call when user in not
        }else{
            add_action('admin_notices', array($this,'acl_wooacd_admin_notice'));
        }

        add_action('wp_footer', array($this, 'custom_request_form'));
        add_shortcode('wooacd-custom-request-search', array($this, 'custom_request_again_shortcode'));
        add_action('wp_ajax_wooacd_custom_request_window', array($this, 'custom_request_window')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_custom_request_window', array($this, 'custom_request_window')); // Call when user in not
        if (get_option('acl_wooacd_price_breakdown') == 'enable') {
            add_action('woocommerce_order_item_meta_end', array($this, 'wooacd_breakdown_button'), 10, 4);
        }

        $customRequestSettings = get_option('acl_wooacd_custom_request');

        if (!empty($customRequestSettings) && in_array("shop", $customRequestSettings)) {
            add_action('woocommerce_after_shop_loop_item', array($this, 'add_custom_request_in_shop'), 10, 4);
        }
        if (!empty($customRequestSettings) && in_array("product", $customRequestSettings)) {
            //add_action('woocommerce_after_add_to_cart_button', array($this, 'add_custom_request_in_single_page'), 10, 4);
            add_action('woocommerce_single_product_summary', array($this, 'add_custom_request_in_single_page'), 10, 4);
            //add_filter('woocommerce_short_description', array($this, 'add_custom_request_in_single_page'), 10, 4);
        }

    }
    public function acl_wooacd_admin_notice()
    {
        $class = 'notice notice-error';
        $message = __("Your Today's Custom Request limit is over!!! if you want unlimited custom request buy our pro version", "acl-wooacd");
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
    public function custom_request_shortcode()
    {
        return '<button type="button" id="wooacd-custom-product-request" class="wooacd-custom-request">' . __('Send Custom Request', 'acl-wooacd') . '</button><div class="wooacd-loader" style = "display:none"></div>';
    }

    public function custom_request_again_shortcode()
    {
        $html = "";
        $html .= '<div class="wooacd-custom-request-form wooacd-custom-request-search-form">';
        $html .= '<input type="search" name="" id="wooacd-product-link">';
        $html .= '<button type="button" class="wooacd-custom-product-request-search">';
        $html .= __("Get Price", "acl-wooacd");
        $html .= '</button>';
        $html .= '</div>';
        $html .= '<div class="wooacd-loader" style = "display:none"></div>';
        return $html;

    }

    public function custom_request_window()
    {

        ob_clean();
        $link = wc_clean($_POST['product_link']);
        $title = $this->wooacd_get_title($link);
        $content = ob_get_clean();
        echo wp_send_json(array('title' => $title));
        wp_die();
    }

    public function wooacd_get_title($url)
    {
        $str = wp_remote_retrieve_body(wp_remote_get($url));
        if (strlen($str) > 0) {
            $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
            preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title); // ignore case
            return $title[1];
        }
        return $title;
    }

    //public function

    public function custom_request_form()
    {
        ?>
<div id="custom-request-wrapper" class="custom-request-wrapper">
    <button class="wooacd-custom-request-close">&times;</button>
    <div class="wooacd-loader" style="display:none"></div>
    <div class="custom-request-container">
        <h2>Request Item</h2>
        <div class="custom-request-form">
            <form action="">
                <?php
if (!is_user_logged_in()) {

            ?>
                <input type="hidden" id="wooacd-visitor" value="guest">
                <div>
                    <label for="request-full-name"><?php _e('Full Name:', 'acl-wooacd');?></label>
                    <input id="request-full-name" type="text">
                    <p class="validation-message" style="display:none; color:red;"></p>
                </div>
                <div>
                    <label for="request-email"><?php _e('Email:', 'acl-wooacd');?></label>
                    <input id="request-email" type="email">
                    <p class="validation-message" style="display:none; color:red;"></p>
                </div>
                <div>
                    <label for="request-phone-number"><?php _e('Phone Number:', 'acl-wooacd');?></label>
                    <input id="request-phone-number" type="text">
                    <p class="validation-message" style="display:none; color:red;"></p>
                </div>
                <?php
} else {
            ?>
                <input type="hidden" id="wooacd-visitor" value="user">
                <?php
}
        ?>

                <div id="wooacd-product-link"> </p>
                    <label for="request-product-link"><?php _e('Product LInk:', 'acl-wooacd');?></label>
                    <input id="request-product-link" type="text">
                    <p class="validation-message" style="display:none; color:red;"></p>
                </div>
                <div>
                    <label for="request-product-note"><?php _e('Note:', 'acl-wooacd');?></label>
                    <textarea name="" id="request-product-note" cols="30" rows="10"
                        placeholder="<?php echo get_option('acl_wooacd_note_placeholder'); ?>"></textarea>
                </div>
                <div>
                    <label for="request-product-qty"><?php _e('Quantity:', 'acl-wooacd');?>
                    </label>
                    <input type="text" id="request-product-qty" value="1">
                    <p class="validation-message" style="display:none; color:red;"></p>
                </div>
                <div>
                    <button type="button" id="request-submit"><?php _e('Submit', 'acl-wooacd');?></button>
                </div>
            </form>
        </div>
        <!-- custom-request-form -->
        <div class="custom-request-notice">
            <P><?php _e('Notice will display here.', 'acl-wooacd');?></P>
        </div>
    </div>
    <!-- custom-request-container -->
</div>
<!-- custom-request-wrapper -->


<?php

    }

    public function submit_request()
    {
        global $wpdb;
        $response = array();
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $db_notifications = $wpdb->prefix . 'wooacd_notifications';

        if (!is_user_logged_in()) {
            $first_name = wc_clean($_POST['full_name']);
            $email = wc_clean($_POST['email']);
            $phone_number = wc_clean($_POST['phone_number']);
            $user_id = username_exists($email);
            //$user_id = email_exists($email);
            if (!$user_id and email_exists($email) == false) {
                //$response = 'dgdg';exit;
                $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                //$user_id = wp_create_user($name, $random_password, $email);
                $user_id = wp_insert_user(array(
                    'first_name' => $first_name,
                    'user_pass' => $random_password,
                    'user_login' => $email,
                    'user_email' => $email,
                    'phone' => $phone_number,
                    'role' => 'customer',
                ));
                // $to = $_POST['email']; //sendto@example.com
                // $subject = 'The subject';
                // $body = 'The email body content';
                // $headers = array('Content-Type: text/html; charset=UTF-8');
                //wp_mail($to, $subject, $body, $headers);
                if (!is_wp_error($user_id)) {
                    wp_mail($email, 'Login Details', 'Hi ' . $first_name . '!, Welcome to our Site. This is your username : ' . $first_name . ' and password : ' . $random_password);
                    wp_set_current_user($user_id, $email);
                    wp_set_auth_cookie($user_id);
                    //do_action('wp_login', $name);
                    $response['status'] = 200;
                    $response['message'] = __('Your account has been created.', '');
                } else {
                    $response['status'] = 403;
                    $response['message'] = __($user_id->get_error_message(), '');
                }
            } else {
                $response['status'] = 300;
                $response['redirect_url'] = get_permalink(wc_get_page_id('myaccount')); //my account login url
                $response['message'] = __('Your username already exist, please login now', '');
            }
        } else {
            $user_id = get_current_user_id();
            $response['status'] = 200;
            //$response['message'] = __('You are already logged in.', '');
        }
        if (is_user_logged_in()) {
            $insert_id = $wpdb->insert($db_cart, array(
                'product_name' => $this->wooacd_get_title(wc_clean($_POST['product_link'])),
                'product_link' => wc_clean($_POST['product_link']),
                'notes' => wc_sanitize_textarea($_POST['note']),
                'quantity' => wc_clean($_POST['quantity']),
                'user_id' => $user_id,
                'cart_type' => 2, // saved item=1, pending item = 2
                'product_type' => 2, // local product =1, out product = 2
            ));
            if ($insert_id) {
                $wpdb->insert($db_notifications, array(
                    'notification_message' => "You have been requested for new product. Please review it.",
                    'sender_id' => $user_id,
                    'sender_type' => 2, //  admin = 1, customer = 2
                    'receiver_id' => null,
                    'receiver_type' => 1, //  admin = 1, customer = 2
                    'order_id' => null,
                    'notification_for' => 3, //1=order-message, 2=order-status, 3= customer-request
                    'custom_cart_id' => $wpdb->insert_id,
                    'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                    'status' => 0, //0 = unread, 1 = read
                ));
            }
            $response['redirect_url'] = wc_get_account_endpoint_url('custom-cart'); //my account login url
            $response['message'] .= __('Requested item submitted successfully!.', '');
        }
        echo wp_send_json($response);
    }

    public function action_function_name_4492()
    {
        global $post;
        $order = wc_get_order($post->ID);
        foreach ($order as $item_id => $item) {
            // Here you get your data
            $custom_field = wc_get_order_item_meta($item_id, '_tmcartepo_data', true);
            // To test data output (uncomment the line below)
            // If it is an array of values
            if (is_array($custom_field)) {
                echo implode('<br>', $custom_field); // one value displayed by line
            } // just one value (a string)
            else {
                echo $custom_field;
            }
        }
    }

    public function wooacd_breakdown_button($item, $order)
    {
        $breakdown = get_post_meta($order->get_product_id(), '_wooacd_cost_breakdown');
        if (!empty($breakdown)) {
            ?>
<div class="wooacd_toggle"><?php _e("Cost Breakdown", "acl-wooacd")?></div>
<div class="wooacd_breakdown" style="display: none;">
    <div class="wooacd-breakdown-list">
        <?php
foreach ($breakdown as $values) {
                foreach ($values as $cost) {
                    echo '<div class="wooacd-breakdown-item"> <div>' . $cost['label'] . ' :</div> <div>' . get_woocommerce_currency_symbol(), sprintf('%0.2f', $cost['value']) . '</div></div>';
                }
            }
            ?>
    </div>
</div>
<?php }
    }
    public function add_custom_request_in_shop()
    {
        global $product;
        $price = $product->get_price();
        if (empty($price)) {
            echo '<input type="hidden" value="' . $product->id . '" class="wooacd-custom-product-id">';
            echo do_shortcode('[wooacd-custom-request]');
        }

    }
    public function add_custom_request_in_single_page()
    {
        global $product;
        $price = $product->get_price();
        if (empty($price)) {
            echo '<input type="hidden" value="' . $product->id . '" class="wooacd-custom-product-id">';
            echo do_shortcode('[wooacd-custom-request]');
        }
    }
    public function grab_local_url()
    {
        ob_clean();
        $pId = wc_clean($_POST['product_id']);
        $url = get_permalink($pId);
        $title = get_the_title($pId);;
        $content = ob_get_clean();
        echo wp_send_json(array(
            'url' => $url,
            'title' => $title
        ));
        wp_die();
    }
}

new Wooacd_Custom_Request_Frontend();
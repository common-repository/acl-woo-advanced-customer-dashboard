<?php

defined('ABSPATH') || exit;

class Wooacd_Custom_Request_Admin
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('admin_footer', array($this, 'cancel_request'));
        add_action('admin_footer', array($this, 'approved_request'));
        add_action('wp_ajax_wooacd_show_custom_product_item', array($this, 'show_custom_product_item')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_show_custom_product_item', array($this, 'show_custom_product_item')); // Call when user in not
        add_action('wp_ajax_wooacd_cancel_request', array($this, 'cancel_request')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_cancel_request', array($this, 'cancel_request')); // Call when user in not
        add_action('wp_ajax_wooacd_insert_admin_note', array($this, 'insert_admin_note')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_insert_admin_note', array($this, 'insert_admin_note')); // Call when user in not
        add_action('wp_ajax_wooacd_add_to_cart_search_product', array($this, 'add_to_cart_search_product')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_add_to_cart_search_product', array($this, 'add_to_cart_search_product')); // Call when user in not
        add_action('wp_ajax_wooacd_approve_product', array($this, 'approve_product')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_approve_product', array($this, 'approve_product')); // Call when user in not
        add_action('wp', array($this, 'wooacd_custom_add_to_cart'));
    }

    public function add_menu_item()
    {
        global $wpdb;
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $db_cart WHERE  `status` = 0 ");
        add_submenu_page(
            'acl-wooacd',
            'Customer Request',
            $rowcount ? sprintf('Customer Request <span class="awaiting-mod">%d</span>', $rowcount) : 'Customer Request',
            'manage_options',
            'wooacd-customer-request',
            array($this, 'custom_request_page')
        );
    }

    public function custom_request_page()
    {
        //echo "hi";
?>
        <div class="wooacd-custom-request-content">
        </div>
    <?php
    }

    public function show_custom_product_item()
    {
        ob_clean();
        global $wpdb;
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $record_per_page = 10;
        $page = isset($_POST['page']) ? wc_clean($_POST['page']) : 1;
        $offset = ($page * $record_per_page) - $record_per_page;
        $results = $wpdb->get_results("SELECT * FROM $db_cart WHERE `cart_type` = 2 ORDER BY `id` DESC LIMIT $offset, $record_per_page");
    ?>
        <div class="wrap">
            <div class="wooacd-container">
                <h1><?php _e('Customer Request', 'acl-wooacd'); ?></h1>
                <div class="wooacd-table customer-request-table">
                    <div class="wooacd-table-row">
                        <div>SL</div>
                        <div><?php _e('Customer Name', 'acl-wooacd'); ?></div>
                        <div><?php _e('Product Name', 'acl-wooacd'); ?></div>
                        <div><?php _e('Product Link', 'acl-wooacd'); ?></div>
                        <div><?php _e('Notes', 'acl-wooacd'); ?></div>
                        <div><?php _e('Quantity', 'acl-wooacd'); ?></div>
                        <div><?php _e('Action', 'acl-wooacd'); ?></div>
                        <div><?php _e('Admin Note', 'acl-wooacd'); ?></div>
                        <div><?php _e('Requested Time', 'acl-wooacd'); ?></div>
                    </div>
                    <!--table-row-->
                    <?php
                    $index = 0;
                    foreach ($results as $row) {
                        $index++;
                    ?>
                        <div class="wooacd-table-row">
                            <div scope="row"><?php echo $index; ?></div>
                            <div>
                                <a href="<?php echo admin_url('user-edit.php?user_id=' . $row->user_id, 'http'); ?>"><?php
                                                                                                                        $user = get_user_by('id', $row->user_id);
                                                                                                                        $customer = ucwords($user->display_name);
                                                                                                                        echo $customer;
                                                                                                                        ?></a>
                            </div>
                            <div><?php echo $row->product_name; ?></div>
                            <div>
                                <p><?php echo $row->product_link; ?></p>
                                <a href="<?php echo $row->product_link; ?>">Click Product Link</a>
                            </div>
                            <div><?php echo $row->notes; ?></div>
                            <div><?php echo $row->quantity; ?></div>
                            <div><?php if ($row->status == 0) {
                                    ?>
                                    <button type="button" class="wooacd-btn wooacd-btn-danger  wooacd-cancel-customer-request" custom-cart-id="<?php echo $row->id ?>" user-id="<?php echo $row->user_id ?>">Cancel
                                    </button>
                                    <button type="button" class="wooacd-btn wooacd-btn-success approve-customer-request" custom-cart-id="<?php echo $row->id ?>" user-id="<?php echo $row->user_id ?>">Approve
                                    </button>
                                <?php
                                    } elseif ($row->status == 1) {
                                        _e('Request Approved.', 'acl-wooacd');
                                    } elseif ($row->status == 2) {

                                        _e('Request Cancelled.', 'acl-wooacd');
                                    } elseif ($row->status == 3) {
                                        _e('Request Processing', 'acl-wooacd');
                                    }
                                ?>
                            </div>
                            <div>
                                <?php echo $row->admin_note; ?>
                            </div>
                            <div>
                                <?php echo date('h:i:s a d/m/Y', strtotime($row->created_at)); ?>
                            </div>
                        </div>
                        <!--table-row-->
                    <?php

                    }
                    ?>
                </div>
                <!--wooacd-table-->
                <div class="wooacd-pagination">
                    <?php $wpdb->get_results("SELECT * FROM $db_cart WHERE `cart_type` = 2");
                    $total_records = $wpdb->num_rows;
                    //echo $total_records;
                    $total_pages = ceil($total_records / $record_per_page);
                    //echo $total_pages;
                    $links = "";
                    if ($total_pages >= 1 && $page <= $total_pages) {
                        $links .= "<span class='wooacd_pagination_link' page-no=1>1</span>";
                        $i = max(2, $page - 5);
                        if ($i > 2) {
                            $links .= " ... ";
                        }
                        for ($i; $i < min($page + 6, $total_pages); $i++) {
                            $links .= "<span class='wooacd_pagination_link'  page-no='" . $i . "'>" . $i . "</span>";
                        }
                        if ($i != $total_pages) {
                            $links .= " ... ";
                        }

                        $links .= "<span class='wooacd_pagination_link' page-no='" . $total_pages . "'>" . $total_pages . "</span>";
                    }
                    echo $links;
                    ?>
                </div>
                <!-- wooacd-pagination-->
            </div>
            <!--wooacd-container-->
        </div>
        <!-- wrap-->
    <?php
        $content = ob_get_clean();
        echo wp_send_json(array('html' => $content));
        wp_die();
    }

    public function cancel_request()
    {
    ?>
        <div id="custom-request-wrapper" class="custom-request-wrapper">

            <div class="custom-request-container">
                <button class="wooacd-custom-request-close">&times;</button>
                <div class="custom-request-form">
                    <form action="">
                        <div>
                            <input type="hidden" value="" id='custom-cart-id'>
                            <input type="hidden" value="" id='user-id'>
                            <label for="request-cancellation-reason"><?php _e('Note', 'acl-wooacd'); ?></label>
                            <textarea name="" id="request-cancellation-reason" cols="30" rows="10"></textarea>
                        </div>
                        <div>
                            <button type="button" id="wooacd-request-cancel"><?php _e('Cancel Request Product', 'acl-wooacd'); ?></button>
                        </div>
                    </form>
                </div>
                <!-- custom-request-form -->
                <div class="custom-request-notice">
                    <P><?php _e('Notice will display here.', 'acl-wooacd'); ?></P>
                </div>
            </div>
            <!-- custom-request-container -->
        </div>
        <!-- custom-request-wrapper -->
    <?php
    }

    public function insert_admin_note()
    {
        global $wpdb;
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $db_notifications = $wpdb->prefix . 'wooacd_notifications';
        $id = wc_clean($_POST['id']);
        $u_id = wc_clean($_POST['uid']);
        $note = wc_clean($_POST['note']);
        $update_db = $wpdb->update(
            $db_cart,
            array(
                'admin_note' => $note,
                'status' => 2,
            ),
            array(
                'id' => $id,
            )
        );
        $enableSettings = get_option('acl_wooacd_enable_notifications');
        if ($update_db && !empty($enableSettings) && in_array('custom-request', $enableSettings)) {
            $wpdb->insert($db_notifications, array(
                'notification_message' => "We reviewed your request. Sorry! We Couldn't approve it.",
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $u_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => null,
                'notification_for' => 3, //1=order-message, 2=order-status, 3= customer-request
                'custom_cart_id' => $id,
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        }
        $response = 'Cancelled Request Successfully';
        echo wp_send_json($response);
    }

    public function approved_request()
    {
    ?>
        <div id="approve-request-wrapper" class="approve-request-wrapper">
            <div class="approve-request-container">
                <button class="wooacd-approve-request-close">&times;</button>
                <div class="approve-request-form">
                    <form role="search">
                        <div>
                            <label for="search"><?php _e('Select Product', 'acl-wooacd'); ?></label>
                            <input type="hidden" id="product_id" value="">
                            <input type="hidden" id="custom_cart_id" value="">
                            <input type="hidden" id="user_id" value="">
                            <input type="text" class="search-autocomplete" placeholder="Search product for approve" name="s" id='wooacd-input-search'>
                        </div>
                        <div>
                            <button type="button" id="wooacd-approve-request"><?php _e('Approve Request Product', 'acl-wooacd'); ?></button>
                        </div>
                    </form>
                </div>
                <!-- approve-request-form -->
                <div class="approve-request-notice">
                    <P><?php _e('Notice will display here.', 'acl-wooacd'); ?></P>
                </div>
            </div>
            <!-- approve-request-container -->
        </div>
        <!-- approve-request-wrapper -->
<?php
    }

    public function add_to_cart_search_product()
    {
        $results = new WP_Query(array(
            'post_type' => array('product'),
            'post_status' => 'publish',
            'nopaging' => true,
            'posts_per_page' => 100,
            's' => stripslashes(wc_clean($_POST['search'])),
        ));
        $items = array();
        if (!empty($results->posts)) {
            foreach ($results->posts as $result) {
                //$items[] = $result->post_title;
                $items[] = array("value" => $result->ID, "label" => $result->post_title);
            }
        }
        wp_send_json_success($items);
    }

    public function approve_product()
    {
        global $wpdb;
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $db_notifications = $wpdb->prefix . 'wooacd_notifications';
        $custom_cart_id = wc_clean($_POST['custom_cart_id']);
        $product_id = wc_clean($_POST['product_id']);
        $u_id = wc_clean($_POST['user_id']);

        $update_db = $wpdb->update(
            $db_cart,
            array(
                'product_id' => $product_id,
                'status' => 3,
            ),
            array(
                'id' => $custom_cart_id,
            )
        );
        $enableSettings = get_option('acl_wooacd_enable_notifications');
        if ($update_db && !empty($enableSettings) && in_array('custom-request', $enableSettings)) {
            $wpdb->insert($db_notifications, array(
                'notification_message' => "We reviewed your request. You can take it to checkout",
                'sender_id' => get_current_user_id(),
                'sender_type' => 1, //  admin = 1, customer = 2
                'receiver_id' => $u_id,
                'receiver_type' => 2, //  admin = 1, customer = 2
                'order_id' => null,
                'notification_for' => 3, //1=order-message, 2=order-status, 3= customer-request
                'custom_cart_id' => $custom_cart_id,
                'notificated_at' => date('Y-m-d H:i:s', strtotime('now')),
                'status' => 0, //0 = unread, 1 = read
            ));
        }
        $response = 'Request Processing';
        echo wp_send_json($response);
    }

    public function wooacd_custom_add_to_cart()
    {
        if (!is_admin()) {
            global $wpdb;
            global $woocommerce;
            $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
            $current_user = wp_get_current_user();
            if (!$current_user->exists()) {
                return;
            }
            $customer_id = get_current_user_id();
            $results = $wpdb->get_results("SELECT * FROM $db_cart WHERE user_id = '$customer_id' AND status = 3");
            //var_dump($results);exit;
            if (isset($results)) {
                foreach ($results as $result) {
                    $woocommerce->cart->add_to_cart($result->product_id, $result->quantity);
                    $wpdb->update(
                        $db_cart,
                        array(
                            'status' => 1,
                        ),
                        array(
                            'user_id' => $customer_id,
                            'status' => 3,
                        )
                    );
                }
            }
        }
    }
}

new Wooacd_Custom_Request_Admin();

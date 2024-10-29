<?php

if (!defined('ABSPATH')) {
    exit;
}

class ACL_WOOACD_Settings
{
    /**
     * The single instance of WOOACD_Plugin_Settings.
     * @var     object
     * @access  private
     * @since     1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     * @var     object
     * @access  public
     * @since     1.0.0
     */
    public $parent = null;

    /**
     * Prefix for plugin settings.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $base = '';

    /**
     * Available settings for plugin.
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();

    public function __construct($parent)
    {

        $this->parent = $parent;

        $this->base = 'acl_wooacd_';

        // Initialize settings

        add_action('init', array($this, 'init_settings'), 11);

        // Register plugin settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add settings page to menu
        add_action('admin_menu', array($this, 'add_menu_item'));
        /**
         * Have to include all others page here .
         */
        add_action('wp_ajax_wooacd_get_default_estimated_time', array($this, 'get_default_estimated_time')); // Call when user logged in
        add_action('wp_ajax_nopriv_wooacd_get_default_estimated_time', array($this, 'get_default_estimated_time')); // Call when user in not
        require_once 'wooacd-info-page.php';

        // Add settings link to plugins page
        //add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
    }

    /**
     * Initialise settings
     * @return void
     */
    public function init_settings()
    {
        $this->settings = $this->settings_fields();
    }

    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item()
    {
        add_submenu_page(
            'acl-wooacd',
            'Settings',
            'Settings',
            'import',
            'wooacd-settings',
            array($this, 'settings_page'));
    }

    /**
     * Load settings JS & CSS
     * @return void
     */
    public function settings_assets()
    {

        // We're including the farbtastic script & styles here because they're needed for the colour picker
        // If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
        wp_enqueue_style('farbtastic');
        wp_enqueue_script('farbtastic');

        // We're including the WP media scripts here because they're needed for the image upload field
        // If you're not including an image upload then you can leave this function call out
        wp_enqueue_media();

        wp_register_script($this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array('farbtastic', 'jquery'), '1.0.0');
        wp_enqueue_script($this->parent->_token . '-settings-js');
    }

    /**
     * Add settings link to plugin list table
     * @param  array $links Existing links
     * @return array         Modified links
     */
    public function add_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __('Settings', 'acl-wooacd') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    public function settings_fields()
    {

        $settings['wooacd_general'] = array(
            'title' => __('General', 'acl-wooacd'),
            'description' => __('All General setting for Woocommerce Customer Dashboard.', 'acl-wooacd'),
            'fields' => array(
                array(
                    'id' => 'custom_request',
                    'label' => __('Custom Request', 'acl-wooacd'),
                    'description' => __('Display custom request button.', 'acl-wooacd'),
                    'type' => 'checkbox_multi',
                    'options' => array('shop' => 'Shop/Category Pages', 'product' => 'Product Details Page'),
                    'default' => array('shop', 'product'),
                ),
                array(
                    'id' => 'traveller',
                    'label' => __('Traveller', 'acl-wooacd'),
                    'description' => __('A standard set of radio buttons.', 'acl-wooacd'),
                    'type' => 'radio',
                    'options' => array('enable' => 'Enabled', 'disable' => 'Disabled'),
                    'default' => 'enable',
                ),
                array(
                    'id' => 'estimated_delivery_time',
                    'label' => __('Estimated Delivery Time', 'acl-wooacd'),
                    'description' => __('A standard set of radio buttons.', 'acl-wooacd'),
                    'type' => 'radio',
                    'options' => array('enable' => 'Enabled', 'disable' => 'Disabled'),
                    'default' => 'enable',
                ),
                array(
                    'id'             => 'set_estimated_delivery_time',
                ),
                // array(
                //     'id'             => 'product_per_page',
                //     'label'            => __( 'Product Per Page'  , 'acl-wooacd' ),
                //     'description'    => __( 'To display products per on the shop page.Example : 5,6,8,10 etc', 'acl-wooacd' ),
                //     'type'            => 'text',
                //     'default'        => '10',
                //     'placeholder'    => __('Product Per Page', 'acl-wooacd' )
                // ),
                // array(
                //     'id'             => 'show_search_form',
                //     'label'            => __( 'Show Search Box', 'acl-wooacd' ),
                //     'description'    => __( 'Enable to show search box on the top of the product list/grid (it can be override by shortcode parameter)', 'acl-wooacd' ),
                //     'type'            => 'checkbox',
                //     'default'        => 'on'
                // ),
                // array(
                //     'id'             => 'show_loarmore',
                //     'label'            => __( 'Show Load More Button', 'acl-wooacd' ),
                //     'description'    => __( 'Enable to show load more button at the bottom of the product list/grid (it can be override by shortcode parameter)', 'acl-wooacd' ),
                //     'type'            => 'checkbox',
                //     'default'        => 'on'
                // )
                /*array(
            'id'             => 'text_field',
            'label'            => __( 'Some Text' , 'acl-wooacd' ),
            'description'    => __( 'This is a standard text field.', 'acl-wooacd' ),
            'type'            => 'text',
            'default'        => '',
            'placeholder'    => __( 'Placeholder text', 'acl-wooacd' )
            ),
            array(
            'id'             => 'password_field',
            'label'            => __( 'A Password' , 'acl-wooacd' ),
            'description'    => __( 'This is a standard password field.', 'acl-wooacd' ),
            'type'            => 'password',
            'default'        => '',
            'placeholder'    => __( 'Placeholder text', 'acl-wooacd' )
            ),
            array(
            'id'             => 'secret_text_field',
            'label'            => __( 'Some Secret Text' , 'acl-wooacd' ),
            'description'    => __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'acl-wooacd' ),
            'type'            => 'text_secret',
            'default'        => '',
            'placeholder'    => __( 'Placeholder text', 'acl-wooacd' )
            ),
            array(
            'id'             => 'text_block',
            'label'            => __( 'A Text Block' , 'acl-wooacd' ),
            'description'    => __( 'This is a standard text area.', 'acl-wooacd' ),
            'type'            => 'textarea',
            'default'        => '',
            'placeholder'    => __( 'Placeholder text for this textarea', 'acl-wooacd' )
            ),
            array(
            'id'             => 'single_checkbox',
            'label'            => __( 'An Option', 'acl-wooacd' ),
            'description'    => __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', 'acl-wooacd' ),
            'type'            => 'checkbox',
            'default'        => ''
            ),
            array(
            'id'             => 'select_box',
            'label'            => __( 'A Select Box', 'acl-wooacd' ),
            'description'    => __( 'A standard select box.', 'acl-wooacd' ),
            'type'            => 'select',
            'options'        => array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
            'default'        => 'wordpress'
            ),
            array(
            'id'             => 'radio_buttons',
            'label'            => __( 'Some Options', 'acl-wooacd' ),
            'description'    => __( 'A standard set of radio buttons.', 'acl-wooacd' ),
            'type'            => 'radio',
            'options'        => array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
            'default'        => 'batman'
            ),
            array(
            'id'             => 'multiple_checkboxes',
            'label'            => __( 'Some Items', 'acl-wooacd' ),
            'description'    => __( 'You can select multiple items and they will be stored as an array.', 'acl-wooacd' ),
            'type'            => 'checkbox_multi',
            'options'        => array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
            'default'        => array( 'circle', 'triangle' )
            )*/
            ),
        );
        $settings['wooacd_customer_dashboard'] = array(
            'title' => __('Customer Dashboard', 'acl-wooacd'),
            'description' => __('Select below template to display the products as default template', 'acl-wooacd'),
            'fields' => array(

                array(
                    'id' => 'dashboard_content',
                    'label' => __('Dashboard Content', 'acl-wooacd'),
                    'description' => __('This is a standard text area.', 'acl-wooacd'),
                    'type' => 'editor',
                    'default' => '',
                    'placeholder' => __('Placeholder text for this textarea', 'acl-wooacd'),
                ),
                array(
                    'id' => 'menus',
                    'label' => __('Menus', 'acl-wooacd'),
                    'description' => __('You can select multiple menus and they will be appeared on the customer dashboard.', 'acl-wooacd'),
                    'type' => 'checkbox_multi',
                    'options' => array('dashboard' => 'Dashboard', 'orders' => 'Orders', 'messages' => 'Messages', 'notifications' => 'Notifications', 'custom_cart' => 'Cart', 'edit-account' => 'Account Details', 'downloads' => 'Download', 'edit-addresses' => 'Addresses', 'payment-methods' => 'Payment Methods', 'customer-logout' => 'Logout'),
                    'default' => array('dashboard', 'orders', 'downloads', 'addresses', 'account_datails', 'logout'),
                ),

            ),
        );
        $settings['wooacd_notification'] = array(
            'title' => __('Notifications', 'acl-wooacd'),
            'description' => __('Setting for Notifications.'),
            'fields' => array(
                array(
                    'id' => 'enable_notifications',
                    'label' => __('Notifications', 'acl-wooacd'),
                    'description' => __('Settings for notification section', 'acl-wooacd'),
                    'type' => 'checkbox_multi',
                    'options' => array('message' => 'Message', 'order' => 'Order', 'refund' => 'Refund', 'custom-request' => 'Custom Request'),
                    'default' => array('message', 'order', 'refund', 'custom-request'),
                ),

            ),
        );

        $settings['wooacd_shipping_tracker'] = array(
            'title' => __('Shipping Tracker', 'acl-wooacd'),
            'description' => __('Select below template to display the products as default template', 'acl-wooacd'),
            'fields' => array(
                array(
                    'id' => 'tracker_steps',
                    'label' => __('Tracker Steps', 'acl-wooacd'),
                    'description' => __('', 'acl-wooacd'),
                    'type' => 'tracker',
                    'default' => '',
                ),

            ),
        );
        $settings['wooacd_translation'] = array(
            'title' => __('Placeholders', 'acl-wooacd'),
            'description' => __('Placeholder for input box', 'acl-wooacd'),
            'fields' => array(
                array(
                    'id' => 'phrase',
                    'label' => __('Placeholder Phrases', 'acl-wooacd'),
                    'description' => __('', 'acl-wooacd'),
                    'type' => 'phrase',
                    'default' => '',
                ),
                array(
                    'id' => 'placeholder',
                    'label' => __('Custom Request Note Placeholder', 'acl-wooacd'),
                    'description' => __('', 'acl-wooacd'),
                    'type' => 'textarea',
                    'default' => '',
                )
            ),
        );
        // $settings['wooacd_custom_style'] = array(
        //     'title'                    => __( 'Custom Style', 'acl-wooacd' ),
        //     'description'            => __( 'Design your store by overriding default with your own style (CSS).', 'acl-wooacd' ),
        //     'fields'                => array(
        //         array(
        //             'id'             => 'custom_css',
        //             'label'            => __( 'Custom CSS' , 'acl-wooacd' ),
        //             'description'    => __( '', 'acl-wooacd' ),
        //             'type'            => 'textarea',
        //             'default'        => '',
        //             'placeholder'    => __( '', 'acl-wooacd' )
        //         ),

        //     ),
        // );
        //import templates settings
        /*if (class_exists('TESTING_TM')) {
        $options_class= new TESTING_TM();
        $option=$options_class->general_options();
        array_push($settings['wooacd_general']['fields'],$option);
        }*/
        $settings = apply_filters($this->parent->_token . '_settings_fields', $settings);
        return $settings;
    }

    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings()
    {
        if (is_array($this->settings)) {

            // Check posted/selected tab
            $current_section = '';
            if (isset($_POST['tab']) && $_POST['tab']) {
                $current_section = wc_clean($_POST['tab']);
            } else {
                if (isset($_GET['tab']) && $_GET['tab']) {
                    $current_section = wc_clean($_GET['tab']);
                }
            }

            foreach ($this->settings as $section => $data) {

                if ($current_section && $current_section != $section) {
                    continue;
                }
                // Add section to page
                add_settings_section($section, $data['title'], array($this, 'settings_section'), $this->parent->_token . '_settings');
                foreach ($data['fields'] as $field) {
                    // Validation callback for field
                    $validation = '';
                    if (isset($field['callback'])) {
                        $validation = $field['callback'];
                    }
                    // Register field
                    $option_name = $this->base . $field['id'];
                    register_setting($this->parent->_token . '_settings', $option_name, $validation);
                    // Add field to page
                    add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_settings', $section, array('field' => $field, 'prefix' => $this->base));
                }
                if (!$current_section) {
                    break;
                }
            }
        }
    }

    public function settings_section($section)
    {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
        echo $html;
    }

    /**
     * Load settings page content
     * @return void
     */
    public function settings_page()
    {

        // Build page HTML
        $html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
        $html .= '<h2>' . __('WooCommerce Customer Dashboard Settings', 'acl-wooacd') . '</h2>' . "\n";

        $tab = '';
        if (isset($_GET['tab']) && $_GET['tab']) {
            $tab .= wc_clean($_GET['tab']);
        }

        // Show page tabs
        if (is_array($this->settings) && 1 < count($this->settings)) {

            $html .= '<h2 class="nav-tab-wrapper">' . "\n";

            $c = 0;
            foreach ($this->settings as $section => $data) {

                // Set tab class
                $class = 'nav-tab';
                if (!isset($_GET['tab'])) {
                    if (0 == $c) {
                        $class .= ' nav-tab-active';
                    }
                } else {
                    if (isset($_GET['tab']) && $section == $_GET['tab']) {
                        $class .= ' nav-tab-active';
                    }
                }

                // Set tab link
                $tab_link = add_query_arg(array('tab' => $section));
                if (isset($_GET['settings-updated'])) {
                    $tab_link = remove_query_arg('settings-updated', $tab_link);
                }

                // Output tab
                $html .= '<a href="' . $tab_link . '" class="' . esc_attr($class) . '">' . esc_html($data['title']) . '</a>' . "\n";

                ++$c;
            }

            $html .= '</h2>' . "\n";
        }
        if (isset($_GET['tab']) && 'wooacd_store' == $_GET['tab']) {
            $html .= '<img src="' . ACL_WOOACD_IMG_URL . 'drop-shipping-pro.png" alt="Drop Shipping Settings Pro Features">' . "\n";
        } else {
            $html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";
            // Get settings fields
            ob_start();
            settings_fields($this->parent->_token . '_settings');
            do_settings_sections($this->parent->_token . '_settings');
            $html .= ob_get_clean();
            $html .= '<p class="submit">' . "\n";
            $html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
            $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'acl-wooacd')) . '" />' . "\n";
            $html .= '</p>' . "\n";
            $html .= '</form>' . "\n";
            $html .= '</div>' . "\n";
        }

        echo $html;
    }

    /**
     * Main WOOACD_Plugin_Settings Instance
     *
     * Ensures only one instance of WOOACD_Plugin_Settings is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WOOACD_Plugin()
     * @return Main WOOACD_Plugin_Settings instance
     */
    public static function instance($parent)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($parent);
        }
        return self::$_instance;
    } // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
    } // End __wakeup()
    public function get_default_estimated_time()
    {
        ob_clean();
        $days = get_option('acl_wooacd_set_estimated_delivery_time');
        $content = ob_get_clean();
        echo wp_send_json(array(
            'days' => $days
        ));
        wp_die();
    }
}
//new ACL_WOOACD_Settings($parent);

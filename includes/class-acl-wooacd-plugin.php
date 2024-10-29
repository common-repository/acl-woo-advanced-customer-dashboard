<?php

defined('ABSPATH') || exit;

final class Acl_Wooacd
{
    //public static $endpoint = 'my-custom-endpoint';

    /**
     * The single instance of ACL_Woo_Onepage_Plugin.
     * @var     object
     * @access  private
     * @since     1.0.0
     */
    private static $instance;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $image_path;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;
    public $data;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct()
    {
        $this->init();
        $this->includes();
        //Load API for generic admin functions
        if (is_admin()) {
            $this->admin = new ACL_Wooacd_Admin_API();
        }
        //
        // new Wooacd_Notifications();
        // new Wooacd_Refund();

    }

    public function init()
    {
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    public function includes()
    {
        include_once ACL_WOOACD_ABSPATH . 'includes/admin/wooacd-admin-api.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/admin/wooacd-settings.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/notifications/class-wooacd-notifications.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/cart/class-wooacd-cart.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/custom_request/class-wooacd-custom-request.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/shipping_tracker/class-wooacd-shipping-tracker.php';
        include_once ACL_WOOACD_ABSPATH . 'includes/dashboard/class-wooacd-dashboard.php';
    }
    // adding sub menu in woocommerce dashboard
    public function add_menu_item()
    {
        add_menu_page(
            'Customer Dashboard',
            'Customer Dashboard',
            'import',
            'acl-wooacd',
            array($this, 'plugin_homepage')
        );
    }
    public function plugin_homepage()
    {
        global $wpdb;
        $db_cart = $wpdb->prefix . 'wooacd_custom_cart';
        $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $db_cart");
        $approve_count = $wpdb->get_var("SELECT COUNT(*) FROM $db_cart WHERE `status`= 1");
        $cancel_count = $wpdb->get_var("SELECT COUNT(*) FROM $db_cart WHERE `status`= 2");
        $processing_count = $wpdb->get_var("SELECT COUNT(*) FROM $db_cart WHERE `status`= 3");
?>
        <div class="wrap">
            <div class="wooacd-container">
                <div class="wooacd-row">
                    <div class="wooacd-col-md-6">
                        <div class="dashbord-box total-request">
                            <div class="dashbord-box-header">
                                <h3> Total Request</h3>
                            </div>
                            <!--dashbord-box-header-->
                            <div class="dashbord-box-body">
                                <p><?php echo $rowcount; ?></p>
                            </div>
                            <!--dashbord-box-body-->
                        </div>
                        <!--dashbord-box-->
                    </div>
                    <!--wooacd-col-md-6-->
                    <div class="wooacd-col-md-6">
                        <div class="dashbord-box process-request">
                            <div class="dashbord-box-header">
                                <h3> Processed Request</h3>
                            </div>
                            <!--dashbord-box-header-->
                            <div class="dashbord-box-body">
                                <p><?php echo $processing_count; ?></p>
                            </div>
                            <!--dashbord-box-body-->
                        </div>
                        <!--dashbord-box-->
                    </div>
                    <!--wooacd-col-md-6-->
                    <div class="wooacd-col-md-6">
                        <div class="dashbord-box approve-request">
                            <div class="dashbord-box-header">
                                <h3> Approved Request</h3>
                            </div>
                            <!--dashbord-box-header-->
                            <div class="dashbord-box-body">
                                <p><?php echo $approve_count; ?></p>
                            </div>
                            <!--dashbord-box-body-->
                        </div>
                        <!--dashbord-box-->
                    </div>
                    <!--wooacd-col-md-6-->

                    <div class="wooacd-col-md-6">
                        <div class="dashbord-box cancel-request">
                            <div class="dashbord-box-header">
                                <h3> Cancel Request</h3>
                            </div>
                            <!--dashbord-box-header-->
                            <div class="dashbord-box-body">
                                <p><?php echo $cancel_count; ?></p>
                            </div>
                            <!--dashbord-box-body-->
                        </div>
                        <!--dashbord-box-->
                    </div>
                    <!--wooacd-col-md-6-->
                </div>
                <!--wooacd-row-->
            </div>
            <!--wooacd-container-->
        </div>
        <!--wrap-->
<?php
    }
    // enqueue scripts
    public function enqueue_scripts()
    {
        if (is_admin()) {
            wp_enqueue_script('acl_wooacd_scripts', plugins_url('acl-woo-advanced-customer-dashboard/assets/js/acl_wooacd_admin_scripts.js'), array('jquery'), '1.0');
            wp_localize_script(
                'acl_wooacd_scripts',
                'wooacd_ajax_object',
                array('ajax_url' => admin_url('admin-ajax.php'))
            );
        } else {
            wp_enqueue_script('acl_wooacd_forntend_scripts', plugins_url('acl-woo-advanced-customer-dashboard/assets/js/acl_wooacd_frontend_scripts.js'), array('jquery'), '1.0');
            wp_localize_script('acl_wooacd_forntend_scripts', 'wooacd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        }
    }

    public function enqueue_styles()
    {
        if (is_admin()) {
            wp_enqueue_style('acl_wooacd_styles', plugins_url('acl-woo-advanced-customer-dashboard/assets/css/acl_wooacd_admin_styles.css'));
        } else {
            wp_enqueue_style('acl_wooacd_styles', plugins_url('acl-woo-advanced-customer-dashboard/assets/css/acl_wooacd_frontend_styles.css'));
        }
    }
}

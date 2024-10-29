<?php

defined('ABSPATH') || exit;

class Wooacd_Dashboard_Frontend
{
    public function __construct()
    {
        $this->init();
    }
    public function init()
    {
        add_action('woocommerce_account_dashboard', array($this, 'wooacd_dashboard_content'));
    }
    public function wooacd_dashboard_content()
    {
        $banner = get_option('acl_wooacd_dashboard_content');
        echo $banner;
    }
}
new Wooacd_Dashboard_Frontend();

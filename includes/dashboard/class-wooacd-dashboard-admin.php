<?php

defined('ABSPATH') || exit;

class Wooacd_Dashboard_Admin
{
    public function __construct()
    {


        $this->init();
    }
    public function init()
    {
        add_action('admin_init', array($this,'wooacd_textarea_to_wp_editor'));
    }
    public function wooacd_textarea_to_wp_editor()
    {
        $settings = array( 'textarea_name' => 'acl_wooacd_text_block' );
        $editor_id = 'text_block';
        $content = "";
        
    }


}
new Wooacd_Dashboard_Admin();
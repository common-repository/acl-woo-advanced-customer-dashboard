<?php
class ACL_Wooacd_Info_Page
{

    function __construct()
    {
        add_action('admin_menu', array($this, 'menu_item'));
    }

    function menu_item(){
        add_submenu_page(
            'acl-wooacd',
            'Woo Advance Customer Dashboard Help & Info',
            'Help',
            'manage_options',
            'wooacd-info-page',
            array($this, 'page_content')
        );
    }
    function page_content(){
        ?>
        <style>


            .wooacd-info-page-container,.wooacd-support-container {
                padding-left: 20px;
            }

            .wooacd-info-page-headline {
                border-bottom: 1px dashed #cccccc;
                margin: 0 0 10px;
                padding: 10px 0;
            }
        </style>
        <div class="wrap">

            <div id="poststuff">

                <div id="post-body" class="metabox-holder columns-2">

                    <div id="post-body-content" style="position: relative;">


                        <div class="wooacd-info-page-container">
                            <h2 class="wooacd-info-page-headline">Help & Info</h2>
                            <!-- <p>
                                Getting started with <strong>Woo OnePage Checkout Shop</strong> instantly after installing the plugin and you may change settings on the
                                <a href="<?php //echo get_admin_url();?>admin.php?page=woo-onepage"><?php //_e('Settings','woocommerce-one-page-checkout-shop') ?></a>  page.
                            </p> -->
                            <h3>About</h3>
                            <!-- <p>Woo OnePage Checkout Shop is a one page WooCommerce Addon. For bulk orders of WooCommerce products by using our WOPCS plugin will save your customers time. You can use our Woo OnePage Checkout Shop for your Restaurant Online Order, Food Menu, Grocery Shops, Wholesale shops, Jewelry Shop etc. </p> -->


                            <h3>ShortCodes Usages:</h3>
                            <p>[wooacd-custom-request] , [wooacd-custom-request-search]</p>
                            <!-- <p>Just copy and paste this shortcode where you want to display Woocommerce one page checkout shop page.</p>

                            <h3>Shortcode Supported Parameter are : </h3>
                            <p> <strong>To Control Category (Category Id) filter : </strong> [woo-onepage-shop cat='5'] </p>
                            <p> <strong>To Control Products Per Page : </strong> [woo-onepage-shop limit='6'] </p>
                            <p> <strong>To Control Template : </strong> [woo-onepage-shop template='1'] </p>
                            <p> <strong>To Control Products Per Row (For grid view): </strong> [woo-onepage-shop columns='4'] </p>
                            <p> <strong>To Control Search Box at the top (on/off) :</strong> [woo-onepage-shop search='on'] </p>
                            <p> <strong>To Control Load More Button at the bottom (on/off) : </strong> [woo-onepage-shop loadmore='off'] </p>

                            <h4>Shortcode with all parameters as </h4>
                            <p>[woo-onepage-shop cat='5' limit='6' template='2' columns='4' search='on' loadmore='on'  ]</p> -->

                        </div>
                        <div class="wooacd-support-container">
                            <h3 style="text-align: center">Bug report, feature request or any feedback – just inbox us at</h3>
                            <p style="text-align: center;font-size: 20px;color:#0D72B2">amadercode@gmail.com</p>

                        </div>

                        <div style="padding: 15px 10px; border: 1px solid #ccc; text-align: center; margin-top: 20px;">
                           Developed By: <a href="https://www.amadercode.com" target="_blank">Web & Mobile Application Developer Company</a> -
                            AmaderCode Lab
                        </div>

                    </div>
                    <!-- /post-body-content -->


                </div>
                <!-- /post-body-->

            </div>
            <!-- /poststuff -->

        </div>
        <!-- /wrap -->

        <?php
    }
}

new ACL_Wooacd_Info_Page();
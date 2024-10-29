<div class="wrap">
    <div class="wooacd-container">
        <h1>Notifications</h1>
        <?php
        global $wpdb;
        $db_notifications = $wpdb->prefix . 'wooacd_notifications';
        $record_per_page =20;
        $page = isset($_GET['cpage']) ? wc_clean($_GET['cpage']) : 1;
        $offset = ($page * $record_per_page) - $record_per_page;
        //var_dump($offset);exit;

        $results = $wpdb->get_results("SELECT * FROM $db_notifications WHERE receiver_id IS NULL ORDER BY id DESC LIMIT $offset, $record_per_page");
        //var_dump($results);exit;
        if (!$wpdb->num_rows == 0) {            
            ?>
            <div class="wooacd-table">
                <div>
                    <div>SL</div>
                    <div>Notification Message</div>
                    <div>Action</div>
                </div>
                <?php
                $index = 0;
                foreach ($results as $row) {
                    $index++;
                    ?>
                    <div <?php if ($row->status == 0) {
                        ?> class="wooacd-table-row-default" <?php } else {
                        ?> class="wooacd-table-row-success" <?php
                    } ?>>
                        <div><?php echo $index; ?></div>
                        <div><a href="<?php if ($row->custom_cart_id) {
                                echo admin_url() . 'admin.php?page=wooacd-customer-request';

                            } else {
                                echo admin_url() . 'admin.php?page=wooacd-messages&order=' . $row->order_id .'&user=' . $row->sender_id;
                            } ?>"
                                class="notify-status"
                                nid="<?php echo $row->id; ?>">
                                <?php echo $row->notification_message; ?>
                            </a>
                        </div>
                        <div>
                            <a class="wooacd-btn wooacd-btn-success wooacd-btn-sm notify-status"
                               href="<?php if ($row->custom_cart_id) {
                                   echo admin_url() . 'admin.php?page=wooacd-customer-request';

                               } else {
                                   echo admin_url() . 'admin.php?page=wooacd-messages&order=' . $row->order_id.'&user=' . $row->sender_id;
                               } ?>"
                                nid="<?php echo $row->id; ?>">View</a>
                        </div>
                    </div>
                    <?php
                } ?>

            </div>
            <!--    awoocd-table-->
            <?php
            $resultsCount = $wpdb->get_results("SELECT * FROM $db_notifications WHERE receiver_id IS NULL ORDER BY id DESC ");
            $total_records = $wpdb->num_rows;
            $total_pages = ceil($total_records / $record_per_page);
            //var_dump($total_records);exit;
            $customPagHTML = "";
            if($total_pages > 1){
                
                $customPagHTML     =  '<div><span>Page '.$page.' of '.$total_pages.'</span>'.paginate_links( array(
                'base' => add_query_arg( 'cpage', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $total_pages,
                'current' => $page
                )).'</div>';
                }
            echo $customPagHTML;

        } else {
            echo 'You Have Not Received Any Notification!!!';
        }
        ?>
    </div>
    <!--awoocd-container-->
</div>
<!--wrap-->
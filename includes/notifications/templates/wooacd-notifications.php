<h2>Notifications</h2>
<?php
global $wpdb;
$db_notifications = $wpdb->prefix . 'wooacd_notifications';
$record_per_page =20;
$page = isset($_GET['cpage']) ? wc_clean($_GET['cpage']) : 1;
$offset = ($page * $record_per_page) - $record_per_page;
$id = get_current_user_id();
$results = $wpdb->get_results("SELECT * FROM $db_notifications WHERE receiver_id = $id ORDER BY id DESC LIMIT $offset, $record_per_page");
//var_dump($results);exit;
if(!$wpdb->num_rows==0){
?>
<div class="wooacd-table">        
        <div>
            <div>#</div>
            <div>Notification Message</div>
            <div>Action</div>
        </div>

        <?php
$index = 0;
foreach ($results as $row) {
    //echo $row->notification_for;exit;
    $index++;
    ?>
        <div <?php if($row->status == 0 ){
            ?> style="color:blue" 
        <?php }else{
            ?>
            style="color:red" <?php
        } ?>>
            <div><?php echo $index; ?></div>
            <div><?php echo $row->notification_message; ?></div>
            <div><a href="<?php

if ($row->notification_for == 2) {
        echo wc_get_endpoint_url('view-order', $row->order_id, wc_get_page_permalink('myaccount'));
    } elseif($row->notification_for == 1) {
        echo wc_get_endpoint_url('messages?order=' . $row->order_id, '', wc_get_page_permalink('myaccount'));
    }elseif($row->notification_for == 3){
       echo wc_get_account_endpoint_url('custom_cart');
    }?>" class="notify-status" nid="<?php echo $row->id; ?>">View</a></div>
        </div>
        <?php }?>
    </tbody>
</div>
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
}else{
    echo 'You Have Not Received Any Notification!!!';
}

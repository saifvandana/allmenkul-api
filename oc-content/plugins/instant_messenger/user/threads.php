<link href="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/css/tipped.css" rel="stylesheet" type="text/css" />
<script src="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/js/tipped.js"></script>
<script src="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/js/user.js?v=<?php echo date('Ymdhis'); ?>"></script>

<?php 
  // REDIRECT LOGGED OUT USER
  if(!osc_is_web_user_logged_in()) {
    osc_add_flash_error_message(__('Please log in to see your conversations.', 'instant_messenger'));
    header('Location:' . osc_user_login_url()); 
    exit;
  }


  // CHECK FOR NEW BAN
  if( Params::getParam('action') <> '' && Params::getParam('action') == 'block_email' && Params::getParam('block-email') <> '' ){
    $email = base64_decode(Params::getParam('block-email'));
    $check_block = ModelIM::newInstance()->checkUserBlocks(osc_logged_user_id(), $email);

    if(isset($check_block) && $check_block['i_user_id'] == osc_logged_user_id()) {
      osc_add_flash_error_message( __('This user is already blocked', 'instant_messenger') );
    } else {
      ModelIM::newInstance()->insertUserBlock( osc_logged_user_id(), $email);
      osc_add_flash_ok_message( __('User with email', 'instant_messenger') . ' ' . im_mask_email($email) . ' ' . __('has been block and will not be able to message you anymore.', 'instant_messenger') );
    }

    header('Location: ' . osc_route_url( 'im-threads'));
  }


  // REMOVE BAN
  if( Params::getParam('remove-id') > 0){
    ModelIM::newInstance()->removeUserBlock( Params::getParam('remove-id') );
    osc_add_flash_ok_message( __('User block removed.', 'instant_messenger') );
    header('Location: ' . osc_route_url( 'im-threads'));
  }


  // CHANGE FLAG ON THREAD
  if( Params::getParam('thread-flag-id') <> '' && Params::getParam('thread-flag-id') > 0 ){
    ModelIM::newInstance()->updateThreadFlag( Params::getParam('thread-flag-id') );
    osc_add_flash_ok_message( __('Thread flag has been updated', 'instant_messenger') );
    header('Location: ' . osc_route_url( 'im-threads'));
  }


  // CHANGE NOTIFICATION OF CURRENT USER FOR SELECTED THREAD
  if( Params::getParam('thread-notify-id') <> '' && Params::getParam('thread-notify-id') > 0 ){
    ModelIM::newInstance()->updateThreadNotify( Params::getParam('thread-notify-id'), osc_logged_user_id() );
    osc_add_flash_ok_message( __('Thread notification has been updated', 'instant_messenger') );
    header('Location: ' . osc_route_url( 'im-threads'));
  }

  $threads_count = ModelIM::newInstance()->countThreadsByUserId(osc_logged_user_id());
  $threads_count = $threads_count['i_count'];

  $per_page = (im_param('threads_per_page') <> '' ? im_param('threads_per_page') : 20);
  $page_id = ( Params::getParam('page-id') <> '' ? Params::getParam('page-id') : 1 );
  $page_count = ceil($threads_count/$per_page);
  $start = ($page_id - 1) * $per_page;

  $threads = ModelIM::newInstance()->getThreadsByUserId( osc_logged_user_id(), $per_page, $start ); 
?>

<div class="im-html im-file-threads im-theme-<?php echo osc_current_web_theme(); ?>">
  <div class="im-table im-threads im-body">
    <div class="im-alt-head" style="display:none;">
      <div class="im-head2"><?php _e('Messages', 'instant_messenger'); ?></div>
      <div class="im-subhead2"><?php echo sprintf(__('You have %s threads in total', 'instant_messenger'), $threads_count); ?></div>
    </div>

    <?php if(is_array($threads) && count($threads) > 0) { ?>
      <div class="im-table-head">
        <div class="im-col-12 im-align-left c1"><strong><?php _e('Thread', 'instant_messenger'); ?></strong></div>
        <div class="im-col-6 c2">&nbsp;</div>
        <div class="im-col-2 c3"><?php _e('Messages', 'instant_messenger'); ?></div>
        <div class="im-col-2 c4"><?php _e('Flag', 'instant_messenger'); ?></div>
        <div class="im-col-2 c5"><?php _e('Notify', 'instant_messenger'); ?></div>
      </div>

      <?php foreach($threads as $t) { ?>
        <?php 
          $item = Item::newInstance()->findByPrimaryKey( $t['fk_i_item_id'] ); 
          $time_diff = im_get_time_diff( $t['d_datetime'] );

          // check if last message was read
          // checking last message received by this user
          $is_read = ModelIM::newInstance()->getThreadIsRead( $t['i_thread_id'], osc_logged_user_id() );
          $is_read = $is_read['i_read'];

          // check notifications for thread
          if($t['i_from_user_id'] == osc_logged_user_id()) {
            $notify = $t['i_from_user_notify'];
          } else {
            $notify = $t['i_to_user_notify'];
          }


          if($t['i_from_user_id'] == osc_logged_user_id()) {
            $u_id = $t['i_to_user_id'];
            $u_mail = $t['s_to_user_email'];
            $other_id = $t['i_from_user_id'];
            $other_email = $t['s_from_user_email'];
          } else {
            $u_id = $t['i_from_user_id'];
            $u_mail = $t['s_from_user_email'];
            $other_id = $t['i_to_user_id'];
            $other_email = $t['s_to_user_email'];
          }

          $img = im_profile_img_url($u_id);
          $def_img = osc_base_url() . 'oc-content/plugins/instant_messenger/img/new-profile-default.png';


          $check_block = ModelIM::newInstance()->checkUserBlocks(osc_logged_user_id(), $u_mail);

          if(isset($check_block['i_user_id']) && $check_block['i_user_id'] == osc_logged_user_id()) {
            $ban_class = 'active';
            $ban_title = osc_esc_html(__('User is already blocked', 'instant_messenger'));
          } else {
            $ban_class = '';
            $ban_title = osc_esc_html(__('Block user', 'instant_messenger'));
          }

          $offer = im_get_offer($t['i_offer_id']);

          $ban_url = osc_route_url('im-ban', array('action' => 'block_email', 'block-email' => base64_encode($u_mail)));
          
          if($offer) {
            if($offer['fk_i_item_id'] == $t['fk_i_item_id']) {
              $offer_item = $item;
            } else {
              $offer_item = Item::newInstance()->findByPrimaryKey($offer['fk_i_item_id']);
            }

            $currency = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);

            $t_title = sprintf(__('New offer on %s - %s', 'instant_messenger'), osc_highlight($item['s_title'], 50), $offer['i_price']/1000000 . $currency['s_description']);
          } else if($t['s_title'] <> '') {
            $t_title = osc_highlight($t['s_title'], 60);
          } else {
            $t_title =  __('No subject', 'instant_messenger');
          }
        ?>


        <div class="im-table-row im-has-tooltip<?php if($is_read == 0) { ?> im-unread<?php } ?><?php if($offer) { ?> im-is-offer<?php } ?>" <?php if($is_read == 0) { ?>title="<?php _e('There are unread messages in this thread!', 'instant_messenger'); ?>"<?php } ?>>
          <div class="im-col-12 no-img">
            <?php if($offer) { ?>
              <div class="im-profile-img im-is-offer"><?php _e('New offer', 'instant_messenger'); ?></div>
            <?php } else if($img <> '') { ?>
              <img class="im-profile-img" src="<?php echo $img; ?>"/>
            <?php } else { ?>
              <img class="im-profile-img im-img-def" src="<?php echo $def_img; ?>"/>
            <?php } ?>

            <div class="im-line im-align-left im-title">
              <a class="im-mes-title mbCl" href="<?php echo osc_route_url( 'im-messages', array('thread-id' => $t['i_thread_id'], 'secret' => 'n') ); ?>"><?php echo $t_title; ?></a>
              <a href="<?php echo $ban_url; ?>" class="im-block-thread <?php echo $ban_class; ?> im-has-tooltip" title="<?php echo $ban_title; ?>"><i class="fa fa-ban"></i></a>
            </div>
            <div class="im-line im-align-left im-item"><a target="_blank" href="<?php echo osc_item_url_from_item( $item ); ?>"><?php echo osc_highlight($item['s_title'], 50); ?></a></div>
          </div>
   
          <div class="im-col-6 im-from-to im-align-right">
            <div class="im-line im-align-left im-title"><?php echo ($t['i_from_user_id'] == osc_logged_user_id() ? __('to', 'instant_messenger') . ' <strong>' . $t['s_to_user_name'] . '</strong>' : __('from', 'instant_messenger') . ' <strong>' . $t['s_from_user_name'] . '</strong>'); ?></div>
            <div class="im-line im-align-left im-time"><?php echo $time_diff; ?></div>
          </div>

          <div class="im-col-2 im-special">
            <?php echo $t['i_count']; ?> <?php echo __('pm', 'instant_messenger') . '<span class="im-small">' . ($t['i_count'] == 1 ? '' : __('s', 'instant_messenger')) . '</span>'; ?>
          </div>

          <div class="im-col-2 im-special im-has-tooltip <?php echo ( $t['i_flag'] == 1 ? 'im-flag-yes' : 'im-flag-no' ); ?>" title="<?php echo ( $t['i_flag'] == 1 ? __('You have marked this message as important. Click to unmark.', 'instant_messenger') : __('Click to mark.', 'instant_messenger') ); ?>">
            <a href="<?php echo osc_route_url( 'im-thread-flag', array('thread-flag-id' => $t['i_thread_id']) ); ?>"><i class="fa <?php echo ( $t['i_flag'] == 1 ? 'fa-flag' : 'fa-flag-o' ); ?>"></i></a>
          </div>

          <div class="im-col-2 im-special im-has-tooltip <?php echo ( $notify == 1 ? 'im-notify-yes' : 'im-notify-no' ); ?>" title="<?php echo ( $notify == 1 ? __('You will receive email when there is new message in this thread. Click to cancel notifications.', 'instant_messenger') : __('Click to enable notifications.', 'instant_messenger') ); ?>">
            <a href="<?php echo osc_route_url( 'im-thread-notify', array('thread-notify-id' => $t['i_thread_id']) ); ?>"><i class="fa <?php echo ( $notify == 1 ? 'fa-envelope' : 'fa-envelope-o' ); ?>"></i></a>
          </div>
        </div>
      <?php } ?>


      <!-- Pagination -->
      <?php echo im_paginate($page_id, $per_page, $threads_count); ?>


      <?php if($page_count > 1 && 1==2) { ?>
        <div id="im-pagination">
          <div class="im-pagination-inside">
            <span><?php _e('Page', 'instant_messenger'); ?></span>
            <?php for($i = 1; $i <= $page_count; $i++) { ?>
              <a <?php if($i == $page_id) { ?>class="im-page-active"<?php } ?> href="<?php echo osc_route_url( 'im-thread-page', array('page-id' => $i) ); ?>"><?php echo $i; ?></a>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

    <?php } else { ?>
      <div class="im-empty-v2"><?php _e('You do not have any conversations yet', 'instant_messenger'); ?></div>
    <?php } ?>
  </div>


  <?php require_once 'block.php'; ?>
</div>
<link href="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/css/tipped.css" rel="stylesheet" type="text/css" />
<script src="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/js/tipped.js"></script>
<script src="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/js/user.js?v=<?php echo date('Ymdhis'); ?>"></script>


<?php 
  if(im_param('only_logged') == 1 && !osc_is_web_user_logged_in()) {
    $item = Item::newInstance()->findByPrimaryKey(Params::getParam('item-id'));
    osc_add_flash_error_message( __('Please login, only authenticated users can send instant messages.', 'instant_messenger'));
    header('Location:' . osc_item_url_from_item($item));
    exit;
  }

  if( Params::getParam('item-id') <> '' && Params::getParam('item-id') > 0 ) {
    $item_id = Params::getParam('item-id');
    $item = Item::newInstance()->findByPrimaryKey( $item_id );
    $item_details = im_get_item_details( $item_id );


    $title = Params::getParam('im-title');

    $from_user_id = (osc_is_web_user_logged_in() ? osc_logged_user_id() : null);
    $from_user_name = ( Params::getParam('im-from-user-name') <> '' ? Params::getParam('im-from-user-name') : osc_logged_user_name() );
    $from_user_email = ( Params::getParam('im-from-user-email') ? Params::getParam('im-from-user-email') : osc_logged_user_email() );

    $to_user_id = $item['fk_i_user_id'];
    $to_user_name = $item['s_contact_name'];
    $to_user_email = $item['s_contact_email'];



    // MESSAGE SENT TO USER
    if(Params::getParam('im-action') == 'create_thread') {


      // CHECK FOR BLOCK
      if(im_check_block($to_user_id, $from_user_email) == 0) {
        //osc_add_flash_error_message( __('You cannot message this user. This user has blocked communication with you.', 'instant_messenger'));
        header('Location: ' . osc_route_url( 'im-threads'));
        return;
      }

      $thread_id = ModelIM::newInstance()->createThread( $item_id, $from_user_id, $from_user_name, $from_user_email, $to_user_id, $to_user_name, $to_user_email, $title, 0);
      $thread = ModelIM::newInstance()->getThreadById( $thread_id ); 

      im_insert_message($thread['i_thread_id'], nl2br(htmlspecialchars(Params::getParam('im-message', false, false))), 0, Params::getFiles('im-file') );
    }
  }

?>

<div class="im-html im-file-create-thread im-theme-<?php echo osc_current_web_theme(); ?>">
  <?php if(osc_is_web_user_logged_in() && $item['fk_i_user_id'] <> osc_logged_user_id() || !osc_is_web_user_logged_in()) { ?>
    <h2 class="im-head"><?php _e('Start conversation', 'instant_messenger'); ?></h2>

    <div class="im-row im-item-related im-body">
      <div class="im-col-3 im-item-resource"><img src="<?php echo $item_details['resource']; ?>" /></div>
      <div class="im-col-21">
        <div class="im-line im-item-title"><a target="_blank" href="<?php echo osc_item_url_from_item( $item ); ?>"><?php echo osc_highlight($item['s_title'], 50); ?></a></div>
        <div class="im-line im-item-price"><?php echo $item_details['price']; ?></div>
        <div class="im-line im-item-location"><?php echo $item_details['location']; ?></div>
      </div>
    </div>

    <ul id="im-error-list" class="error-list im-error-list im-body"></ul>

    <form id="im-create-thread-form" name="im-create-thread-form" class="im-body im-form-validate" action="<?php echo osc_route_url( 'im-create-thread', array('item-id' => $item_id) ); ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="im-action" id="im-action" value="create_thread">

      <div class="im-row">
        <div class="im-col-24">
          <label class="im-label" for="im-from-user-name"><?php _e('Your name', 'instant_messenger'); ?></label>
          <input type="text" class="im-input" name="im-from-user-name" id="im-from-user-name" value="<?php echo osc_esc_html(osc_logged_user_name()); ?>" />
        </div>
      </div>

      <div class="im-row">
        <div class="im-col-24">
          <label class="im-label" for="im-from-user-email"><?php _e('Your email', 'instant_messenger'); ?></label>
          <input type="text" class="im-input" name="im-from-user-email" id="im-from-user-name" value="<?php echo osc_esc_html(osc_logged_user_email()); ?>" />
        </div>
      </div>

      <div class="im-row">
        <div class="im-col-24">
          <label class="im-label" for="im-title"><?php _e('Title', 'instant_messenger'); ?></label>
          <input type="text" class="im-input im-big" name="im-title" id="im-title" placeholder="<?php echo osc_esc_html(__('Message title', 'instant_messenger')); ?>" value="" />
        </div>
      </div>

      <div class="im-row">
        <div class="im-col-24">
          <label class="im-label" for="im-message"><?php _e('Message', 'instant_messenger'); ?></label>
          <textarea name="im-message" id="im-message" class="im-textarea" placeholder="<?php echo osc_esc_html(__('Write all details here', 'instant_messenger')); ?>"></textarea>
        </div>
      </div>

      <button type="submit" class="im-button-green"><?php _e('Send message', 'instant_messenger'); ?></button>

      <?php if(im_param('att_enable') == 1) { ?>
        <div class="im-attachment">
          <div class="im-att-box">
            <label class="im-status">
              <span class="im-wrap"><i class="fa fa-paperclip"></i> <span><?php _e('Upload file', 'instant_messenger'); ?></span></span>
              <input type="file" name="im-file" id="im-file" class="im-file" />
            </label>
          </div>
        </div>
      <?php } ?>
    </form>

    <?php $threads = ModelIM::newInstance()->getThreadsByItemId( $item_id, osc_logged_user_id() ); ?>

    <?php if(count($threads) > 0) { ?>
      <div class="im-threads-exist im-body">
        <h3 class="im-head"><?php _e('You have already contacted seller on this listing, you may want to continue in existing conversation', 'instant_messenger'); ?></h3>

        <?php foreach($threads as $t) { ?>
          <?php $time_diff = im_get_time_diff( $t['d_datetime'] ); ?>

          <a class="im-row im-has-tooltip-left" href="<?php echo osc_route_url( 'im-messages', array('thread-id' => $t['i_thread_id'], 'secret' => 'n') ); ?>" title="<?php _e('Open conversation', 'instant_messenger'); ?>">
            <div class="im-col-12 im-b im-title"><?php echo ( $t['s_title'] <> '' ? osc_highlight($t['s_title'], 40) : __('No subject', 'instant_messenger') ); ?></div>
            <div class="im-col-4 im-from-to"><?php echo ($t['i_from_user_id'] == osc_logged_user_id() ? __('to', 'instant_messenger') : __('from', 'instant_messenger')); ?> <strong><?php echo $t['s_to_user_name']; ?></strong></div>
            <div class="im-col-4 im-pms im-align-center"><?php echo $t['i_count'] . ' ' . ( $t['i_count'] == 1 ? __('pm', 'instant_messenger') : __('pms', 'instant_messenger') ); ?></div>
            <div class="im-col-4 im-time im-align-right"><?php echo $time_diff; ?></div>
          </a>
        <?php } ?>
      </div>
    <?php } ?>

  <?php } else { ?>
    <div class="im-empty flashmessage flashmessage-warning"><?php _e('You cannot contact yourself', 'instant_messenger'); ?></div>
  <?php } ?>
</div>

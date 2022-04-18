<link href="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/css/tipped.css" rel="stylesheet" type="text/css" />
<script src="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/js/tipped.js"></script>
<script src="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/js/user.js?v=<?php echo date('Ymdhis'); ?>"></script>

<?php 
  $secret = Params::getParam('secret');
  $att_enable = im_param('att_enable');
  $message_delete = im_param('message_delete');

  $ajax = (im_param('ajax') <> '' ? im_param('ajax') : 1);
  $interval = (im_param('interval') <> '' ? im_param('interval') : 3000);   // miliseconds refresh
  $is_chat_refresh = (Params::getParam('imaction') == 'refresh' ? true : false); 

  $thread = ModelIM::newInstance()->getThreadById( Params::getParam('thread-id') ); 
  $item = Item::newInstance()->findByPrimaryKey( $thread['fk_i_item_id'] ); 

  $item_details = im_get_item_details( $thread['fk_i_item_id'] );


  // Message types: 0 - FROM user send message to TO user, 1 - TO user send message to FROM user
  if( (osc_is_web_user_logged_in() && osc_logged_user_id() == $thread['i_from_user_id']) || $secret == $thread['s_from_secret']) {
    $type = 0;
  } else {
    $type = 1;
  }


  // GET TARGET USER NAME FROM THREAD
  if(($thread['i_from_user_id'] == osc_logged_user_id() && osc_is_web_user_logged_in()) || ($secret == $thread['s_from_secret'])) {
    $thread_target_name = $thread['s_to_user_name']; 
    $thread_target_id = $thread['i_to_user_id']; 
  } else {
    $thread_target_name = $thread['s_from_user_name']; 
    $thread_target_id = $thread['i_from_user_id']; 
  }

  $target_user = User::newInstance()->findByPrimaryKey($thread_target_id);

  $last_seen = '';

  if(@$target_user['dt_access_date'] <> '') {
    $last_seen = im_get_time_diff($target_user['dt_access_date']);
  }



  // MARK AS VIEWED FOR THIS USER
  $is_read = ModelIM::newInstance()->getThreadIsRead( $thread['i_thread_id'], osc_logged_user_id(), $secret );
 
  if( $is_read['pk_i_id'] <> '' && $is_read['pk_i_id'] > 0 && $is_read['i_read'] == 0 ) {
    ModelIM::newInstance()->updateMessagesRead( $thread['i_thread_id'], ($type*(-1) + 1) );
  }


  $offer = im_get_offer($thread['i_offer_id']);

  if($offer) {
    if($offer['fk_i_item_id'] == $thread['fk_i_item_id']) {
      $offer_item = $item;
    } else {
      $offer_item = Item::newInstance()->findByPrimaryKey($offer['fk_i_item_id']);
    }

    $currency = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);

    $t_title = sprintf(__('New offer on %s - %s', 'instant_messenger'), osc_highlight($item['s_title'], 50), $offer['i_price']/1000000 . $currency['s_description']);
  } else if($thread['s_title'] <> '') {
    $t_title = osc_highlight($thread['s_title'], 60);
  } else {
    $t_title =  __('No subject', 'instant_messenger');
  }



  // MESSAGE SENT TO USER
  if(Params::getParam('im-action') == 'send_message') {
    im_insert_message($thread['i_thread_id'], nl2br(htmlspecialchars(Params::getParam('im-message', false, false))), $type, Params::getFiles('im-file'));
  }


  // DELETE MESSAGE
  if( Params::getParam('del-message-id') <> '' && Params::getParam('del-message-id') > 0 && $message_delete == 1 ) {
    $del_message = ModelIM::newInstance()->getMessageById( Params::getParam('del-message-id') );

    if( $del_message['fk_i_thread_id'] == $thread['i_thread_id'] && $del_message['i_type'] == $type ) {
      ModelIM::newInstance()->deleteMessageById( Params::getParam('del-message-id') );
      osc_add_flash_ok_message( __('Message removed', 'instant_messenger') );

      header('Location: ' . osc_route_url( 'im-messages', array('thread-id' => $del_message['fk_i_thread_id'], 'secret' => $secret)));
    } else {
      osc_add_flash_error_message( __('This is not your message, you cannot remove it!', 'instant_messenger') );
    }

  }


  // DELETE ATTACHMENT
  if(  Params::getParam('del-att-message-id') <> '' && Params::getParam('del-att-message-id') > 0 && Params::getParam('del-file-name') <> '' ) {
    $del_message = ModelIM::newInstance()->getMessageById( Params::getParam('del-att-message-id') );

    if( $del_message['fk_i_thread_id'] == $thread['i_thread_id'] && $del_message['i_type'] == $type ) {
      @unlink( osc_base_path() . 'oc-content/plugins/instant_messenger/download/' . Params::getParam('del-file-name') );
      ModelIM::newInstance()->deleteMessageAttachment( Params::getParam('del-att-message-id') );
      osc_add_flash_ok_message( __('Attachment removed', 'instant_messenger') );

      header('Location: ' . osc_route_url( 'im-messages', array('thread-id' => $del_message['fk_i_thread_id'], 'secret' => $secret)));
    } else {
      osc_add_flash_error_message( __('This is not your message, you cannot remove attachment on it!', 'instant_messenger') );
    }
  }


  $messages = ModelIM::newInstance()->getMessagesByThreadId( Params::getParam('thread-id') ); 
?>


<div class="im-html im-file-messages im-theme-<?php echo osc_current_web_theme(); ?>">
  <?php if( (($thread['i_from_user_id'] == osc_logged_user_id() || $thread['i_to_user_id'] == osc_logged_user_id()) && osc_is_web_user_logged_in()) || ($secret == $thread['s_from_secret'] || $secret == $thread['s_to_secret'])) { ?>

    <h2 class="im-head"><?php echo $t_title; ?></h2>

    <div class="im-alt-head" style="display:none;">
      <div class="im-head2"><?php echo $thread_target_name; ?> - <?php echo $t_title; ?></div>
      <?php if($last_seen <> '') { ?>
        <div class="im-subhead2"><?php echo sprintf(__('Last online %s', 'instant_messenger'), $last_seen); ?></div>
      <?php } ?>
    </div>
      
    <div class="im-row im-item-related im-body">
      <div class="im-col-3 im-item-resource"><a target="_blank" href="<?php echo osc_item_url_ns( $item['pk_i_id'] ); ?>"><img src="<?php echo $item_details['resource']; ?>" /></a></div>
      <div class="im-col-21">
        <div class="im-line im-item-title"><a target="_blank" href="<?php echo osc_item_url_ns( $item['pk_i_id'] ); ?>"><?php echo osc_highlight($item['s_title'], 50); ?></a></div>
        <div class="im-line im-item-price"><?php echo $item_details['price']; ?></div>
        <div class="im-line im-item-location"><?php echo $item_details['location']; ?></div>
      </div>
    </div>

    <?php if($offer) { ?>
      <a href="<?php echo osc_route_url('mo-show-offers', array('offerId' => $thread['i_offer_id'])); ?>" class="im-row im-body im-offer">
        <div class="im-line"><?php echo sprintf(__('Offer: %sx %s for %s%s', 'instant_messenger'), $offer['i_quantity'], $offer_item['s_title'], $offer['i_price']/1000000, $currency['s_description']); ?></div>
        <div class="im-line"><?php _e('Click to view offer', 'instant_messenger'); ?> <i class="fa fa-angle-double-right"></i></div>
      </a>
    <?php } ?>


    <ul id="im-error-list" class="error-list im-error-list im-body"></ul>


    <?php if( count($messages) > 0 ) { ?>
      <div class="im-table im-messages im-body">
        <div class="im-vertical">
          <span class="top"></span>
          <span class="bot"></span>
        </div>

        <?php $i = 1; ?>
        <?php $show_last = 10; ?>

        <?php if(count($messages) > $show_last) { ?>
          <div class="im-show-older"><span><?php _e('Show older messages', 'instant_messenger'); ?></span></div>
        <?php } ?>

        <?php foreach($messages as $m) { ?>
          <?php 
            // CHECK IF LOGGED USER IS OWNER OF THIS MESSAGE
            if( (osc_is_web_user_logged_in() && (osc_logged_user_id() == $thread['i_from_user_id'] && $m['i_type'] == 0 || osc_logged_user_id() == $thread['i_to_user_id'] && $m['i_type'] == 1)) || ($secret == $thread['s_from_secret'] && $m['i_type'] == 0 || $secret == $thread['s_to_secret'] && $m['i_type'] == 1) ) {
              $logged_is_owner = true;
            } else {
              $logged_is_owner = false;
              
              if($m['i_type'] == 0) {
                $identify_name = __('customer', 'instant_messenger');
              } else {
                $identify_name = __('seller', 'instant_messenger');
              }
            } 

            if($thread['i_from_user_id'] == osc_logged_user_id()) {
              $u_id = $thread['i_to_user_id'];
            } else {
              $u_id = $thread['i_from_user_id'];
            }

            if($logged_is_owner) {
              $def_img = osc_base_url() . 'oc-content/plugins/instant_messenger/img/new-profile-default.png';
              $u_id = osc_logged_user_id();
            } else {
              $def_img = osc_base_url() . 'oc-content/plugins/instant_messenger/img/new-profile-default.png';
              $u_id = ($thread['i_from_user_id'] == osc_logged_user_id() ? $thread['i_to_user_id'] : $thread['i_from_user_id']);
            }

            $img = im_profile_img_url($u_id);
          ?>

          <div class="im-table-row<?php if($logged_is_owner) { ?> im-from<?php } ?><?php if(count($messages) - $i >= $show_last) { ?> hidden<?php } ?>" data-message-id="<?php echo $m['pk_i_id']; ?>">
            <div class="im-horizontal">
              <span class="left"></span>
              <span class="right"><img src="<?php echo ($img <> '' ? $img : $def_img); ?>" title="<?php if( $m['i_type'] == 0 ) { echo osc_esc_html($thread['s_from_user_name']); } else { echo osc_esc_html($thread['s_to_user_name']); } ?>"></span>
            </div>

            <div class="im-line im-name-top">
              <div class="im-col-12 im-name im-align-left">
                <strong><?php if( $m['i_type'] == 0 ) { echo $thread['s_from_user_name']; } else { echo $thread['s_to_user_name']; } ?></strong> 
                <span class="im-identifier"><?php if( $logged_is_owner ) { ?><?php _e('you', 'instant_messenger'); ?><?php } else { ?><?php echo $identify_name; ?><?php } ?></span>
              </div>
              <div class="im-col-12 im-date im-align-right im-i im-gray" title="<?php echo date('d/m/Y H:i:s', strtotime($m['d_datetime'])); ?>">
                <span><?php echo im_get_time_diff($m['d_datetime']); ?></span>

                <?php if($m['i_read'] == 1) { ?>
                  <i class="fa fa-check im-has-tooltip" title="<?php echo osc_esc_html(sprintf(__('%s has already read this message', 'instant_messenger'), ($m['i_type'] == 1 ? $thread['s_from_user_name'] : $thread['s_to_user_name']))); ?>"></i> 
                <?php } ?>
              </div>
            </div>

            <div class="im-line im-message-content">
              <div class="im-col-24 im-align-left"><?php echo $m['s_message']; ?></div>
            </div>

            <div class="im-line im-message-extra <?php if($m['s_file'] <> '' && $att_enable == 1) { ?>im-box-gray<?php } else { ?>im-box-empty<?php } ?>">
              <div class="im-col-10" class="im-align-left">
                <?php if($m['s_file'] <> '' && $att_enable == 1) { ?>
                  <a class="im-download" href="<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/download/<?php echo $m['s_file']; ?>" target="_blank">
                    <?php echo im_get_extension_icon($m['s_file']); ?>
                    <i class="fa fa-download" style="display:none;"></i>
                    <?php _e('Attachment', 'instant_messenger'); ?>
                  </a>
                <?php } ?>
              </div>

              <?php if( $logged_is_owner ) {?>
                <div class="im-col-14 im-align-right">
                  <?php if($m['s_file'] <> '' && $att_enable == 1) { ?>
                    <a class="im-hide" href="<?php echo osc_route_url( 'im-delete-attachment', array('thread-id' => $thread['i_thread_id'], 'del-att-message-id' => $m['pk_i_id'], 'del-file-name' => $m['s_file'], 'secret' => $secret) ); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to delete attachment', 'instant_messenger')); ?>?')"><span><?php _e('Remove file', 'instant_messenger'); ?></span><i class="fa fa-trash" style="display:none;"></i></a>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>


            <?php if( $logged_is_owner && $message_delete == 1) {?>
              <div class="im-del-mes-box">
                <a href="<?php echo osc_route_url( 'im-delete-message', array('thread-id' => $thread['i_thread_id'], 'del-message-id' => $m['pk_i_id'], 'secret' => $secret) ); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to delete this message', 'instant_messenger')); ?>?')"><i class="fa fa-trash"></i></a>
              </div>
            <?php } ?>
          </div>

          <?php $i++; ?>
        <?php } ?>
      </div>
    <?php } else { ?>
      <div class="im-empty flashmessage flashmessage-warning"><?php _e('You do not have any messages', 'instant_messenger'); ?></div>
    <?php } ?>


    <form id="im-message-form" class="im-row im-body im-form-validate" action="<?php echo osc_route_url( 'im-messages', array('thread-id' => $thread['i_thread_id'], 'secret' => $secret) ); ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="im-action" id="im-action" value="send_message" />
     
      <textarea name="im-message" id="im-message" class="im-textarea" placeholder="<?php echo osc_esc_js(__('Write your message here...', 'instant_messenger')); ?>" required></textarea>

      <button type="submit" class="im-button-green"><?php _e('Send message', 'instant_messenger'); ?></button>
      <button type="submit" class="im-button-green im-button-alt" style="display:none;"><i class="fa fa-paper-plane"></i></button>

      <?php if($att_enable == 1) { ?>
        <div class="im-attachment">
          <div class="im-att-box">
            <label class="im-status">
              <span class="im-wrap"><i class="fa fa-paperclip"></i> <span><span class="im-def-text"><?php _e('Upload file', 'instant_messenger'); ?></span></span></span>
              <input type="file" name="im-file" id="im-file" class="im-file" />
            </label>
          </div>
        </div>
      <?php } ?>
    </form>

  <?php } else { ?>
    <div class="im-empty flashmessage flashmessage-warning"><?php _e('This is not your thread, you cannot read communication of other users!', 'instant_messenger'); ?></div>
  <?php } ?>
</div>


<?php
  $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<script>
//var imMessageUrl = "<?php echo $actual_link; ?>";
var imMessageUrl = "<?php echo osc_route_url('im-refresh-messages', array('thread-id' => Params::getParam('thread-id'), 'secret' => (Params::getParam('secret') <> '' ? Params::getParam('secret') : 'n'), 'imaction' => 'refresh')); ?>";


var imShowOlder = 0;
var imAjax = <?php echo $ajax; ?>;

$(document).ready(function() {
  $('body').click();

  // SHOW HIDDEN
  $('body').on('click', '.im-show-older', function(e){
    e.preventDefault();
    imShowOlderMessages();
  });

  // SUBMIT MESSAGE
  $('body').on('click', '#im-message-form button', function(e){
    var button = $(this);
    var form = $(this).closest('form');
    var inputs = form.find('input, select, textarea');

    // Validate form first
    inputs.each(function(){
      form.validate().element($(this));
    });


    if((form.find('input[name="im-file"]').val() == '' || !form.find('input[name="im-file"]').length) && imAjax == 1) {
      if(form.valid()) {
        e.preventDefault();
        button.addClass('btn-loading').attr('disabled', true);

        $.ajax({
          url: form.attr('action'),
          type: "POST",
          data: form.find(':input[value!=""]').serialize(),
          success: function(response){
            //console.log('Message sent!');

            imRefreshMessages();
            imClearForm();

            button.removeClass('btn-loading').attr('disabled', false);

          }
        });
      }
    } else {
      // submit form with file
    }

  });


  // REFRESH MESSAGES
  if(imAjax == 1) {
    setInterval(function(){
      imRefreshMessages();
    }, <?php echo $interval; ?>);
  }


  // TURN OFF NOTIFICATION
  $(window).on('blur focus click', function() {
    PageTitleNotification.Off();
  });
});


// REFRESH MESSAGES
function imRefreshMessages() {
  $.ajax({
    url: imMessageUrl,
    type: "GET",
    success: function(response){
      //console.log('Messages loaded');

      if(response.length) {
        var content = $(response).contents().find('.im-table.im-messages').html();
        var messagesCount = $(response).contents().find('.im-table.im-messages .im-table-row').length;
        var lastMessageId = $('.im-table.im-messages .im-table-row:last-child').attr('data-message-id');

        if(
          messagesCount != $('.im-table.im-messages .im-table-row').length
          || (!$('.im-table.im-messages .im-table-row:last-child .im-date .fa-check').length && $(response).contents().find('.im-table.im-messages .im-table-row:last-child .im-date .fa-check').length)
        ) {
          $('.im-table.im-messages').html(content).animate({ scrollTop: $('.im-table.im-messages').prop("scrollHeight")}, 200);


          // IF USER SEEING OLDER MESSAGES, DO NOT COLLAPSE THEM
          if(imShowOlder == 1) {
            imShowOlderMessages();
          }

          // IF THERE IS NEW MESSAGE AND IT'S NOT FROM SENDER
          if(!$('.im-table.im-messages .im-table-row:last-child').hasClass('im-from') && $('.im-table.im-messages .im-table-row:last-child').attr('data-message-id') != lastMessageId) {
            imPlayBeep();

            PageTitleNotification.On('<?php echo osc_esc_js(__('You have new message!', 'instant_messenger')); ?>');

            setTimeout(function(){
              //PageTitleNotification.Off();
            }, 3000);
          }
        }
      }

    }, error: function(response) {
      console.log('Error: Messages not loaded');
    }
  });
}


// CLEAR FORM WHEN MESSAGE IS SENT
function imClearForm() {
  $('#im-message-form textarea[name="im-message"], #im-message-form input[name="im-file"]').val('');
}


// PLAY BEEP SOUND ON NEW MESSAGE
function imPlayBeep() {
  var obj = document.createElement("audio");
  obj.src= "<?php echo osc_base_url(); ?>oc-content/plugins/instant_messenger/audio/beep.mp3";
  obj.volume=0.30;
  obj.autoPlay=false;
  obj.preLoad=true;       
  obj.play();
  obj.remove();
}


// BROWSER BAR NOTIFICATION
var PageTitleNotification = {
  Vars:{
    OriginalTitle: document.title,
    Interval: null
  },  
  On: function(notification, intervalSpeed){
    var _this = this;
    _this.Vars.Interval = setInterval(function(){
      document.title = (_this.Vars.OriginalTitle == document.title) ? notification : _this.Vars.OriginalTitle;
    }, (intervalSpeed) ? intervalSpeed : 1000);
  },
  Off: function(){
    clearInterval(this.Vars.Interval);
    document.title = this.Vars.OriginalTitle;   
  }
}


// SHOW OLDER MESSAGES
function imShowOlderMessages() {
  $('.im-show-older').hide(0);
  $('.im-table.im-messages .im-table-row').removeClass('hidden');
  imShowOlder = 1;
}
</script>
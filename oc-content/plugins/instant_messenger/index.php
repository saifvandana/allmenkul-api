<?php
/*
  Plugin Name: Instant Messenger Plugin
  Plugin URI: https://osclasspoint.com/osclass-plugins/messaging-and-communication/instant-messenger-plugin_i50
  Description: Add messenger feature to osclass
  Version: 2.2.4
  Author: MB Themes
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: instant_messenger
  Plugin update URI: instant-messenger-plugin
  Support URI: http://forums.osclasspoint.com/instant-messenger-plugin/
  Product Key: CNMxiwkWshE8H3F1JyMo
*/


require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'email.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelIM.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'admin/pagination.php';


osc_enqueue_style('font-awesome47', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
osc_enqueue_style('im-user-style', osc_base_url() . 'oc-content/plugins/instant_messenger/css/user.css?v=' . date('Ymdhis'));
osc_enqueue_script('jquery-validate');


// INSTALL FUNCTION - DEFINE VARIABLES
function im_call_after_install() {
  ModelIM::newInstance()->install();


  // General settings
  osc_set_preference('contact_seller', 1, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('notify_once', 1, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('item_hook', 0, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('att_enable', 1, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('att_max_size', 512, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('att_extension', 'jpg, jpeg, png, gif, doc, docx, pdf, txt', 'plugin-instant_messenger', 'STRING');
  osc_set_preference('threads_per_page', 20, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('thread_days', 360, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('att_days', 360, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('link_reg_only', 0, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('only_logged', 0, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('ajax', 1, 'plugin-instant_messenger', 'INTEGER');
  osc_set_preference('interval', 3000, 'plugin-instant_messenger', 'INTEGER');


  // UPLOAD EMAIL TEMPLATES
  $locales = OSCLocale::newInstance()->listAllEnabled();
  foreach($locales as $l) {
    $email_text  = '<p>Hi {TO_NAME},</p>';
    $email_text .= '<p>{FROM_NAME} has sent you new message on your product {ITEM_LINK} and is listed below:<hr></p>';
    $email_text .= '<p><strong>{THREAD_TITLE}</strong></p>';
    $email_text .= '<p>{MESSAGE}</p>';
    $email_text .= '<p><hr></p>';
    $email_text .= '<p><br/></p>';
    $email_text .= '<p>You can directly answer <a target="_blank" href="{THREAD_LINK}">clicking here</a>.';
    $email_text .= '<p>Remember, older conversations can always be viewed under "Messages" in your user account.</p>';
    $email_text .= '<p></p>';
    $email_text .= '<p>Thank you, <br />{WEB_TITLE}</p>';

    $im_notify[$l['pk_c_code']]['s_title'] = '[{WEB_TITLE}] New message: {ITEM_TITLE}';
    $im_notify[$l['pk_c_code']]['s_text'] = $email_text;
  }

  Page::newInstance()->insert( array('s_internal_name' => 'im_email_message_notify', 'b_indelible' => '1'), $im_notify);

}


// PLUGIN UPDATE
function im_update_version() {
  ModelIM::newInstance()->versionUpdate();
}

osc_add_hook(osc_plugin_path(__FILE__) . '_enable', 'im_update_version');



function im_call_after_uninstall() {
  ModelIM::newInstance()->uninstall();
  osc_delete_preference('contact_seller', 'plugin-instant_messenger');
  osc_delete_preference('notify_once', 'plugin-instant_messenger');
  osc_delete_preference('item_hook', 'plugin-instant_messenger');
  osc_delete_preference('att_enable', 'plugin-instant_messenger');
  osc_delete_preference('att_max_size', 'plugin-instant_messenger');
  osc_delete_preference('att_extension', 'plugin-instant_messenger');
  osc_delete_preference('threads_per_page', 'plugin-instant_messenger');
  osc_delete_preference('thread_days', 'plugin-instant_messenger');
  osc_delete_preference('att_days', 'plugin-instant_messenger');
  osc_delete_preference('link_reg_only', 'plugin-instant_messenger');
  osc_delete_preference('only_logged', 'plugin-instant_messenger');
  osc_delete_preference('ajax', 'plugin-instant_messenger');
  osc_delete_preference('interval', 'plugin-instant_messenger');


  // get list of primary keys of static pages (emails) that should be deleted on uninstall
  $pages = ModelIM::newInstance()->getPages();  
  foreach($pages as $page) {
    Page::newInstance()->deleteByPrimaryKey($page['pk_i_id']);
  }
}


// ADD JS STRINGS TO HEAD
function im_js() {
  $html = '<script>';
  $html .= 'var imRqName="' . osc_esc_js(__('Your Name: This field is required, enter your name please.', 'instant_messenger')) . '";';
  $html .= 'var imDsName="' . osc_esc_js(__('Your Name: Name is too short, enter at least 3 characters.', 'instant_messenger')) . '";';
  $html .= 'var imRqEmail="' . osc_esc_js(__('Your Email: This field is required, enter your email please.', 'instant_messenger')) . '";';
  $html .= 'var imDsEmail="' . osc_esc_js(__('Your Email: Address your have entered is not in valid format.', 'instant_messenger')) . '";';
  $html .= 'var imRqTitle="' . osc_esc_js(__('Title: Please enter title of this converstation.', 'instant_messenger')) . '";';
  $html .= 'var imDsTitle="' . osc_esc_js(__('Title: Title is too short, enter at least 2 characters.', 'instant_messenger')) . '";';
  $html .= 'var imRqMessage="' . osc_esc_js(__('Message: This field is required, please enter your message.', 'instant_messenger')) . '";';
  $html .= 'var imDsMessage="' . osc_esc_js(__('Message: Enter at least 2 characters.', 'instant_messenger')) . '";';
  $html .= '</script>';
  echo $html;
}

osc_add_hook('header', 'im_js');


// CREATE LINK WITH MESSAGES
function im_messages() {
  $html = '';

  $user_id = osc_logged_user_id();

  if( !osc_is_web_user_logged_in() ) {
    $user_id = rand(1000000,9999999);
  }

  if( osc_is_web_user_logged_in() || im_param('link_reg_only') <> 1) {
    $threads = ModelIM::newInstance()->getThreadsByUserId( $user_id, 6 );
    $count = ModelIM::newInstance()->countMessagesByUserId( $user_id );
    $count = $count['i_count'];

    $html .= '<div id="im-link" class="right">';
    $html .= '<a href="' . osc_route_url( 'im-threads') . '">' . __('Messages', 'instant_messenger') . '<span class="im-t-unread">' . $count . ' ' . __('unread', 'instant_messenger') . '</span></a>';

    $html .= '<div id="im-thread-wrap">';
    $html .= '<div class="im-thread-list im-body">';

    foreach( $threads as $t ) {
      $message = ModelIM::newInstance()->getLastMessageByThreadId( $t['i_thread_id'] );

      $m_unread = false;
      if( osc_logged_user_id() == $t['i_from_user_id'] && $message['i_type'] == 0 || osc_logged_user_id() == $t['i_to_user_id'] && $message['i_type'] == 1 ) {
        $logged_is_owner = true;
      } else {
        $logged_is_owner = false;

        if($message['i_read'] == 0) {
          $m_unread = true;
        }
      }

      $item_details = im_get_item_details( $t['fk_i_item_id'] );


      $html .= '<a class="im-entry im-row ' . ($m_unread ? 'im-unread' : '') . '" href="' . osc_route_url('im-messages', array('thread-id' => $t['i_thread_id'], 'secret' => 'n')) . '" title="' . __('Click to open conversation', 'instant_messenger') . '">';
        $html .= '<div class="im-col-6 im-img"><img src="' . $item_details['resource'] . '" /></div>';
        $html .= '<div class="im-col-18">';
          $html .= '<div class="im-row im-name">' . ($t['i_from_user_id'] == osc_logged_user_id() ? __('to', 'instant_messenger') . ' ' . $t['s_to_user_name'] : __('from', 'instant_messenger') . ' ' . $t['s_from_user_name']) . '</div>';
          $html .= '<div class="im-row im-text">' . (!$logged_is_owner ? '<i class="fa fa-mail-reply"></i>' : '') . ' ' . osc_highlight($message['s_message'], 36) . '</div>';
          $html .= '<div class="im-row im-time">' . im_get_time_diff($message['d_datetime']) . '</div>';
        $html .= '</div>';
      $html .= '</a>';
    }

    if(count($threads) > 0) {
      $html .= '<div class="im-row im-show-all"><a href="' . osc_route_url( 'im-threads') . '">' . __('Show all messages', 'instant_messenger') . '</a></div>';
    } else {
      if(osc_is_web_user_logged_in()) {
        $html .= '<div class="im-row im-show-all"><a href="' . osc_route_url( 'im-threads') . '">' . __('You have no messages yet', 'instant_messenger') . '</a></div>';
      } else {
        $html .= '<div class="im-row im-show-all"><a href="' . osc_route_url( 'im-threads') . '">' . __('Login to show messages', 'instant_messenger') . '</a></div>';
      }
    }

    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

  } else {
    return false;
  }

  return $html;

}



// COUNT UNREAD MESSAGES
function im_count_unread( $user_id ) {
  $count = ModelIM::newInstance()->countMessagesByUserId( $user_id );
  $count = $count['i_count'];
  return $count;
}


// MANAGE CONTACT SELLER BUTTON TO SEND MESSAGE INSTEAD OF MAIL
function im_manage_contact_seller( $aItem ) {
  if(im_param('contact_seller') == 1) {
    $item_id = $aItem['id'];
    $item = Item::newInstance()->findByPrimaryKey( $item_id );
    $item_details = im_get_item_details( $item_id );


    $title = __('Listing', 'instant_messenger') . ' #' . $item_id . ' ' . __('inquiry', 'instant_messenger');

    if(osc_logged_user_id() <> '' && osc_logged_user_id() > 0) {
      $from_user_id = osc_logged_user_id();
    } else {
      $from_user_id = '';
    }

    $from_user_name = $aItem['yourName'];
    $from_user_email = $aItem['yourEmail'];

    $to_user_id = $item['fk_i_user_id'];
    $to_user_name = $item['s_contact_name'];
    $to_user_email = $item['s_contact_email'];



    // MESSAGE SENT TO USER
    $thread_id = ModelIM::newInstance()->createThread( $item_id, $from_user_id, $from_user_name, $from_user_email, $to_user_id, $to_user_name, $to_user_email, $title, 0);
    $thread = ModelIM::newInstance()->getThreadById( $thread_id ); 

    $message = nl2br(htmlspecialchars($aItem['message']));

    if($aItem['phoneNumber'] <> '') {
      $message .= '<br/><br/>' . __('Contact phone', 'instant_messenger') . ': ' . $aItem['phoneNumber'];
    }

    $attachment = Params::getFiles('attachment');

    im_insert_message($thread['i_thread_id'],  $message, 0, $attachment );

    header('Location: ' . osc_route_url( 'im-messages', array('thread-id' => $thread['i_thread_id'], 'secret' => $thread['s_from_secret'])));
    exit;
  }
}

osc_add_hook('hook_email_item_inquiry', 'im_manage_contact_seller');

//if(osc_logged_user_id() > 0 && im_param('contact_seller') == 1) {
if(osc_get_preference('contact_seller', 'plugin-instant_messenger') == 1) {
  osc_remove_hook('hook_email_item_inquiry', 'fn_email_item_inquiry');
}



// DELETE THREADS RELATED TO ITEM, MESSAGES AND ATTACHMENTS
function im_delete_threads_item( $item_id ) {
  $threads = ModelIM::newInstance()->getThreadsByItemIdOnly( $item_id );

  foreach($threads as $t) {
    $messages_files = ModelIM::newInstance()->getMessagesByThreadIdWithFile( $t['i_thread_id'] );

    foreach($messages_files as $m) {
      $file_to_delete = osc_base_path() . 'oc-content/plugins/instant_messenger/download/' . $m['s_file'];
      unlink($file_to_delete);
    }

    ModelIM::newInstance()->removeMessagesByThreadId( $t['i_thread_id'] );
  }

  ModelIM::newInstance()->removeThreadsByItemId( $item_id );
}

osc_add_hook('delete_item', 'im_delete_threads_item');



// DELETE USER THREADS, MESSAGES RELATED TO IT AND ATTACHMENTS
function im_delete_threads_user( $user_id ) {
  $threads = ModelIM::newInstance()->getThreadsByUserIdOnly( $user_id );

  foreach($threads as $t) {
    $messages_files = ModelIM::newInstance()->getMessagesByThreadIdWithFile( $t['i_thread_id'] );

    foreach($messages_files as $m) {
      $file_to_delete = osc_base_path() . 'oc-content/plugins/instant_messenger/download/' . $m['s_file'];
      unlink($file_to_delete);
    }

    ModelIM::newInstance()->removeMessagesByThreadId( $t['i_thread_id'] );
  }

  ModelIM::newInstance()->removeThreadsByUserId( $user_id );
}

osc_add_hook('delete_user', 'im_delete_threads_user');



// UPDATE THREADS WHEN EMAIL OF USER IS CHANGED
function im_update_email( $new_email, $valid_url ) {
  $parts = parse_url($valid_url);
  parse_str($parts['query'], $query);
  $user_id = $query['userId'];

  ModelIM::newInstance()->updateThreadEmail( $user_id, $new_email );
}

osc_add_hook('hook_email_new_email', 'im_update_email');



// UPDATE THREADS WHEN USER IS REGISTERED
function im_user_register( $user_id ) {
  $user = User::newInstance()->findByPrimaryKey( $user_id );
  $user_email = $user['s_email'];
  $user_name = $user['s_name'];

  ModelIM::newInstance()->updateThreadUserId( $user_id, $user_email, $user_name );
}

osc_add_hook('user_register_completed', 'im_user_register');



// Add expired listings into user menu
function im_user_menu_link(){
  if(osc_current_web_theme() == 'veronika' || osc_current_web_theme() == 'stela' || osc_current_web_theme() == 'starter' || (defined('USER_MENU_ICONS') && USER_MENU_ICONS == 1)) {
    echo '<li class="opt_instant_messenger"><a href="' . osc_route_url('im-threads') .'" ><i class="fa fa-envelope-o"></i> '.__('Messages', 'instant_messenger').'<span class="im-user-account-count im-count-' . im_count_unread(osc_logged_user_id()) . '">' . im_count_unread(osc_logged_user_id()) . '</a></li>';
  } else {
    echo '<li class="opt_instant_messenger"><a href="' . osc_route_url('im-threads') .'" >'.__('Message center', 'instant_messenger').'<span class="im-user-account-count im-count-' . im_count_unread(osc_logged_user_id()) . '">' . im_count_unread(osc_logged_user_id()) . '</span></a></li>';
  }
}


if (osc_is_web_user_logged_in()) {
  $menu = true;
} else {
  $menu = false;
}

osc_add_route('im-threads', 'im-threads', 'im-threads', osc_plugin_folder(__FILE__).'user/threads.php', $menu, 'im', 'thread', __('Threads', 'instant_messenger'));
osc_add_route('im-thread-page', 'im-thread-page/([0-9]+)', 'im-thread-page/{page-id}', osc_plugin_folder(__FILE__).'user/threads.php', $menu, 'im', 'thread', __('Threads', 'instant_messenger'));
osc_add_route('im-thread-flag', 'im-thread-flag/([0-9]+)', 'im-thread-flag/{thread-flag-id}', osc_plugin_folder(__FILE__).'user/threads.php', $menu, 'im', 'thread');
osc_add_route('im-thread-notify', 'im-thread-notify/([0-9]+)', 'im-thread-notify/{thread-notify-id}', osc_plugin_folder(__FILE__).'user/threads.php', $menu, 'im', 'thread');

osc_add_route('im-messages', 'im-messages/([0-9]+)/(.+)', 'im-messages/{thread-id}/{secret}', osc_plugin_folder(__FILE__).'user/messages.php', $menu, 'im', 'message', __('Messages', 'instant_messenger'));
osc_add_route('im-delete-message', 'im-delete-message/([0-9]+)/([0-9]+)/(.+)', 'im-delete-message/{thread-id}/{del-message-id}/{secret}', osc_plugin_folder(__FILE__).'user/messages.php', $menu, 'im', 'message');
osc_add_route('im-delete-attachment', 'im-delete-attachment/([0-9]+)/([0-9]+)/(.+)/(.+)', 'im-delete-attachment/{thread-id}/{del-att-message-id}/{del-file-name}/{secret}', osc_plugin_folder(__FILE__).'user/messages.php', $menu, 'im', 'message');
osc_add_route('im-refresh-messages', 'im-refresh-messages/([0-9]+)/(.+)/(.+)', 'im-refresh-messages/{thread-id}/{secret}/{imaction}', osc_plugin_folder(__FILE__).'user/messages.php', $menu, 'im', 'refresh-message', __('Messages', 'instant_messenger'));
osc_add_route('im-create-thread', 'im-create-thread/([0-9]+)', 'im-create-thread/{item-id}', osc_plugin_folder(__FILE__).'user/create_thread.php', $menu, 'im', 'create-thread', __('Create thread', 'instant_messenger'));
osc_add_route('im-ban', 'im-ban/(.+)/(.+)', 'im-ban/{action}/{block-email}', osc_plugin_folder(__FILE__).'user/threads.php', $menu, 'im', 'thread');
osc_add_route('im-remove-ban', 'im-remove-ban/(.+)', 'im-remove-ban/{remove-id}', osc_plugin_folder(__FILE__).'user/threads.php', $menu, 'im', 'thread');

osc_add_hook('user_menu', 'im_user_menu_link');





// CREATE BUTTON TO SEND PM
function im_contact_button( $item = NULL, $link_only = false ) {
  if(!isset($item['pk_i_id']) || $item['pk_i_id'] == '') {
    $item_id = osc_item_id();
  } else {
    $item_id = $item['pk_i_id'];
  }


  if(im_param('only_logged') == 1 && !osc_is_web_user_logged_in()) {
    if($link_only) {
      return osc_user_login_url();
    } else {
      echo '<a href="' . osc_user_login_url() . '" class="im-contact"><span class="im-top">' . __('Send message', 'instant_messenger') . '</span><span class="im-bot">' . __('Only for logged in users', 'instant_messenger') . '</span><i class="fa fa-envelope"></i></a>';
      return true;
    }

  } else if( $item_id <> '' && $item_id > 0) {
    if($link_only) {
      return osc_route_url('im-create-thread', array('item-id' => $item_id));
    } else {
      echo '<a href="' . osc_route_url( 'im-create-thread', array('item-id' => $item_id) ) . '" class="im-contact"><span class="im-top">' . __('Send message', 'instant_messenger') . '</span><span class="im-bot">' . __('to seller', 'instant_messenger') . '</span><i class="fa fa-envelope"></i></a>';
      return true; 
    }

  } else {
    return false;
  }
}


if(osc_get_preference('item_hook', 'plugin-instant_messenger') == 1) {
  osc_add_hook('item_detail', 'im_contact_button');
}





// ADMIN MENU
function im_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/instant_messenger/css/admin.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/instant_messenger/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/instant_messenger/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="//fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" rel="stylesheet" type="text/css" />';
  echo '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/instant_messenger/js/admin.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/instant_messenger/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/instant_messenger/js/bootstrap-switch.js"></script>';



  if( $title == '') { $title = __('Configure', 'instant_messenger'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>Instant Messenger Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=instant_messenger/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'instant_messenger') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=instant_messenger/admin/threads.php"><i class="fa fa-comments"></i><span>' . __('Converstations', 'instant_messenger') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=instant_messenger/admin/maintenance.php"><i class="fa fa-gears"></i><span>' . __('Maintenance', 'instant_messenger') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function im_footer() {
  $pluginInfo = osc_plugin_get_info('instant_messenger/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="http://osclasspoint.com"><img src="http://osclasspoint.com/favicon.ico" alt="MB Themes" /> osclasspoint.com</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'instant_messenger') . '</a>';
  $text .= '<a target="_blank" href="http://forums.osclasspoint.com/"><i class="fa fa-comments"></i> ' . __('Support Forums', 'instant_messenger') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'instant_messenger') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}



// ADD MENU LINK TO PLUGIN LIST
function im_admin_menu() {
echo '<h3><a href="#">Instant Messenger Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'instant_messenger') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/threads.php') . '">&raquo; ' . __('Conversations', 'instant_messenger') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/maintenance.php') . '">&raquo; ' . __('Maintenance', 'instant_messenger') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','im_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function im_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'im_conf' );	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'im_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'im_call_after_uninstall');

?>
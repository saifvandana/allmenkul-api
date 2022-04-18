<?php

require_once 'email.php';



// GENERATE PAGINATION
function im_paginate($page_id, $per_page, $count_all, $class = '') {
  $html = '';
  $page_id = (int)$page_id;
  $page_id = ($page_id <= 0 ? 1 : $page_id);

  if($per_page < $count_all) {
    $html .= '<div class="im-pagination ' . $class . '">';

    $pages = ceil($count_all/$per_page); 
    $page_actual = ($page_id == '' ? 1 : $page_id);

    if($pages > 6) {

      // Too many pages to list them all
      if($page_id == 1) { 
        $ids = array(1,2,3, $pages);

      } else if ($page_id > 1 && $page_id < $pages) {
        $ids = array(1,$page_id-1, $page_id, $page_id+1, $pages);

      } else {
        $ids = array(1, $page_id-2, $page_id-1, $page_id);
      }

      $old = -1;
      $ids = array_unique(array_filter($ids));

      foreach($ids as $i) {
        $url = osc_route_url('im-thread-page', array('page-id' => $i));

        if($old <> -1 && $old <> $i - 1) {
          $html .= '<span>&middot;&middot;&middot;</span>';
        }

        $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="im-active"' : '') . '>' . $i . '</a>';
        $old = $i;
      }

    } else {

      // List all pages
      for ($i = 1; $i <= $pages; $i++) {
        $url = osc_route_url('im-thread-page', array('page-id' => $i));

        $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="im-active"' : '') . '>' . $i . '</a>';
      }
    }

    $html .= '</div>';
  }

  return $html;
}

// INCLUDE MAILER SCRIPT
function im_include_mailer() {
  if(file_exists(osc_lib_path() . 'phpmailer/class.phpmailer.php')) {
    require_once osc_lib_path() . 'phpmailer/class.phpmailer.php';
  } else if(file_exists(osc_lib_path() . 'vendor/phpmailer/phpmailer/class.phpmailer.php')) {
    require_once osc_lib_path() . 'vendor/phpmailer/phpmailer/class.phpmailer.php';
  }
}


// MASK EMAIL
function im_mask_email($mail) {
  $mail = explode('@', $mail);
  $a = substr($mail[0], 0, -2) . 'xx';
  $b = explode('.', @$mail[1]);
  $b[0] = 'xxxx';
  $mail_masked = $a . '@' . implode('.', $b);
  
  return $mail_masked;
}


// CHECK IF OSCLASS HAS PROFILE PICTURE
function im_has_profile_img() {
  if(defined('OSCLASS_AUTHOR') && defined('OSCLASS_AUTHOR') == 'OSCLASSPOINT' && osc_version() > 420) {
    return true;
  }
  
  return false;
}


// GET USER PROFILE IMAGE URL
function im_profile_img_url($user_id, $user = array()) {
  if($user_id <= 0) {
    return osc_base_url() . 'oc-content/plugins/instant_messenger/img/default-user-image.png';
  }
  
  if(im_has_profile_img()) {
    if(!is_array($user) || empty($user) || !isset($user['pk_i_id'])) {
      $user = User::newInstance()->findByPrimaryKey($user_id);
    }
    
    if(isset($user['s_profile_img']) && $user['s_profile_img'] != '') {
      return osc_base_url() . 'oc-content/uploads/user-images/' . $user['s_profile_img'];
    }
    
    return osc_base_url() . 'oc-content/uploads/user-images/default-user-image.png';
    
  } else if(function_exists('profile_picture_show')) {
    $picture = ModelUR::newInstance()->getPictureByUserId($user_id);
    $picture['pic_ext'] = isset($picture['pic_ext']) ? $picture['pic_ext'] : '.jpg';  

    if(file_exists(osc_base_path() . 'oc-content/plugins/profile_picture/images/profile' . $user_id . $picture['pic_ext'])) { 
      return osc_base_url() . 'oc-content/plugins/profile_picture/images/profile' . $user_id . $picture['pic_ext'];
    } 
    
  } else if(function_exists('show_avatar')) {
    $picture = ModelAvatar::newInstance()->getAvatar($user_id); 

    if($picture <> '') {
      if(file_exists(osc_base_path() . 'oc-content/plugins/avatar_plugin/avatar/' . $picture)) { 
        return osc_base_url() . 'oc-content/plugins/avatar_plugin/avatar/' . $picture;
      } 
    }
  }
  
  return osc_base_url() . 'oc-content/plugins/instant_messenger/img/default-user-image.png';
}


// GET USER IMAGE
function im_get_user_image($user_id) {
  return im_profile_img_url($user_id);
  
  
  // this will not be executed!!
  $img = '';

  // profile picture plugin
  if(function_exists('profile_picture_show')) {
    $picture = ModelIM::newInstance()->getPictureByUserId($user_id);

    if(file_exists(osc_base_path() . 'oc-content/plugins/profile_picture/images/profile' . $user_id . @$picture['pic_ext'])) { 
      $img = osc_base_url() . 'oc-content/plugins/profile_picture/images/profile' . $user_id . @$picture['pic_ext'];
    } 
  }


  // avatar plugin
  if(function_exists('show_avatar')) {
    $picture = ModelAvatar::newInstance()->getAvatar($user_id); 

    if($picture <> 0 && $picture <> '') {
      if(file_exists(osc_base_path() . 'oc-content/plugins/avatar_plugin/avatar/' . $picture)) { 
        $img = osc_base_url() . 'oc-content/plugins/avatar_plugin/avatar/' . $picture;
      } 
    }
  }

  return $img;
}


// GET OFFER
function im_get_offer($offer_id) {
  if(osc_plugin_is_enabled('make_offer/index.php') && $offer_id > 0) {
    $offer = ModelMO::newInstance()->getOfferById($offer_id);
    return $offer;
  }

  return false;
}


// CHECK IF USER IS BLOCKED
function im_check_block($to_user_id, $from_user_email) {
  $check_block = ModelIM::newInstance()->checkUserBlocks($to_user_id, $from_user_email);

  if(isset($check_block) && @$check_block['i_user_id'] > 0) {
    osc_add_flash_error_message( __('User has blocked you. You cannot message this user anymore.', 'instant_messenger'));
    return 0;
  }

  $banned = osc_is_banned($from_user_email);

  if($banned==1) {
    osc_add_flash_error_message( __('Your current email is not allowed', 'instant_messenger'));
    return 0;
  } else if($banned==2) {
    osc_add_flash_error_message( __('Your current IP is not allowed', 'instant_messenger'));
    return 0;
  }

  return 1;
}



// GET PICTURE FOR FILE EXTENSION
function im_get_extension_icon( $file ) {
  $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
  $return = '';

  if( $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' || $ext == 'bmp' || $ext == 'tiff' ) {
    $return = 'img.png';
  } else if ( $ext == 'zip' || $ext == 'rar' || $ext == 'tar' || $ext == '7z') {
    $return = 'zip.png';
  } else if ( $ext == 'txt') {
    $return = 'txt.png';
  } else if ( $ext == 'doc' || $ext == 'docx') {
    $return = 'doc.png';
  } else if ( $ext == 'xls' || $ext == 'xlsx') {
    $return = 'xls.png';
  } else if ( $ext == 'ppt' || $ext == 'pptx') {
    $return = 'ppt.png';
  } else if ( $ext == 'pdf') {
    $return = 'pdf.png';
  } else {
    $return = 'def.png';
  }

  $return = '<img class="im-att-icon" src="' . osc_base_url() . 'oc-content/plugins/instant_messenger/img/icon/' . $return . '" />';
  return $return;
}


// GET ITEM DETAILS
function im_get_item_details( $item_id ) {
  $item = Item::newInstance()->findByPrimaryKey( $item_id ); 
  $item_location = Item::newInstance()->findByPrimaryKey( $item_id ); 

  $resource = ItemResource::newInstance()->getResource( $item_id );

  $location = array( $item_location['s_country'], $item_location['s_region'], $item_location['s_city'] );
  $location = array_filter( $location );
  $location = implode(', ', $location);

  if(isset($resource['s_name']) && $resource['s_name'] <> '') {
    $resource_url = osc_base_url() . $resource['s_path'] . $resource['pk_i_id'] . '_thumbnail.' . $resource['s_extension'];
  } else {
    $resource_url = osc_base_url() . 'oc-content/plugins/instant_messenger/img/no-image.png';
  }

  $currency_full = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);
  $currency_symbol = isset($currency_full['s_description']) ? $currency_full['s_description'] : '';


  if($item['i_price'] == '') {
    $price = __('Check with seller', 'instant_messenger');
  } else if($item['i_price'] == 0) {
    $price = __('Free', 'instant_messenger');
  } else {
    $price = round($item['i_price']/1000000, 2); 
  }

  $price = osc_format_price($item['i_price'], $currency_symbol);


  return array('item_id' => $item_id, 'resource' => $resource_url, 'price' => $price, 'location' => $location);

}



// MANAGE MESSAGE INSERT INTO DATABASE
function im_insert_message($thread_id, $message, $type, $file, $notify = true ) {
  $thread = ModelIM::newInstance()->getThreadById( $thread_id ); 
  $item = Item::newInstance()->findByPrimaryKey( $thread['fk_i_item_id'] ); 


  // MANAGE FILE UPLOAD
  $allowed_extensions = (im_param('att_extension') <> '' ? im_param('att_extension') : 'jpg, jpeg, gif, png' );
  $allowed_extensions = array_map('strtolower', array_filter( array_map('trim', explode( ',', $allowed_extensions ) ) ) );

  $extension = strtolower(pathinfo(@$file['name'], PATHINFO_EXTENSION));
  $max_file_size = (im_param('att_max_size') <> '' ? im_param('att_max_size') : 512) * 1000;  //(in bytes)
  $file_size = $file['size'];
  $file_name = $thread_id . '_' . date('Ymd') . '_' . mb_generate_rand_int(6) . '.' . $extension;

  $update_file_name = '';
  if(@$file['name'] <> '' && im_param('att_enable') == 1) {
    if( $file['error'] == UPLOAD_ERR_OK) {
      if(in_array($extension, $allowed_extensions)) {
        if( $file_size < $max_file_size ) {
          if( move_uploaded_file(@$file['tmp_name'], osc_base_path() . 'oc-content/plugins/instant_messenger/download/' . $file_name ) ) {
            $update_file_name = $file_name;
          } else {
            osc_add_flash_error_message(__('An error with file sending has occurred, please try again', 'instant_messenger') );
          }
        } else {
          osc_add_flash_error_message( __('File is too big and was not sent. Maximum file size is:', 'instant_messenger') . ' ' . round($max_file_size/1000) . 'kb' );
        }
      } else {
        osc_add_flash_error_message( __('File extension is not allowed, file was not sent. Only files with following extensions are allowed to send in attachment', 'instant_messenger') . ': ' . implode(', ', $allowed_extensions) );
      }
    } else {
      osc_add_flash_error_message( __('An error with file sending has occurred, please try again.', 'instant_messenger') );
    }
  }


  // MANAGE EMAIL SENDING WHEN NEW MESSAGE IS ADDED
  if( $type == 0 ) {

    // Message send by FROM user, send notification to TO user
    $notify_enabled = $thread['i_to_user_notify'];
    $send_to_user_id = $thread['i_to_user_id'];
    $send_to_user_name = $thread['s_to_user_name'];
    $send_to_user_email = $thread['s_to_user_email'];
    $send_from_user_name = $thread['s_from_user_name'];
    $send_from_user_email = $thread['s_from_user_email'];
    $secret_mail = $thread['s_to_secret'];
    $secret = $thread['s_from_secret'];


  } else {

    // Message send by TO user, send notification to FROM user
    $notify_enabled = $thread['i_from_user_notify'];
    $send_to_user_id = $thread['i_from_user_id'];
    $send_to_user_name = $thread['s_from_user_name'];
    $send_to_user_email = $thread['s_from_user_email'];
    $send_from_user_name = $thread['s_to_user_name'];
    $send_from_user_email = $thread['s_to_user_email'];
    $secret_mail = $thread['s_from_secret'];
    $secret = $thread['s_to_secret'];

  }


  // CHECK FOR BLOCK
  if(im_check_block($send_to_user_id, $send_from_user_email) == 0) {
    //osc_add_flash_error_message( __('You cannot message this user. This user has blocked communication with you.', 'instant_messenger'));
    header('Location: ' . osc_route_url( 'im-messages', array('thread-id' => $thread['i_thread_id'], 'secret' => $secret) ));
    exit;
  }


  $email_sent = 0;    
  if( $notify_enabled == 1 && $notify === true ) {
    if(im_param('notify_once') == 1) {

    // SEND NEXT EMAIL ONLY IF PREVIOUS WAS READ BY USER ------- ADD PREFERENCE
    $last_unread = ModelIM::newInstance()->getLastMessage( $thread['i_thread_id'], $type, 0, 1);
     

    if( @$last_unread['pk_i_id'] <> '' && @$last_unread['pk_i_id'] > 0 ) {
      // We found message where user was notified by email and was not read yes, DO NOTHING
    } else {
      im_email_message_notify($send_to_user_name, $send_to_user_email, $send_from_user_name, $item['pk_i_id'], $item['s_title'], $thread['i_thread_id'], $thread['s_title'], $message, $update_file_name, $secret_mail );
      $email_sent = 1;
    }

    } else {

      // SEND EMAIL FOR EACH MESSAGE NO MATTER IT WAS READ BY USER
      im_email_message_notify($send_to_user_name, $send_to_user_email, $send_from_user_name, $item['pk_i_id'], $item['s_title'], $thread['i_thread_id'], $thread['s_title'], $message, $update_file_name, $secret_mail );
      $email_sent = 1;
    }
  }



  // INSERT MESSAGE INTO DATABASE
  ModelIM::newInstance()->insertMessage( $thread['i_thread_id'], $type, 0, $message, $update_file_name, $email_sent );
  osc_add_flash_ok_message( __('Message successfully sent to', 'instant_messenger') . ' ' . $send_to_user_name );
  header('Location: ' . osc_route_url( 'im-messages', array('thread-id' => $thread['i_thread_id'], 'secret' => $secret) ));
  exit;
}



function im_get_time_diff( $time ) {
  $time_diff = round(abs(time() - strtotime( $time )) / 60);
  $time_diff_h = floor($time_diff/60);
  $time_diff_d = floor($time_diff/1440);
  $time_diff_w = floor($time_diff/10080);
  $time_diff_m = floor($time_diff/43200);
  $time_diff_y = floor($time_diff/518400);


  if($time_diff < 2) {
    $time_diff_name = __('Minute ago', 'instant_messenger');
  } else if ($time_diff < 60) {
    $time_diff_name = sprintf(__('%d minutes ago', 'instant_messenger'), $time_diff);
  } else if ($time_diff < 120) {
    $time_diff_name = sprintf(__('%d hour ago', 'instant_messenger'), $time_diff_h);
  } else if ($time_diff < 1440) {
    $time_diff_name = sprintf(__('%d hours ago', 'instant_messenger'), $time_diff_h);
  } else if ($time_diff < 2880) {
    $time_diff_name = sprintf(__('%d day ago', 'instant_messenger'), $time_diff_d);
  } else if ($time_diff < 10080) {
    $time_diff_name = sprintf(__('%d days ago', 'instant_messenger'), $time_diff_d);
  } else if ($time_diff < 20160) {
    $time_diff_name = sprintf(__('%d week ago', 'instant_messenger'), $time_diff_w);
  } else if ($time_diff < 43200) {
    $time_diff_name = sprintf(__('%d weeks ago', 'instant_messenger'), $time_diff_w);
  } else if ($time_diff < 86400) {
    $time_diff_name = sprintf(__('%d month ago', 'instant_messenger'), $time_diff_m);
  } else {
    $time_diff_name = sprintf(__('%d months ago', 'instant_messenger'), $time_diff_m);
  }

  return $time_diff_name;
}


// CHECK IF RUNNING ON DEMO
function im_is_demo() {
  if(osc_logged_admin_username() == 'admin') {
    return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
  }
}


function im_param($name) {
  return osc_get_preference($name, 'plugin-instant_messenger');
}


if(!function_exists('mb_param_update')) {
  function mb_param_update( $param_name, $update_param_name, $type = NULL, $plugin_var_name = NULL ) {
  
    $val = '';
    if( $type == 'check') {

      // Checkbox input
      if( Params::getParam( $param_name ) == 'on' ) {
        $val = 1;
      } else {
        if( Params::getParam( $update_param_name ) == 'done' ) {
          $val = 0;
        } else {
          $val = ( osc_get_preference( $param_name, $plugin_var_name ) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
        }
      }
    } else {

      // Other inputs (text, password, ...)
      if( Params::getParam( $update_param_name ) == 'done' && Params::existParam($param_name)) {
        $val = Params::getParam( $param_name );
      } else {
        $val = ( osc_get_preference( $param_name, $plugin_var_name) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
      }
    }


    // If save button was pressed, update param
    if( Params::getParam( $update_param_name ) == 'done' ) {

      if(osc_get_preference( $param_name, $plugin_var_name ) == '') {
        osc_set_preference( $param_name, $val, $plugin_var_name, 'STRING');  
      } else {
        $dao_preference = new Preference();
        $dao_preference->update( array( "s_value" => $val ), array( "s_section" => $plugin_var_name, "s_name" => $param_name ));
        osc_reset_preferences();
        unset($dao_preference);
      }
    }

    return $val;
  }
}

if(!function_exists('mb_generate_rand_int')) {
  function mb_generate_rand_int($length = 18) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}


if(!function_exists('mb_generate_rand_string')) {
  function mb_generate_rand_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}


if(!function_exists('message_ok')) {
  function message_ok( $text ) {
    $final  = '<div class="flashmessage flashmessage-ok flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


if(!function_exists('message_error')) {
  function message_error( $text ) {
    $final  = '<div class="flashmessage flashmessage-error flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


// Cookies work
if(!function_exists('mb_set_cookie')) {
  function mb_set_cookie($name, $val) {
    Cookie::newInstance()->set_expires( 86400 * 30 );
    Cookie::newInstance()->push($name, $val);
    Cookie::newInstance()->set();
  }
}

if(!function_exists('mb_get_cookie')) {
  function mb_get_cookie($name) {
    return Cookie::newInstance()->get_value($name);
  }
}

if(!function_exists('mb_drop_cookie')) {
  function mb_drop_cookie($name) {
    Cookie::newInstance()->pop($name);
  }
}

?>
<?php

// CHECK IF OSCLASS HAS PROFILE PICTURE
function ur_has_profile_img() {
  if(defined('OSCLASS_AUTHOR') && defined('OSCLASS_AUTHOR') == 'OSCLASSPOINT' && osc_version() > 420) {
    return true;
  }
  
  return false;
}


// GET USER PROFILE IMAGE URL
function ur_profile_img_url($user_id, $user = array()) {
  if($user_id <= 0) {
    return osc_base_url() . 'oc-content/plugins/user_rating/img/default-user-image.png';
  }
  
  if(ur_has_profile_img()) {
    if(!is_array($user) || empty($user) || !isset($user['pk_i_id'])) {
      $user = User::newInstance()->findByPrimaryKey($user_id);
    }
    
    if(isset($user['s_profile_img']) && $user['s_profile_img'] != '') {
      return osc_base_url() . 'oc-content/uploads/user-images/' . $user['s_profile_img'];
    }
  }
  
  if(function_exists('profile_picture_show')) {
    $picture = ModelUR::newInstance()->getPictureByUserId($user_id);
    $picture['pic_ext'] = isset($picture['pic_ext']) ? $picture['pic_ext'] : '.jpg';  

    if(file_exists(osc_base_path() . 'oc-content/plugins/profile_picture/images/profile' . $user_id . $picture['pic_ext'])) { 
      return osc_base_url() . 'oc-content/plugins/profile_picture/images/profile' . $user_id . $picture['pic_ext'];
    } 
  }
  
  if(function_exists('show_avatar')) {
    $picture = ModelAvatar::newInstance()->getAvatar($user_id); 

    if($picture <> '') {
      if(file_exists(osc_base_path() . 'oc-content/plugins/avatar_plugin/avatar/' . $picture)) { 
        return osc_base_url() . 'oc-content/plugins/avatar_plugin/avatar/' . $picture;
      } 
    }
  }

  return osc_base_url() . 'oc-content/plugins/user_rating/img/default-user-image.png';
}


// GENERATE RATING FORM
function ur_ajax_new_form() {
  include osc_plugins_path() . osc_plugin_folder(__FILE__) . 'user/new.php';
  exit;
}

osc_add_hook('ajax_ur_ajax_new_form', 'ur_ajax_new_form');


// GENERATE LIST OF RATINGS
function ur_ajax_list() {
  include osc_plugins_path() . osc_plugin_folder(__FILE__) . 'user/rating.php';
  exit;
}

osc_add_hook('ajax_ur_ajax_list', 'ur_ajax_list');


// GET USER COLOR
function ur_user_color($rating) {
  return 'color' . round($rating);
  
  // not applicable
  if($rating > 4.0) {
    return 'color5';
  } else if($rating > 3.0) {
    return 'color4';
  } else if($rating > 2.0) {
    return 'color3';
  } else if($rating > 1.0) {
    return 'color2';
  } else {
    return 'color1';
  }
}


// GET LIST OF USER AVAILABLE LEVELS
function ur_user_level_list() {
  $reg = array(
    array(
      'id' => 'level1',
      'name' => __('Superhero', 'user_rating')
    ),
    array(
      'id' => 'level2',
      'name' => __('Captain', 'user_rating')
    ),
    array(
      'id' => 'level3',
      'name' => __('Conqueror', 'user_rating')
    ),
    array(
      'id' => 'level4',
      'name' => __('Novice', 'user_rating')
    )
  );


  $unreg = array(
    array(
      'id' => 'level2',
      'name' => __('Captain', 'user_rating')
    ),
    array(
      'id' => 'level3',
      'name' => __('Conqueror', 'user_rating')
    ),
    array(
      'id' => 'level4',
      'name' => __('Novice', 'user_rating')
    )
  );

  return array(
    'reg' => $reg,
    'unreg' => $unreg
  );
}


// GET USER LEVEL
function ur_user_level($rating, $reg_date, $user_id, $count) {
  $today = strtotime(date('Y-m-d'));
  $reg_date = strtotime(date('Y-m-d', strtotime($reg_date)));
  $days = abs($reg_date - $today)/86400;

  $id = '';
  $name = '';
  $levels = ur_user_level_list();

  if($user_id == 0 || $user_id == '') {
    $type = 'unreg';
    
    foreach($levels['unreg'] as $l) {
      if($rating >= ur_param($type . '_' . $l['id'] . '_avg')) {
        if($count >= ur_param($type . '_' . $l['id'] . '_count')) {
          $id = $l['id'];
          $name = $l['name'];
          break;
        }
      }
    }

  } else {
    $type = 'reg';

     foreach($levels['reg'] as $l) {
      if($rating >= ur_param($type . '_' . $l['id'] . '_avg')) {
        if($count >= ur_param($type . '_' . $l['id'] . '_count')) {
          if($days >= ur_param($type . '_' . $l['id'] . '_days')) {
            $id = $l['id'];
            $name = $l['name'];
            break;
          }
        }
      }
    }

  }


  if($id == '' && $name == '') {
    $id = 'level4';
    $name = __('Novice', 'user_rating');
  }

  return array('id' => $id, 'name' => $name, 'days' => $days, 'type' => $type);
}



// GET STARS BASED ON RATING OF USER
function ur_get_stars($rating) {
  $html = '';
  $color = ur_user_color($rating);
  $rating = round($rating);
  $monocolor_stars = ur_param('monocolor_stars') <> '' ? ur_param('monocolor_stars') : 0;
  $monocolor_class = ($monocolor_stars == 1 ? 'ur-no-cl' : 'ur-has-cl');

  for($i=1;$i<=5;$i++) { 
    $html .='<div class="ur-rate ' . $monocolor_class . ' ' . ($i > $rating ? 'ur-gray' : '') . ' ' . $color . '"><span></span></div>';
  }

  return $html;
}


// GENERATE AVAILABLE RATING OPTIONS
function ur_options() {
  $cat1 = ur_param('cat1') <> '' ? ur_param('cat1') : 0;
  $cat2 = ur_param('cat2') <> '' ? ur_param('cat2') : 0;
  $cat3 = ur_param('cat3') <> '' ? ur_param('cat3') : 0;
  $cat4 = ur_param('cat4') <> '' ? ur_param('cat4') : 0;
  $cat5 = ur_param('cat5') <> '' ? ur_param('cat5') : 0;

  $options = array();

  if($cat1 == 1) {
    $options[] = array('id' => 'cat1', 'name' => __('Communication', 'user_rating'));
  }

  if($cat2 == 1) {
    $options[] = array('id' => 'cat2', 'name' => __('Delivery', 'user_rating'));
  }

  if($cat3 == 1) {
    $options[] = array('id' => 'cat3', 'name' => __('Quality', 'user_rating'));
  }

  if($cat4 == 1) {
    $options[] = array('id' => 'cat4', 'name' => __('Speed', 'user_rating'));
  }

  if($cat5 == 1) {
    $options[] = array('id' => 'cat5', 'name' => __('Recommend', 'user_rating'));
  }

  return $options;
}


// GENERATE ALL RATING OPTIONS (CATEGORIES)
function ur_options_all() {
  $cat1 = ur_param('cat1') <> '' ? ur_param('cat1') : 0;
  $cat2 = ur_param('cat2') <> '' ? ur_param('cat2') : 0;
  $cat3 = ur_param('cat3') <> '' ? ur_param('cat3') : 0;
  $cat4 = ur_param('cat4') <> '' ? ur_param('cat4') : 0;
  $cat5 = ur_param('cat5') <> '' ? ur_param('cat5') : 0;

  $options = array();
  $options[] = array('id' => 'cat0', 'name' => __('Overall', 'user_rating'));

  if($cat1 == 1) {
    $options[] = array('id' => 'cat1', 'name' => __('Communication', 'user_rating'));
  }

  if($cat2 == 1) {
    $options[] = array('id' => 'cat2', 'name' => __('Delivery', 'user_rating'));
  }

  if($cat3 == 1) {
    $options[] = array('id' => 'cat3', 'name' => __('Quality', 'user_rating'));
  }

  if($cat4 == 1) {
    $options[] = array('id' => 'cat4', 'name' => __('Speed', 'user_rating'));
  }

  if($cat5 == 1) {
    $options[] = array('id' => 'cat5', 'name' => __('Recommend', 'user_rating'));
  }

  return $options;
}


// GENERATE RAW LINK TO ADD NEW RATING
function ur_add_rating_link_raw($user_id = NULL, $item_id = NULL) {
  if($user_id === NULL && $item_id === NULL) {
    $user_id = osc_item_user_id();
  }

  $link = osc_base_url(true) . '?page=ajax&action=runhook&hook=ur_ajax_new_form&userId=' . $user_id . '&itemId=' . $item_id;
  return $link;
}


// GENERATE RAW LINK TO SHOW RATINGS
function ur_show_rating_link_raw($user_id = NULL, $item_id = NULL) {
  if($user_id === NULL && $item_id === NULL) {
    $user_id = osc_item_user_id();
  }

  $link = osc_base_url(true) . '?page=ajax&action=runhook&hook=ur_ajax_list&userId=' . $user_id . '&itemId=' . $item_id;
  return $link;
}


// ADD RATING BUTTON
function ur_button_add($user_id = NULL, $item_id = NULL) {
  $link = ur_add_rating_link_raw($user_id, $item_id);
  $options = ur_options();
  return '<a href="' . $link . '" class="ur-new-rating ur-button-new" data-options-count="' . count($options) . '">' . __('Rate this user', 'user_rating') . '</a>';
}


// SHOW RATING BUTTON
function ur_button_show($user_id = NULL, $user_email = NULL, $item_id = NULL) {
  $link = ur_show_rating_link_raw($user_id, $item_id);
  $validate =  ur_param('validate') <> '' ? ur_param('validate') : 0;
  $global_rating = ModelUR::newInstance()->getRatingAverageByUserId($user_id, $user_email, 9, $validate); 

  return '<a href="' . $link . '" class="ur-show-rating ur-button-new ur-button-counts"><strong>' . number_format($global_rating, 1) . '</strong> <span>' . __('Average rating', 'user_rating') . '</span></a>';
}


// SHOW RATING STARS
function ur_button_stars($user_id = NULL, $user_email = NULL, $item_id = NULL) {
  $link = ur_show_rating_link_raw($user_id, $item_id);
  $validate =  ur_param('validate') <> '' ? ur_param('validate') : 0;
  $global_rating = ModelUR::newInstance()->getRatingAverageByUserId($user_id, $user_email, 9, $validate); 
  
  return '<a href="' . $link . '" class="ur-show-stars ur-stars-smaller">' . ur_get_stars($global_rating) . '<em>' . number_format($global_rating, 1) . '</em></a>';
}


// SHOW AVERAGE RATING
function ur_show_rating_score($user_id = NULL, $user_email = NULL, $item_id = NULL) {
  $validate =  ur_param('validate') <> '' ? ur_param('validate') : 0;
  $global_rating = ModelUR::newInstance()->getRatingAverageByUserId($user_id, $user_email, 9, $validate); 
  
  return '<div class="ur-show-score">' . number_format($global_rating, 1) . '</a>';
}


// SHOW USER LEVEL
function ur_show_rating_level($user_id = NULL, $user_email = NULL) {
  $validate =  ur_param('validate') <> '' ? ur_param('validate') : 0;
  $global_rating = ModelUR::newInstance()->getRatingAverageByUserId($user_id, $user_email, 9, $validate); 
  $count_rating = ModelUR::newInstance()->getRatingCounts($user_id, $user_email, '', '', 0, $validate);
  
  $user = array();
  if($user_id > 0) {
    $user = User::newInstance()->findByPrimaryKey($user_id);
  }
  
  $user_reg = (isset($user['dt_reg_date']) ? $user['dt_reg_date'] : date('Y-m-d H:i:s'));
  $level = ur_user_level($global_rating, $user_reg, $user_id, $count_rating); 

  if(isset($level['id'])) {  
    return '<div class="ur-show-level"><span class="ur-' . $level['id'] . '">' . $level['name'] . '</span></div>';
  }
  
  return false;
}




// BUTTONS LINE - USED IN HOOK
function ur_buttons_box() {
  ?>
  <div id="ur-buttons" class="ur-item-buttons-wrap">
    <?php echo ur_button_show(osc_item_user_id(), osc_item_contact_email(), osc_item_id()); ?>
    <?php echo ur_button_add(osc_item_user_id(), osc_item_id()); ?>
  </div>
  <?php
}


// HOOK BUTTONS TO ITEM PAGE
function ur_hook_buttons() {
  if(ur_param('hook_item') == 1) {
    ur_buttons_box();
  }  
}

osc_add_hook('item_detail', 'ur_hook_buttons', 5);


// LEGACY BUTTON TO ADD RATING
function ur_add_rating_link($user_id = NULL, $item_id = NULL) {
  $link = ur_add_rating_link_raw($user_id, $item_id);
  return '<a href="' . $link . '" id="add-new-rating" class="add-new-rating ur-button">' . __('Rate this user', 'user_rating') . '</a>';
}


// LEGACY BUTTON TO SHOW RATING
function ur_show_rating_link($user_id = NULL, $item_id = NULL) {
  $link = ur_show_rating_link_raw($user_id, $item_id);
  return '<a href="' . $link . '" id="show-rating" class="show-rating ur-button">' . __('Show rating', 'user_rating') . '</a>';
}


// LEGACY BUTTON TO SHOW RATING STARS
function ur_show_rating_stars($user_id = NULL, $user_email = NULL, $item_id = NULL) {
  $link = ur_show_rating_link_raw($user_id, $item_id);

  $validate =  ur_param('validate') <> '' ? ur_param('validate') : 0;
  $global_rating = ModelUR::newInstance()->getRatingAverageByUserId($user_id, $user_email, 9, $validate); 

  $html = '<a href="' . $link . '" id="show-rating" class="show-rating show-stars" title="' . osc_esc_html(__('Click to show user reviews', 'user_rating')) . '">';
  $html .= ur_get_stars($global_rating);
  $html .= '<span>' . number_format($global_rating, 1) . '</span>';
  $html .= '</a>';

  return $html;
}



// SMART DATE
function ur_smart_date($time) {
  $time_diff = round(abs(time() - strtotime( $time )) / 60);
  $time_diff_h = floor($time_diff/60);
  $time_diff_d = floor($time_diff/1440);
  $time_diff_w = floor($time_diff/10080);
  $time_diff_m = floor($time_diff/43200);
  $time_diff_y = floor($time_diff/518400);


  if($time_diff < 2) {
    $time_diff_name = __('minute ago', 'user_rating');
  } else if ($time_diff < 60) {
    $time_diff_name = sprintf(__('%d minutes ago', 'user_rating'), $time_diff);
  } else if ($time_diff < 120) {
    $time_diff_name = sprintf(__('%d hour ago', 'user_rating'), $time_diff_h);
  } else if ($time_diff < 1440) {
    $time_diff_name = sprintf(__('%d hours ago', 'user_rating'), $time_diff_h);
  } else if ($time_diff < 2880) {
    $time_diff_name = sprintf(__('%d day ago', 'user_rating'), $time_diff_d);
  } else if ($time_diff < 10080) {
    $time_diff_name = sprintf(__('%d days ago', 'user_rating'), $time_diff_d);
  } else if ($time_diff < 20160) {
    $time_diff_name = sprintf(__('%d week ago', 'user_rating'), $time_diff_w);
  } else if ($time_diff < 43200) {
    $time_diff_name = sprintf(__('%d weeks ago', 'user_rating'), $time_diff_w);
  } else if ($time_diff < 86400) {
    $time_diff_name = sprintf(__('%d month ago', 'user_rating'), $time_diff_m);
  } else if ($time_diff < 518400) {
    $time_diff_name = sprintf(__('%d months ago', 'user_rating'), $time_diff_m);
  } else if ($time_diff < 1036800) {
    $time_diff_name = sprintf(__('%d year ago', 'user_rating'), $time_diff_y);
  } else {
    $time_diff_name = sprintf(__('%d years ago', 'user_rating'), $time_diff_y);
  }

  return $time_diff_name;
}



// CORE FUNCTIONS
function ur_param($name) {
  return osc_get_preference($name, 'plugin-user_rating');
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


// CHECK IF RUNNING ON DEMO
function ur_is_demo() {
  if(osc_logged_admin_username() == 'admin') {
    return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
  }
}


if( !function_exists('osc_is_contact_page') ) {
  function osc_is_contact_page() {
    $location = Rewrite::newInstance()->get_location();
    $section = Rewrite::newInstance()->get_section();
    if( $location == 'contact' ) {
      return true ;
    }

    return false ;
  }
}


?>
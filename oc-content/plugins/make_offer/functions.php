<?php


// INCLUDE MAILER SCRIPT
function mo_include_mailer() {
  if(file_exists(osc_lib_path() . 'phpmailer/class.phpmailer.php')) {
    require_once osc_lib_path() . 'phpmailer/class.phpmailer.php';
  } else if(file_exists(osc_lib_path() . 'vendor/phpmailer/phpmailer/class.phpmailer.php')) {
    require_once osc_lib_path() . 'vendor/phpmailer/phpmailer/class.phpmailer.php';
  }
}


function mo_validate_name($id = null) {
  if($id == 1) {
    return __('Valid', 'make_offer');
  } else {
    return __('Pending admin validation', 'make_offer');
  }
}

function mo_fancy_box() {
  echo '<div style="display:none;" id="mo-fancy-dialog"></div>';
  echo '<div style="display:none;" id="mo-fancy-overlay"></div>';
}

osc_add_hook('footer', 'mo_fancy_box');


function mo_validate_icon($id = null) {
  if($id == 1) {
    return '<i class="fa fa-check mo-green" title="' . osc_esc_html(mo_validate_name($id)) . '"></i>';
  } else {
    return '<i class="fa fa-question mo-blue" title="' . osc_esc_html(mo_validate_name($id)) . '"></i>';
  }
}


function mo_status_name($id = null) {
  if($id == 1) {
    return __('Accepted', 'make_offer');
  } else if($id == 2) {
    return __('Declined', 'make_offer');
  } else {
    return __('Pending', 'make_offer');
  }
}


function mo_status_icon($id = null) {
  if($id == 1) {
    return '<i class="fa fa-check mo-green" title="' . osc_esc_html(mo_status_name($id)) . '"></i>';
  } else if($id == 2) {
    return '<i class="fa fa-times mo-red" title="' . osc_esc_html(mo_status_name($id)) . '"></i>';
  } else {
    return '<i class="fa fa-question mo-blue" title="' . osc_esc_html(mo_status_name($id)) . '"></i>';
  }
}


// CHECK IF OSCLASS HAS PROFILE PICTURE
function mo_has_profile_img() {
  if(defined('OSCLASS_AUTHOR') && defined('OSCLASS_AUTHOR') == 'OSCLASSPOINT' && osc_version() > 420) {
    return true;
  }
  
  return false;
}


// GET USER PROFILE IMAGE URL
function mo_profile_img_url($user_id, $user = array()) {
  if($user_id <= 0) {
    return osc_base_url() . 'oc-content/plugins/make_offer/img/default-user-image.png';
  }
  
  if(mo_has_profile_img()) {
    if(!is_array($user) || empty($user) || !isset($user['pk_i_id'])) {
      $user = User::newInstance()->findByPrimaryKey($user_id);
    }
    
    if(isset($user['s_profile_img']) && $user['s_profile_img'] != '') {
      return osc_base_url() . 'oc-content/uploads/user-images/' . $user['s_profile_img'];
    }
  }
  
  if(function_exists('profile_picture_show')) {
    $picture = ModelMO::newInstance()->getPictureByUserId($user_id);
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

  return osc_base_url() . 'oc-content/plugins/make_offer/img/default-user-image.png';
}



// DRAW USER CARD
function mo_draw_offer($user, $offer, $currency_symbol) {
  $show_status = osc_get_preference('show_status', 'plugin-make_offer') <> '' ? osc_get_preference('show_status', 'plugin-make_offer') : 0;
  $show_quantity = osc_get_preference('show_quantity', 'plugin-make_offer') <> '' ? osc_get_preference('show_quantity', 'plugin-make_offer') : 0;

?>
  <div class="mo-one">
    <div class="mo-img">
      <img src="<?php echo mo_profile_img_url($user['pk_i_id'], $user); ?>" alt="<?php echo osc_esc_html($user['s_name']); ?>"/>
    </div>
    
    <div class="mo-about">
      <strong>
        <span class="mo-prc"><?php echo osc_format_price($offer['i_price'], $currency_symbol); ?><?php if($show_quantity == 1) { ?>, <?php } ?><span>
        <?php if($show_quantity == 1) { ?><span class="mo-qt"><?php echo sprintf(__('Qty: %dx', 'make_offer'), $offer['i_quantity']); ?></span><?php } ?>
      </strong>
      
      <?php
        $uname = (@$user['s_name'] != '' ? $user['s_name'] : $offer['s_user_name']);
        
        if(isset($user['pk_i_id']) && $user['pk_i_id'] > 0) {
          $uname = '<a href="' . osc_user_public_profile_url($user['pk_i_id']) . '">' . $uname . '</a>'; 
        } else {
          $uname = '<u>' . $uname . '</u>';
        }
      ?>
      <span><?php echo sprintf(__('%s on %s', 'make_offer'), $uname, date('j. M Y', strtotime($offer['d_datetime']))); ?></span>
    </div>
    
    <div class="mo-buttons">
      <?php if($show_status == 1) { ?>
        <?php if($offer['i_status']==1) { ?>
          <div class="mo-offer-status mo-offer-status-<?php echo $offer['i_status']; ?>" title="<?php echo osc_esc_html(__('Seller has accepted this offer', 'make_offer')); ?>">
            <i class="fa fa-check" ></i> <?php echo __('Accepted', 'make_offer'); ?>
          </div>
        <?php } else if($offer['i_status']==2) { ?>
          <div class="mo-offer-status mo-offer-status-<?php echo $offer['i_status']; ?>" title="<?php echo osc_esc_html(__('Seller has declined this offer', 'make_offer')); ?>">
            <i class="fa fa-times"></i> <?php echo __('Declined', 'make_offer'); ?>
          </div>
        <?php } else { ?>
          <div class="mo-offer-status mo-offer-status-<?php echo $offer['i_status']; ?>" title="<?php echo osc_esc_html(__('Seller has not yet responded to this offer', 'make_offer')); ?>">
            <i class="fa fa-question"></i> <?php echo osc_esc_html(__('Pending', 'make_offer')); ?>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </div>
<?php
}


function mo_show_offer_link_raw() {
  $history = mo_param('history');
  $category = mo_param('category');
  $category_array = explode(',', $category);
  $link = osc_base_url(true) . '?page=ajax&action=runhook&hook=mo_offers_list&itemId=' . osc_item_id() . '&currency=' . osc_item_currency();

  if((in_array(osc_item_category_id(), $category_array) || trim($category) == '') && (osc_item_price() > 0 || osc_item_price() !== 0)) {
    $setting = ModelMO::newInstance()->getOfferSettingByItemId(osc_item_id());

    if((isset($setting['i_enabled']) && $setting['i_enabled'] == 1) || ((!isset($setting['i_enabled']) || $setting['i_enabled'] == '') && $history == 1)) {
      return $link;
    }
  }

  return false;
}


function mo_show_offer_link_price() {
  $link = mo_show_offer_link_raw();

  if($link !== false) {
    return '<a href="' . $link . '" id="make-offer" class="make-offer-link mo-price-link">' . __('Make offer', 'make_offer') . '</a>';
  }
}


function mo_show_offer_link() {
  $link = mo_show_offer_link_raw();

  if($link !== false) {
    return '<a href="' . $link  . '" id="make-offer" class="make-offer-link mo-hook-link mo-button"><div class="mo-link-left"><i class="fa fa-gavel"></i></div><div class="mo-link-right"><div class="mo-link-top">' . __('Make offer', 'make_offer') . '</div><div class="mo-link-bottom">' . __('Create good deal', 'make_offer') . '</div></div></a>';
  }

  return false;
}


function mo_offer_counts_button() {
  $link = mo_show_offer_link_raw();
  
  if($link !== false) {
    $validate = mo_param('validate') <> '' ? mo_param('validate') : 0;
    $count = ModelMO::newInstance()->countOffersByItemId(osc_item_id(), $validate);
    $count = (isset($count['i_count']) ? $count['i_count'] : 0);
  
    return '<a href="' . $link  . '" class="mo-open-offer mo-button-counts mo-button-new">' . ($count == 1 ? sprintf(__('%s Offer', 'make_offer'), '<strong>' . $count . '</strong>') : sprintf(__('%s Offers', 'make_offer'), '<strong>' . $count . '</strong>')) . '</a>';
  }
  
  return false;
}


function mo_offer_create_button() {
  $link = mo_show_offer_link_raw();
  
  if($link !== false) {
    $link .= '&createNew=1';
    return '<a href="' . $link  . '" class="mo-open-offer mo-button-create mo-button-new">' . __('Submit a new offer', 'make_offer') . '</a>';
  }
  
  return false;
}


function mo_param($name) {
  return osc_get_preference($name, 'plugin-make_offer');
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
function mo_is_demo() {
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



// CATEGORIES WORK
function mo_cat_tree($list = array()) {
  if(!is_array($list) || empty($list)) {
    $list = Category::newInstance()->listAll();
  }

  $array = array();
  //$root = Category::newInstance()->findRootCategoriesEnabled();

  foreach($list as $c) {
    if($c['fk_i_parent_id'] <= 0) {
      $array[$c['pk_i_id']] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name']);
      $array[$c['pk_i_id']]['sub'] = mo_cat_sub($list, $c['pk_i_id']);
    }
  }

  return $array;
}

function mo_cat_sub($list, $parent_id) {
  $array = array();
  //$cats = Category::newInstance()->findSubcategories($id);

  if(is_array($list) && count($list) > 0) {
    foreach($list as $c) {
      if($c['fk_i_parent_id'] == $parent_id) {  echo $c['s_name'];
        $array[$c['pk_i_id']] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name']);
        $array[$c['pk_i_id']]['sub'] = mo_cat_sub($list, $c['pk_i_id']);
      }
    }
  }
      
  return $array;
}


function mo_cat_list($selected = array(), $categories = '', $level = 0) {
  if($categories == '' || $level == 0) {
    $categories = mo_cat_tree($categories);
  }


  foreach($categories as $c) {
    echo '<option value="' . $c['pk_i_id'] . '" ' . (in_array($c['pk_i_id'], $selected) ? 'selected="selected"' : '') . '>' . str_repeat('-', $level) . ($level > 0 ? ' ' : '') . $c['s_name'] . '</option>';

    if(is_array($c['sub']) && count($c['sub']) > 0) {
      mo_cat_list($selected, $c['sub'], $level + 1);
    }
  }
}



?>
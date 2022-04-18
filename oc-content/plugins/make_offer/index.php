<?php
/*
  Plugin Name: Make Offer Plugin
  Plugin URI: https://osclasspoint.com/osclass-plugins/extra-fields-and-other/make-offer-plugin_i54
  Description: Enhance sales conversion by adding option to buyers to "make offer" to sellers
  Version: 2.1.1
  Author: MB Themes
  Author URI: https://osclasspoint.com/
  Author Email: info@osclasspoint.com
  Short Name: make_offer
  Plugin update URI: make-offer-plugin
  Support URI: https://forums.osclasspoint.com/make-offer-plugin/
  Product Key: C7IDtk5QVGn39wsnSOE2
*/


require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelMO.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'email.php';


osc_enqueue_style('font-awesome47', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
osc_enqueue_style('mo-user-style', osc_base_url() . 'oc-content/plugins/make_offer/css/user.css?v=' . date('YmdHis'));
osc_register_script('mo-user', osc_base_url() . 'oc-content/plugins/make_offer/js/user.js?v=' . date('YmdHis'), 'jquery');
osc_enqueue_script('mo-user');

osc_add_route('mo-show-offers', 'user/offers/([0-9]+)', 'user/offers/{offerId}', osc_plugin_folder(__FILE__).'user/user_offers.php', true);   // for instant messenger plugin
osc_add_route('mo-offers', 'user/offers', 'user/offers', osc_plugin_folder(__FILE__).'user/user_offers.php', true, 'mo', 'offers', __('My offers', 'make_offer'));



/* SECTION TO MANAGE ITEM POST/EDIT AND CUSTOM SETTING FOR OFFER */
function mo_item_post_form( $catId = '' ) {
  $category = mo_param('category');
  $category_array = explode(',', $category);

  if(in_array($catId, $category_array) || trim($category) == '') {
    include_once 'user/item_post_edit.php';
  }
}


function mo_item_post_insert( $item ) {
  ModelMO::newInstance()->insertOfferSetting( $item['pk_i_id'], Params::getParam('mo_item_setting') );
}


function mo_item_edit_form($catId = null, $item_id = null) {
  $category = mo_param('category');
  $category_array = explode(',', $category);

  if(in_array($catId, $category_array) || trim($category) == '') {
    include_once 'user/item_post_edit.php';
  }
}


function mo_item_edit_update( $item ) {
  $setting = ModelMO::newInstance()->getOfferSettingByItemId( $item['pk_i_id'] );

  ModelMO::newInstance()->updateOfferSetting( $item['pk_i_id'], Params::getParam('mo_item_setting') );
}


function mo_item_meta_preserve() {
  Session::newInstance()->_setForm('mo_item_setting', Params::getParam('mo_item_setting'));
  Session::newInstance()->_keepForm('mo_item_setting');
}


function mo_item_delete_meta($item_id) {
  ModelMO::newInstance()->removeOfferSetting( $item_id ) ;
}


osc_add_hook('item_form', 'mo_item_post_form');
osc_add_hook('posted_item', 'mo_item_post_insert');
osc_add_hook('item_edit', 'mo_item_edit_form');
osc_add_hook('edited_item', 'mo_item_edit_update');
osc_add_hook('delete_item', 'mo_item_delete_meta');
osc_add_hook('pre_item_post', 'mo_item_meta_preserve') ;



// ADD LINK TO USER ACCOUNT LEFT SIDEBAR
function mo_user_menu(){
  if(osc_current_web_theme() == 'veronika' || osc_current_web_theme() == 'stela' || osc_current_web_theme() == 'starter' || (defined('USER_MENU_ICONS') && USER_MENU_ICONS == 1)) {
    echo '<li class="opt_user_offers"><a href="' . osc_route_url('mo-offers') . '" ><i class="fa fa-gavel"></i> ' . __('Offers', 'make_offer') . '</a></li>';
  } else {
    echo '<li class="opt_user_offers"><a href="' . osc_route_url('mo-offers') . '" >' . __('Offers', 'make_offer') . '</a></li>';
  }
}

osc_add_hook('user_menu', 'mo_user_menu');


function mo_add_to_price($formatted_price) {
  if(mo_param('add_price') == 1) {
    if(osc_item_price() != 0) {
      if(osc_is_ad_page()) {            
        return $formatted_price . ' ' . mo_show_offer_link_price();
      }
    }
  }

  return $formatted_price;
}

osc_add_filter('item_price', 'mo_add_to_price');



// BUTTONS LINE
function mo_buttons_box() {
  if(osc_item_price() > 0 || osc_item_price() !== 0) {
  ?>
    <div id="mo-buttons" class="mo-item-buttons-wrap">
      <?php echo mo_offer_counts_button(); ?>
      <?php echo mo_offer_create_button(); ?>
    </div>
  <?php
  }
}


// AUTO HOOK BUTTON
function mo_hook_button() {
  if(mo_param('add_hook') == 1) {
    mo_buttons_box();
  }
}

osc_add_hook('item_detail', 'mo_hook_button', 5);


// ADD AJAX URL TO VARIABLES
function mo_footer_js() {
?><script type="text/javascript">var moValidPriceReq="<?php echo osc_esc_html(__('Price: This field is required.', 'make_offer')); ?>";var moValidNameReq="<?php echo osc_esc_html(__('Name: This field is required.', 'make_offer')); ?>";var moValidNameShort="<?php echo osc_esc_html(__('Name: Name is too short.', 'make_offer')); ?>";var moValidEmailReq="<?php echo osc_esc_html(__('Email: This field is required.', 'make_offer')); ?>";var moValidEmailShort="<?php echo osc_esc_html(__('Email: Not valid email format.', 'make_offer')); ?>";var moYourReply="<?php echo osc_esc_html(__('Your reply', 'make_offer')); ?>";</script><?php
}

osc_add_hook('footer', 'mo_footer_js');



// ADD OFFER TO DATABASE
function mo_new_offer_manage() {
  $validate = mo_param('validate') <> '' ? mo_param('validate') : 0;
  $notify = mo_param('notify') <> '' ? mo_param('notify') : 0;
  $valid = ($validate == 1 ? 0 : 1);
  $item_id = Params::getParam('itemId');
  $user_id = Params::getParam('userId');
  $user_name = Params::getParam('name');
  $user_email = Params::getParam('email');
  $user_phone = Params::getParam('phone');
  $quantity = Params::getParam('quantity');
  $price = Params::getParam('price')*1000000;
  $comment = trim(Params::getParam('comment'));

  if($quantity <= 0) {
    $quantity = 1;
  }


  $offer_id = ModelMO::newInstance()->insertOffer( $item_id, $quantity, $price, 0, $valid, $comment, $user_id, $user_name, $user_email, $user_phone );

  // SEND EMAIL TO SELLER
  if($validate == 0 && $notify == 1) {
    if(mo_param('instant_messenger') == 1 && osc_plugin_is_enabled('instant_messenger/index.php')) {
      require_once osc_base_path() . 'oc-content/plugins/instant_messenger/model/ModelIM.php';

      $item = Item::newInstance()->findByPrimaryKey($item_id);
      $currency = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);
      $title = sprintf(__('New offer on %s - %s', 'make_offer'), osc_highlight($item['s_title'], 50), Params::getParam('price') . $currency['s_description']);
      $thread_id = ModelIM::newInstance()->createThread( $item_id, $user_id, $user_name, $user_email, $item['fk_i_user_id'], $item['s_contact_name'], $item['s_contact_email'], $title, 0, $offer_id);

      im_insert_message($thread_id, nl2br(htmlspecialchars(Params::getParam('comment', false, false))), 0, '' );

    } else {
      mo_notify_seller($offer_id);
    }
  }
}

osc_add_hook('ajax_mo_new_offer_manage', 'mo_new_offer_manage');


// ADD OFFER TO DATABASE
function mo_respond_offer_manage() {
  $notify = mo_param('notify') <> '' ? mo_param('notify') : 0;
  $offer_id = Params::getParam('offerId');
  $status_id = Params::getParam('statusId');
  $respond = Params::getParam('respond');

  ModelMO::newInstance()->sellerManageOffer($offer_id, $status_id, $respond);

  // SEND EMAIL TO SELLER
  if($notify == 1) {
    mo_notify_buyer($offer_id);
  }
}

osc_add_hook('ajax_mo_respond_offer_manage', 'mo_respond_offer_manage');


function mo_offer_list() {
  include 'user/offer.php'; 
}

osc_add_hook('ajax_mo_offers_list', 'mo_offer_list');


// INSTALL FUNCTION - DEFINE VARIABLES
function mo_call_after_install() {
  ModelMO::newInstance()->import('make_offer/model/struct.sql');
  
  // General settings
  osc_set_preference('validate', 0, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('only_reg', 0, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('show_status', 1, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('show_quantity', 1, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('add_price', 0, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('add_hook', 1, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('notify', 1, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('category', '', 'plugin-make_offer', 'STRING');
  osc_set_preference('history', 1, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('instant_messenger', 0, 'plugin-make_offer', 'INTEGER');
  osc_set_preference('check_styled', 1, 'plugin-make_offer', 'INTEGER');



  // UPLOAD EMAIL TEMPLATES
  $locales = OSCLocale::newInstance()->listAllEnabled();
  foreach($locales as $l) {
    $email_text  = '<p>Hi {TO_NAME},</p>';
    $email_text .= '<p>{FROM_NAME} has sent you new offer on your listing {ITEM_LINK}:<hr></p>';
    $email_text .= '<p>{OFFER}</p>';
    $email_text .= '<p><hr></p>';
    $email_text .= '<p><br/></p>';
    $email_text .= '<p>You can check your offers by <a target="_blank" href="{OFFER_LINK}">clicking here</a>.';
    $email_text .= '<p>Remember, all offers can be viewed under "Offers" in your user account.</p>';
    $email_text .= '<p></p>';
    $email_text .= '<p>Thank you, <br />{WEB_TITLE}</p>';

    $mo_notify_seller[$l['pk_c_code']]['s_title'] = '[{WEB_TITLE}] New offer: {ITEM_TITLE}';
    $mo_notify_seller[$l['pk_c_code']]['s_text'] = $email_text;
  }

  foreach($locales as $l) {
    $email_text  = '<p>Hi {TO_NAME},</p>';
    $email_text .= '<p>{FROM_NAME} has reviewed your offer on your listing {ITEM_LINK}:<hr></p>';
    $email_text .= '<p>{OFFER}<br/></p>';
    $email_text .= '<p>Your offer has been <strong>{OFFER_STATUS}</strong></p>';
    $email_text .= '<p><hr></p>';
    $email_text .= '<p><br/></p>';
    $email_text .= '<p>You can check your offers by <a target="_blank" href="{OFFER_LINK}">clicking here</a>.';
    $email_text .= '<p>Remember, all offers can be viewed under "Offers" in your user account.</p>';
    $email_text .= '<p></p>';
    $email_text .= '<p>Thank you, <br />{WEB_TITLE}</p>';

    $mo_notify_buyer[$l['pk_c_code']]['s_title'] = '[{WEB_TITLE}] Offer reply: {ITEM_TITLE}';
    $mo_notify_buyer[$l['pk_c_code']]['s_text'] = $email_text;
  }

  Page::newInstance()->insert( array('s_internal_name' => 'mo_notify_buyer', 'b_indelible' => '1'), $mo_notify_buyer);
  Page::newInstance()->insert( array('s_internal_name' => 'mo_notify_seller', 'b_indelible' => '1'), $mo_notify_seller);

}


function mo_call_after_uninstall() {
  ModelMO::newInstance()->uninstall();
  osc_delete_preference('validate', 'plugin-make_offer');
  osc_delete_preference('only_reg', 'plugin-make_offer');
  osc_delete_preference('show_status', 'plugin-make_offer');
  osc_delete_preference('show_quantity', 'plugin-make_offer');
  osc_delete_preference('add_price', 'plugin-make_offer');
  osc_delete_preference('add_hook', 'plugin-make_offer');
  osc_delete_preference('notify', 'plugin-make_offer');
  osc_delete_preference('category', 'plugin-make_offer');
  osc_delete_preference('history', 'plugin-make_offer');


  // get list of primary keys of static pages (emails) that should be deleted on uninstall
  $pages = ModelMO::newInstance()->getPages();  
  foreach($pages as $page) {
    Page::newInstance()->deleteByPrimaryKey($page['pk_i_id']);
  }
}



// ADMIN MENU
function mo_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/make_offer/css/admin.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/make_offer/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/make_offer/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/make_offer/js/admin.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/make_offer/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/make_offer/js/bootstrap-switch.js"></script>';



  if( $title == '') { $title = __('Configure', 'make_offer'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>Make Offer Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=make_offer/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'make_offer') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=make_offer/admin/offers.php"><i class="fa fa-handshake-o"></i><span>' . __('Offers', 'make_offer') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function mo_footer() {
  $pluginInfo = osc_plugin_get_info('make_offer/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="http://osclasspoint.com"><img src="http://osclasspoint.com/favicon.ico" alt="MB Themes" /> osclasspoint.com</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'make_offer') . '</a>';
  $text .= '<a target="_blank" href="http://forums.osclasspoint.com/"><i class="fa fa-handshake-o"></i> ' . __('Support Forums', 'make_offer') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'make_offer') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}



// ADD MENU LINK TO PLUGIN LIST
function mo_admin_menu() {
echo '<h3><a href="#">Make Offer Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'make_offer') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/offers.php') . '">&raquo; ' . __('Offers', 'make_offer') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','mo_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function mo_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'mo_conf' );	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'mo_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'mo_call_after_uninstall');

?>
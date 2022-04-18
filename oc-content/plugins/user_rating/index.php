<?php
/*
  Plugin Name: User Rating Plugin
  Plugin URI: https://osclasspoint.com/osclass-plugins/rating-and-review/user-rating-plugin-i57
  Description: Allow visitors to leave rating and feedback on users
  Version: 2.0.1
  Author: MB Themes
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: user_rating
  Plugin update URI: user-rating-plugin
  Support URI: https://forums.osclasspoint.com/user-rating-plugin/
  Product Key: KlqIQ1ClzI4eQTlNJ7xc
*/

require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelUR.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';


osc_enqueue_style('font-awesome47', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
osc_enqueue_style('ur-user-style', osc_base_url() . 'oc-content/plugins/user_rating/css/user.css?v=' . date('YmdHis'));
osc_register_script('ur-user', osc_base_url() . 'oc-content/plugins/user_rating/js/user.js?v=' . date('YmdHis'), 'jquery');
osc_enqueue_script('ur-user');

osc_add_route('ur-ratings', 'user/rating', 'user/rating', osc_plugin_folder(__FILE__).'user/rating.php', true, 'ur', 'myrating', __('Ratings', 'user_rating'));


// ADD LINK TO USER ACCOUNT LEFT SIDEBAR
function ur_user_menu(){
  if(ur_param('user_sidebar') == 1) {
    if(osc_current_web_theme() == 'veronika' || osc_current_web_theme() == 'stela' || osc_current_web_theme() == 'starter' || (defined('USER_MENU_ICONS') && USER_MENU_ICONS == 1)) {
      echo '<li class="opt_user_rating"><a href="' . osc_route_url('ur-ratings') . '" ><i class="fa fa-star"></i> ' . __('Ratings', 'user_rating') . '</a></li>';
    } else {
      echo '<li class="opt_user_rating"><a href="' . osc_route_url('ur-ratings') . '" >' . __('Ratings', 'user_rating') . '</a></li>';
    }
  }
}

osc_add_hook('user_menu', 'ur_user_menu');



// ADD RATING TO DATABASE
function ur_new_rating_manage() {
  $only_logged =  ur_param('only_reg') <> '' ? ur_param('only_reg') : 0;
  
  if(($only_logged == 1 && osc_is_web_user_logged_in()) || $only_logged == 0) {
    $user_id = Params::getParam('userId');
    $user_email = base64_decode(Params::getParam('userEmail'));
    $from_user_id = Params::getParam('fromUserId');

    $r_type = Params::getParam('ratingType');
    $cat0 = Params::getParam('cat0');
    $cat1 = Params::getParam('cat1');
    $cat2 = Params::getParam('cat2');
    $cat3 = Params::getParam('cat3');
    $cat4 = Params::getParam('cat4');
    $cat5 = Params::getParam('cat5');
    $response = osc_esc_html(Params::getParam('response'));

    ModelUR::newInstance()->insertRating($user_id, $user_email, $from_user_id, $r_type, $cat0, $cat1, $cat2, $cat3, $cat4, $cat5, $response);
  }
}

osc_add_hook('ajax_ur_new_rating_manage', 'ur_new_rating_manage');



// function ur_show_rating() {
  // include 'user/rating.php';
// }




// ADD NOTIFICATION TO ADMIN TOOLBAR MENU
function ur_admin_toolbar(){
  if( !osc_is_moderator() && ur_param('validate') == 1) {
    $total = ModelUR::newInstance()->countNotValidated();
    $total = isset($total['i_count']) ? $total['i_count'] : 0;

    if($total > 0) {
      $title = '<i class="circle circle-red">'.$total.'</i>' . ($total == 1 ? __('New Rating', 'user_rating') : __('New Ratings', 'user_rating'));
      AdminToolbar::newInstance()->add_menu(
        array(
          'id' => 'user_rating',
          'title' => $title,
          'href'  => osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=user_rating/admin/rating.php',
          'meta'  => array('class' => 'action-btn action-btn-black')
        )
      );
    }
  }
}

osc_add_hook( 'add_admin_toolbar_menus', 'ur_admin_toolbar', 1 );




// INSTALL FUNCTION - DEFINE VARIABLES
function ur_call_after_install() {
  ModelUR::newInstance()->import('user_rating/model/struct.sql');
  
  // General settings
  osc_set_preference('validate', 0, 'plugin-user_rating', 'INTEGER');
  osc_set_preference('only_reg', 0, 'plugin-user_rating', 'INTEGER');
  osc_set_preference('user_sidebar', 1, 'plugin-user_rating', 'INTEGER');
  osc_set_preference('hook_item', 1, 'plugin-user_rating', 'INTEGER');
  osc_set_preference('monocolor_stars', 0, 'plugin-user_rating', 'INTEGER');
  osc_set_preference('upscale_bars', 1, 'plugin-user_rating', 'INTEGER');


  // Ranks
  osc_set_preference('reg_level1_avg', '4', 'plugin-user_rating', 'STRING');
  osc_set_preference('reg_level1_days', '180', 'plugin-user_rating', 'STRING');
  osc_set_preference('reg_level1_count', '10', 'plugin-user_rating', 'STRING');

  osc_set_preference('reg_level2_avg', '3.5', 'plugin-user_rating', 'STRING');
  osc_set_preference('reg_level2_days', '90', 'plugin-user_rating', 'STRING');
  osc_set_preference('reg_level2_count', '6', 'plugin-user_rating', 'STRING');

  osc_set_preference('reg_level3_avg', '3', 'plugin-user_rating', 'STRING');
  osc_set_preference('reg_level3_days', '30', 'plugin-user_rating', 'STRING');
  osc_set_preference('reg_level3_count', '3', 'plugin-user_rating', 'STRING');

  osc_set_preference('unreg_level2_avg', '4', 'plugin-user_rating', 'STRING');
  osc_set_preference('unreg_level2_count', '10', 'plugin-user_rating', 'STRING');

  osc_set_preference('unreg_level3_avg', '3.5', 'plugin-user_rating', 'STRING');
  osc_set_preference('unreg_level3_count', '5', 'plugin-user_rating', 'STRING');
}


function ur_call_after_uninstall() {
  ModelUR::newInstance()->uninstall();
  osc_delete_preference('validate', 'plugin-user_rating');
  osc_delete_preference('only_reg', 'plugin-user_rating');
  osc_delete_preference('user_sidebar', 'plugin-user_rating');
  osc_delete_preference('hook_item', 'plugin-user_rating');
  osc_delete_preference('monocolor_stars', 'plugin-user_rating');
  osc_delete_preference('upscale_bars', 'plugin-user_rating');
  osc_delete_preference('reg_level1_avg', 'plugin-user_rating');
  osc_delete_preference('reg_level1_days', 'plugin-user_rating');
  osc_delete_preference('reg_level1_count', 'plugin-user_rating');
  osc_delete_preference('reg_level2_avg', 'plugin-user_rating');
  osc_delete_preference('reg_level2_days', 'plugin-user_rating');
  osc_delete_preference('reg_level2_count', 'plugin-user_rating');
  osc_delete_preference('reg_level3_avg', 'plugin-user_rating');
  osc_delete_preference('reg_level3_days', 'plugin-user_rating');
  osc_delete_preference('reg_level3_count', 'plugin-user_rating');
  osc_delete_preference('unreg_level2_avg', 'plugin-user_rating');
  osc_delete_preference('unreg_level2_count', 'plugin-user_rating');
  osc_delete_preference('unreg_level3_avg', 'plugin-user_rating');
  osc_delete_preference('unreg_level3_count', 'plugin-user_rating');
}



// ADMIN MENU
function ur_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/user_rating/css/admin.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/user_rating/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/user_rating/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="//fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" rel="stylesheet" type="text/css" />';
  echo '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/user_rating/js/admin.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/user_rating/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/user_rating/js/bootstrap-switch.js"></script>';



  if( $title == '') { $title = __('Configure', 'user_rating'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>User Rating Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=user_rating/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'user_rating') . '</span></a></li>';
  $text .= '<li><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=user_rating/admin/rating.php"><i class="fa fa-comments"></i><span>' . __('Rating', 'user_rating') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function ur_footer() {
  $pluginInfo = osc_plugin_get_info('user_rating/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="http://osclasspoint.com"><img src="http://osclasspoint.com/favicon.ico" alt="MB Themes" /> osclasspoint.com</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'user_rating') . '</a>';
  $text .= '<a target="_blank" href="http://forums.osclasspoint.com/"><i class="fa fa-comments"></i> ' . __('Support Forums', 'user_rating') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'user_rating') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}



// ADD MENU LINK TO PLUGIN LIST
function ur_admin_menu() {
echo '<h3><a href="#">User Rating Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'user_rating') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/rating.php') . '">&raquo; ' . __('Rating', 'user_rating') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','ur_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function ur_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'ur_conf' );	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'ur_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'ur_call_after_uninstall');

?>
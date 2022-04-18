<?php
/*
  Plugin Name: Banners & Advertisement Plugin
  Plugin URI: https://osclasspoint.com/osclass-plugins/advertisement-and-monetize/banner-ads-plugin-i56
  Description: Monetize your classifieds creating banners and advertisement
  Version: 2.0.0
  Author: MB Themes
  Author URI: http://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: banner_ads
  Plugin update URI: banner-ads-plugin
  Support URI: http://forums.osclasspoint.com/banner-ads-plugin/
  Product Key: PQCkHq7JVdhTpFxeeR3l
*/


require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelBA.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';


osc_add_route('ba-advert', 'advertisement-preview/(.+)', 'advertisement-preview/{key}', osc_plugin_folder(__FILE__).'form/advert.php', false);


// ADVERT REDIRECT
if(Params::getParam('baAdvertRedirect') <> '' && Params::getParam('baAdvertId') <> '' && Params::getParam('baAdvertId') > 0) {
  if(!osc_is_admin_user_logged_in()) {
    ModelBA::newInstance()->updateClicks(Params::getParam('baAdvertId'));
  }

  header('Location:' . urldecode(Params::getParam('baAdvertRedirect')));
  exit;
}


// ADD SLIDE CONFIGURATION TO FOOTER
function ba_slide_js() {
?>
<script type="text/javascript"> 
  $(document).ready(function(){
    $('.ba-banner.ba-slide').each(function(){
      var banner = $(this);
      var slide = 1;
      var slideCount = $(this).attr('data-slide-count');
      var slideSpeed = <?php echo ba_param('slide_speed'); ?>;
      var slideTicker = <?php echo ba_param('slide_ticker'); ?>;

      setInterval(function(){ 
        slide = slide + 1;
        if(slide > slideCount) {
          slide = 1;
        }

        banner.find('.ba-advert.active').fadeOut(slideSpeed, function() {
          $(this).removeClass('active');
          banner.find('.ba-advert[data-slide-id="' + slide + '"]').addClass('active').fadeIn(slideSpeed);
        });
      }, slideTicker);
    });
  });
</script>
<?php
}

osc_add_hook('footer', 'ba_slide_js');


// INSTALL FUNCTION - DEFINE VARIABLES
function ba_call_after_install() {
  ModelBA::newInstance()->import('banner_ads/model/struct.sql');
  
  // General settings
  osc_set_preference('validate', 0, 'plugin-banner_ads', 'INTEGER');
  osc_set_preference('hooks', 'hook1,hook2,hook3', 'plugin-banner_ads', 'STRING');
  osc_set_preference('currency', '$', 'plugin-banner_ads', 'STRING');
  osc_set_preference('slide_speed', '200', 'plugin-banner_ads', 'STRING');
  osc_set_preference('slide_ticker', '5000', 'plugin-banner_ads', 'STRING');

}


function ba_call_after_uninstall() {
  ModelBA::newInstance()->uninstall();
  osc_delete_preference('validate', 'plugin-banner_ads');
  osc_delete_preference('hooks', 'plugin-banner_ads');
  osc_delete_preference('currency', 'plugin-banner_ads');
  osc_delete_preference('slide_speed', 'plugin-banner_ads');
  osc_delete_preference('slide_ticker', 'plugin-banner_ads');

}



// ADMIN MENU
function ba_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/banner_ads/css/admin.css?v=' . date('YmdHis') . '" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/banner_ads/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/banner_ads/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/banner_ads/js/admin.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/banner_ads/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/banner_ads/js/bootstrap-switch.js"></script>';



  if( $title == '') { $title = __('Dashboard', 'banner_ads'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>Banners & Advertisement Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=banner_ads/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'banner_ads') . '</span></a></li>';
  $text .= '<li><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=banner_ads/admin/banners.php"><i class="fa fa-clone"></i><span>' . __('Banners', 'banner_ads') . '</span></a></li>';
  $text .= '<li><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=banner_ads/admin/adverts.php"><i class="fa fa-bookmark-o"></i><span>' . __('Adverts', 'banner_ads') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function ba_footer() {
  $pluginInfo = osc_plugin_get_info('banner_ads/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="https://osclasspoint.com"><img src="https://osclasspoint.com/favicon.ico" alt="MB Themes" /> osclasspoint.com</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'banner_ads') . '</a>';
  $text .= '<a target="_blank" href="https://forums.osclasspoint.com/"><i class="fa fa-comments"></i> ' . __('Support Forums', 'banner_ads') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'banner_ads') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}



// ADD MENU LINK TO PLUGIN LIST
function ba_admin_menu() {
echo '<h3><a href="#">Banners & Advertisement Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Dashboard', 'banner_ads') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/banners.php') . '">&raquo; ' . __('Banners', 'banner_ads') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/adverts.php') . '">&raquo; ' . __('Adverts', 'banner_ads') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','ba_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function ba_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'ba_conf' );	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'ba_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'ba_call_after_uninstall');

?>
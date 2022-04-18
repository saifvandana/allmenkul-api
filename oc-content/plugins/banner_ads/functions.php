<?php

// ADD SOME STYLES TO FOOTER
function ba_footer_asset() {
?>
  <style>
  .ba-banner {display:block;width:auto;height:auto;max-width:100%;overflow:hidden;margin:0px auto;text-align:center;clear:both;}
  .ba-advert {display:block;width:auto;height:auto;max-width:100%;overflow:hidden;margin:10px auto;text-align:center;}
  .ba-advert img.ba-advert-image {width:auto;height:auto;max-width:100%;max-height:100%;display:block;margin:auto auto;}
  .ba-banner.ba-slide .ba-advert:not(.active) {display:none;}
  </style>
<?php
}

osc_add_hook('footer', 'ba_footer_asset');


// SHOW BANNER AND IT'S ADVERTS
function ba_show_banner($id) {
  $banner = ModelBA::newInstance()->getBanner($id);
  $adverts = ModelBA::newInstance()->getAdvertByBannerId($id);

  $count = count($adverts);
  $rand = rand(1, $count);
  $i = 1;
  $html = '<div class="ba-banner' . ($banner['i_type'] == 2 ? ' ba-slide' : '') . '" data-banner-id="' . $id . '" ' . ($banner['i_type'] == 2 ? 'data-slide-count="' . $count . '"' : '') . '>';

  foreach($adverts as $a) {
    if(($banner['i_type'] == 3 && $i == $rand) || $banner['i_type'] <> 3) {
      
      if($banner['i_type'] == 2 && $i == 1) {
        $slide_class = ' active';
      } else {
        $slide_class = '';
      }


      $html .= '<div class="ba-advert' . $slide_class . '" data-advert-id="' . $a['pk_i_id'] . '" style="' . ($a['s_size_width'] <> '' ? 'width:' . $a['s_size_width'] . ';' : '') . ($a['s_size_height'] <> '' ? 'height:' . $a['s_size_height'] . ';' : '') . '" ' . ($banner['i_type'] == 2 ? 'data-slide-id="' . $i . '"' : '') . '>';

      if($a['s_url'] <> '' && $a['i_type'] <> 3) {
        $html .= '<a href="' . osc_base_url(true) . '?baAdvertId=' . $a['pk_i_id'] . '&baAdvertRedirect=' . urlencode($a['s_url']) . '" target="_blank">';
      }

      if($a['i_type'] == 2) {
        $html .= '<img class="ba-advert-image" src="' . osc_base_url() . 'oc-content/plugins/banner_ads/img/advert/' . $a['s_image'] . '"/>';
      } else {
        $html .= $a['s_code'];
      }

      if($a['s_url'] <> '' && $a['i_type'] <> 3) {
        $html .= '</a>';
      }

      $html .= '</div>';

      if(!osc_is_admin_user_logged_in()) {
        ModelBA::newInstance()->updateViews($a['pk_i_id']);
      }
    }

    $i++;
  }

  $html .= '</div>';

  echo $html;
  return $html;
}


// SHOW ADVERT
function ba_show_advert($id) {
  $a = ModelBA::newInstance()->getAdvert($id);
  
  $html = '';

  $html .= '<div class="ba-advert" data-advert-id="' . $a['pk_i_id'] . '" style="' . ($a['s_size_width'] <> '' ? 'width:' . $a['s_size_width'] . ';' : '') . ($a['s_size_height'] <> '' ? 'height:' . $a['s_size_height'] . ';' : '') . '">';

  if($a['s_url'] <> '' && $a['i_type'] <> 3) {
    $html .= '<a href="' . osc_base_url(true) . '?baAdvertId=' . $a['pk_i_id'] . '&baAdvertRedirect=' . urlencode($a['s_url']) . '" target="_blank">';
  }

  if($a['i_type'] == 2) {
    $html .= '<img class="ba-advert-image" src="' . osc_base_url() . 'oc-content/plugins/banner_ads/img/advert/' . $a['s_image'] . '"/>';
  } else {
    $html .= $a['s_code'];
  }

  if($a['s_url'] <> '' && $a['i_type'] <> 3) {
    $html .= '</a>';
  }

  $html .= '</div>';

  if(!osc_is_admin_user_logged_in()) {
    ModelBA::newInstance()->updateViews($a['pk_i_id']);
  }

  return $html;
}


// HOOK FUNCTION - SHOW ALL BANNERS WITH SPECIFIC HOOK
function ba_hook($hook) {
  $banners = ModelBA::newInstance()->getBannersByHook($hook);

  if(count($banners) > 0) {
    foreach($banners as $b) { 
      ba_show_banner($b['pk_i_id']);
      osc_run_hook('ba_show_banner', $b['pk_i_id']);
    }
  }
}


// LIST OF USER DEFINED HOOKS
function ba_hooks() {
  $user_hooks = osc_get_preference('hooks', 'plugin-banner_ads');

  $hooks = explode(',', $user_hooks);
  $hooks = array_map('trim', $hooks);
  $hooks = array_filter($hooks);

  $array = array();

  if(count($hooks) > 0) {
    foreach($hooks as $h) {
      $array[] = $h;
    }
  }

  return $array;   
}


// CREATE IMAGE LINK
function ba_image_link($id, $file_name = '') {
  if($file_name <> '') {
    return osc_base_url() . 'oc-content/plugins/banner_ads/img/advert/' . $file_name;
  } else {
    $a = ModelBA::newInstance()->getAdvert($id);

    if($a['s_image'] <> '') {
      return osc_base_url() . 'oc-content/plugins/banner_ads/img/advert/' . $a['s_image'];
    } else {
      return false;
    }
  }
}


// CHECK IF RUNNING ON DEMO
function ba_is_demo($ignore_admin = false) {
  if(!$ignore_admin && osc_logged_admin_username() == 'admin') {
    return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
  }
}

// CORE FUNCTIONS
function ba_param($name) {
  return osc_get_preference($name, 'plugin-banner_ads');
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


?>
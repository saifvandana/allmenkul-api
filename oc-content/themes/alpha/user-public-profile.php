<?php
  $address = '';
  if(osc_user_address()!='') {
    $address = osc_user_address();
  }

  $location = alp_get_full_loc(osc_user_field('fk_c_country_code'), osc_user_region_id(), osc_user_city_id());

  if(osc_user_zip() <> '') {
    $location .= ' ' . osc_user_zip();
  }

  $user = osc_user();


  $mobile_found = true;

  $mobile = $user['s_phone_mobile'];
  if($mobile == '') { $mobile = $user['s_phone_land']; } 
 
  if(trim($mobile) == '' || strlen(trim($mobile)) < 4) { 
    $mobile = __('No phone number', 'alpha');
    $mobile_found = false;
  }  

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
  <script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.validate.min.js') ; ?>"></script>
</head>

<body id="body-user-public-profile">
  <?php View::newInstance()->_exportVariableToView('user', $user); ?>
  <?php osc_current_web_theme_path('header.php') ; ?>

  <div class="inside user_public_profile">
    <!-- LEFT BLOCK -->
    <div id="pp-side">
      <div class="img">
        <div class="box"><img src="<?php echo alp_profile_picture($user['pk_i_id']); ?>"/></div>
        <strong><?php echo osc_user_name(); ?></strong>
      </div>
 
      <?php if($location != '' || $address != '') { ?>
        <div class="loc-wrap">
          <?php if ($location != '') { ?><div class="location"><?php echo $location; ?></div><?php } ?>
          <?php if ($address != '') { ?><div class="address"><?php echo $address; ?></div><?php } ?>
        </div>
      <?php } ?>

      <?php if($mobile_found) { ?>
        <div class="phone-wrap">
          <a href="#" class="mobile" data-phone="<?php echo $mobile; ?>" title="<?php echo osc_esc_html(__('Click to show number', 'alpha')); ?>"><?php echo substr($mobile, 0, strlen($mobile) - 4) . 'xxxx'; ?></a>
        </div>
      <?php } ?>

      <?php if (osc_user_website() != '') { ?><div class="web"><a href="<?php echo osc_user_website(); ?>" target="_blank" rel="nofollow"><?php echo osc_user_website(); ?></a></div><?php } ?>

      <?php if (osc_user_info() <> '') { ?><div class="desc"><?php echo osc_user_info(); ?></div><?php } ?>

      <?php if(osc_reg_user_can_contact() && osc_is_web_user_logged_in() || !osc_reg_user_can_contact() ) { ?>
        <div class="buttons">
          <a href="<?php echo alp_fancy_url('contact_public', array('userId' => osc_user_id())); ?>" class="open-form contact_public btn alpBg" data-type="contact_public" data-user-id="<?php echo osc_user_id(); ?>"><?php _e('Message seller', 'alpha'); ?></a>
        </div>
      <?php } ?>
    </div>




    <!-- LISTINGS OF SELLER -->
    <div id="public-items" class="products grid">
      <h1><?php _e('Latest items of seller', 'alpha'); ?></h1>

      <?php if(osc_count_items() > 0) { ?>
        <div class="block">
          <div class="wrap">
            <?php $c = 1; ?>
            <?php while( osc_has_items() ) { ?>
              <?php alp_draw_item($c); ?>
        
              <?php $c++; ?>
            <?php } ?>
          </div>
        </div>
      <?php } else { ?>
        <div class="ua-items-empty"><img src="<?php echo osc_current_web_theme_url('images/ua-empty.jpg'); ?>"/> <span><?php _e('This seller has no active listings', 'alpha'); ?></span></div>
      <?php } ?>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){

      // SHOW PHONE NUMBER
      $('body').on('click', '.phone-wrap .mobile', function(e) {
        if($(this).attr('href') == '#') {
          e.preventDefault()

          var phoneNumber = $(this).attr('data-phone');
          $(this).text(phoneNumber);
          $(this).attr('href', 'tel:' + phoneNumber);
          $(this).attr('title', '<?php echo osc_esc_js(__('Click to call', 'alpha')); ?>');
        }        
      });

    });
  </script>


  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>
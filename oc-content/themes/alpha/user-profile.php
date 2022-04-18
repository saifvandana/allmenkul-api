<?php
  $locales = __get('locales');
  $user = osc_user();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
</head>

<body id="body-user-profile" class="body-ua">
  <?php osc_current_web_theme_path('header.php') ; ?>
  <div class="inside user_account">
    <div class="user-menu-wrap">
      <div class="user-button isMobile"><?php _e('Menu', 'alpha'); ?><i class="fa fa-angle-down"></i></div>

      <div id="user-menu" class="sc-block">
        <?php echo alp_user_menu(); ?>
        <?php if(function_exists('profile_picture_upload')) { profile_picture_upload(); } ?>
      </div>
    </div>

    <div id="main" class="profile">
      <div class="inside">

        <div class="box">
          <h1><?php _e('My profile', 'alpha'); ?></h1>

          <form action="<?php echo osc_base_url(true); ?>" method="post">
            <input type="hidden" name="page" value="user" />
            <input type="hidden" name="action" value="profile_post" />

            <div id="left-user" class="line">
              <div class="row">
                <label for="name"><span><?php _e('Name', 'alpha'); ?></span><span class="req">*</span></label>
                <div class="input-box"><?php UserForm::name_text(osc_user()); ?></div>

                <?php if(function_exists('profile_picture_show')) { ?>
                  <a href="#" class="update-avatar"><?php _e('Update avatar', 'alpha'); ?></a>
                <?php } ?>
              </div>



              <div class="row">
                <label for="email"><span><?php _e('E-mail', 'alpha'); ?></span></label>
                <span class="update current_email">
                  <span><?php echo osc_user_email(); ?></span>
                </span>
              </div>

              <div class="row">
                <label for="phoneMobile"><span><?php _e('Mobile phone', 'alpha'); ?></span><span class="req">*</span></label>
                <div class="input-box"><?php UserForm::mobile_text(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="phoneLand"><?php _e('Land Phone', 'alpha'); ?></label>
                <div class="input-box"><?php UserForm::phone_land_text(osc_user()); ?></div>
              </div>                        

              <div class="row">
                <label for="info"><?php _e('About you', 'alpha'); ?></label>
                <?php UserForm::multilanguage_info($locales, osc_user()); ?>
              </div>
            </div>

            <div id="right-user" class="line">
              <div class="row">
                <input type="hidden" name="countryId" id="countryId" class="sCountry" value="<?php echo $user['fk_c_country_code']; ?>"/>
                <input type="hidden" name="regionId" id="regionId" class="sRegion" value="<?php echo $user['fk_i_region_id']; ?>"/>
                <input type="hidden" name="cityId" id="cityId" class="sCity" value="<?php echo $user['fk_i_city_id']; ?>"/>

                <label for="term"><?php _e('Location', 'alpha'); ?></label>

                <div id="location-picker" class="loc-picker ctr-<?php echo (alp_count_countries() == 1 ? 'one' : 'more'); ?>">
                  <input type="text" name="term" id="term" class="term" placeholder="<?php _e('Location', 'alpha'); ?>" value="<?php echo alp_get_term(Params::getParam('term'), alp_ajax_country(), alp_ajax_region(), alp_ajax_city()); ?>" autocomplete="off"/>
                  <i class="fa fa-angle-down"></i>

                  <div class="shower-wrap">
                    <div class="shower" id="shower">
                      <?php echo alp_def_location(); ?>
                    </div>
                  </div>

                  <div class="loader"></div>
                </div>
              </div>

              <div class="row">
                <label for="cityArea"><?php _e('City Area', 'alpha'); ?></label>
                <div class="input-box"><?php UserForm::city_area_text(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="address"><?php _e('Street', 'alpha'); ?></label>
                <div class="input-box"><?php UserForm::address_text(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="address"><?php _e('ZIP', 'alpha'); ?></label>
                <div class="input-box"><?php UserForm::zip_text(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="user_type"><?php _e('User type', 'alpha'); ?></label>
                <div class="input-box"><?php UserForm::is_company_select(osc_user()); ?></div>
              </div>

              <div class="row">
                <label for="webSite"><?php _e('Website', 'alpha'); ?></label>
                <div class="input-box"><?php UserForm::website_text(osc_user()); ?></div>
              </div>

              <?php osc_run_hook('user_form'); ?>

              <div class="row user-buttons">
                <button type="submit" class="btn btn-primary alpBg"><?php _e('Save changes', 'alpha'); ?></button>
              </div>
            </div>
          </form>
        </div>


        <!-- CHANGE EMAIL FORM -->
        <div class="box second">
          <h3 class="tblock"><?php _e('Change email', 'alpha'); ?></h3>

          <form action="<?php echo osc_base_url(true); ?>" method="post" id="user_email_change" class="user-change" style="display:none;">
            <?php if(alp_is_demo()) { ?>
            <input type="hidden" name="page" value="user" />
            <input type="hidden" name="action" value="change_email_post" />
            <?php } ?>

            <div class="row">
              <label for="email"><?php _e('Current e-mail', 'alpha'); ?></label>
              <span class="bold current_email"><?php echo osc_logged_user_email(); ?></span>
            </div>

            <div class="row">
              <label for="new_email"><?php _e('New e-mail', 'alpha'); ?> *</label>
              <div class="input-box"><input type="text" name="new_email" id="new_email" value="" /></div>
            </div>

            <div class="row user-buttons">
              <?php if(alp_is_demo()) { ?>
                <a class="btn alpBg disabled" onclick="return false;" title="<?php echo osc_esc_html(__('You cannot do this on demo site', 'alpha')); ?>"><?php _e('Change email', 'alpha'); ?></a>
              <?php } else { ?>
                <button type="submit" class="btn alpBg"><?php _e('Change email', 'alpha'); ?></button>
              <?php } ?>
            </div>
          </form>
        </div>


        <!-- CHANGE PASSWORD FORM -->
        <div class="box third">
          <h3 class="tblock"><?php _e('Change password', 'alpha'); ?></h3>

          <form action="<?php echo osc_base_url(true); ?>" method="post" id="user_password_change" class="user-change" style="display:none;">
            <?php if(!alp_is_demo()) { ?>
            <input type="hidden" name="page" value="user" />
            <input type="hidden" name="action" value="change_password_post" />
            <?php } ?>

            <div class="row">
              <label for="password"><?php _e('Current password', 'alpha'); ?> *</label>
              <div class="input-box"><input type="password" name="password" id="password" value="" /></div>
            </div>

            <div class="row">
              <label for="new_password"><?php _e('New password', 'alpha'); ?> *</label>
              <div class="input-box"><input type="password" name="new_password" id="new_password" value="" /></div>
            </div>

            <div class="row">
              <label for="new_password2"><?php _e('Repeat new password', 'alpha'); ?> *</label>
              <div class="input-box"><input type="password" name="new_password2" id="new_password2" value="" /></div>
            </div>


            <div class="row user-buttons">
              <?php if(alp_is_demo()) { ?>
                <a class="btn alpBg disabled" onclick="return false;" title="<?php echo osc_esc_html(__('You cannot do this on demo site', 'alpha')); ?>"><?php _e('Change password', 'alpha'); ?></a>
              <?php } else { ?>
                <button type="submit" class="btn alpBg"><?php _e('Change password', 'alpha'); ?></button>
              <?php } ?>
            </div>
          </form>
        </div>
      </div>

      <?php if(!alp_is_demo()) { ?>
        <a class="btn-remove-account btn" href="<?php echo osc_base_url(true).'?page=user&action=delete&id='.osc_user_id().'&secret='.$user['s_secret']; ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to delete your account? This action cannot be undone', 'alpha')); ?>?')"><span><?php _e('Delete account', 'alpha'); ?></span></a>
      <?php } ?>
    </div>
  </div>

  <?php 
    $locale = osc_get_current_user_locale();
    $locale_code = $locale['pk_c_code'];
    $locale_name = $locale['s_name'];
  ?>

  <script>
    $(document).ready(function() {

      // Unify selected locale  
      function alpUserLocCheck() {
        if($('.tabbernav li').length) {
          var localeText = "<?php echo trim(osc_esc_html($locale_name)); ?>";

          $('.tabbernav > li > a:contains("' + localeText+ '")').click();

          clearInterval(checkTimer);
          return;
        }
      }

      var checkTimer = setInterval(alpUserLocCheck, 150);

    });
  </script>

  <?php osc_current_web_theme_path('footer.php'); ?>
</body>
</html>
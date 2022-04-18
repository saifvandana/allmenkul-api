<?php osc_goto_first_locale(); ?>

<header>
  <div class="inside">
    <div class="left">
      <div class="logo">
        <a href="<?php echo osc_base_url(); ?>"><?php echo logo_header(); ?></a>
      </div>
    </div>

    <div class="right isDesktop isTablet">

      <?php if(osc_is_web_user_logged_in()) { ?>
        <a class="logout" href="<?php echo osc_user_logout_url(); ?>"><i class="fa fa-sign-out"></i></a>
      <?php } ?>

      <a class="publish btn alpBg" href="<?php echo osc_item_post_url(); ?>">
        <span><?php _e('Add listing', 'alpha'); ?></span>
      </a>

      <div class="header-user">
        <?php if(osc_is_web_user_logged_in()) { ?>
          <a class="profile is-logged" href="<?php echo osc_user_dashboard_url(); ?>">
            <span class="img"><img src="<?php echo alp_profile_picture(osc_logged_user_id(), 'small'); ?>" /></span>
            <span><?php _e('My account', 'alpha'); ?></span>
          </a>
        <?php } else { ?>
          <a class="profile not-logged" href="<?php echo osc_register_account_url(); ?>"><?php _e('Register', 'alpha'); ?></a>
          <span class="or"><?php _e('or', 'alpha'); ?></span>
          <a class="profile not-logged" href="<?php echo osc_user_login_url(); ?>"><?php _e('Log in', 'alpha'); ?></a>

        <?php } ?>
      </div>

      <?php if(function_exists('blg_home_link')) { ?>
        <a href="<?php echo blg_home_link(); ?>"><?php _e('Blog', 'alpha'); ?></a>
      <?php } ?>

      <?php if(function_exists('bpr_companies_url')) { ?>
        <a href="<?php echo bpr_companies_url(); ?>"><?php _e('Companies', 'alpha'); ?></a>
      <?php } ?>

      <?php if(function_exists('frm_home')) { ?>
        <a href="<?php echo frm_home(); ?>"><?php _e('Forums', 'alpha'); ?></a>
      <?php } ?>

      <?php if(function_exists('im_messages') && osc_is_web_user_logged_in()) { ?>
        <a href="<?php echo osc_route_url('im-threads'); ?>"><?php _e('Messages', 'alpha'); ?></a>
      <?php } ?>


      <!-- PLUGINS TO HEADER -->
      <div class="plugins">
        <?php osc_run_hook('header_links'); ?>
      </div>

    </div>   

    <div class="mobile-block isMobile">
      <a href="#" id="m-options" class="mobile-menu" data-menu-id="#menu-options"><img src="<?php echo osc_current_web_theme_url('images/mobile-menu.png'); ?>"/></a>
      <?php if(1==2) { ?><a href="#" id="m-user" class="mobile-menu" data-menu-id="#menu-user"><img src="<?php echo osc_current_web_theme_url('images/mobile-user.png'); ?>"/></a><?php } ?>
    </div>
  </div>
</header>

<?php 
  $loc = (osc_get_osclass_location() == '' ? 'home' : osc_get_osclass_location());
  $sec = (osc_get_osclass_section() == '' ? 'default' : osc_get_osclass_section());
?>

<section class="content loc-<?php echo $loc; ?> sec-<?php echo $sec; ?>">

<?php
  if(osc_is_home_page()) { 
    osc_current_web_theme_path('inc.search.php'); 
    osc_current_web_theme_path('inc.category.php');
  }
?>



<div class="flash-box">
  <div class="flash-wrap">
    <?php osc_show_flash_message(); ?>
  </div>
</div>


<?php
  osc_show_widgets('header');
  $breadcrumb = osc_breadcrumb('/', false);
  $breadcrumb = str_replace('<span itemprop="title">' . osc_page_title() . '</span>', '<span itemprop="title">' . __('Home', 'alpha') . '</span>', $breadcrumb);
?>

<?php if($breadcrumb != '') { ?>
  <div id="bread">
    <?php echo $breadcrumb; ?>
  </div>
<?php } ?>
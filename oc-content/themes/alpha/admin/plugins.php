<?php
  require_once 'functions.php';


  // Create menu
  $title = __('Plugins', 'alpha');
  alp_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = alp_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check, value or code

  $scrolltop = alp_param_update('scrolltop', 'theme_action', 'check', 'theme-alpha');
  $related = alp_param_update('related', 'theme_action', 'check', 'theme-alpha');
  $related_count = alp_param_update('related_count', 'theme_action', 'value', 'theme-alpha');
 

  if(Params::getParam('theme_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'alpha') );
  }
?>


<div class="mb-body">

  <div class="mb-info-box" style="margin:5px 0 30px 0;">
    <div class="mb-line"><strong><?php _e('Plugins for this theme', 'alpha'); ?></strong></div>
    <div class="mb-line"><?php _e('We have modified for you many plugins to fit theme design that will work without need of any modifications', 'alpha'); ?>.</div>
    <div class="mb-line"><?php _e('Plugins are not delivered in theme package, must be downloaded separately', 'alpha'); ?>.</div>
    <div class="mb-line" style="margin:10px 0;"><a href="https://osclasspoint.com/theme-plugins/alpha_plugins_20190201_X7kj9a.zip" target="_blank" class="mb-button-white"><i class="fa fa-download"></i> <?php _e('Download plugins', 'alpha'); ?></a></div>
    <div class="mb-line" style="margin-top:15px;">- <?php _e('upload and extract downloaded file <strong>alpha-plugins.zip</strong> into folder <strong>oc-content/plugins/</strong> on your hosting', 'alpha'); ?>.</div>
    <div class="mb-line">- <?php _e('go to <strong>oc-admin > Plugins</strong> and install plugins you like', 'alpha'); ?>.</div>
  </div>


 
  <!-- PLUGINS SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-puzzle-piece"></i> <?php _e('Plugin settings', 'alpha'); ?></div>

    <div class="mb-inside mb-minify">
      <form action="<?php echo osc_admin_render_theme_url('oc-content/themes/alpha/admin/plugins.php'); ?>" method="POST">
        <input type="hidden" name="theme_action" value="done" />

        <div class="mb-row">
          <label for="scrolltop" class="h1"><span><?php _e('Enable Scroll to Top', 'alpha'); ?></span></label> 
          <input name="scrolltop" id="scrolltop" class="element-slide" type="checkbox" <?php echo (alp_param('scrolltop') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, button that enables scroll to top will be added.', 'alpha'); ?></div>
        </div>

        <div class="mb-row">
          <label for="related" class="h2"><span><?php _e('Enable Related Listings', 'alpha'); ?></span></label> 
          <input name="related" id="related" class="element-slide" type="checkbox" <?php echo (alp_param('related') == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, related listings will be shown at listing page.', 'alpha'); ?></div>
        </div>

        <div class="mb-row">
          <label for="related_count" class="h3"><span><?php _e('Number of Related Items', 'alpha'); ?></span></label> 
          <input name="related_count" id="related_count" type="number" min="1" value="<?php echo alp_param('related_count'); ?>" />

          <div class="mb-explain"><?php _e('Enter how many related listings will be shown on item page.', 'alpha'); ?></div>
        </div>
        


        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <button type="submit" class="mb-button"><?php _e('Save', 'alpha');?></button>
        </div>
      </form>
    </div>
  </div>

</div>


<?php echo alp_footer(); ?>
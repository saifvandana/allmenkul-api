<?php
  // Create menu
  $title = __('Configure', 'user_rating');
  ur_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value

  $validate = mb_param_update( 'validate', 'plugin_action', 'check', 'plugin-user_rating' );
  $only_reg = mb_param_update( 'only_reg', 'plugin_action', 'check', 'plugin-user_rating' );
  $user_sidebar = mb_param_update( 'user_sidebar', 'plugin_action', 'check', 'plugin-user_rating' );
  $hook_item = mb_param_update( 'hook_item', 'plugin_action', 'check', 'plugin-user_rating' );
  $monocolor_stars = mb_param_update( 'monocolor_stars', 'plugin_action', 'check', 'plugin-user_rating' );
  $upscale_bars = mb_param_update( 'upscale_bars', 'plugin_action', 'check', 'plugin-user_rating' );
  
  
  $cat1 = mb_param_update( 'cat1', 'plugin_option', 'check', 'plugin-user_rating' );
  $cat2 = mb_param_update( 'cat2', 'plugin_option', 'check', 'plugin-user_rating' );
  $cat3 = mb_param_update( 'cat3', 'plugin_option', 'check', 'plugin-user_rating' );
  $cat4 = mb_param_update( 'cat4', 'plugin_option', 'check', 'plugin-user_rating' );
  $cat5 = mb_param_update( 'cat5', 'plugin_option', 'check', 'plugin-user_rating' );

  $levels = ur_user_level_list();

  foreach($levels['reg'] as $l) {
    ${'reg_' . $l['id'] . '_avg'} = mb_param_update( 'reg_' . $l['id'] . '_avg', 'plugin_rank', 'value', 'plugin-user_rating' );
    ${'reg_' . $l['id'] . '_days'} = mb_param_update( 'reg_' . $l['id'] . '_days', 'plugin_rank', 'value', 'plugin-user_rating' );
    ${'reg_' . $l['id'] . '_count'} = mb_param_update( 'reg_' . $l['id'] . '_count', 'plugin_rank', 'value', 'plugin-user_rating' );
  }

  foreach($levels['unreg'] as $l) {
    ${'unreg_' . $l['id'] . '_avg'} = mb_param_update( 'unreg_' . $l['id'] . '_avg', 'plugin_rank', 'value', 'plugin-user_rating' );
    ${'unreg_' . $l['id'] . '_count'} = mb_param_update( 'unreg_' . $l['id'] . '_count', 'plugin_rank', 'value', 'plugin-user_rating' );
  }

  if(Params::getParam('plugin_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'user_rating') );
  }

  if(Params::getParam('plugin_option') == 'done') {
    message_ok( __('Rating options were successfully saved', 'user_rating') );
  }

  if(Params::getParam('plugin_rank') == 'done') {
    message_ok( __('User ranks were successfully saved', 'user_rating') );
  }
?>



<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-cog"></i> <?php _e('Configure', 'user_rating'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <?php if(!ur_is_demo()) { ?>
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_action" value="done" />
        <?php } ?>
        
        <div class="mb-row">
          <label for="validate" class="h1"><span><?php _e('Require validation', 'user_rating'); ?></span></label> 
          <input name="validate" id="validate" class="element-slide" type="checkbox" <?php echo ($validate == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('Each rating must be validated by admin before it is shown.', 'user_rating'); ?></div>
        </div>

        <div class="mb-row">
          <label for="only_reg" class="h3"><span><?php _e('Only logged users', 'user_rating'); ?></span></label> 
          <input name="only_reg" id="only_reg" class="element-slide" type="checkbox" <?php echo ($only_reg == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, only logged in users can rate other users.', 'user_rating'); ?></div>
        </div>

        <div class="mb-row">
          <label for="user_sidebar" class="h4"><span><?php _e('Show ratings in user account', 'user_rating'); ?></span></label> 
          <input name="user_sidebar" id="user_sidebar" class="element-slide" type="checkbox" <?php echo ($user_sidebar == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, link to show existing ratings of user is visible in user account sidebar.', 'user_rating'); ?></div>
        </div>

        <div class="mb-row">
          <label for="hook_item" class="h5"><span><?php _e('Hook buttons', 'user_rating'); ?></span></label> 
          <input name="hook_item" id="show_hook" class="element-slide" type="checkbox" <?php echo ($hook_item == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, all buttons (show rating, add rating) are automatically hooked to listing detail (item_detail hook).', 'user_rating'); ?></div>
        </div>

        <div class="mb-row">
          <label for="monocolor_stars" class="h6"><span><?php _e('Monocolor stars', 'user_rating'); ?></span></label> 
          <input name="monocolor_stars" id="monocolor_stars" class="element-slide" type="checkbox" <?php echo ($monocolor_stars == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, only monocolor stars are used (yellow). Star color will not be influenced by overall rating (red to green).', 'user_rating'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="upscale_bars" class="h7"><span><?php _e('Upscale rating bars', 'user_rating'); ?></span></label> 
          <input name="upscale_bars" id="upscale_bars" class="element-slide" type="checkbox" <?php echo ($upscale_bars == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, rating bars of chart on rating page will be upscaled to 100% in order to maximize usage of chart area. Enabled by default.', 'user_rating'); ?></div>
        </div>
        
        <div class="mb-foot">
          <?php if(!ur_is_demo()) { ?>
            <button type="submit" class="mb-button"><?php _e('Save', 'user_rating');?></button>
          <?php } else { ?>
            <a href="#" onclick="return false" title="<?php echo osc_esc_html(__('You are on demo site, this option is not available there', 'user_rating')); ?>" class="mb-button mb-disabled mb-has-tooltip"><?php _e('Save (demo - disabled)', 'user_rating');?></a>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>



  <!-- OPTIONS DEFINITION SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-list"></i> <?php _e('Option configuration', 'user_rating'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_option" value="done" />

        
        <div class="mb-row" style="margin-bottom:20px;"><?php _e('Select options that are available for rating', 'user_rating'); ?></div>


        <div class="mb-row">
          <label for="cat1" class="h2"><span><?php _e('Communication', 'user_rating'); ?></span></label> 
          <input name="cat1" id="cat1" class="element-slide" type="checkbox" <?php echo ($cat1 == 1 ? 'checked' : ''); ?> />
        </div>

        <div class="mb-row">
          <label for="cat2" class="h2"><span><?php _e('Delivery', 'user_rating'); ?></span></label> 
          <input name="cat2" id="cat2" class="element-slide" type="checkbox" <?php echo ($cat2 == 1 ? 'checked' : ''); ?> />
        </div>

        <div class="mb-row">
          <label for="cat3" class="h2"><span><?php _e('Quality', 'user_rating'); ?></span></label> 
          <input name="cat3" id="cat3" class="element-slide" type="checkbox" <?php echo ($cat3 == 1 ? 'checked' : ''); ?> />
        </div>

        <div class="mb-row">
          <label for="cat4" class="h2"><span><?php _e('Speed', 'user_rating'); ?></span></label> 
          <input name="cat4" id="cat4" class="element-slide" type="checkbox" <?php echo ($cat4 == 1 ? 'checked' : ''); ?> />
        </div>

        <div class="mb-row">
          <label for="cat5" class="h2"><span><?php _e('Recommend', 'user_rating'); ?></span></label> 
          <input name="cat5" id="cat5" class="element-slide" type="checkbox" <?php echo ($cat5 == 1 ? 'checked' : ''); ?> />
        </div>

        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if(!ur_is_demo()) { ?>
            <button type="submit" class="mb-button"><?php _e('Save', 'user_rating');?></button>
          <?php } else { ?>
            <a href="#" onclick="return false" title="<?php echo osc_esc_html(__('You are on demo site, this option is not available there', 'user_rating')); ?>" class="mb-button mb-disabled mb-has-tooltip"><?php _e('Save (demo - disabled)', 'user_rating');?></a>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>



  <!-- USER RANKS SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-star"></i> <?php _e('User ranks configuration', 'user_rating'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_rank" value="done" />

        <div class="mb-row" style="font-weight:bold;"><?php _e('Registered user', 'user_rating'); ?></div>
        
        <div class="mb-table mb-with-inputs" style="margin-bottom:30px;">
          <div class="mb-table-head">
            <div class="mb-col-4 mb-align-left"><?php _e('ID', 'user_rating'); ?></div>
            <div class="mb-col-8 mb-align-left"><?php _e('Rank', 'user_rating'); ?></div>
            <div class="mb-col-4 mb-align-left"><?php _e('Min. average rating', 'user_rating'); ?></div>
            <div class="mb-col-4 mb-align-left"><?php _e('Min. rating count', 'user_rating'); ?></div>
            <div class="mb-col-4 mb-align-left"><?php _e('Registered at least (days)', 'user_rating'); ?></div>
          </div>

          <?php foreach($levels['reg'] as $l) { ?>
            <div class="mb-table-row">
              <div class="mb-col-4 mb-align-left"><?php echo $l['id']; ?></div>
              <div class="mb-col-8 mb-align-left mb-bold"><?php echo $l['name']; ?></div>

              <?php if($l['id'] <> 'level4') { ?>
                <div class="mb-col-4"><input size="6" name="reg_<?php echo $l['id']; ?>_avg" id="reg_<?php echo $l['id']; ?>_avg" class="mb-short" type="text" value="<?php echo ${'reg_' . $l['id'] . '_avg'}; ?>" /></div>
                <div class="mb-col-4"><input size="6" name="reg_<?php echo $l['id']; ?>_count" id="reg_<?php echo $l['id']; ?>_count" class="mb-short" type="text" value="<?php echo ${'reg_' . $l['id'] . '_count'}; ?>" /></div>
                <div class="mb-col-4"><input size="6" name="reg_<?php echo $l['id']; ?>_days" id="reg_<?php echo $l['id']; ?>_days" class="mb-short" type="text" value="<?php echo ${'reg_' . $l['id'] . '_days'}; ?>" /></div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>


        <div class="mb-row" style="font-weight:bold;margin-top:20px;"><?php _e('Unregistered user', 'user_rating'); ?></div>

        <div class="mb-table mb-with-inputs" style="margin-bottom:30px;">
          <div class="mb-table-head">
            <div class="mb-col-4 mb-align-left"><?php _e('ID', 'user_rating'); ?></div>
            <div class="mb-col-8 mb-align-left"><?php _e('Rank', 'user_rating'); ?></div>
            <div class="mb-col-4 mb-align-left"><?php _e('Min. average rating', 'user_rating'); ?></div>
            <div class="mb-col-4 mb-align-left"><?php _e('Min. rating count', 'user_rating'); ?></div>
            <div class="mb-col-4 mb-align-left">&nbsp;</div>
          </div>

          <?php foreach($levels['unreg'] as $l) { ?>
            <div class="mb-table-row">
              <div class="mb-col-4 mb-align-left"><?php echo $l['id']; ?></div>
              <div class="mb-col-8 mb-align-left mb-bold"><?php echo $l['name']; ?></div>

              <?php if($l['id'] <> 'level4') { ?>
                <div class="mb-col-4"><input size="6" name="reg_<?php echo $l['id']; ?>_avg" id="reg_<?php echo $l['id']; ?>_avg" class="mb-short" type="text" value="<?php echo ${'reg_' . $l['id'] . '_avg'}; ?>" /></div>
                <div class="mb-col-4"><input size="6" name="reg_<?php echo $l['id']; ?>_count" id="reg_<?php echo $l['id']; ?>_count" class="mb-short" type="text" value="<?php echo ${'reg_' . $l['id'] . '_count'}; ?>" /></div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>

        <div class="mb-foot">
          <?php if(!ur_is_demo()) { ?>
            <button type="submit" class="mb-button"><?php _e('Save', 'user_rating');?></button>
          <?php } else { ?>
            <a href="#" onclick="return false" title="<?php echo osc_esc_html(__('You are on demo site, this option is not available there', 'user_rating')); ?>" class="mb-button mb-disabled mb-has-tooltip"><?php _e('Save (demo - disabled)', 'user_rating');?></a>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>


  <!-- PLUGIN INTEGRATION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Plugin Setup', 'user_rating'); ?></div>

    <div class="mb-inside">
      <div class="mb-row"><?php _e('No theme modification are required to use all functions of plugin, but you may want to customize positions of buttons', 'user_rating'); ?></div>

      <div class="mb-row">
        <div class="mb-line"><?php _e('To add link to leave rating on user, place following code to theme files', 'user_rating'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('ur_button_add')) { echo ur_button_add($user_id = osc_item_user_id(), $item_id = osc_item_id()); } ?&gt;</span>
      </div>


      <div class="mb-row">&nbsp;</div>


      <div class="mb-row">
        <div class="mb-line"><?php _e('To add link to show rating of user, place following code to theme files', 'user_rating'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('ur_button_show')) { echo ur_button_show($user_id = osc_item_user_id(), $user_email = osc_item_contact_email(), $item_id = osc_item_id()); } ?&gt;</span>
      </div>


      <div class="mb-row">&nbsp;</div>


      <div class="mb-row">
        <div class="mb-line"><?php _e('To add user rating status (stars and rating) with click action to show rating of this user, place following code to theme files', 'user_rating'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('ur_button_stars')) { echo ur_button_stars($user_id = osc_item_user_id(), $user_email = osc_item_contact_email(), $item_id = osc_item_id()); } ?&gt;</span>
      </div>      


      <div class="mb-row">&nbsp;</div>


      <div class="mb-row">
        <div class="mb-line"><?php _e('To add user average rating score, place following code to theme files', 'user_rating'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('ur_show_rating_score')) { echo ur_show_rating_score($user_id = osc_item_user_id(), $user_email = osc_item_contact_email(), $item_id = osc_item_id()); } ?&gt;</span>
      </div>   
      

      <div class="mb-row">&nbsp;</div>


      <div class="mb-row">
        <div class="mb-line"><?php _e('To add user rating level, place following code to theme files', 'user_rating'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('ur_show_rating_level')) { echo ur_show_rating_level($user_id = osc_item_user_id(), $user_email = osc_item_contact_email()); } ?&gt;</span>
      </div>   
      
      
    </div>
  </div>



  <!-- HELP TOPICS -->
  <div class="mb-box" id="mb-help">
    <div class="mb-head"><i class="fa fa-question-circle"></i> <?php _e('Help', 'user_rating'); ?></div>

    <div class="mb-inside">
      <div class="mb-row mb-help"><span class="sup">(1)</span> <div class="h1"><?php _e('Enable to require validation of each rating by admin. Then, rating is shown and counted just in case admin has validated it.', 'user_rating'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(2)</span> <div class="h2"><?php _e('Enable what options are available on user rating form.', 'user_rating'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(3)</span> <div class="h3"><?php _e('Enable to allow leave rating only to logged in users. Users not logged in will be requested to login to be able to leave rating.', 'user_rating'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(4)</span> <div class="h4"><?php _e('Enable to show link with user ratings in user account left sidebar.', 'user_rating'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(5)</span> <div class="h5"><?php _e('When enabled, osclass hooks are used to show Add / Show links to listing page and no modifications of theme are required.', 'user_rating'); ?></div></div>
    </div>
  </div>
</div>

<?php echo ur_footer(); ?>
	
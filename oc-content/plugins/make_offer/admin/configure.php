<?php
  // Create menu
  $title = __('Configure', 'make_offer');
  mo_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value


  $validate = mb_param_update( 'validate', 'plugin_action', 'check', 'plugin-make_offer' );
  $only_reg = mb_param_update( 'only_reg', 'plugin_action', 'check', 'plugin-make_offer' );
  $show_status = mb_param_update( 'show_status', 'plugin_action', 'check', 'plugin-make_offer' );
  $show_quantity = mb_param_update( 'show_quantity', 'plugin_action', 'check', 'plugin-make_offer' );
  $add_price = mb_param_update( 'add_price', 'plugin_action', 'check', 'plugin-make_offer' );
  $add_hook = mb_param_update( 'add_hook', 'plugin_action', 'check', 'plugin-make_offer' );
  $notify = mb_param_update( 'notify', 'plugin_action', 'check', 'plugin-make_offer' );
  $category = mb_param_update( 'category', 'plugin_action', 'value', 'plugin-make_offer' );
  $history = mb_param_update( 'history', 'plugin_action', 'check', 'plugin-make_offer' );
  $instant_messenger = mb_param_update( 'instant_messenger', 'plugin_action', 'check', 'plugin-make_offer' );
  $check_styled = mb_param_update( 'check_styled', 'plugin_action', 'check', 'plugin-make_offer' );

  $category_array = explode(',', $category);
 


  if(Params::getParam('plugin_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'make_offer') );
  }
  
  $category_all = Category::newInstance()->listAll();


?>



<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-cog"></i> <?php _e('Configure', 'make_offer'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <?php if(!mo_is_demo()) { ?>
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_action" value="done" />
        <?php } ?>
        
        <div class="mb-row">
          <label for="validate" class="h1"><span><?php _e('Require validation', 'make_offer'); ?></span></label> 
          <input name="validate" id="validate" class="element-slide" type="checkbox" <?php echo ($validate == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('Each offer must be validated by admin before it is shown.', 'make_offer'); ?></div>
        </div>

        <div class="mb-row">
          <label for="only_reg" class="h2"><span><?php _e('Only logged users', 'make_offer'); ?></span></label> 
          <input name="only_reg" id="only_reg" class="element-slide" type="checkbox" <?php echo ($only_reg == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, only logged in users can make new offer.', 'make_offer'); ?></div>
        </div>

        <div class="mb-row">
          <label for="show_status" class="h3"><span><?php _e('Show status', 'make_offer'); ?></span></label> 
          <input name="show_status" id="show_status" class="element-slide" type="checkbox" <?php echo ($show_status == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, seller\'s respond status is shown to other users in offer list.', 'make_offer'); ?></div>
        </div>

        <div class="mb-row">
          <label for="show_quantity" class="h4"><span><?php _e('Show quantity', 'make_offer'); ?></span></label> 
          <input name="show_quantity" id="show_quantity" class="element-slide" type="checkbox" <?php echo ($show_quantity == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('Enable to set quantity into offer.', 'make_offer'); ?></div>
        </div>

        <div class="mb-row">
          <label for="add_price" class="h5"><span><?php _e('Add offer button to price', 'make_offer'); ?></span></label> 
          <input name="add_price" id="add_price" class="element-slide" type="checkbox" <?php echo ($add_price == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, link to create/show offers is added directly to price.', 'make_offer'); ?></div>
        </div>

        <div class="mb-row">
          <label for="add_hook" class="h6"><span><?php _e('Auto-hook offer button', 'make_offer'); ?></span></label> 
          <input name="add_hook" id="add_hook" class="element-slide" type="checkbox" <?php echo ($add_hook == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, link to create/show new offer added to hook item_detail and visible on listing page.', 'make_offer'); ?></div>
        </div>

        <div class="mb-row">
          <label for="notify" class="h7"><span><?php _e('Notify seller', 'make_offer'); ?></span></label> 
          <input name="notify" id="notify" class="element-slide" type="checkbox" <?php echo ($notify == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, notification email is send to seller when there is new offer.', 'make_offer'); ?></div>
        </div>

        <div class="mb-row">
          <label for="history" class="h9"><span><?php _e('Enable on existing items', 'make_offer'); ?></span></label> 
          <input name="history" id="history" class="element-slide" type="checkbox" <?php echo ($history == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled, make offer button is available on existing listings created before plugin installation.', 'make_offer'); ?></div>
        </div>

        <div class="mb-row">
          <label for="instant_messenger" class="h10"><span><?php _e('Connect to Messenger', 'make_offer'); ?></span></label> 
          <input name="instant_messenger" id="instant_messenger" class="element-slide" type="checkbox" <?php echo ($instant_messenger == 1 ? 'checked' : ''); ?> />
          
          <div class="mb-explain"><?php _e('When enabled and new offer is made, message will come to instant messenger instead of user email. Require Instant Messenger Plugin to be installed.', 'make_offer'); ?></div>
        </div>


        <div class="mb-line mb-row-select-multiple">
          <label for="category_multiple" class="h8"><span class="mb-has-tooltip"><?php _e('Category', 'make_offer'); ?></span></label> 

          <input type="hidden" name="category" id="category" value="<?php echo $category; ?>"/>
          <select id="category_multiple" name="category_multiple" multiple>
            <?php echo mo_cat_list($category_array, $category_all); ?>
          </select>
          
          <div class="mb-explain"><?php _e('If no category is selected, "make offer" is enabled in all categories.', 'make_offer'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="check_styled" class="h11"><span><?php _e('Styled checkboxes', 'make_offer'); ?></span></label> 
          <input name="check_styled" id="check_styled" class="element-slide" type="checkbox" <?php echo ($check_styled == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, on plugin checkboxes are applied CSS styles to format them. Otherwise checkboxes are raw.', 'make_offer'); ?></div>
        </div>

        <div class="mb-foot">
          <?php if(!mo_is_demo()) { ?>
            <button type="submit" class="mb-button"><?php _e('Save', 'make_offer');?></button>
          <?php } else { ?>
            <a href="#" onclick="return false" class="mb-button"><?php _e('Save (demo - disabled)', 'make_offer');?></button>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>




  <!-- PLUGIN INTEGRATION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Plugin Setup', 'make_offer'); ?></div>

    <div class="mb-inside">
      <div class="mb-row"><?php _e('No theme modification are required to use all functions of plugin, but you may want to customize positions of buttons', 'make_offer'); ?></div>

      <div class="mb-row">
        <div class="mb-line"><?php _e('To add button to show list of offers with count of active offers, place following code to theme files', 'make_offer'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('mo_offer_counts_button')) { echo mo_offer_counts_button(); } ?&gt;</span>
      </div>
      
      <div class="mb-row">
        <div class="mb-line"><?php _e('To add button to create a new offer, place following code to theme files', 'make_offer'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('mo_offer_create_button')) { echo mo_offer_create_button(); } ?&gt;</span>
      </div>
      
      <div class="mb-row">
        <div class="mb-line"><?php _e('To add button to create/show offers (legacy button), place following code to theme files', 'make_offer'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('mo_show_offer_link')) { echo mo_show_offer_link(); } ?&gt;</span>
      </div>
      
      <div class="mb-row">
        <div class="mb-line"><?php _e('To get raw link to see list of offers, use following code', 'make_offer'); ?>:</div>
        <span class="mb-code">&lt;?php if(function_exists('mo_show_offer_link_raw')) { echo mo_show_offer_link_raw(); } ?&gt;</span>
      </div>
      
      <div class="mb-line"><?php _e('This codes can be put anywhere osc_item() is available (item loop, item page, user items, ...).', 'make_offer'); ?></div>

    </div>
  </div>



  <!-- HELP TOPICS -->
  <div class="mb-box" id="mb-help">
    <div class="mb-head"><i class="fa fa-question-circle"></i> <?php _e('Help', 'make_offer'); ?></div>

    <div class="mb-inside">
      <div class="mb-row mb-help"><span class="sup">(1)</span> <div class="h1"><?php _e('When enabled, each offer must be validated by admin before it is shown to seller.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(2)</span> <div class="h2"><?php _e('When enabled, only logged in users can create new offer.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(3)</span> <div class="h3"><?php _e('When enabled, respond status of seller is shown in front in list of all offers.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(4)</span> <div class="h4"><?php _e('When enabled, quantity can be specified in offer.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(5)</span> <div class="h5"><?php _e('When enabled, link to create/show offers is added directly to price field on listing page. No theme modifications are required then.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(6)</span> <div class="h6"><?php _e('When enabled, link to create/show offers is added to hook item_detail on listing page. No theme modifications are required then.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(7)</span> <div class="h7"><?php _e('When enabled, seller is notified by email about new offer on it\'s listing.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(8)</span> <div class="h8"><?php _e('Select categories where "Make Offer" function is available. If no category is selected, make offer is enabled in all categories.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(9)</span> <div class="h9"><?php _e('When enabled, make offer button is available on listings that has been created before plugin installation (no selection of make offer option).', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(10)</span> <div class="h10"><?php _e('When enabled and new offer is created, email will not be sent, rather then instant message is sent to user and communication can continue via messenger.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><div><?php _e('Select categories where "Make Offer" function is available. If no category is selected, make offer is enabled in all categories.', 'make_offer'); ?></div></div>
      <div class="mb-row mb-help"><div><?php _e('Make offer button will be shown on listing just in case price is not set to Free.', 'make_offer'); ?></div></div>
    </div>
  </div>
</div>

<?php echo mo_footer(); ?>
	
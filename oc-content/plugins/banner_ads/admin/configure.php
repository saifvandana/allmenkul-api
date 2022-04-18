<?php
  // Create menu
  $title = __('Configure', 'banner_ads');
  ba_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value

  $hooks = mb_param_update( 'hooks', 'plugin_action', 'value', 'plugin-banner_ads' );
  $currency = mb_param_update( 'currency', 'plugin_action', 'value', 'plugin-banner_ads' );
  $slide_speed = mb_param_update( 'slide_speed', 'plugin_action', 'value', 'plugin-banner_ads' );
  $slide_ticker = mb_param_update( 'slide_ticker', 'plugin_action', 'value', 'plugin-banner_ads' );



  if(Params::getParam('plugin_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'banner_ads') );
  }

  $cur = osc_get_preference('currency', 'plugin-banner_ads');
?>



<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-cog"></i> <?php _e('Configure', 'banner_ads'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_action" value="done" />

        <div class="mb-notes">
          <div class="mb-line"><?php _e('Explanation of 3 key words in this plugin: Hook > Banner > Advert (relation is N:N:N)', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('Advert is children/subgroup of banner. Advert is shown in front-office via banner.', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('Banner is build from adverts and is basically container for adverts.', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('Hook is container for banners, basically place where banner is hooked and shown. Hooks are shown in front-office and using hooks, banners & adverts are shown.', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('Hook can contain one or more banners; Banner can contain one or more adverts; Advert can be part of one or more banners; Banner can be hooked to one or more hooks;', 'banner_ads'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="hooks" class="h1"><span><?php _e('Custom Hooks', 'banner_ads'); ?></span></label> 
          <input size="60" name="hooks" id="hooks" class="mb-short" type="text" value="<?php echo $hooks; ?>" />

          <div class="mb-explain"><?php _e('Define your custom hooks, delimit by comma, no white spaces allowed. Example: my_hook,custom_hook,hook1,hook2', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row">
          <label for="currency" class="h3"><span><?php _e('Currency Symbol', 'banner_ads'); ?></span></label> 
          <input size="6" name="currency" id="currency" class="mb-short" type="text" value="<?php echo $currency; ?>" />

          <div class="mb-explain"><?php _e('Currency symbol used on financial values in plugin.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row">
          <label for="slide_speed" class="h2"><span><?php _e('Slide Speed', 'banner_ads'); ?></span></label> 
          <input size="6" name="slide_speed" id="slide_speed" class="mb-short" type="text" value="<?php echo $slide_speed; ?>" />
          <div class="mb-input-desc"><?php _e('miliseconds', 'banner_ads'); ?></div>

          <div class="mb-explain"><?php _e('Specify time in miliseconds for banners of type Rotate Adverts how fast will be adverts rotated.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row">
          <label for="slide_ticker" class="h2"><span><?php _e('Slide Ticker', 'banner_ads'); ?></span></label> 
          <input size="6" name="slide_ticker" id="slide_ticker" class="mb-short" type="text" value="<?php echo $slide_ticker; ?>" />
          <div class="mb-input-desc"><?php _e('miliseconds', 'banner_ads'); ?></div>

          <div class="mb-explain"><?php _e('Specify time in miliseconds for banners of type Rotate Adverts how long will be each advert shown.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-foot">
          <button type="submit" class="mb-button"><?php _e('Save', 'banner_ads');?></button>
        </div>
      </form>
    </div>
  </div>



  <!-- PLUGIN INTEGRATION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Plugin Setup', 'banner_ads'); ?></div>

    <div class="mb-inside">

      <div class="mb-row"><strong><?php _e('In general, plugin setup is very easy and you need to follow few basic steps:', 'banner_ads'); ?></strong></div>
      <div class="mb-row"><strong class="mb-point">1</strong> <?php _e('Create advert - there are different types of adverts and each advert has different fields that are described on advert page. Advert is what is shown in front to users. You can create advert i.e. from Google Adsense code.', 'banner_ads'); ?></div>
      <div class="mb-row"><strong class="mb-point">2</strong> <?php _e('Create banner - when advert is done, you want to show it in front. Adverts are shown to users via banners. Banner is hook/container for adverts. Each banner can contain 1 or more adverts and you can choose in what way will be shown.', 'banner_ads'); ?></div>
      <div class="mb-row"><strong class="mb-point">3</strong> <?php _e('Add advert to banner - when banner & advert are created, add advert into newly created banner. Go to edit banner and in section Adverts in Banner you can choose adverts that will be visible in this banner. Alternatively you can go to edit advert and in section Banners, add it to your banner. You can do this also in time when banner is created.', 'banner_ads'); ?></div>
      <div class="mb-row"><strong class="mb-point">4</strong> <?php _e('Ready to show banner in front - you need to define your own hooks (require theme modification). Go to banner edit page and in section Hook choose hooks where you want to show this banner. You can select multiple hooks. If you define your custom hooks in Dashboard page, these hooks will be visible there as well. Note that for your custom hooks, you need to modify your theme files to make them funcitonal. You can define hooks also in time when banner is created.', 'banner_ads'); ?></div>
      <div class="mb-row"><strong class="mb-point">5</strong> <?php _e('Custom hooks - if you want to place banners on specific position in your theme files, you can create your custom hook. When done, you need to place this hooks into your theme files in following form (replace hook1 with name of hook you have defined in section above):', 'banner_ads'); ?></div>
      <div class="mb-row">
        <span class="mb-code">&lt;?php if(function_exists('ba_hook')) { ba_hook('hook1'); } ?&gt;</span>
      </div>
    </div>
  </div>



  <!-- HELP TOPICS -->
  <div class="mb-box" id="mb-help">
    <div class="mb-head"><i class="fa fa-question-circle"></i> <?php _e('Help', 'banner_ads'); ?></div>

    <div class="mb-inside">
      <div class="mb-row mb-help"><span class="sup">(1)</span> <div class="h1"><?php _e('When you define some hook, you need to add it to your theme files to make it funcitonal. Place following code to your theme files (replace hook1 with name you have defined for hook):', 'banner_ads'); ?> <strong>&lt;?php if(function_exists('ba_hook') { ba_hook('hook1'); } ?&gt;</strong></div></div>
      <div class="mb-row mb-help"><span class="sup">(2)</span> <div class="h2"><?php _e('Define slide speed and ticker for banners with type Rotate Advert how fast will take to change advert and how long will be each advert visible. Fade effect is used.', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(3)</span> <div class="h3"><?php _e('Define currency symbol used on plugin financial information (price for clicks, price for views, budget, ...).', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><div><?php _e('Views and Clicks on advert are increased just for regular users. If you are logged in as admin (in oc-admin) and in same browser you browser your site and view/click on banner, views/clicks are not increased.', 'banner_ads'); ?></div></div>
    </div>
  </div>
</div>

<?php echo ba_footer(); ?>
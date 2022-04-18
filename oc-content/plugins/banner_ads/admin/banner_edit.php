<?php
  // Create menu
  $title = __('Banner', 'banner_ads');
  ba_menu($title);

  $id = Params::getParam('bannerId');

  if(Params::getParam('plugin_action') == 'done') {
    if($id == '') {
      $id = ModelBA::newInstance()->insertBanner(Params::getParam('name'), Params::getParam('type'), Params::getParam('hook'));
      osc_add_flash_ok_message(__('Banner successfully created.', 'banner_ads'), 'admin');
    } else {
      ModelBA::newInstance()->updateBanner($id, Params::getParam('name'), Params::getParam('type'), Params::getParam('hook'));
      osc_add_flash_ok_message(__('Banner successfully updated.', 'banner_ads'), 'admin');
    }
    
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=banner_ads/admin/banner_edit.php&bannerId=' . $id);
    exit;
  }


  $all_adverts = ModelBA::newInstance()->getAdverts();


  // CHECK ADVERT ASSIGNMENT TO BANNER
  if(Params::getParam('plugin_action') == 'done' && $id <> '' && $id > 0) {
    $to_adverts_active = explode(',', Params::getParam('advert'));           // List of adverts linked to banner

    foreach($all_adverts as $al) {
       $to_banners = explode(',', $al['fk_s_banner_id']);                    // List of banners to that is advert linked to

       // ADVERT IS LINKED TO BANNER AND IT SHOULD NOT BE ANYMORE
       if(in_array($id, $to_banners) && !in_array($al['pk_i_id'], $to_adverts_active)) {
         $to_banners = array_diff($to_banners, [$id]);                       // Remove unwanted value from array

       // ADVERT IS NOT LINKED TO BANNER AND IT SHOULD BE
       } else if(!in_array($id, $to_banners) && in_array($al['pk_i_id'], $to_adverts_active)) {
         $to_banners[] = $id;
       }

       $to_banners = array_filter($to_banners);
       ModelBA::newInstance()->updateAdvertBanners($al['pk_i_id'], implode(',', $to_banners));
    }
  }

  
  if($id <> '' && $id > 0) {
    $b = ModelBA::newInstance()->getBanner($id);
    $banner_adverts = ModelBA::newInstance()->getAdvertByBannerId($id);
    $hook_array = explode(',', $b['s_hook']);

    $advert_array = array();
    
    foreach($banner_adverts as $ba) {
      $advert_array[] = $ba['pk_i_id'];
    }
  } else {
    $advert_array = array();
    $hook_array = array();
  }


  // REMOVE BANNER
  if(Params::getParam('what') == 'delete') { 
    ModelBA::newInstance()->removeBanner($id);
    osc_add_flash_ok_message(__('Banner successfully removed', 'banner_ads'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=banner_ads/admin/banners.php');
    exit;
  }
?>



<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-cog"></i> <?php _e('Banner configuration', 'banner_ads'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>banner_edit.php" />
        <input type="hidden" name="plugin_action" value="done" />
        <input type="hidden" name="bannerId" value="<?php echo $id; ?>" />
        
        <div class="mb-notes">
          <div class="mb-line"><?php _e('Banner is build from adverts and is basically container for adverts.', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('Banner can be interpreted newspaper those contains many pages (adverts).', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('In order to make banner visible in front-office, assign/link it to one or more hooks or place banner code directly into theme files.', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('One banner can contain one or more adverts. One advert can be part of one or more banners.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-notes">
          <div class="mb-line"><?php _e('If you want to show banner directly without adding it to hook, you can do it by adding following code into your theme file:', 'banner_ads'); ?></div>
          <div class="mb-line"><strong style="font-family: Consolas;">&lt;?php ba_show_banner(<?php echo ($id > 0 ? $id : 12345); ?>); ?&gt;</strong></div>
        </div>
        
        

        <?php if($id > 0) { ?>
          <div class="mb-row">
            <label for="id" class="h0"><span><?php _e('Banner ID', 'banner_ads'); ?></span></label> 
            <input size="10" id="id" class="mb-short" type="text" value="<?php echo $id; ?>" readonly/>
          </div>
        <?php } ?>

        <div class="mb-row">
          <label for="name" class="h1"><span><?php _e('Name', 'banner_ads'); ?></span></label> 
          <input size="50" name="name" id="name" class="mb-short" type="text" value="<?php echo isset($b['s_name']) ? $b['s_name'] : ''; ?>" />

          <div class="mb-explain"><?php _e('Name is used just for internal purpose.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row">
          <label for="type" class="h2"><span><?php _e('Type', 'banner_ads'); ?></span></label> 
          <select id="type" name="type">
            <option value="1" <?php if ( (isset($b['i_type']) ? $b['i_type'] : '') == 1) { ?>selected="selected"<?php } ?>><?php _e('Show all adverts', 'banner_ads'); ?></option>
            <option value="2" <?php if ( (isset($b['i_type']) ? $b['i_type'] : '') == 2) { ?>selected="selected"<?php } ?>><?php _e('Rotate adverts (with fade effect)', 'banner_ads'); ?></option>
            <option value="3" <?php if ( (isset($b['i_type']) ? $b['i_type'] : '') == 3) { ?>selected="selected"<?php } ?>><?php _e('Show 1 random advert', 'banner_ads'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Based on type of banner, adverts are shown in different way.', 'banner_ads'); ?></div>
        </div>


        <div class="mb-row mb-row-select-multiple">
          <label for="hook_multiple" class="h3"><span><?php _e('Hook', 'banner_ads'); ?></span></label> 

          <input type="hidden" name="hook" id="hook" value="<?php echo $b['s_hook']; ?>"/>
          <select id="hook_multiple" name="hook_multiple" multiple>
            <?php $hooks = ba_hooks(); ?>
            <?php foreach($hooks as $h) { ?>
              <option value="<?php echo $h; ?>" <?php if(in_array($h, $hook_array)) { ?>selected="selected"<?php } ?>><?php echo $h; ?></option>
            <?php } ?>
          </select>

          <div class="mb-explain">
            <div class="mb-line"><?php _e('Select hooks where you want to add banner. You should select at least 1 hook.', 'banner_ads'); ?></div>
            <div class="mb-line"><?php _e('New hooks can be created on Configure page of plugin.', 'banner_ads'); ?></div>
          </div>
        </div>

        <div class="mb-row mb-row-select-multiple">
          <label for="advert_multiple" class="h4"><span><?php _e('Adverts in Banner', 'banner_ads'); ?></span></label> 

          <input type="hidden" name="advert" id="advert" value="<?php echo implode(',', $advert_array); ?>"/>
          <select id="advert_multiple" name="advert_multiple" multiple>
            <?php if(count($all_adverts) > 0) { ?>
              <?php foreach($all_adverts as $a) { ?>
                <option value="<?php echo $a['pk_i_id']; ?>" <?php if(in_array($a['pk_i_id'], $advert_array)) { ?>selected="selected"<?php } ?>><?php echo $a['s_name']; ?></option>
              <?php } ?>
            <?php } else { ?>
              <option value="" disabled="disabled"><?php echo __('No adverts created yet', 'banner_ads'); ?></option>
            <?php } ?>
          </select>

          <div class="mb-explain">
            <div class="mb-line"><?php _e('Select adverts that will be shown in this banner.', 'banner_ads'); ?></div>
            <div class="mb-line"><a target="_blank" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/advert_edit.php"><?php _e('Create a new advert', 'banner_ads'); ?></a></div>
          </div>
        </div>

        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if($id == '' || $id == 0) { ?>
            <button type="submit" class="mb-button"><?php _e('Create', 'banner_ads');?></button>
          <?php } else { ?>
            <button type="submit" class="mb-button"><?php _e('Update', 'banner_ads');?></button>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>




  <!-- HELP TOPICS -->
  <div class="mb-box" id="mb-help">
    <div class="mb-head"><i class="fa fa-question-circle"></i> <?php _e('Help', 'banner_ads'); ?></div>

    <div class="mb-inside">
      <div class="mb-row mb-help"><span class="sup">(2)</span> <div class="h2"><?php _e('Based on type of advert, different fields are available.', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(3)</span> <div class="h3"><?php _e('Name of advert is used only for internal use, to help you easily identify ad you are looking for.', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(4)</span> <div class="h4"><?php _e('Client key is used to share advert stats with your client (views, clicks, remaining budget...).', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(5)</span> <div class="h5"><?php _e('Define onclick URL if you want to redirect your customer to specific page after clicking on advert.', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(6)</span> <div class="h6"><?php _e('HTML/Adsense code is used to specify code for your advert. In both cases code in HTML format must be used (no PHP or so).', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(7)</span> <div class="h7"><?php _e('You can upload 1 image per advert in format .jpg, .png or .gif. Size of image is limited by your PHP settings.', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(8)</span> <div class="h8"><?php _e('Define price settings if there exist budget for advert. Based on views, clicks, their prices and budget, when budget is spent, advert is not shown anymore.', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(9)</span> <div class="h9"><?php _e('Set when advert expire. After this date advert will not be visible. Leave blank to disable this option.', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(10)</span> <div class="h10"><?php _e('If you want to show your advert just in some categories, select them. If you do not select any category, advert will be shown in all categories. Only root categories can be selected.', 'banner_ads'); ?></div></div>
      <div class="mb-row mb-help"><span class="sup">(11)</span> <div class="h11"><?php _e('Define size of banner in Pixels or Percentage. Insert only integer value or leave blank to use default size.', 'banner_ads'); ?></div></div>
    </div>
  </div>
</div>

<?php echo ba_footer(); ?>
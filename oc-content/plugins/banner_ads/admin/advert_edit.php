<?php
  // Create menu
  $title = __('Advert', 'banner_ads');
  ba_menu($title);

  $id = Params::getParam('advertId');

  if(Params::getParam('plugin_action') == 'done') {
    if($id == '') {
      $id = ModelBA::newInstance()->insertAdvert(Params::getParam('type'), Params::getParam('banner'), Params::getParam('name'), Params::getParam('key'), Params::getParam('url'), stripslashes(Params::getParam('code', false, false)), Params::getParam('price_view'), Params::getParam('price_click'), Params::getParam('budget'), Params::getParam('expire'), Params::getParam('category'), Params::getParam('size_width'), Params::getParam('size_height'));
      osc_add_flash_ok_message(__('Advert successfully created.', 'banner_ads'), 'admin');
    } else {
      ModelBA::newInstance()->updateAdvert($id, Params::getParam('type'), Params::getParam('banner'), Params::getParam('name'), Params::getParam('key'), Params::getParam('url'), stripslashes(Params::getParam('code', false, false)), Params::getParam('price_view'), Params::getParam('price_click'), Params::getParam('budget'), Params::getParam('expire'), Params::getParam('category'), Params::getParam('size_width'), Params::getParam('size_height'));
      osc_add_flash_ok_message(__('Advert successfully updated.', 'banner_ads'), 'admin');
    }
    
    $a = ModelBA::newInstance()->getAdvert($id);

    // UPLOAD IMAGE FOR IMAGE ADVERT
    if(isset($_FILES['image']) && $_FILES['image']['name'] <> ''){
      $upload_dir = osc_plugins_path() . 'banner_ads/img/advert/';

      if(@$a['s_image'] <> '') {
        if(file_exists($upload_dir . $a['s_image'])) {
          unlink($upload_dir . $a['s_image']);
        }
      }

      $file_ext   = strtolower(end(explode('.', $_FILES['image']['name'])));
      $file_name  = $id . '.' . $file_ext;
      $file_tmp   = $_FILES['image']['tmp_name'];
      $file_type  = $_FILES['image']['type'];   
      $extensions = array('jpg', 'png', 'gif');

      if(in_array($file_ext,$extensions) === false) {
        $errors = __('Extension not allowed, only allowed extension are jpg, png or gif!', 'banner_ads');
      }
            
      if(empty($errors)==true){
        move_uploaded_file($file_tmp, $upload_dir.$file_name);
        ModelBA::newInstance()->updateAdvertImage($id, $file_name);

        osc_add_flash_ok_message(__('Advert image uploaded successfully.', 'banner_ads'), 'admin');
      } else {
        osc_add_flash_error_message(sprintf(__('There was error when uploading image: %s', 'banner_ads'), $errors), 'admin');
      }
    }
    
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=banner_ads/admin/advert_edit.php&advertId=' . $id);
    exit;
  }

  
  if($id <> '' && $id > 0) {
    $a = ModelBA::newInstance()->getAdvert($id);
    $category_array = explode(',', $a['s_category']);
    $banner_array = explode(',', $a['fk_s_banner_id']);
  } else {
    $category_array = array();
    $banner_array = array();
  }


  // CHECK IF TYPE IS CHANGED AND THERE EXIST IMAGE
  if($id <> '' && $id > 0 && $a['s_image'] <> '' && $a['i_type'] <> 2) {
    $upload_dir = osc_plugins_path() . 'banner_ads/img/advert/';

    if(file_exists($upload_dir . $a['s_image'])) {
      unlink($upload_dir . $a['s_image']);
    }

    ModelBA::newInstance()->updateAdvertImage($id, '');

    osc_add_flash_ok_message(__('Advert image has been removed.', 'banner_ads'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=banner_ads/admin/advert_edit.php&advertId=' . $id);
    exit;
  }


  // REMOVE ADVERT
  if(Params::getParam('what') == 'delete') { 
    $upload_dir = osc_plugins_path() . 'banner_ads/img/advert/';

    if($a['s_image'] <> '' && file_exists($upload_dir . $a['s_image'])) {
      unlink($upload_dir . $a['s_image']);
    }

    ModelBA::newInstance()->removeAdvert($id);
    osc_add_flash_ok_message(__('Advert successfully removed', 'banner_ads'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=banner_ads/admin/adverts.php');
    exit;
  }
?>


<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-cog"></i> <?php _e('Advert configuration', 'banner_ads'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>advert_edit.php" />
        <input type="hidden" name="plugin_action" value="done" />
        <input type="hidden" name="advertId" value="<?php echo $id; ?>" />
        
        <div class="mb-notes">
          <div class="mb-line"><?php _e('Advert is children/subgroup of banner. Advert is shown in front-office via banner.', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('Advert can be interpreted as 1 page in newspaper (banner).', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('In order to make advert visible in front-office, assign/link it to one or more banners.', 'banner_ads'); ?></div>
          <div class="mb-line"><?php _e('One banner can contain one or more adverts. One advert can be part of one or more banners.', 'banner_ads'); ?></div>
        </div>
        
        <?php if($id <> '' && $a['s_key'] <> '') { ?>
          <div class="mb-notes">
            <div class="mb-line"><?php _e('Your client can review banner in front-office on following URL:', 'banner_ads'); ?></div>
            <div class="mb-line"><a target="_blank" href="<?php echo osc_route_url('ba-advert', array('key' => $a['s_key']) ); ?>"><?php echo osc_route_url('ba-advert', array('key' => $a['s_key'])); ?></a></div>
          </div>
        <?php } ?>

        <div class="mb-row">
          <label for="type" class="h2"><span><?php _e('Type', 'banner_ads'); ?></span></label> 
          <select id="type" name="type">
            <option value="1" <?php if ( (isset($a['i_type']) ? $a['i_type'] : '') == 1) { ?>selected="selected"<?php } ?>><?php _e('HTML Advert', 'banner_ads'); ?></option>
            <option value="2" <?php if ( (isset($a['i_type']) ? $a['i_type'] : '') == 2) { ?>selected="selected"<?php } ?>><?php _e('Image Advert', 'banner_ads'); ?></option>
            <option value="3" <?php if ( (isset($a['i_type']) ? $a['i_type'] : '') == 3) { ?>selected="selected"<?php } ?>><?php _e('Adsense Advert', 'banner_ads'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Based on type of advert, different fields are available.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row">
          <label for="name" class="h3"><span><?php _e('Name', 'banner_ads'); ?></span></label> 
          <input size="50" name="name" id="name" class="mb-short" type="text" value="<?php echo isset($a['s_name']) ? $a['s_name'] : ''; ?>" />

          <div class="mb-explain"><?php _e('Name is used just for internal purpose.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row key">
          <label for="key" class="h4"><span><?php _e('Client Key', 'banner_ads'); ?></span></label> 
          <input size="20" name="key" id="key" class="mb-short" type="text" value="<?php echo isset($a['s_key']) ? $a['s_key'] : ''; ?>" />

          <div class="mb-explain">
            <div class="mb-line"><?php _e('Using this key your client can see stats of advert. Leave blank if you do not want to share data with external clients.', 'banner_ads'); ?></div>

            <?php if($id <> '' && $a['s_key'] <> '') { ?>
              <div class="mb-line"><?php _e('Client URL', 'banner_ads'); ?>: <a target="_blank" href="<?php echo osc_route_url( 'ba-advert', array('key' => $a['s_key']) ); ?>"><?php echo osc_route_url( 'ba-advert', array('key' => $a['s_key']) ); ?></a></div>
            <?php } ?>
          </div>
        </div>

        <div class="mb-row url">
          <label for="url" class="h5"><span><?php _e('On Click URL', 'banner_ads'); ?></span></label> 
          <input size="50" name="url" id="url" class="mb-short" type="text" value="<?php echo isset($a['s_url']) ? $a['s_url'] : ''; ?>" />

          <div class="mb-explain"><?php _e('If not defined directly in advert, specify target URL where customers will be redirected after click on advert.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row code">
          <label for="code" class="h6">
            <span class="adsense" style="display:none;"><?php _e('Adsense Code', 'banner_ads'); ?></span>
            <span class="html"><?php _e('HTML Code', 'banner_ads'); ?></span>
          </label>
 
          <textarea name="code" id="code" class="mb-textarea"><?php echo isset($a['s_code']) ? stripslashes($a['s_code']) : ''; ?></textarea>

          <div class="mb-explain"><?php _e('Place here advert code, adsense code or any other code in HTML format.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row image">
          <label for="image" class="h7"><span><?php _e('Image', 'banner_ads'); ?></span></label> 
 
          <?php $img = ba_image_link($id); ?>
          <?php if(!!$img) { ?>
            <a href="<?php echo $img; ?>" target="_blank"><img class="mb-banner-image-admin" src="<?php echo $img; ?>" /></a>
          <?php } ?>


          <div class="mb-file">
            <label class="file-label">
              <span class="wrap"><i class="fa fa-paperclip"></i> <span><?php echo ($a['s_image'] == '' ? __('Upload image', 'banner_ads') : __('Replace image', 'banner_ads')); ?></span></span>
              <input type="file" id="image" name="image" />
            </label>

            <div class="file-text"><?php _e('Allowed extensions', 'banner_ads'); ?>: .png, .jpg, .gif</div>
          </div>
        </div>

        <div class="mb-row">&nbsp;</div>

        <div class="mb-row price">
          <label for="price_click" class="h8"><span><?php _e('Price for 1 click', 'banner_ads'); ?></span></label> 
          <input size="6" name="price_click" id="price_click" class="mb-short" type="text" value="<?php echo isset($a['d_price_click']) ? $a['d_price_click'] : ''; ?>" />
          <div class="mb-input-desc"><?php echo osc_get_preference('currency', 'plugin-banner_ads'); ?></div>
          <div class="mb-explain"><?php _e('How much cost 1 click on banner. This amount will be substracted from budget.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row price">
          <label for="price_view" class="h8"><span><?php _e('Price for 1 view', 'banner_ads'); ?></span></label> 
          <input size="6" name="price_view" id="price_view" class="mb-short" type="text" value="<?php echo isset($a['d_price_view']) ? $a['d_price_view'] : ''; ?>" />
          <div class="mb-input-desc"><?php echo osc_get_preference('currency', 'plugin-banner_ads'); ?></div>
          <div class="mb-explain"><?php _e('How much cost 1 view of banner. This amount will be substracted from budget.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row price">
          <label for="budget" class="h8"><span><?php _e('Budget', 'banner_ads'); ?></span></label> 
          <input size="6" name="budget" id="budget" class="mb-short" type="text" value="<?php echo isset($a['d_budget']) ? $a['d_budget'] : ''; ?>" />
          <div class="mb-input-desc"><?php echo osc_get_preference('currency', 'plugin-banner_ads'); ?></div>
          <div class="mb-explain"><?php _e('Price for views and clicks are substracted from budget. When budget is spent, advert is not active anymore.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row date">
          <label for="expire" class="h9"><span><?php _e('Expiration date', 'banner_ads'); ?></span></label> 
          <input size="20" name="expire" id="expire" class="mb-short" type="text" value="<?php echo isset($a['dt_expire']) ? $a['dt_expire'] : ''; ?>" />
          <div class="mb-explain"><?php _e('When expiration date is reached, advert is not active anymore.', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row date">
          <label for="size_width" class="h11"><span><?php _e('Size', 'banner_ads'); ?></span></label> 
          <input size="6" name="size_width" id="size_width" class="mb-short" type="text" value="<?php echo isset($a['s_size_width']) ? $a['s_size_width'] : ''; ?>" />
          <div class="mb-input-desc"><?php _e('px or %', 'banner_ads'); ?></div>
          
          <span class="mb-cross">x</span> 
          
          <input size="6" name="size_height" id="size_height" class="mb-short" type="text" value="<?php echo isset($a['s_size_height']) ? $a['s_size_height'] : ''; ?>" />
          <div class="mb-input-desc"><?php _e('px or %', 'banner_ads'); ?></div>

          <div class="mb-size-error" style="display:none;"><?php _e('Value must be px or %. I.e.: 100%, 50%, 240px, 120px...', 'banner_ads'); ?></div>

          <div class="mb-explain"><?php _e('Only pixels or percentage value. No whitespace allowed. Leave blank to use default. Example: 100%, 50%, 240px, 120px... Invalid: 50, 100, 20vh, 10rem, 5pt, ...', 'banner_ads'); ?></div>
        </div>

        <div class="mb-row mb-row-select-multiple">
          <label for="category_multiple" class="h10"><span><?php _e('Category', 'banner_ads'); ?></span></label> 

          <input type="hidden" name="category" id="category" value="<?php echo $a['s_category']; ?>"/>
          <select id="category_multiple" name="category_multiple" multiple>
            <?php while(osc_has_categories()) { ?>
              <option value="<?php echo osc_category_id(); ?>" <?php if(in_array(osc_category_id(), $category_array)) { ?>selected="selected"<?php } ?>><?php echo osc_category_name(); ?></option>
            <?php } ?>
          </select>

          <div class="mb-explain"><?php _e('If not category selected, advert is shown in all categories.', 'banner_ads'); ?></div>
        </div>


        <div class="mb-row mb-row-select-multiple">
          <label for="banner_multiple" class="h12"><span><?php _e('Banner', 'banner_ads'); ?></span></label> 

          <input type="hidden" name="banner" id="banner" value="<?php echo $a['fk_s_banner_id']; ?>"/>
          <select id="banner_multiple" name="banner_multiple" multiple>
            <?php $banners = ModelBA::newInstance()->getBanners(); ?>
            <?php if(count($banners) > 0) { ?>
              <?php foreach($banners as $b) { ?>
                <option value="<?php echo $b['pk_i_id']; ?>" <?php if(in_array($b['pk_i_id'], $banner_array)) { ?>selected="selected"<?php } ?>><?php echo $b['s_name']; ?></option>
              <?php } ?>
            <?php } else { ?>
              <option value="" disabled="disabled"><?php _e('No banners created yet', 'banner_ads'); ?></option>
            <?php } ?>
          </select>

          <div class="mb-explain">
            <div class="mb-line"><?php _e('Select in which banners will be this advert visible. You should select at least 1, as each advert is shown via banner.', 'banner_ads'); ?></div>
            <div class="mb-line"><a target="_blank" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/banner_edit.php"><?php _e('Create a new banner', 'banner_ads'); ?></a></div>
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
      <div class="mb-row mb-help"><span class="sup">(12)</span> <div class="h12"><?php _e('Choose banners where advert will be shown/visible. Each advert must be added to banner to be visible.', 'banner_ads'); ?></div></div>
    </div>
  </div>
</div>

<?php echo ba_footer(); ?>
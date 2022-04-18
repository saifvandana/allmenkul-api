<?php
  // Create menu
  $title = __('Banners', 'banner_ads');
  ba_menu($title);


  $banners = ModelBA::newInstance()->getBanners($with_adverts = true);
  $cur = osc_get_preference('currency', 'plugin-banner_ads');
?>



<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-clone"></i> <?php _e('Banners', 'banner_ads'); ?></div>

    <div class="mb-inside">
      <div class="mb-notes">
        <div class="mb-line"><?php _e('Banner is build from adverts and is basically container for adverts.', 'banner_ads'); ?></div>
        <div class="mb-line"><?php _e('Banner can be interpreted newspaper those contains many pages (adverts).', 'banner_ads'); ?></div>
        <div class="mb-line"><?php _e('In order to make banner visible in front-office, assign/link it to one or more hooks or place banner code directly into theme files.', 'banner_ads'); ?></div>
        <div class="mb-line"><?php _e('One banner can contain one or more adverts. One advert can be part of one or more banners.', 'banner_ads'); ?></div>
      </div>
      
      <div class="mb-notes">
        <div class="mb-line"><?php _e('If you want to show banner directly without adding it to hook, you can do it by adding following code into your theme file (replace 12345 with ID of your banner):', 'banner_ads'); ?></div>
        <div class="mb-line"><strong style="font-family: Consolas;">&lt;?php ba_show_banner(12345); ?&gt;</strong></div>
      </div>

      <div class="mb-table mb-table-banners">
        <div class="mb-table-head">
          <div class="mb-col-1"><?php _e('ID', 'banner_ads'); ?></div>
          <div class="mb-col-4 mb-align-left"><?php _e('Banner name', 'banner_ads'); ?></div>
          <div class="mb-col-5 mb-align-left"><?php _e('Type', 'banner_ads');?></div>
          <div class="mb-col-7 mb-align-left"><?php _e('Hooks', 'banner_ads'); ?></div>
          <div class="mb-col-3 mb-align-left"><?php _e('Adverts', 'banner_ads'); ?></div>
          <div class="mb-col-4">&nbsp;</div>
        </div>

        <?php if(count($banners) <= 0) { ?>
          <div class="mb-table-row mb-row-empty"><i class="fa fa-warning"></i><span><?php _e('No banners found.', 'banner_ads'); ?></span></div>
        <?php } else { ?>
          <?php foreach($banners as $b) { ?>
            <div class="mb-table-row">
              <div class="mb-col-1"><?php echo $b['pk_i_id']; ?></div>
              
              <div class="mb-col-4 mb-align-left">
                <a class="mb-add-tooltip" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/banner_edit.php&bannerId=<?php echo $b['pk_i_id']; ?>" title="<?php echo osc_esc_html(__('Click to edit banner', 'banner_ads')); ?>"><?php echo $b['s_name']; ?></a>
              </div>
              
              <div class="mb-col-5 mb-align-left">
                <?php 
                  if($b['i_type'] == 1) {
                    _e('Show all adverts', 'banner_ads');
                  } else if($b['i_type'] == 2) {
                    _e('Rotate adverts (with fade effect)', 'banner_ads');
                  } else if($b['i_type'] == 1) {
                    _e('Show 1 random advert', 'banner_ads');
                  } else {
                    _e('Unknown', 'banner_ads');
                  }
                ?>
              </div>

              <div class="mb-col-7 mb-align-left mb-hooks"><?php echo (trim($b['s_hook']) <> '' ? $b['s_hook'] : '-'); ?></div>

              <div class="mb-col-3 mb-align-left">
                <?php if(!isset($b['adverts']) || !is_array($b['adverts']) || count($b['adverts']) <= 0) { ?>
                  <?php $messg = __('This banner does not contain any advert, therefore it will not display anything in front-office. First, add at least one advert into this banner.', 'banner_ads'); ?>
                  <em class="mb-blank mb-add-tooltip" title="<?php echo osc_esc_html($messg); ?>"><i class="fa fa-info-circle"></i> <?php _e('No advert assigned (hidden)', 'banner_ads'); ?></em>
                <?php } else { ?>
                  <a href="#" class="mb-show-banners"><?php echo sprintf(__('Contains %d adverts', 'banner_ads'), count($b['adverts'])); ?></a>
                <?php } ?>                   
              </div>
              
              <div class="mb-col-4 mb-align-right">
                <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/banner_edit.php&bannerId=<?php echo $b['pk_i_id']; ?>" class="mb-btn mb-button-blue"><i class="fa fa-pencil"></i> <?php _e('Edit', 'banner_ads'); ?></a>

                <?php if(!ba_is_demo()) { ?>
                  <a class="mb-btn mb-button-red" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/banner_edit.php&bannerId=<?php echo $b['pk_i_id']; ?>&what=delete" onclick="return confirm('<?php echo osc_esc_html(__('Are you sure you want to delete this banner?', 'banner_ads')); ?>')"><i class="fa fa-trash"></i> <?php echo __('Delete', 'banner_ads'); ?></a>
                <?php } ?>
              </div>
              
              
              <?php if(isset($b['adverts']) && is_array($b['adverts']) && count($b['adverts']) > 0) { ?>
                <div class="mb-row mb-banner-list">
                  <strong class="mb-titl"><?php _e('List of adverts those contains this banner', 'banner_ads'); ?></strong>

                  <div class="mb-table-head">
                    <div class="mb-col-1"><?php _e('ID', 'banner_ads'); ?></div>
                    <div class="mb-col-4 mb-align-left"><?php _e('Advert Name', 'banner_ads'); ?></div>
                    <div class="mb-col-2 mb-align-left"><?php _e('Type', 'banner_ads');?></div>
                    <div class="mb-col-2 mb-align-left"><?php _e('Budget', 'banner_ads'); ?></div>
                    <div class="mb-col-5 mb-align-left"><?php _e('Stats', 'banner_ads'); ?></div>
                    <div class="mb-col-3"><?php _e('Status', 'banner_ads'); ?></div>
                    <div class="mb-col-4 mb-align-left"><?php _e('Assignment', 'banner_ads'); ?></div>
                    <div class="mb-col-3">&nbsp;</div>
                  </div>                
                  
                  <?php foreach($b['adverts'] as $a) { ?>
                    <div class="mb-table-row">
                      <div class="mb-col-1"><?php echo $a['pk_i_id']; ?></div>
                      
                      <div class="mb-col-4 mb-align-left">
                        <a class="mb-add-tooltip" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/advert_edit.php&advertId=<?php echo $a['pk_i_id']; ?>" title="<?php echo osc_esc_html(__('Click to edit advert', 'banner_ads')); ?>"><?php echo $a['s_name']; ?></a>
                      </div>
                      
                      <div class="mb-col-2 mb-align-left">
                        <?php if($a['i_type'] == 1) { ?>
                          <i class="fa fa-code"></i> <?php echo __('HTML', 'banner_ads'); ?>
                        <?php } else if($a['i_type'] == 2) { ?>
                          <i class="fa fa-image"></i> <?php echo __('Image', 'banner_ads'); ?>
                        <?php } else if($a['i_type'] == 3) { ?>
                          <i class="fa fa-google"></i> <?php echo __('Adsense', 'banner_ads'); ?>
                        <?php } ?>
                      </div>
                      
                      <div class="mb-col-2 mb-align-left"><?php echo number_format($a['d_budget'], 2); ?><?php echo $cur; ?></div>
                      
                      <div class="mb-col-5 mb-align-left">
                        <?php echo $a['i_views']; ?>x <?php _e('view', 'banner_ads'); ?> / 
                        <?php echo $a['i_clicks']; ?>x <?php _e('click', 'banner_ads'); ?> / 

                        <?php echo number_format($a['d_price_view']*$a['i_views'] + $a['d_price_click']*$a['i_clicks'], 2); ?><?php echo $cur; ?> 
                        (<?php if($a['d_budget'] > 0) { echo round(($a['d_price_view']*$a['i_views'] + $a['d_price_click']*$a['i_clicks'])/$a['d_budget']*100); } else { echo 0; } ?>%)
                      </div>
                    

                      <div class="mb-col-3 mb-align-center status">
                        <?php
                          $status = 1;
                          $status_text = __('Active', 'banner_ads');

                          if( $a['d_budget'] > 0 && $a['i_views']*$a['d_price_view'] + $a['i_clicks']*$a['d_price_click'] >= $a['d_budget'] ) {
                            $status = 0;
                            $status_text = __('Budget exceed - spent', 'banner_ads') . ' ' . ($a['d_budget'] > 0 && $a['i_views']*$a['d_price_view'] + $a['i_clicks']*$a['d_price_click']) . $currency . ' ' . __('of', 'banner_ads') . ' ' . $a['d_budget'] . $currency;
                          } else if ($a['dt_expire'] <> '0000-00-00' && date('Y-m-d', strtotime($a['dt_expire'])) < date('Y-m-d')) {
                            $status = 0;
                            $status_text = __('Advert expired on', 'banner_ads') . ' ' . $a['dt_expire'];
                          }
                        ?>

                        <?php if($status == 1) { ?>
                          <span class="mb-stat mb-bg-green mb-add-tooltip" title="<?php echo osc_esc_html($status_text); ?>"><i class="fa fa-check"></i> <?php echo $status_text; ?></span>
                        <?php } else { ?>
                          <span class="mb-stat mb-bg-red mb-add-tooltip" title="<?php echo osc_esc_html($status_text); ?>"><i class="fa fa-times"></i> <?php echo $status_text; ?></span>
                        <?php } ?>
                      </div>
                      
                      <div class="mb-col-4 mb-align-left">
                        <em class="mb-blank"><?php echo sprintf(__('Assigned to %d banners', 'banner_ads'), count(array_filter(explode(',', $a['fk_s_banner_id'])))); ?></em>
                      </div>

                      <div class="mb-col-3 remove mb-align-right">
                        <a class="mb-btn mb-button-blue" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/advert_edit.php&advertId=<?php echo $a['pk_i_id']; ?>"><i class="fa fa-pencil"></i> <?php echo __('Edit', 'banner_ads'); ?></a>

                        <?php if(!ba_is_demo()) { ?>
                          <a class="mb-btn mb-button-red" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/advert_edit.php&advertId=<?php echo $a['pk_i_id']; ?>&what=delete" onclick="return confirm('<?php echo osc_esc_html(__('Are you sure you want to delete this advert?', 'banner_ads')); ?>')"><i class="fa fa-trash"></i> <?php echo __('Delete', 'banner_ads'); ?></a>
                        <?php } ?>

                      </div>
                      
                    </div>
                  <?php } ?>
                </div>
              <?php } ?>
              
            </div>
          <?php } ?>
        <?php } ?>
      </div>
      
      
      <div class="mb-row"></div>
      
      <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=banner_ads/admin/banner_edit.php" class="mb-button-green mb-add"><i class="fa fa-plus-circle"></i><?php _e('Create a new banner', 'banner_ads'); ?></a>
    </div>
  </div>
</div>

<?php echo ba_footer(); ?>
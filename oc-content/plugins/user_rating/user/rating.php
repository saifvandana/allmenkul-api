<?php
  $options = ur_options_all();
  $validate =  ur_param('validate') <> '' ? ur_param('validate') : 0;
  $monocolor_stars = ur_param('monocolor_stars') <> '' ? ur_param('monocolor_stars') : 0;
  $monocolor_class = ($monocolor_stars == 1 ? 'ur-no-cl' : 'ur-has-cl');
  $upscale_bars =  ur_param('upscale_bars') <> '' ? ur_param('upscale_bars') : 0;
  
  $user_id = Params::getParam('userId');
  $item_id = Params::getParam('itemId');
  
  $is_user_profile = false;
  if(Params::getParam('action') != 'runhook' && osc_get_osclass_location() == 'ur' && osc_get_osclass_section() == 'myrating') {
    $user_id = osc_logged_user_id();
    $is_user_profile = true;
  }
  
  if($item_id <> osc_item_id()) {
    $item = Item::newInstance()->findByPrimaryKey($item_id);
  } else {
    $item = osc_item();
  }  
  
  $user = array();
  
  if($user_id > 0) {
    $user = User::newInstance()->findByPrimaryKey($user_id);
    $user_name = @$user['s_name'];
    $user_email = @$user['s_email'];
  } else {
    $user_name = @$item['s_contact_name'];
    $user_email = @$item['s_contact_email'];
  }
  
  if($user_name == '') {
    $user_name = __('Anonymous', 'user_rating');
  }
  
  $img = ur_profile_img_url($user_id, $user);
  
  
  $user_details = '';
  
  if(isset($user['pk_i_id'])) {
    $loc = trim($user['s_city']);
    
    if($loc != '') {
      $loc .= ' (' . $user['s_region'] . ')';
    } else {
      $loc = $user['s_region'];
    }
    
    $reg = sprintf(__('Registered on %s', 'user_rating'), date('j. M Y', strtotime($user['dt_reg_date'])));
    
    if(trim($loc) != '') {
      $user_details = $loc;
    } else {
      $user_details = $reg; 
    }
  } else {
    $user_details = __('Unregistered user', 'user_rating'); 
  }
  
  $user_reg = (isset($user['dt_reg_date']) ? $user['dt_reg_date'] : date('Y-m-d H:i:s'));
  
  $total = ModelUR::newInstance()->getRatingCounts($user_id, $user_email, '', '', 0, $validate);
  $responses = ModelUR::newInstance()->getRatingByUserId($user_id, $user_email, 0, $validate);
?>

<?php if($user_id <= 0 && $user_email == '') { ?>
  <div id="ur-box-show" class="ur-show">
    <span class="ur-empty"><?php _e('User has not been recognized', 'user_rating'); ?></span>
  </div>
<?php } else { ?>
  <?php if(!$is_user_profile) { ?>
    <div class="ur-head"><?php echo sprintf(__('%s\'s rating', 'user_rating'), $user_name); ?></div>    
  <?php } ?>
  
  <div class="ur-box-content<?php if($is_user_profile) { ?>-false<?php } ?>">
    <?php if(!$is_user_profile) { ?>
      <div class="ur-card">
        <div class="ur-img"><img src="<?php echo $img; ?>" alt="<?php echo osc_esc_html($user_name); ?>"/></div>
        <div class="ur-about">
          <strong><?php echo $user_name; ?></strong>
          <span><?php echo $user_details; ?></span> 
        </div>
        
        <?php if($user_id > 0) { ?>
          <div class="ur-buttons">
            <a href="<?php echo osc_user_public_profile_url($user_id); ?>"><?php _e('Profile', 'user_rating'); ?></a>
          </div>
        <?php } ?>
      </div>
    <?php } ?>

    <div id="ur-box-show">
      <div class="ur-over">
        <?php 
          $global_rating = ModelUR::newInstance()->getRatingAverageByUserId($user_id, $user_email, 0, $validate); 
          $count_rating = ModelUR::newInstance()->getRatingCounts($user_id, $user_email, '', '', 0, $validate);
          $color = ur_user_color($global_rating);
          $level = ur_user_level($global_rating, $user_reg, $user_id, $count_rating); 
        ?>

        <strong class="ur-glob"><?php echo number_format($global_rating, 1); ?></strong>
        <div class="ur-stars-wrap"><div class="ur-stars-large"><?php echo ur_get_stars($global_rating); ?></div></div>
        <div class="ur-rat-count"><?php echo sprintf(__('based on %s reviews', 'user_rating'), $count_rating); ?></div>
        <div class="ur-level">
          <span class="<?php echo $level['id']; ?>"><?php echo $level['name']; ?></span>
        </div>
      </div>


      <?php if($total == 0) { ?>
        <div class="mb-total-zero"><?php _e('User has not been rated yet', 'user_rating'); ?></div>
      <?php } else { ?>

        <div class="ur-charts" data-options="<?php echo count($options); ?>">
          <?php if(count($options) > 1) { ?>
            <div class="ur-nav">
              <?php foreach($options as $o) { ?>
                <a href="#" data-tab="<?php echo $o['id']; ?>" class="<?php if($o['id'] == 'cat0') { ?>active<?php } ?>"><?php _e($o['name']); ?></a>
              <?php } ?>
            </div>
          <?php } ?>
          
          <div class="ur-tabs">
            <?php foreach($options as $o) { ?>
              <?php
                $r_array = array();
                $w_array = array();
                $rt = ModelUR::newInstance()->getRatingCounts($user_id, $user_email, 'i_' . $o['id'], '', 0, $validate);

                for($i=5;$i>=1;$i--) {
                  $r = ModelUR::newInstance()->getRatingCounts($user_id, $user_email, 'i_' . $o['id'], $i, 0, $validate);
                  $r_array[$i] = $r;
                  $w_array[$i] = ceil($r/$rt*100);
                }
                
                $max_width = max($w_array);
                $multiplier = 1;
                
                if(ceil($max_width) > 0) {
                  $multiplier = 100/ceil($max_width);
                }
                
                $w_array_unmodified = $w_array;

                if($upscale_bars === true) {
                  for($i=5;$i>=1;$i--) {
                    $w_array[$i] = $w_array[$i] * $multiplier;
                    $w_array[$i] = ($w_array[$i] > 100 ? 100 : $w_array[$i]);
                  }
                }
              ?>
              
              <div class="ur-tab <?php if($o['id'] == 'cat0') { ?>active<?php } ?>" data-tab="<?php echo $o['id']; ?>">
                <?php for($i=5;$i>=1;$i--) { ?>
                  <?php 
                    $r = $r_array[$i];
                  ?>
          
                  <div class="ur-line">
                    <div class="ur-lab">
                      <span><?php echo ($i == 1 ? sprintf(__('%s star', 'user_rating'), $i) : sprintf(__('%s stars', 'user_rating'), $i)); ?></span>
                    </div>

                    <div class="ur-bar">
                      <div class="<?php if($w_array[$i] > 5) { ?>ur-has-lab<?php } ?> <?php echo $monocolor_class; ?> color<?php echo $i; ?>" style="width:<?php echo round($w_array[$i], 2); ?>%;" title="<?php echo round($r/$rt*100, 0); ?>% - <?php echo $r; ?> <?php echo osc_esc_html(__('rating(s)', 'user_rating')); ?>">
                        <?php echo ($w_array[$i] ? $r : ''); ?>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>


        <div class="ur-ratings">
          <?php if(is_array($responses) && count($responses) > 0) { ?>
            <?php foreach($responses as $r) { ?>
              <?php 
                $avg = ($r['i_cat0'] + $r['i_cat1'] + $r['i_cat2'] + $r['i_cat3'] + $r['i_cat4'] + $r['i_cat5']) / ( min($r['i_cat0'], 1) + min($r['i_cat1'], 1) + min($r['i_cat2'], 1) + min($r['i_cat3'], 1) + min($r['i_cat4'], 1) + min($r['i_cat5'], 1) ); 
                $from_user_name = '';
                $from_user = array();

                if($r['fk_i_from_user_id'] > 0) {
                  $from_user = User::newInstance()->findByPrimaryKey($r['fk_i_from_user_id']);
                  $from_user_name = $from_user['s_name'];
                }
                
                if($from_user_name == '') {
                  $from_user_name = __('Anonymous', 'user_rating');
                }
                
                $from_user_img = ur_profile_img_url($r['fk_i_from_user_id'], $from_user);
              ?>

              <div class="ur-row">
                <div class="ur-user">
                  <div class="ur-img">
                    <img src="<?php echo $from_user_img; ?>" alt="<?php echo osc_esc_html($from_user_name); ?>"/>
                  </div>
                  
                  <div class="ur-about">
                    <strong><?php echo $from_user_name; ?></strong>
                    <span class="ur-stars-small">
                      <?php echo ur_get_stars($avg); ?>
                      <em class="ur-rate-value"><?php echo number_format($avg, 1); ?></em>
                    </span>
                  </div>
                  
                  <div class="ur-date"><?php echo ur_smart_date($r['d_datetime']); ?></div>
                </div>
                
                <div class="ur-text"><?php echo (trim($r['s_comment']) == '' ? __('No comment', 'user_rating') : trim(osc_esc_html($r['s_comment']))); ?></div>
              </div>
            <?php } ?>
          <?php } ?>
        </div>

      <?php } ?>
    </div>
  </div>
<?php } ?>

<?php
  $validation = ur_param('validate') <> '' ? ur_param('validate') : 0;
  $only_logged =  ur_param('only_reg') <> '' ? ur_param('only_reg') : 0;

  $user_id = Params::getParam('userId');
  $item_id = Params::getParam('itemId');
  
  if($item_id <> osc_item_id()) {
    $item = Item::newInstance()->findByPrimaryKey($item_id);
  } else {
    $item = osc_item();
  }

  if($user_id <= 0) {
    $user_email = @$item['s_contact_email'];
  } else {
    $user_email = '';
  }

  $user = array();
  if($user_id > 0) {
    $user = User::newInstance()->findByPrimaryKey($user_id);
    $user_name = @$user['s_name'];
  } else {
    $user_name = @$item['s_contact_name'];
  }

  $from_user_id = osc_logged_user_id();
  $img = ur_profile_img_url($user_id, $user);
  

  if($from_user_id == 0 || $from_user_id == '') {
    $already_has = 0;
  } else {
    $already_has = ModelUR::newInstance()->countRatingsByUserId($user_id, $user_email, $from_user_id);
    $already_has = (isset($already_has['i_count']) ? $already_has['i_count'] : 0);
  }
  
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

  $options = ur_options();
?>


<?php if(($only_logged == 1 && osc_is_web_user_logged_in()) || $only_logged == 0) { ?>
  <?php if($user_id == $from_user_id && $user_id <> '' && $user_id <> 0) { ?>

    <div class="ur-status ur-info">
      <div class="ur-row"><i class="fa fa-exclamation-circle"></i></div>
      <div class="ur-row"><?php _e('This is your profile, you cannot rate yourself.', 'user_rating'); ?></div>
    </div>

  <?php } else if($already_has < 1) { ?>
    <div class="ur-head"><?php _e('Leave a rating', 'user_rating'); ?></div>    
    
    <div class="ur-box-content">
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
        
      <form id="ur-box-new" name="new-rating" class="ur-box" action="<?php echo osc_base_url(); ?>?page=ajax&action=runhook&hook=ur_new_rating_manage" method="POST">
        <input type="hidden" name="userId" value="<?php echo $user_id; ?>"/>
        <input type="hidden" name="userEmail" value="<?php echo base64_encode($user_email); ?>"/>
        <input type="hidden" name="fromUserId" value="<?php echo $from_user_id; ?>"/>
        <input type="hidden" name="ratingType" value="0"/>

        <div class="ur-row ur-overall">
          <div class="ur-left"><?php echo sprintf(__('Overall experience with %s', 'user_rating'), $user_name); ?></div>
          <div class="ur-right ur-rating-box rating-0 ur-stars-medium">
            <input type="hidden" name="cat0" value="3"/>
            <?php for($i=1;$i<=5;$i++) { ?>
              <?php if($i <= 3) { ?>
                <a href="#" class="ur-rate i<?php echo $i; ?>" data-order="<?php echo $i; ?>"><span></span></a>
              <?php } else { ?>
                <a href="#" class="ur-rate ur-gray i<?php echo $i; ?>" data-order="<?php echo $i; ?>"><span></span></a>
              <?php } ?>
            <?php } ?> 
          </div>
        </div>
        
        <?php if(is_array($options) && count($options) > 0) { ?>
          <?php foreach($options as $o) { ?>
            <div class="ur-row ur-option">
              <div class="ur-left"><?php echo $o['name']; ?></div>
              <div class="ur-right ur-rating-box rating-<?php echo $o['id']; ?>">
                <input type="hidden" name="<?php echo $o['id']; ?>" value="3"/>
                <?php for($i=1;$i<=5;$i++) { ?>
                  <?php if($i <= 3) { ?>
                    <a href="#" class="ur-rate i<?php echo $i; ?>" data-order="<?php echo $i; ?>"><span></span></a>
                  <?php } else { ?>
                    <a href="#" class="ur-rate ur-gray i<?php echo $i; ?>" data-order="<?php echo $i; ?>"><span></span></a>
                  <?php } ?>
                <?php } ?> 
              </div>
            </div>
          <?php } ?>
        <?php } ?>
        
        <div class="ur-row ur-resp">
          <label for="response"><?php _e('Would you like to add something?', 'user_rating'); ?></label>
          <textarea id="response" class="ur-response" name="response"></textarea>
        </div>

        <div class="ur-row"><button type="submit" class="ur-button ur-submit" id="submit-button"><?php _e('Submit', 'user_rating'); ?></button></div>
      </form>
    </div>

    <div class="ur-status ur-success">
      <div class="ur-row"><i class="fa fa-check-circle"></i></div>
      <div class="ur-row">
        <?php _e('Your rating was successfully submitted!', 'user_rating'); ?>
        
        <?php if($validation == 1) { ?>
          <?php _e('Rating will be published as soon as our team validates it.', 'user_rating'); ?>
        <?php } ?>
      </div>
    </div>

    <div class="ur-status ur-error">
      <div class="ur-row"><i class="fa fa-times-circle"></i></div>
      <div class="ur-row">
        <?php _e('Whoops, there was some error. Please try to add rating later.', 'user_rating'); ?>
      </div>
    </div>

  <?php } else { ?>
    <div class="ur-status ur-info">
      <div class="ur-row"><i class="fa fa-exclamation-circle"></i></div>
      <div class="ur-row">
        <?php _e('You have already rated this user.', 'user_rating'); ?>
      </div>
    </div>
  <?php } ?>
<?php } else { ?>
  <div class="ur-status ur-info">
    <div class="ur-row"><i class="fa fa-exclamation-circle"></i></div>
    <div class="ur-row">
      <?php _e('Only logged in users can leave rating.', 'user_rating'); ?>
    </div>
  </div>
<?php } ?>
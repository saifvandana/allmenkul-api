<?php
  // Create menu
  $title = __('Ratings', 'user_rating');
  ur_menu($title);

  $validate = osc_get_preference('validate', 'plugin-user_rating');


  // VALIDATE LISTINGS
  if( Params::getParam('plugin_action') == 'validate' ) {

    // APPROVE SINGLE
    $params_array = Params::getParamsAsArray();

    $approved = '';
    foreach($params_array as $key => $value){
      $exp_key = explode('_', $key);
      if($exp_key[0] == 'approve-single'){
        // ID of prompted rating stored in $value
        $approved .= $exp_key[1] . ', ';
        message_ok( __('Rating(s) with following IDs were approved:', 'user_rating') . ' ' .  substr($approved, 0, strlen($approved)-2));

        ModelUR::newInstance()->validateRatingById( $exp_key[1] );
      }
    }



    // REMOVE SINGLE
    $params_array = Params::getParamsAsArray();

    $removed = '';
    foreach($params_array as $key => $value){
      $exp_key = explode('_', $key);
      if($exp_key[0] == 'remove-single'){
        // ID of prompted rating stored in $value
        $removed .= $exp_key[1] . ', ';
        message_ok( __('Rating(s) with following IDs were removed:', 'user_rating') . ' ' .  substr($removed, 0, strlen($removed)-2));

        if(!ur_is_demo()) {
          ModelUR::newInstance()->removeRatingById( $exp_key[1] );
        }
      }
    }



    // APPROVE SELECTED
    if( Params::getParam('item_action') == __('Approve Selected', 'user_rating') ) {
      $params_array = Params::getParamsAsArray();

      if( is_array($params_array) && !empty($params_array) ) { 
        $approved = '';
        foreach($params_array as $key => $value){
          $exp_key = explode('_', $key);
          if($exp_key[0] == 'valid'){
            // ID of prompted ratings stored in $value
            $approved .= $value . ', ';

            ModelUR::newInstance()->validateRatingById( $exp_key[1] );

          }
        }
      }

      message_ok( __('Rating(s) with following IDs were approved:', 'user_rating') . ' ' .  substr($approved, 0, strlen($approved)-2));
    }



    // REMOVE SELECTED
    if( Params::getParam('item_action') == __('Remove Selected', 'user_rating') && !ur_is_demo()) {
      $params_array = Params::getParamsAsArray();

      if( is_array($params_array) && !empty($params_array) ) { 
        $removed = '';
        foreach($params_array as $key => $value){
          $exp_key = explode('_', $key);
          if($exp_key[0] == 'valid'){
            // ID of prompted ratings stored in $value
            $removed .= $value . ', ';

            ModelUR::newInstance()->removeRatingById( $exp_key[1] );
          }
        }
      }

      message_ok( __('Rating(s) with following IDs were removed:', 'user_rating') . ' ' .  substr($removed, 0, strlen($removed)-2));
    }
  }
?>



<div class="mb-body">

  <!-- TO BE VALIDATED SECTION -->
  <?php if($validate == 1) { ?>
    <div class="mb-box">
      <div class="mb-head"><i class="fa fa-stack-overflow"></i> <?php _e('Ratings to be Validated', 'user_rating'); ?></div>

      <div class="mb-inside">
        <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
          <input type="hidden" name="page" value="plugins" />
          <input type="hidden" name="action" value="renderplugin" />
          <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>rating.php" />
          <input type="hidden" name="plugin_action" value="validate" />


          <?php $ratings = ModelUR::newInstance()->getAllRatings(2, 100); ?>


          <?php if(count($ratings) > 0) { ?>
            <div class="mb-info-box" style="margin-top:20px;margin-bottom:25px;">
              <div class="mb-line"><?php _e('Only latest 100 ratings are shown!', 'user_rating'); ?></div>
            </div>

            <div class="mb-table" style="margin-bottom:30px;">
              <div class="mb-table-head">
                <div class="mb-col-1"><input type="checkbox" class="mb_mark_all" name="mb_mark_all" id="mb_mark_all" value="valid_" /></div>
                <div class="mb-col-1"><?php _e('ID', 'user_rating'); ?></div>
                <div class="mb-col-4 mb-align-left"><?php _e('Rating', 'user_rating'); ?></div>
                <div class="mb-col-2"><?php _e('User', 'user_rating'); ?></div>
                <div class="mb-col-10 mb-align-left"><?php _e('Comment', 'user_rating'); ?></div>
                <div class="mb-col-2"><?php _e('Status', 'user_rating'); ?></div>
                <div class="mb-col-4 mb-align-left">&nbsp;</div>
              </div>


              <?php foreach( $ratings as $r ) { ?>
                <?php 
                  if($r['fk_i_user_id'] <> 0 && $r['fk_i_user_id'] <> '') {
                    $user = User::newInstance()->findByPrimaryKey($r['fk_i_user_id']);
                    $user_name = $user['s_name'] . ' (' . $user['s_email'] . ')';
                    $user_type = __('Registered', 'user_rating'); 
                  } else {
                    $user_name = $r['s_user_email'];
                    $user_type = __('Unregistered', 'user_rating'); 
                  }

                  if($r['fk_i_from_user_id'] <> 0 && $r['fk_i_from_user_id'] <> '') {
                    $from_user = User::newInstance()->findByPrimaryKey($r['fk_i_from_user_id']);
                    $from_user_name = $from_user['s_name'] . ' (' . $from_user['s_email'] . ')';
                    $from_user_type = __('Registered', 'user_rating'); 
                  } else {
                    $from_user_name = __('Unknown', 'user_rating'); 
                    $from_user_type = __('Unregistered', 'user_rating'); 
                  }

                  $user_title = __('Rated user', 'user_rating') . '<br/>';
                  $user_title .= __('Name', 'user_rating') . ': ' . $user_name . '<br/>';
                  $user_title .= __('Type', 'user_rating') . ': ' . $user_type . '<br/><br/>';
                  $user_title .= __('Rating left by', 'user_rating') . '<br/>';
                  $user_title .= __('Name', 'user_rating') . ': ' . $from_user_name . '<br/>';
                  $user_title .= __('Type', 'user_rating') . ': ' . $from_user_type . '<br/><br/>';
                  $user_title .= __('Date', 'user_rating') . ': ' . $r['d_datetime'];

                  $status = $r['i_validate'];
                  $status_name = '';
                  $status_class = '';

                  if($status == 0) {
                    $status_name = __('Pending', 'user_rating'); 
                    $status_class = 'mb-blue'; 
                  } else if ($status == 1) {
                    $status_name = __('Valid', 'user_rating'); 
                    $status_class = 'mb-green'; 
                  }

                  $empty = '<span class="mb-i mb-gray">' . __('No comment', 'user_rating') . '</span>';

                  $avg = ModelUR::newInstance()->getRatingAverageByRatingId($r['i_rating_id']);
                  $color = ur_user_color($avg);
                ?>

                <div class="mb-table-row">
                  <div class="mb-col-1 <?php echo osc_esc_html($status_class); ?>" title="<?php echo osc_esc_html($status_name); ?>"><input type="checkbox" name="valid_<?php echo $r['i_rating_id']; ?>" id="valid_<?php echo $r['i_rating_id']; ?>" value="<?php echo $r['i_rating_id']; ?>" /></div>
                  <div class="mb-col-1"><?php echo $r['i_rating_id']; ?></div>
                  <div class="mb-col-4 mb-align-left mb-stars ur-stars-small <?php echo $color; ?>"><?php echo ur_get_stars($avg); ?><em><?php echo number_format($avg, 1); ?></em></div>
                  <div class="mb-col-2 mb-has-tooltip-user" title="<?php echo osc_esc_html($user_title); ?>"><i class="fa fa-user from"></i> <i class="fa fa-long-arrow-right"></i> <i class="fa fa-user to"></i></div>
                  <div class="mb-col-10 mb-align-left mb-no-wrap" title="<?php echo osc_esc_html($r['s_comment']); ?>"><?php echo trim($r['s_comment']) <> '' ? osc_esc_html($r['s_comment']) : $empty; ?></div>
                  <div class="mb-col-2"><?php echo $status_name; ?></div>
                  <div class="mb-col-4 mb-align-left">
                    <input type="submit" name="approve-single_<?php echo $r['i_rating_id']; ?>" class="mb-button-green" style="float:left;margin: -5 0; height: 27px; line-height: 13px;" value="<?php echo osc_esc_html(__('Approve', 'user_rating')); ?>"/>
 
                    <?php if(!ur_is_demo()) { ?>
                      <input type="submit" name="remove-single_<?php echo $r['i_rating_id']; ?>" class="mb-button-red" style="float:left;margin: -5px 0 -5px 6px; height: 27px; line-height: 13px;" value="<?php echo osc_esc_html(__('Remove', 'user_rating')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this rating? Action cannot be undone', 'user_rating')); ?>?')" />
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>
            </div>

            <div class="mb-foot">
              <input type="submit" name="item_action" class="mb-button-white" style="float:left;margin-right:10px;" value="<?php echo osc_esc_html(__('Approve Selected', 'user_rating')); ?>" />

              <?php if(!ur_is_demo()) { ?>
                <input type="submit" name="item_action" class="mb-button-white" style="float:left" value="<?php echo osc_esc_html(__('Remove Selected', 'user_rating')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove selected ratings? Action cannot be undone', 'user_rating')); ?>?')" />
              <?php } ?>
            </div>

          <?php } else { ?>
            <div class="mb-info-box" style="margin-top:20px;margin-bottom:25px;">
              <div class="mb-line"><?php _e('No ratings waiting for validation', 'user_rating'); ?></div>
            </div>
          <?php } ?>
        </div>
      </form>
    </div>
  <?php } ?>



  <!-- VALIDATED SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-check-circle"></i> <?php _e('Validated Ratings', 'user_rating'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>rating.php" />
        <input type="hidden" name="plugin_action" value="validate" />


        <?php $ratings = ModelUR::newInstance()->getAllRatings($validate, 100); ?>


        <?php if(count($ratings) > 0) { ?>
          <div class="mb-info-box" style="margin-top:20px;margin-bottom:25px;">
            <div class="mb-line"><?php _e('Only latest 100 ratings are shown!', 'user_rating'); ?></div>
          </div>

          <div class="mb-table" style="margin-bottom:30px;">
            <div class="mb-table-head">
              <div class="mb-col-1"><input type="checkbox" class="mb_mark_all" name="mb_mark_all" id="mb_mark_all" value="valid_" /></div>
              <div class="mb-col-1"><?php _e('ID', 'user_rating'); ?></div>
              <div class="mb-col-4 mb-align-left"><?php _e('Rating', 'user_rating'); ?></div>
              <div class="mb-col-2"><?php _e('User', 'user_rating'); ?></div>
              <div class="mb-col-10 mb-align-left"><?php _e('Comment', 'user_rating'); ?></div>
              <div class="mb-col-2"><?php _e('Status', 'user_rating'); ?></div>
              <div class="mb-col-4 mb-align-left">&nbsp;</div>
            </div>


            <?php foreach( $ratings as $r ) { ?>
              <?php 
                if($r['fk_i_user_id'] <> 0 && $r['fk_i_user_id'] <> '') {
                  $user = User::newInstance()->findByPrimaryKey($r['fk_i_user_id']);
                  $user_name = $user['s_name'] . ' (' . $user['s_email'] . ')';
                  $user_type = __('Registered', 'user_rating'); 
                } else {
                  $user_name = $r['s_user_email'];
                  $user_type = __('Unregistered', 'user_rating'); 
                }

                if($r['fk_i_from_user_id'] <> 0 && $r['fk_i_from_user_id'] <> '') {
                  $from_user = User::newInstance()->findByPrimaryKey($r['fk_i_from_user_id']);
                  //$from_user_name = $user['s_name'] . ' (' . $user['s_email'] . ')';
                  $from_user_name = $from_user['s_name'] . ' (' . $from_user['s_email'] . ')';
                  $from_user_type = __('Registered', 'user_rating'); 
                } else {
                  $from_user_name = __('Unknown', 'user_rating'); 
                  $from_user_type = __('Unregistered', 'user_rating'); 
                }

                $user_title = __('Rated user', 'user_rating') . '<br/>';
                $user_title .= __('Name', 'user_rating') . ': ' . $user_name . '<br/>';
                $user_title .= __('Type', 'user_rating') . ': ' . $user_type . '<br/><br/>';
                $user_title .= __('Rating left by', 'user_rating') . '<br/>';
                $user_title .= __('Name', 'user_rating') . ': ' . $from_user_name . '<br/>';
                $user_title .= __('Type', 'user_rating') . ': ' . $from_user_type . '<br/><br/>';
                $user_title .= __('Date', 'user_rating') . ': ' . $r['d_datetime'];


                $status = $r['i_validate'];
                $status_name = '';
                $status_class = '';

                if($status == 0) {
                  if($validate == 1) {
                    $status_name = __('Pending', 'user_rating'); 
                    $status_class = 'mb-blue'; 
                  } else {
                    $status_name = __('Valid', 'user_rating'); 
                    $status_class = 'mb-green'; 
                  }
                } else if ($status == 1) {
                  $status_name = __('Valid', 'user_rating'); 
                  $status_class = 'mb-green'; 
                }

                $empty = '<span class="mb-i mb-gray">' . __('No comment', 'user_rating') . '</span>';

                $avg = ModelUR::newInstance()->getRatingAverageByRatingId($r['i_rating_id']);
                $color = ur_user_color($avg);
              ?>

              <div class="mb-table-row">
                <div class="mb-col-1 <?php echo osc_esc_html($status_class); ?>" title="<?php echo osc_esc_html($status_name); ?>"><input type="checkbox" name="valid_<?php echo $r['i_rating_id']; ?>" id="valid_<?php echo $r['i_rating_id']; ?>" value="<?php echo $r['i_rating_id']; ?>" /></div>
                <div class="mb-col-1"><?php echo $r['i_rating_id']; ?></div>
                <div class="mb-col-4 mb-stars mb-align-left ur-stars-small <?php echo $color; ?>"><?php echo ur_get_stars($avg); ?><em><?php echo number_format($avg, 1); ?></em></div>
                <div class="mb-col-2 mb-has-tooltip-user" title="<?php echo osc_esc_html($user_title); ?>"><i class="fa fa-user from"></i> <i class="fa fa-long-arrow-right"></i> <i class="fa fa-user to"></i></div>
                <div class="mb-col-10 mb-align-left mb-no-wrap" title="<?php echo $r['s_comment']; ?>"><?php echo trim($r['s_comment']) <> '' ? $r['s_comment'] : $empty; ?></div>
                <div class="mb-col-2"><?php echo $status_name; ?></div>
                <div class="mb-col-4 mb-align-left">
                  <?php if(!ur_is_demo()) { ?>
                    <input type="submit" name="remove-single_<?php echo $r['i_rating_id']; ?>" class="mb-button-red" style="float:left;margin: -5px 0; height: 27px; line-height: 13px;" value="<?php echo osc_esc_html(__('Remove', 'user_rating')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this rating? Action cannot be undone', 'user_rating')); ?>?')" />
                  <?php } ?>
                </div>
              </div>
            <?php } ?>   
          </div>

          <?php if(!ur_is_demo()) { ?>
            <div class="mb-foot">
              <input type="submit" name="item_action" class="mb-button-white" style="float:left" value="<?php echo osc_esc_html(__('Remove Selected', 'user_rating')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove selected ratings? Action cannot be undone', 'user_rating')); ?>?')" />
            </div>
          <?php } ?>

        <?php } else { ?>
          <div class="mb-info-box" style="margin-top:20px;margin-bottom:25px;">
            <div class="mb-line"><?php _e('No validated ratings yet', 'user_rating'); ?></div>
          </div>
        <?php } ?>
      </div>
    </form>
  </div>
</div>

<?php echo ur_footer(); ?>
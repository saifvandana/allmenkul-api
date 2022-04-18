<?php
  // Create menu
  $title = __('Offers', 'make_offer');
  mo_menu($title);

  $validate = osc_get_preference('validate', 'plugin-make_offer');
  $notify = osc_get_preference('notify', 'plugin-make_offer');

  // VALIDATE LISTINGS
  if( Params::getParam('plugin_action') == 'validate' ) {

    // APPROVE SINGLE
    $params_array = Params::getParamsAsArray();

    $approved = '';
    foreach($params_array as $key => $value){
      $exp_key = explode('_', $key);
      if($exp_key[0] == 'approve-single'){
        // ID of prompted offer stored in $value
        $approved .= $exp_key[1] . ', ';
        message_ok( __('Offers(s) with following IDs were approved:', 'make_offer') . ' ' .  substr($approved, 0, strlen($approved)-2));

        ModelMO::newInstance()->validateOfferById( $exp_key[1] );

        // SEND EMAIL TO SELLER
        if($validate == 1 && $notify == 1) {
          mo_notify_seller($exp_key[1]);
        }
      }
    }



    // REMOVE SINGLE
    $params_array = Params::getParamsAsArray();

    $removed = '';
    foreach($params_array as $key => $value){
      $exp_key = explode('_', $key);
      if($exp_key[0] == 'remove-single'){
        // ID of prompted offers stored in $value
        $removed .= $exp_key[1] . ', ';
        message_ok( __('Offer(s) with following IDs were removed:', 'make_offer') . ' ' .  substr($removed, 0, strlen($removed)-2));

        ModelMO::newInstance()->removeOfferById( $exp_key[1] );
      }
    }



    // APPROVE SELECTED
    if( Params::getParam('item_action') == __('Approve Selected', 'make_offer') ) {
      $params_array = Params::getParamsAsArray();

      if( is_array($params_array) && !empty($params_array) ) { 
        $approved = '';
        foreach($params_array as $key => $value){
          $exp_key = explode('_', $key);
          if($exp_key[0] == 'valid'){
            // ID of prompted offers stored in $value
            $approved .= $value . ', ';

            ModelMO::newInstance()->validateOfferById( $exp_key[1] );

            // SEND EMAIL TO SELLER
            if($validate == 1 && $notify == 1) {
              mo_notify_seller($exp_key[1]);
            }
          }
        }
      }

      message_ok( __('Offers(s) with following IDs were approved:', 'make_offer') . ' ' .  substr($approved, 0, strlen($approved)-2));
    }



    // REMOVE SELECTED
    if( Params::getParam('item_action') == __('Remove Selected', 'make_offer') ) {
      $params_array = Params::getParamsAsArray();

      if( is_array($params_array) && !empty($params_array) ) { 
        $removed = '';
        foreach($params_array as $key => $value){
          $exp_key = explode('_', $key);
          if($exp_key[0] == 'valid'){
            // ID of prompted offers stored in $value
            $removed .= $value . ', ';

            ModelMO::newInstance()->removeOfferById( $exp_key[1] );
          }
        }
      }

      message_ok( __('Offers(s) with following IDs were removed:', 'make_offer') . ' ' .  substr($removed, 0, strlen($removed)-2));
    }
  }
?>



<div class="mb-body">

  <!-- TO BE VALIDATED SECTION -->
  <?php if($validate == 1) { ?>
    <div class="mb-box">
      <div class="mb-head"><i class="fa fa-stack-overflow"></i> <?php _e('Offers to be Validated', 'make_offer'); ?></div>

      <div class="mb-inside">
        <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
          <input type="hidden" name="page" value="plugins" />
          <input type="hidden" name="action" value="renderplugin" />
          <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>offers.php" />
          <input type="hidden" name="plugin_action" value="validate" />


          <?php $offers = ModelMO::newInstance()->getAllOffers(2, 100); ?>


          <?php if(count($offers) > 0) { ?>
            <div class="mb-info-box" style="margin-top:20px;margin-bottom:25px;">
              <div class="mb-line"><?php _e('Only latest 100 offers are shown!', 'make_offer'); ?></div>
            </div>

            <div class="mb-table" style="margin-bottom:30px;">
              <div class="mb-table-head">
                <div class="mb-col-1"><input type="checkbox" class="mb_mark_all" name="mb_mark_all" id="mb_mark_all" value="valid_" /></div>
                <div class="mb-col-1"><?php _e('ID', 'make_offer'); ?></div>
                <div class="mb-col-3 mb-align-left"><?php _e('Item', 'make_offer'); ?></div>
                <div class="mb-col-3 mb-align-left"><?php _e('User', 'make_offer'); ?></div>
                <div class="mb-col-1"><?php _e('Qty', 'make_offer'); ?></div>
                <div class="mb-col-3 mo-bo-price"><?php _e('Offer Price', 'make_offer'); ?> <span>/ <?php _e('Original', 'make_offer'); ?></span></div>
                <div class="mb-col-6 mb-align-left"><?php _e('Comment', 'make_offer'); ?></div>
                <div class="mb-col-2"><?php _e('Validate', 'make_offer'); ?></div>
                <div class="mb-col-4 mb-align-left">&nbsp;</div>
              </div>


              <?php foreach( $offers as $r ) { ?>
                <?php 
                  $valid = $r['i_validate'];
                  $valid_name = '';
                  $valid_class = '';

                  if($valid == 0) {
                    $valid_name = __('Pending', 'make_offer'); 
                    $valid_class = 'mb-blue'; 
                  } else if ($valid == 1) {
                    $valid_name = __('Valid', 'make_offer'); 
                    $valid_class = 'mb-green'; 
                  }

                  $empty = '<span class="mb-i mb-gray">' . __('No comment', 'make_offer') . '</span>';

                  $item = Item::newInstance()->findByPrimaryKey($r['fk_i_item_id']);
                  $currency_full = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);
                  $currency_symbol = isset($currency_full['s_description']) ? $currency_full['s_description'] : '';
                  $user_title = osc_esc_html(__('Email', 'make_offer') . ': ' . $r['s_user_email'] . '<br/>' . __('Phone', 'make_offer') . ': ' . $r['s_user_phone']);
                ?>

                <div class="mb-table-row">
                  <div class="mb-col-1 <?php echo osc_esc_html($valid_class); ?>" title="<?php echo osc_esc_html($valid_name); ?>"><input type="checkbox" name="valid_<?php echo $r['i_offer_id']; ?>" id="valid_<?php echo $r['i_offer_id']; ?>" value="<?php echo $r['i_offer_id']; ?>" /></div>
                  <div class="mb-col-1"><?php echo $r['i_offer_id']; ?></div>
                  <div class="mb-col-3 mb-align-left mb-no-wrap"><a href="<?php echo osc_item_url_ns($item['pk_i_id']); ?>" target="_blank"><?php echo $item['s_title']; ?></a></div>
                  <div class="mb-col-3 mb-align-left mb-has-tooltip-user mb-no-wrap" title="<?php echo osc_esc_html($user_title); ?>">
                    <?php if($r['i_user_id'] > 0 && $r['i_user_id'] <> '') { ?>
                      <a href="<?php echo osc_admin_base_url(true); ?>?page=users&action=edit&id=<?php echo $r['i_user_id']; ?>" target="_blank"><?php echo $r['s_user_name']; ?></a>
                    <?php } else { ?>
                      <?php echo $r['s_user_name']; ?>
                    <?php } ?>
                  </div>
                  <div class="mb-col-1"><?php echo $r['i_quantity']; ?>x</div>
                  <div class="mb-col-3 mo-bo-price">
                    <?php echo osc_format_price($r['i_price'], $currency_symbol); ?> <span>/ <?php echo osc_format_price($item['i_price'], $currency_symbol); ?></span>
                  </div>
                  <div class="mb-col-6 mb-align-left mb-no-wrap" title="<?php echo osc_esc_html($r['s_comment']); ?>"><?php echo trim($r['s_comment']) <> '' ? $r['s_comment'] : $empty; ?></div>
                  <div class="mb-col-2"><?php echo $valid_name; ?></div>
                  <div class="mb-col-4 mb-align-left">
                    <input type="submit" name="approve-single_<?php echo $r['i_offer_id']; ?>" class="mb-button-green" style="float:left;margin: -4px 0; height: 27px; line-height: 13px;" value="<?php echo osc_esc_html(__('Approve', 'make_offer')); ?>"/>
                    <input type="submit" name="remove-single_<?php echo $r['i_offer_id']; ?>" class="mb-button-red" style="float:left;margin: -4px 0 -4px 6px; height: 27px; line-height: 13px;" value="<?php echo osc_esc_html(__('Remove', 'make_offer')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this offer? Action cannot be undone', 'make_offer')); ?>?')" />
                  </div>
                </div>
              <?php } ?>
            </div>

            <div class="mb-foot">
              <input type="submit" name="item_action" class="mb-button-white" style="float:left;margin-right:10px;" value="<?php echo osc_esc_html(__('Approve Selected', 'make_offer')); ?>" />
              <input type="submit" name="item_action" class="mb-button-white" style="float:left" value="<?php echo osc_esc_html(__('Remove Selected', 'make_offer')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove selected offers? Action cannot be undone', 'make_offer')); ?>?')" />
            </div>

          <?php } else { ?>
            <div class="mb-info-box" style="margin-top:20px;margin-bottom:25px;">
              <div class="mb-line"><?php _e('No offers waiting for validation', 'make_offer'); ?></div>
            </div>
          <?php } ?>
        </div>
      </form>
    </div>
  <?php } ?>



  <!-- VALIDATED SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-check-circle"></i> <?php _e('Validated Offers', 'make_offer'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>offers.php" />
        <input type="hidden" name="plugin_action" value="validate" />


        <?php $offers = ModelMO::newInstance()->getAllOffers($validate, 100); ?>


        <?php if(count($offers) > 0) { ?>
          <div class="mb-info-box" style="margin-top:20px;margin-bottom:25px;">
            <div class="mb-line"><?php _e('Only latest 100 offers are shown!', 'make_offer'); ?></div>
          </div>

          <div class="mb-table" style="margin-bottom:30px;">
            <div class="mb-table-head">
              <div class="mb-col-1"><input type="checkbox" class="mb_mark_all" name="mb_mark_all" id="mb_mark_all" value="valid_" /></div>
              <div class="mb-col-1"><?php _e('ID', 'make_offer'); ?></div>
              <div class="mb-col-3 mb-align-left"><?php _e('Item', 'make_offer'); ?></div>
              <div class="mb-col-3 mb-align-left"><?php _e('User', 'make_offer'); ?></div>
              <div class="mb-col-1"><?php _e('Qty', 'make_offer'); ?></div>
              <div class="mb-col-3 mo-bo-price"><?php _e('Offer Price', 'make_offer'); ?> <span>/ <?php _e('Original', 'make_offer'); ?></span></div>
              <div class="mb-col-6 mb-align-left"><?php _e('Comment', 'make_offer'); ?></div>
              <div class="mb-col-2"><?php _e('Validate', 'make_offer'); ?></div>
              <div class="mb-col-2"><?php _e('Accepted?', 'make_offer'); ?></div>
              <div class="mb-col-2 mb-align-left">&nbsp;</div>
            </div>


            <?php foreach( $offers as $r ) { ?>
              <?php
                $valid = $r['i_validate'];
                $valid_name = '';
                $valid_class = '';

                if($valid == 0) {
                  if($validate == 1) {
                    $valid_name = __('Pending', 'make_offer'); 
                    $valid_class = 'mb-blue'; 
                  } else {
                    $valid_name = __('Valid', 'make_offer'); 
                    $valid_class = 'mb-green'; 
                  }
                } else if ($valid == 1) {
                  $valid_name = __('Valid', 'make_offer'); 
                  $valid_class = 'mb-green'; 
                }


                $status = $r['i_status'];
                $status_name = '';
                $status_class = '';

                if($status == 1) {
                  $status_name = __('Accepted', 'make_offer'); 
                  $status_class = 'mb-green mo-seller-status'; 
                  $status_fa = '<i class="fa fa-check ' . $status_class . '"></i>'; 
                } else if($status == 2) {
                  $status_name = __('Declined', 'make_offer'); 
                  $status_class = 'mb-red mo-seller-status'; 
                  $status_fa = '<i class="fa fa-times ' . $status_class . '"></i>'; 
                } else {
                  $status_name = __('Pending', 'make_offer'); 
                  $status_class = 'mb-blue mo-seller-status'; 
                  $status_fa = '<i class="fa fa-question ' . $status_class . '"></i>'; 
                }

                $empty = '<span class="mb-i mb-gray">' . __('No comment', 'make_offer') . '</span>';

                $item = Item::newInstance()->findByPrimaryKey($r['fk_i_item_id']);
                $currency_full = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);
                $currency_symbol = isset($currency_full['s_description']) ? $currency_full['s_description'] : '';
                $user_title = osc_esc_html(__('Email', 'make_offer') . ': ' . $r['s_user_email'] . '<br/>' . __('Phone', 'make_offer') . ': ' . $r['s_user_phone']);
              ?>

              <div class="mb-table-row">
                <div class="mb-col-1 <?php echo osc_esc_html($valid_class); ?>" title="<?php echo osc_esc_html($valid_name); ?>"><input type="checkbox" name="valid_<?php echo $r['i_offer_id']; ?>" id="valid_<?php echo $r['i_offer_id']; ?>" value="<?php echo $r['i_offer_id']; ?>" /></div>
                <div class="mb-col-1"><?php echo $r['i_offer_id']; ?></div>
                <div class="mb-col-3 mb-align-left mb-no-wrap"><a href="<?php echo osc_item_url_ns($item['pk_i_id']); ?>" target="_blank"><?php echo $item['s_title']; ?></a></div>
                <div class="mb-col-3 mb-align-left mb-has-tooltip-user mb-no-wrap" title="<?php echo osc_esc_html($user_title); ?>">
                  <?php if($r['i_user_id'] > 0 && $r['i_user_id'] <> '') { ?>
                    <a href="<?php echo osc_admin_base_url(true); ?>?page=users&action=edit&id=<?php echo $r['i_user_id']; ?>" target="_blank"><?php echo $r['s_user_name']; ?></a>
                  <?php } else { ?>
                    <?php echo $r['s_user_name']; ?>
                  <?php } ?>
                </div>
                <div class="mb-col-1"><?php echo $r['i_quantity']; ?>x</div>
                <div class="mb-col-3 mo-bo-price">
                  <?php echo osc_format_price($r['i_price'], $currency_symbol); ?> <span>/ <?php echo osc_format_price($item['i_price'], $currency_symbol); ?></span>
                </div>
                <div class="mb-col-6 mb-align-left mb-no-wrap" title="<?php echo osc_esc_html($r['s_comment']); ?>"><?php echo trim($r['s_comment']) <> '' ? $r['s_comment'] : $empty; ?></div>
                <div class="mb-col-2"><?php echo $valid_name; ?></div>
                <div class="mb-col-2" title="<?php echo osc_esc_html($status_name); ?>"><?php echo $status_fa; ?></div>
                <div class="mb-col-2 mb-align-left">
                  <input type="submit" name="remove-single_<?php echo $r['i_offer_id']; ?>" class="mb-button-red" style="float:left;margin: -4px 0; height: 27px; line-height: 13px;" value="<?php echo osc_esc_html(__('Remove', 'make_offer')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this offer? Action cannot be undone', 'make_offer')); ?>?')" />
                </div>
              </div>
            <?php } ?>   
          </div>

          <div class="mb-foot">
            <input type="submit" name="item_action" class="mb-button-white" style="float:left" value="<?php echo osc_esc_html(__('Remove Selected', 'make_offer')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove selected offers? Action cannot be undone', 'make_offer')); ?>?')" />
          </div>

        <?php } else { ?>
          <div class="mb-info-box" style="margin-top:20px;margin-bottom:25px;">
            <div class="mb-line"><?php _e('No validated offers yet', 'make_offer'); ?></div>
          </div>
        <?php } ?>
      </div>
    </form>
  </div>
</div>

<?php echo mo_footer(); ?>
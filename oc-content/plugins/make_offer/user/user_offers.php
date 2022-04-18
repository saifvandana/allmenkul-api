<link href="<?php echo osc_base_url(); ?>oc-content/plugins/make_offer/css/tipped.css" rel="stylesheet" type="text/css" />
<script src="<?php echo osc_base_url(); ?>oc-content/plugins/make_offer/js/tipped.js"></script>

<?php
  $type = (Params::getParam('pageType') <> '' ? Params::getParam('pageType') : 'my-items');
  $show_quantity = mo_param('show_quantity') <> '' ? mo_param('show_quantity') : 0;
  $validate = mo_param('validate') <> '' ? mo_param('validate') : 0;

  // Your items that contains offers
  $your_items = ModelMO::newInstance()->getItemsWithOffersByUserId(osc_logged_user_id());

  // Offers placed by you
  $my_offers = ModelMO::newInstance()->getYourOffersByUserId(osc_logged_user_id());
  
  $highlight_id = Params::getParam('offerId');
  $respond_offer_link = osc_base_url(true) . '?page=ajax&action=runhook&hook=mo_respond_offer_manage';
?>


<div class="mo-body">
  <div class="mo-nav">
    <a href="#" data-tab="my-items" class="<?php if($type == 'my-items') { ?>active<?php } ?>"><?php _e('Offers on your items', 'make_offer'); ?></a>
    <a href="#" data-tab="my-offers" class="<?php if($type == 'my-offers') { ?>active<?php } ?>"><?php _e('Offers placed by you', 'make_offer'); ?></a>
  </div>
  
  <div class="mo-tabs">
    <div class="mo-tab" data-tab="my-items" <?php if($type != 'my-items') { ?>style="display:none;"<?php } ?>>
      <?php if(is_array($your_items) && count($your_items) > 0) { ?>
        <?php foreach($your_items as $i) { ?>
          <div class="mo-item">
            <?php 
              $your_offers = ModelMO::newInstance()->getOffersByItemId($i['pk_i_id'], $validate); 
              $currency_full = Currency::newInstance()->findByPrimaryKey($i['fk_c_currency_code']);
              $currency_symbol = isset($currency_full['s_description']) ? $currency_full['s_description'] : '';
              $resource = ItemResource::newInstance()->getAllResourcesFromItem($i['pk_i_id']);
              $resource = isset($resource[0]) ? $resource[0] : '';
            ?>

            <div class="mo-item-top">
              <div class="mo-item-img">
                <?php if(isset($resource['pk_i_id']) && $resource['pk_i_id'] > 0) { ?>
                  <img src="<?php echo osc_apply_filter('resource_path', osc_base_url().$resource['s_path']) . $resource['pk_i_id'] . '_thumbnail.' . $resource['s_extension'];?>"/>
                <?php } else { ?>
                  <img src="<?php echo osc_base_url(); ?>oc-content/plugins/make_offer/img/no-image.png"/>
                <?php } ?>
              </div>

              <a class="mo-item-title" href="<?php echo osc_item_url_ns($i['fk_i_item_id']); ?>"><?php echo $i['s_title']; ?></a>
              <div class="mo-item-price"><?php echo osc_format_price($i['i_price'], $currency_symbol); ?></div>
              
              <a href="#" class="mo-item-showhide" data-status="expanded"><i class="fa fa-angle-down"></i></a>
            </div>
            

            <div class="mo-two-wrap">
              <?php foreach($your_offers as $offer) { ?>
                <?php 
                  $user = User::newInstance()->findByPrimaryKey($offer['i_user_id']); 
                  
                  $offer_replied = true;
                  
                  if($offer['i_status'] == '' || $offer['i_status'] == 0) {
                    $offer_replied = false;
                  }
                ?>
                
                <div class="mo-two<?php if(!mo_has_profile_img()) { ?> noimg<?php } ?><?php if($offer['i_offer_id'] == $highlight_id) { ?> mo-blick<?php } ?>" data-offer-id="<?php echo $offer['i_offer_id']; ?>">
                  <?php if(mo_has_profile_img()) { ?>
                    <div class="mo-img">
                      <img src="<?php echo (@$user['s_profile_img'] != '' ? osc_base_url() . 'oc-content/uploads/user-images/' . $user['s_profile_img'] : osc_base_url() . 'oc-content/uploads/user-images/default-user-image.png'); ?>" alt="<?php echo osc_esc_html($user['s_name']); ?>"/>
                    </div>
                  <?php } ?>
                
                  <div class="mo-box-left">
                    <div class="mo-line-title">
                      <?php 
                        $uname = (@$user['s_name'] != '' ? $user['s_name'] : $offer['s_user_name']);
                        $uname = ($uname <> '' ? $uname : __('Anonymous', 'make_offer')); 
                        
                        if(isset($user['pk_i_id']) && $user['pk_i_id'] > 0) {
                          $uname = '<a href="' . osc_user_public_profile_url($user['pk_i_id']) . '">' . $uname . '</a>'; 
                        } else {
                          $uname = '<u>' . $uname . '</u>';
                        }
                      ?>
                      <span><?php echo sprintf(__('%s on %s', 'make_offer'), $uname, date('j. M Y', strtotime($offer['d_datetime']))); ?></span>
                    </div>
                    
                    <div class="mo-line-sub">
                      <span class="mo-prc"><?php echo osc_format_price($offer['i_price_offered'], $currency_symbol); ?><?php if($show_quantity == 1) { ?>, <?php } ?><span>
                      <?php if($show_quantity == 1) { ?><span class="mo-qt"><?php echo sprintf(__('Qty: %dx', 'make_offer'), $offer['i_quantity']); ?></span><?php } ?>
                    </div>
                  </div>
                  
                  <div class="mo-box-right">
                    <div class="mo-line-reply"><strong><?php _e('Comment', 'make_offer'); ?>:</strong> <?php echo (trim($offer['s_comment']) <> '' ? $offer['s_comment'] : '-'); ?></div>

                    <div class="mo-line-actions">
                      <?php if($offer_replied === false) { ?>
                         <form name="mo-form-reply" class="mo-form-reply no-csrf" action="<?php echo $respond_offer_link; ?>" method="POST">
                          <input type="hidden" name="statusId" value=""/>
                          <input type="hidden" name="offerId" value="<?php echo $offer['i_offer_id']; ?>"/>
                            <div class="mo-input-wrap">
                              <input name="respond" id="respond" type="text" placeholder="<?php echo osc_esc_html(__('Write your comment here', 'make_offer')); ?>" />
                              <a href="#" class="mo-respond-button mo-respond-accept" data-accept="1" title="<?php echo osc_esc_html(__('Accept', 'make_offer')); ?>"><i class="fa fa-check"></i></a>
                              <a href="#" class="mo-respond-button mo-respond-decline" data-accept="2" title="<?php echo osc_esc_html(__('Decline', 'make_offer')); ?>"><i class="fa fa-times"></i></a>
                            </div>
                          </form>
                      <?php } else { ?>
                        <div class="mo-col mo-respond mo-done">
                          <div class="mo-line-reply-text"><strong><?php _e('Your reply', 'make_offer'); ?>:</strong> <?php echo (trim($offer['s_respond']) <> '' ? $offer['s_respond'] : '-'); ?></div>

                          <div class="mo-line-status">
                            <?php if($offer['i_status']==1) { ?>
                              <div class="mo-offer-status mo-offer-status-<?php echo $offer['i_status']; ?>" title="<?php echo osc_esc_html(__('You had accepted this offer', 'make_offer')); ?>">
                                <i class="fa fa-check" ></i> <?php echo __('Accepted', 'make_offer'); ?>
                              </div>
                            <?php } else if($offer['i_status']==2) { ?>
                              <div class="mo-offer-status mo-offer-status-<?php echo $offer['i_status']; ?>" title="<?php echo osc_esc_html(__('You had declined this offer', 'make_offer')); ?>">
                                <i class="fa fa-times"></i> <?php echo __('Declined', 'make_offer'); ?>
                              </div>
                            <?php } ?>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      <?php } else { ?>
        <div class="mo-tab-empty"><?php _e('There are no offers on your items yet', 'make_offer'); ?></div>
      <?php } ?>
    </div>
    
    
    <div class="mo-tab" data-tab="my-offers" <?php if($type != 'my-offers') { ?>style="display:none;"<?php } ?>>
      <?php if(is_array($my_offers) && count($my_offers) > 0) { ?>
        <?php foreach($my_offers as $offer) { ?>
          <?php
            $item = Item::newInstance()->findByPrimaryKey($offer['fk_i_item_id']);
            $currency_full = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);
            $currency_symbol = isset($currency_full['s_description']) ? $currency_full['s_description'] : '';
            $resource = ItemResource::newInstance()->getAllResourcesFromItem($offer['fk_i_item_id']);
            $resource = isset($resource[0]) ? $resource[0] : '';
            
            $offer_replied = true;
            
            if($offer['i_status'] == '' || $offer['i_status'] == 0) {
              $offer_replied = false;
            }
          ?>
          
          <div class="mo-two mo-twi<?php if($offer['i_offer_id'] == $highlight_id) { ?> mo-blick<?php } ?>" data-offer-id="<?php echo $offer['i_offer_id']; ?>">
            <div class="mo-img">
              <?php if(isset($resource['pk_i_id']) && $resource['pk_i_id'] > 0) { ?>
                <img src="<?php echo osc_apply_filter('resource_path', osc_base_url().$resource['s_path']) . $resource['pk_i_id'] . '_thumbnail.' . $resource['s_extension'];?>"/>
              <?php } else { ?>
                <img src="<?php echo osc_base_url(); ?>oc-content/plugins/make_offer/img/no-image.png"/>
              <?php } ?>
            </div>
          
            <div class="mo-box-left">
              <a class="mo-line-link" href="<?php echo osc_item_url_ns($item['pk_i_id']); ?>"><?php echo $item['s_title']; ?></a>
              <div class="mo-line-title">
                <?php 
                  $uname = (@$item['s_contact_name'] != '' ? $item['s_contact_name'] : __('Anonymous', 'make_offer'));
                  
                  if(isset($item['fk_i_user_id']) && $item['fk_i_user_id'] > 0) {
                    $uname = '<a href="' . osc_user_public_profile_url($item['fk_i_user_id']) . '">' . $uname . '</a>'; 
                  } else {
                    $uname = '<u>' . $uname . '</u>';
                  }
                ?>
                <span><?php echo sprintf(__('%s\'s item on %s', 'make_offer'), $uname, date('j. M Y', strtotime($offer['d_datetime']))); ?></span>
              </div>
              
              <div class="mo-line-sub">
                <span class="mo-prc"><?php echo osc_format_price($offer['i_price_offered'], $currency_symbol); ?><?php if($show_quantity == 1) { ?>, <?php } ?><span>
                <?php if($show_quantity == 1) { ?><span class="mo-qt"><?php echo sprintf(__('Qty: %dx', 'make_offer'), $offer['i_quantity']); ?></span><?php } ?>
              </div>
            </div>

            
            <div class="mo-box-right">
              <div class="mo-line-reply"><strong><?php _e('Your comment', 'make_offer'); ?>:</strong> <?php echo (trim($offer['s_comment']) <> '' ? $offer['s_comment'] : '-'); ?></div>

              <div class="mo-line-actions">
                <div class="mo-col mo-respond mo-done">
                  <?php if($offer_replied !== false) { ?>
                    <div class="mo-line-reply-text"><strong><?php _e('Seller\'s reply', 'make_offer'); ?>:</strong> <?php echo (trim($offer['s_respond']) <> '' ? $offer['s_respond'] : '-'); ?></div>
                  <?php } ?>

                  <div class="mo-line-status">
                    <?php if($offer_replied === false) { ?>
                      <div class="mo-offer-status mo-offer-status-0" title="<?php echo osc_esc_html(__('Seller has not yet responded to this offer', 'make_offer')); ?>">
                        <i class="fa fa-check" ></i> <?php echo __('Pending', 'make_offer'); ?>
                      </div>
                    <?php } else if($offer['i_status']==1) { ?>
                      <div class="mo-offer-status mo-offer-status-<?php echo $offer['i_status']; ?>" title="<?php echo osc_esc_html(__('Seller has accepted this offer', 'make_offer')); ?>">
                        <i class="fa fa-check" ></i> <?php echo __('Accepted', 'make_offer'); ?>
                      </div>
                    <?php } else if($offer['i_status']==2) { ?>
                      <div class="mo-offer-status mo-offer-status-<?php echo $offer['i_status']; ?>" title="<?php echo osc_esc_html(__('Seller has declined this offer', 'make_offer')); ?>">
                        <i class="fa fa-times"></i> <?php echo __('Declined', 'make_offer'); ?>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      <?php } else { ?>
        <div class="mo-tab-empty"><?php _e('You have not placed any offers yet', 'make_offer'); ?></div>
      <?php } ?>
    </div>
  </div>
</div>


<div class="mo-placeholders" style="display:none;">
  <div class="mo-status-1">
    <div class="mo-offer-status mo-offer-status-1" title="<?php echo osc_esc_html(__('You had accepted this offer', 'make_offer')); ?>">
      <i class="fa fa-check" ></i> <?php echo __('Accepted', 'make_offer'); ?>
    </div>
  </div>
  
  <div class="mo-status-2">
    <div class="mo-offer-status mo-offer-status-2" title="<?php echo osc_esc_html(__('You had declined this offer', 'make_offer')); ?>">
      <i class="fa fa-times"></i> <?php echo __('Declined', 'make_offer'); ?>
    </div>
  </div>
</div>
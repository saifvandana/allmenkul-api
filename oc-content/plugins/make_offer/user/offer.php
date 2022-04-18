<?php
  $validate = mo_param('validate') <> '' ? mo_param('validate') : 0;
  $only_logged = mo_param('only_reg') <> '' ? mo_param('only_reg') : 0;
  $show_status = mo_param('show_status') <> '' ? mo_param('show_status') : 0;
  $show_quantity = mo_param('show_quantity') <> '' ? mo_param('show_quantity') : 0;
  $create_new = Params::getParam('createNew') == 1 ? true : false;


  $item_id = Params::getParam('itemId');
  $item = Item::newInstance()->findByPrimaryKey( $item_id );
  $counter = ModelMO::newInstance()->countOffers( $item_id, osc_logged_user_id(), $validate );
  $counter = isset($counter['i_count']) ? $counter['i_count'] : 0;

  $currency_full = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);
  $currency_symbol = isset($currency_full['s_description']) ? $currency_full['s_description'] : '$';

  $offers = ModelMO::newInstance()->getOffersByItemId($item_id, $validate);
  $count = ModelMO::newInstance()->countOffersByItemId($item_id, $validate);
  $count = (isset($count['i_count']) ? $count['i_count'] : 0);

  $new_offer_link = osc_base_url(true) . '?page=ajax&action=runhook&hook=mo_new_offer_manage';
?>


<div class="show-offer-wrap mo-fancy-dialog">
  <div id="mo-box-show" class="mo-show" data-item-id="<?php echo $item_id; ?>">
    <div id="mo-list" <?php if($create_new) { ?>style="display:none;"<?php } ?>>
      <div class="mo-head">
        <?php echo ($count == 1 ? sprintf(__('%d offer', 'make_offer'), $count) : sprintf(__('%d offers', 'make_offer'), $count)); ?>
      </div>
      
      <div class="mo-box-content mo-offers-list">
        <?php if($item_id <= 0 || $item_id == '') { ?>
          <div class="mo-empty"><i class="fa fa-list"></i> <?php _e('Listing has not been found', 'make_offer'); ?></div>
        <?php } else { ?>
          <?php if(is_array($offers) && count($offers) > 0) { ?>
            <?php
              foreach($offers as $offer) {
                if($offer['i_user_id'] > 0) {
                  $user = User::newInstance()->findByPrimaryKey($offer['i_user_id']);
                }
                
                if($offer['i_user_id'] <= 0 || !isset($user['pk_i_id'])) {
                  $user = array(
                    's_name' => ($offer['s_user_name'] <> '' ? $offer['s_user_name'] : __('Anonymous', 'make_offer')),
                    's_email' => $offer['s_user_email'],
                    's_phonel' => $offer['s_user_phone'],
                    's_profile_img' => ''
                  );
                }
                
                mo_draw_offer($user, $offer, $currency_symbol);
              }
            ?>
          <?php } else { ?>
            <div class="mo-empty"><?php _e('No offers has been submitted yet', 'make_offer'); ?></div>
          <?php } ?>
          

        <?php } ?>
      </div>
      
      <?php if($item_id > 0) { ?>
        <div class="mo-box-footer">
          <a href="#" class="mo-button-box mo-goto-new"><?php _e('Submit a new offer', 'make_offer'); ?></a>
        </div>
      <?php } ?>
    </div>


    <div id="mo-new" <?php if($create_new) { ?>style="display:block;"<?php } ?>>
      <?php if(osc_is_web_user_logged_in() && osc_logged_user_id() == $item['fk_i_user_id']) { ?>
        <div class="mo-status mo-info">
          <div class="mo-row"><i class="fa fa-exclamation-circle"></i></div>
          <div class="mo-row">
            <?php _e('This is your own listing, you cannot make offer to yourself.', 'make_offer'); ?>
          </div>
        </div>
      <?php } else if(osc_is_web_user_logged_in() && $counter > 0) { ?>
        <div class="mo-status mo-info">
          <div class="mo-row"><i class="fa fa-exclamation-circle"></i></div>
          <div class="mo-row">
            <?php _e('You have already submitted offer that was not checked by seller or was already approved by seller.', 'make_offer'); ?>
          </div>
        </div>
      <?php } else if(($only_logged == 1 && osc_is_web_user_logged_in()) || $only_logged == 0) { ?>
      
        <form name="mo-form-new" class="mo-form-new no-csrf" action="<?php echo $new_offer_link; ?>" method="POST">
          <input type="hidden" name="userId" value="<?php echo osc_logged_user_id(); ?>"/>
          <input type="hidden" name="itemId" value="<?php echo $item_id; ?>"/>

          <?php if(osc_is_web_user_logged_in()) { ?>
            <input type="hidden" id="email" name="email" value="<?php echo osc_logged_user_email(); ?>"/>
          <?php } ?>

          <div class="mo-head"><i class="fa fa-angle-left mo-back"></i><?php _e('Submit a new offer', 'make_offer'); ?></div>

          <div class="mo-box-content">
            <ul id="error_list" class="mo-error-list"></ul>

            <div class="mo-row">
              <?php if($show_quantity == 1) { ?>
                <div class="mo-row-30">
                  <label for="quantity"><?php _e('Quantity', 'make_offer'); ?></label>
                  <input type="text" id="quantity" name="quantity" value="1"/>
                </div>
              <?php } ?>

              <div class="mo-row-50">
                <label for="price"><?php _e('Price', 'make_offer'); ?></label>
                <div class="mo-input-wrap">
                  <span><span><?php echo $currency_symbol; ?></span></span>
                  <input type="text" id="price" name="price"/>
                </div>
              </div>

              <div class="mo-row-20 unit-price">
                <label>&nbsp;</label>
                <div class="mo-top"></div>
                <div class="mo-bot"><?php _e('per piece', 'make_offer'); ?></div>
              </div>
            </div>

            <div class="mo-del"><span></span></div>


            <div class="mo-row">
              <label for="name"><?php _e('Your name', 'make_offer'); ?></label>
              <div class="mo-input-wrap">
                <input type="text" id="name" name="name" value="<?php echo osc_esc_html(osc_logged_user_name()); ?>"/>
              </div>
            </div>

            <?php if(!osc_is_web_user_logged_in()) { ?>
              <div class="mo-row">
                <label for="email"><?php _e('Email', 'make_offer'); ?></label>
                <div class="mo-input-wrap">
                  <input type="text" id="email" name="email"/>
                </div>
              </div>
            <?php } ?>

            <div class="mo-row">
              <label for="phone"><?php _e('Phone', 'make_offer'); ?></label>
              <div class="mo-input-wrap">
                <input type="text" id="phone" name="phone" value="<?php echo osc_esc_html(osc_logged_user_phone()); ?>"/>
              </div>
            </div>

            <div class="mo-row">
              <label for="comment"><?php _e('Comment to your offer', 'make_offer'); ?></label>
              <textarea id="comment" name="comment"></textarea>
            </div>

            <div class="mo-row">
              <button type="submit" class="mo-submit mo-button-box"><?php _e('Submit', 'make_offer'); ?></button>
            </div>
          </div>
        </form>

        <div class="mo-status mo-success">
          <div class="mo-row"><i class="fa fa-check-circle"></i></div>
          <div class="mo-row">
            <?php _e('Your offer has been successfully submitted!', 'make_offer'); ?>
            
            <?php if($validate == 1) { ?>
              <br/><?php _e('Offer will be sent to seller once it is validated by our team.', 'make_offer'); ?>
            <?php } ?>
          </div>
        </div>

        <div class="mo-status mo-error">
          <div class="mo-row"><i class="fa fa-times-circle"></i></div>
          <div class="mo-row">
            <?php _e('Whooops, there was some error, you offer has not been submitted.', 'make_offer'); ?>
          </div>
        </div>

      <?php } else { ?>
        <div class="mo-status mo-info">
          <div class="mo-row"><i class="fa fa-exclamation-circle"></i></div>
          <div class="mo-row">
            <?php _e('Only logged in users can submit a new offer.', 'make_offer'); ?>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
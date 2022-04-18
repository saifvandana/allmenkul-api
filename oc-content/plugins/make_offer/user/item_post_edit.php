<?php
if(isset($item_id) && $item_id > 0) {
  $setting = ModelMO::newInstance()->getOfferSettingByItemId($item_id); 
  $enabled = isset($setting['i_enabled']) ? $setting['i_enabled'] : 0;
} else {
  $enabled = 0;
}

$styled = (mo_param('check_styled') == 0 ? '' : ' styled');


if(Params::existParam('mo_item_setting')) {
  $enabled = (Params::getParam('mo_item_setting') <> '' ? Params::getParam('mo_item_setting') : 0);
}
?>


<div class="control-group<?php echo $styled; ?>" id="mo-check">
  <div class="controls checkbox">
    <div class="input-box-check">
      <input id="mo_item_setting" type="checkbox" name="mo_item_setting" value="1" <?php echo ($enabled == 1 ? 'checked="checked"' : ''); ?>>
      <label class="control-label" for="mo_item_setting"><?php _e('Enable buyers to "make offer" on this item', 'make_offer');?></label>
    </div>
  </div>
</div>
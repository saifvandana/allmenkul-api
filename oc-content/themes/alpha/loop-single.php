<?php $item_extra = alp_item_extra(osc_item_id()); ?>

<div class="simple-prod o<?php echo $c; ?><?php if(osc_item_is_premium()) { ?> is-premium<?php } ?><?php if($class <> '') { echo ' ' . $class; } ?><?php if($item_extra['i_sold'] == 1) { echo ' st-sold'; } else if($item_extra['i_sold'] == 2) { echo ' st-reserved'; } ?> <?php osc_run_hook("highlight_class"); ?>">
  <div class="simple-wrap">
    <?php if($item_extra['i_sold'] == 1) { ?>
      <a class="label lab-sold" href="<?php echo osc_item_url(); ?>">
        <span><?php _e('sold', 'alpha'); ?></span>
      </a>
    <?php } else if($item_extra['i_sold'] == 2) { ?>
      <a class="label lab-res" href="<?php echo osc_item_url(); ?>">
        <span><?php _e('reserved', 'alpha'); ?></span>
      </a>
    <?php } else if(osc_item_is_premium()) { ?>
      <a class="label lab-prem alpBg" href="<?php echo osc_item_url(); ?>">
        <span><?php _e('premium', 'alpha'); ?></span>
      </a>
    <?php } ?>       

    <div class="img-wrap<?php if(osc_count_item_resources() == 0) { ?> no-image<?php } ?>">
      <?php if(osc_count_item_resources() > 0) { ?>
        <a class="img" href="<?php echo osc_item_url(); ?>"><img class="<?php echo (alp_is_lazy() ? 'lazy' : ''); ?>" src="<?php echo (alp_is_lazy() ? alp_get_noimage() : osc_resource_thumbnail_url()); ?>" data-src="<?php echo osc_resource_thumbnail_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?>" /></a>
      <?php } else { ?>
        <a class="img" href="<?php echo osc_item_url(); ?>"><img class="<?php echo (alp_is_lazy() ? 'lazy' : ''); ?>" src="<?php echo alp_get_noimage(); ?>" data-src="<?php echo alp_get_noimage(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?>" /></a>
      <?php } ?>

      <div class="publish isGrid"><?php echo alp_smart_date(osc_item_pub_date()); ?></div>

      <?php if(function_exists('fi_save_favorite')) { echo fi_save_favorite(); } ?>

      <?php if(alp_param('preview') == 1) { ?>
        <a class="preview" href="<?php echo alp_fancy_url('itemviewer'); ?>"><i class="fa fa-search"></i><span><?php _e('Preview', 'alpha'); ?></span></a>
      <?php } ?>
    </div>

    <div class="data">
      <?php if(osc_price_enabled_at_items()) { ?>
        <div class="price isGrid"><span><?php echo osc_item_formated_price(); ?></span></div>
      <?php } ?>
         
      <a class="title" href="<?php echo osc_item_url(); ?>"><?php echo osc_highlight(osc_item_title(), 100); ?></a>

      <div class="description isList"><?php echo osc_highlight(strip_tags(osc_item_description()), 320); ?></div>

      <div class="extra isList">
        <span><?php echo alp_item_location(); ?></span><span class="slash">/</span> 
        <span><?php echo alp_smart_date(osc_item_pub_date()); ?></span><span class="slash">/</span> 
        <span><?php echo osc_item_views(); ?> <?php echo (osc_item_views() == 1 ? __('hit', 'alpha') : __('hits', 'alpha')); ?></span>
      </div>

      <div class="location isGrid"><?php echo alp_item_location(); ?></div>

      <?php if(osc_price_enabled_at_items()) { ?>
        <div class="price isList alpCl"><span><?php echo osc_item_formated_price(); ?></span></div>
      <?php } ?>

      <?php if(osc_item_user_id() > 0) { ?>
        <a class="user isList" href="<?php echo osc_user_public_profile_url(osc_item_user_id()); ?>"><?php echo osc_item_contact_name(); ?></a>
      <?php } ?>
    </div>

  </div>
</div>
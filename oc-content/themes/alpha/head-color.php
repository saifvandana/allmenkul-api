<?php
  $color = '.alpCl,body #user-menu li.active a, body #user-menu a:hover, body a, body a:hover';
  $background = '.alpBg,.blg-btn.blg-btn-primary,.bpr-prof .bpr-btn, #img .bx-wrapper .bx-prev:hover:before, #fi_user_new_list button, #img .bx-wrapper .bx-next:hover:before, .post-edit .price-wrap .selection a.active,.tabbernav li.tabberactive a';
  $background_after = '.alpBgAf:after';
  $background_active = '.alpBgActive.active';
  $background_color = 'body .fancybox-close';
  $border_color = '.alpBr';
  $border_background = '.input-box-check input[type="checkbox"]:checked + label:before,#atr-search .atr-input-box input[type="checkbox"]:checked + label:before, #atr-search .atr-input-box input[type="radio"]:checked + label:before,#atr-form .atr-input-box input[type="checkbox"]:checked + label:before, #atr-form .atr-input-box input[type="radio"]:checked + label:before,.bpr-box-check input[type="checkbox"]:checked + label:before, #gdpr-check.styled .input-box-check input[type="checkbox"]:checked + label:before, .pol-input-box input[type="checkbox"]:checked + label:before, .pol-values:not(.pol-nm-star) .pol-input-box input[type="radio"]:checked + label:before';
  $border_bottom = '#search-sort .user-type a.active, #search-sort .user-type a:hover';
?>

<style>
  <?php echo $color; ?> {color:<?php echo alp_param('color'); ?>;}
  <?php echo $background; ?> {background:<?php echo alp_param('color'); ?>!important;color:#fff!important;}
  <?php echo $background_after; ?> {background:<?php echo alp_param('color'); ?>!important;}
  <?php echo $background_active; ?> {background:<?php echo alp_param('color'); ?>!important;}
  <?php echo $background_color; ?> {background-color:<?php echo alp_param('color'); ?>!important;}
  <?php echo $border_background; ?> {border-color:<?php echo alp_param('color'); ?>!important;background-color:<?php echo alp_param('color'); ?>!important;}
  <?php echo $border_bottom; ?> {border-bottom-color:<?php echo alp_param('color'); ?>!important;}
</style>

<script>
  var alpCl = '<?php echo $color; ?>';
  var alpBg = '<?php echo $background; ?>';
  var alpBgAf= '<?php echo $background_after; ?>';
  var alpBgAc= '<?php echo $background_active; ?>';
  var alpBr= '<?php echo $border_color; ?>';
  var alpBrBg= '<?php echo $border_background; ?>';
  var alpBrBt= '<?php echo $border_bottom; ?>';
</script>

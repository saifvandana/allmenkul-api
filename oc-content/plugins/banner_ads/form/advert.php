<?php
  $key = Params::getParam('key');
  $cur = osc_get_preference('currency', 'plugin-banner_ads');

  if($key <> '') { 
    $adverts = ModelBA::newInstance()->getAdvertsByKey($key);
  }
?>

<style>
.ba-client {display:inline-block;width:100%;text-align:center;}
.ba-client, .ba-client * {box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;}
.ba-client-advert {display:inline-block;width: 520px; max-width: 98%; margin:0 1% 20px 1%;vertical-align:text-top;}
.ba-head {font-size:16px;line-height:18px;padding:15px;width:100%;display:inline-block;clear:both;background:#f0f0f0;color:#333;border:1px solid #ddd;}
.ba-body {display:inline-block;width:100%;clear:both;font-size:14px;line-height:18px;border:1px solid #ddd;border-top:none;padding:15px;}
.ba-body .ba-row {display:inline-block;width:100%;padding:0;margin:0 0 10px 0;}
.ba-body .ba-row.ba-showcase {margin-top: 25px; border-bottom: 1px dashed #ddd; margin-bottom: 8px; font-style: italic; color: #888; padding-bottom: 5px;}
.ba-body .ba-left {display:inline-block;width:45%;text-align:right;padding-right:10px;font-weight:500;color:#888;vertical-align:top;}
.ba-body .ba-right {display:inline-block;width:45%;text-align:left;padding-left:10px;font-weight:bold;color:#111;}
.ba-body .ba-advert {margin-top:0;margin-bottom:0;}
.ba-body .ba-row i {font-size: 17px; vertical-align: -2px; margin-right: 3px; float: left; display: block;}
.ba-body .ba-row.ba-showcase-wrap {display:inline-block;width:100%;padding:20px;background:#e0e0e0;margin:0;border:1px solid #c0c0c0;}
.mb-color-green {color:#5cb85c;}
.mb-color-red {color:#d9534f;}
</style>

<div class="ba-client">
  <?php foreach($adverts as $a) { ?>
    <div class="ba-client-advert">
      <div class="ba-head">
        <strong><?php echo $a['s_name']; ?> #<?php echo $a['pk_i_id']; ?></strong>
      </div>
      <div class="ba-body">
        <div class="ba-row">
          <div class="ba-left"><?php _e('Type', 'banner_ads'); ?>:</div>
          <div class="ba-right">
            <?php 
              if($a['i_type'] == 1) {
                _e('HTML Advert', 'banner_ads');
              } else if($a['i_type'] == 2) { 
                _e('Image Advert', 'banner_ads');
              } else if($a['i_type'] == 3) {
                _e('Adsense Advert', 'banner_ads');
              }
            ?>
          </div>
        </div>

        <?php if($a['d_price_view'] <> '') { ?>
          <div class="ba-row"><div class="ba-left"><?php _e('Price for view', 'banner_ads'); ?>:</div><div class="ba-right"><?php echo $a['d_price_view']; ?><?php echo $cur; ?></div></div>
        <?php } ?>

        <?php if($a['d_price_click'] <> '') { ?>
          <div class="ba-row"><div class="ba-left"><?php _e('Price for click', 'banner_ads'); ?>:</div><div class="ba-right"><?php echo $a['d_price_click']; ?><?php echo $cur; ?></div></div>
        <?php } ?>

        <?php if($a['i_views'] <> '') { ?>
          <div class="ba-row"><div class="ba-left"><?php _e('Views count', 'banner_ads'); ?>:</div><div class="ba-right"><?php echo $a['i_views']; ?>x</div></div>
        <?php } ?>

        <?php if($a['i_clicks'] <> '') { ?>
          <div class="ba-row"><div class="ba-left"><?php _e('Clicks count', 'banner_ads'); ?>:</div><div class="ba-right"><?php echo $a['i_clicks']; ?>x</div></div>
        <?php } ?>


        <?php if($a['d_budget'] > 0 && ($a['d_price_view'] <> '' || $a['d_price_click'])) { ?>
          <div class="ba-row"><div class="ba-left"><?php _e('Spent', 'banner_ads'); ?>:</div><div class="ba-right"><?php echo $a['d_price_view']*$a['i_views'] + $a['d_price_click']*$a['i_clicks']; ?></div></div>
        <?php } ?>

        <?php if($a['d_budget'] > 0 && ($a['d_price_view'] <> '' || $a['d_price_click'])) { ?>
          <div class="ba-row"><div class="ba-left"><?php _e('Spent %', 'banner_ads'); ?>:</div><div class="ba-right"><?php echo round(($a['d_price_view']*$a['i_views'] + $a['d_price_click']*$a['i_clicks'])/$a['d_budget']*100); ?>%</div></div>
        <?php } ?>

        <?php if($a['d_budget'] > 0) { ?>
          <div class="ba-row"><div class="ba-left"><?php _e('Budget', 'banner_ads'); ?>:</div><div class="ba-right"><?php echo $a['d_budget']; ?><?php echo $cur; ?></div></div>
        <?php } ?>

        <?php if($a['dt_expire'] <> '' && $a['dt_expire'] <> '0000-00-00') { ?>
          <div class="ba-row"><div class="ba-left"><?php _e('Expire on', 'banner_ads'); ?>:</div><div class="ba-right"><?php echo $a['dt_expire']; ?></div></div>
        <?php } ?>


        <?php
          $status = 1;
          $status_text = __('Active', 'banner_ads');

          if( $a['d_budget'] > 0 && $a['i_views']*$a['d_price_view'] + $a['i_clicks']*$a['d_price_click'] >= $a['d_budget'] ) {
            $status = 0;
            $status_text = __('Budget exceed - spent', 'banner_ads') . ' ' . ($a['d_budget'] > 0 && $a['i_views']*$a['d_price_view'] + $a['i_clicks']*$a['d_price_click']) . $cur . ' ' . __('of', 'banner_ads') . ' ' . $a['d_budget'] . $cur;
          } else if ($a['dt_expire'] <> '0000-00-00' && date('Y-m-d', strtotime($a['dt_expire'])) < date('Y-m-d')) {
            $status = 0;
            $status_text = __('Advert expired on', 'banner_ads') . ' ' . $a['dt_expire'];
          }
        ?>

        <div class="ba-row">
          <div class="ba-left"><?php _e('Status', 'banner_ads'); ?>:</div>
          <div class="ba-right">
            <?php if($status == 1) { ?>
              <i class="fa fa-check-circle mb-color-green" title="<?php echo osc_esc_html($status_text); ?>"></i> <?php echo $status_text; ?>
            <?php } else { ?>
              <i class="fa fa-times-circle mb-color-red" title="<?php echo osc_esc_html($status_text); ?>"></i> <?php echo $status_text; ?>
            <?php } ?>
          </div>
        </div>



        <div class="ba-row ba-showcase"><?php _e('Advert showcase', 'banner_ads'); ?></div>
        <div class="ba-row ba-showcase-wrap"><?php echo ba_show_advert($a['pk_i_id']); ?></div>
      </div>
    </div>
  <?php } ?>
</div>
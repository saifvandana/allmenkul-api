<div class="search-items-wrap">
  <div class="block">
    <div class="wrap">

      <?php 
        // PREMIUM ITEMS
        osc_get_premiums(alp_param('premium_search_count'));
        $c = 1;

        if(osc_count_premiums() > 0 && alp_param('premium_search') == 1) {
          while(osc_has_premiums()) {
            alp_draw_item($c, true, 'premium-loop');
            $c++;
          }
        }
      ?>


      <?php $c = 1; ?>
      <?php while( osc_has_items() ) { ?>
        <?php alp_draw_item($c); ?>
        <?php $c++; ?>
      <?php } ?>

    </div>
  </div>
 
  <?php View::newInstance()->_erase('items') ; ?>
</div>
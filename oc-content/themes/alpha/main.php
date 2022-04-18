<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()) ; ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
</head>

<body id="body-home" class="layout-<?php echo alp_param('home_layout'); ?>">
  <?php osc_current_web_theme_path('header.php') ; ?>
  <?php echo alp_banner('home_top'); ?>


  <?php if(function_exists('osc_slider')) { ?>

    <!-- Slider Block -->
    <div class="home-containers hc-slider">
      <div class="inner">
        <div id="home-slider">
          <?php osc_slider(); ?>
        </div>
      </div>
    </div>
  <?php } ?>








      <?php if(function_exists('fi_most_favorited_items') && alp_param('favorite_home') == 1) { ?>

        <!-- MOST FAVORITED -->

        <?php
          $limit = (osc_get_preference('maxLatestItems@home', 'osclass') > 0 ? osc_get_preference('maxLatestItems@home', 'osclass') : 24);


          // SEARCH ITEMS IN LIST AND CREATE ITEM ARRAY
          $aSearch = new Search();
          $aSearch->addField(sprintf('count(%st_item.pk_i_id) as count_id', DB_TABLE_PREFIX) );
          $aSearch->addConditions(sprintf("%st_favorite_list.list_id = %st_favorite_items.list_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
          $aSearch->addConditions(sprintf("%st_favorite_items.item_id = %st_item.pk_i_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
          $aSearch->addConditions(sprintf("%st_favorite_list.user_id <> coalesce(%st_item.fk_i_user_id, 0)", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
          $aSearch->addTable(sprintf("%st_favorite_items", DB_TABLE_PREFIX));
          $aSearch->addTable(sprintf("%st_favorite_list", DB_TABLE_PREFIX));
          $aSearch->addGroupBy(DB_TABLE_PREFIX.'t_item.pk_i_id');

          $aSearch->order('count(*)', 'DESC');

          $aSearch->limit(0, $limit);
          $list_items = $aSearch->doSearch();


          // EXPORT FAVORITE ITEMS TO VARIABLE
          GLOBAL $fi_global_items2;
          $fi_global_items2 = View::newInstance()->_get('items'); 
          View::newInstance()->_exportVariableToView('items', $list_items);
        ?>

        <div id="favorite" class="products grid single-tab" data-tab="favorite" style="display:none;">
          <h2><?php _e('Most favorited listings by users', 'alpha'); ?></h2>

          <div class="block">
            <div class="prod-wrap">
              <?php $c = 1; ?>
              <?php while( osc_has_items() ) { ?>
                <?php alp_draw_item($c); ?>
                 
                <?php $c++; ?>
              <?php } ?>

              <?php if(osc_count_items() <= 0) { ?>
                <div class="home-empty">
                  <img src="<?php echo osc_current_web_theme_url('images/home-empty.png'); ?>" />
                  <strong><?php _e('No listing favorited yet', 'alpha'); ?></strong>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>

        <?php
          GLOBAL $fi_global_items2; 
          View::newInstance()->_exportVariableToView('items', $fi_global_items2);  
        ?>
      <?php } ?>



      <?php if(function_exists('blg_param') && alp_param('blog_home') == 1) { ?>

        <!-- BLOG WIDGET -->
        <div id="blog" class="products grid single-tab" data-tab="blog" style="display:none;">
          <a class="h2" href="<?php echo blg_home_link(); ?>"><?php _e('Latest articles on our blog', 'alpha'); ?></a>

          <?php osc_run_hook('blg_widget'); ?>
        </div>
      <?php } ?>


      <?php if(function_exists('bpr_companies_block') && alp_param('company_home') == 1) { ?>

        <!-- BUSINESS PROFILE WIDGET -->
        <div id="company" class="products grid single-tab" data-tab="company" style="display:none;">
          <a class="h2" href="<?php echo bpr_companies_url(); ?>"><?php _e('Our partner companies', 'alpha'); ?></a>

          <?php echo bpr_companies_block(8, 'NEW'); ?>
        </div>
      <?php } ?>




    </div>
  </div>

  <?php echo alp_banner('home_bottom'); ?>


  <?php osc_current_web_theme_path('footer_main.php') ; ?>

</body>
</html>	
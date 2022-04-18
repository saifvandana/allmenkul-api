<?php $search_params = alp_search_params_all(); ?>

<?php
  // CURRENT CATEGORY
  $search_cat_id = osc_search_category_id();
  $search_cat_id = isset($search_cat_id[0]) ? $search_cat_id[0] : 0;
  $search_cat_full = Category::newInstance()->findByPrimaryKey($search_cat_id);

  // ROOT CATEGORY
  $root_cat_id = Category::newInstance()->findRootCategory($search_cat_id);
  $root_cat_id = (isset($root_cat_id['pk_i_id']) ? $root_cat_id['pk_i_id'] : null);

  // HIERARCHY OF SEARCH CATEGORY
  $hierarchy = Category::newInstance()->toRootTree($search_cat_id);

  // SUBCATEGORIES OF SEARCH CATEGORY
  $subcats = Category::newInstance()->findSubcategoriesEnabled($search_cat_id);

  if(empty($subcats)) {
    $is_subcat = false;
    $subcats = Category::newInstance()->findSubcategoriesEnabled(isset($search_cat_full['fk_i_parent_id']) ? $search_cat_full['fk_i_parent_id'] : null);
  } else {
    $is_subcat = true;
  }
?>


<?php if(osc_is_home_page() || (osc_is_search_page() && $search_cat_id <= 0)) { ?>

  <!-- ROOT CATEGORIES -->
  <div id="home-cat">
    <div class="inside">
      <div class="box">

        <?php while(osc_has_categories()) { ?>
          <?php
            $search_params['sCategory'] = osc_category_id();
            $color = alp_get_cat_color(osc_category_id());
          ?>

          <a href="<?php echo osc_search_url($search_params); ?>"><div class="img<?php  if($color == '') { ?> no-color<?php } ?>">
              <span <?php if($color <> '') { ?>style="background:<?php echo $color; ?>;"<?php } ?>></span>

              <?php if(alp_param('cat_icons') == 1) { ?>
                <i class="fa <?php echo alp_get_cat_icon( osc_category_id(), true ); ?>" <?php if($color <> '') { ?>style="color:<?php echo $color; ?>;"<?php } ?>></i>
              <?php } else { ?>
                <img src="<?php echo osc_current_web_theme_url();?>images/small_cat/<?php echo osc_category_id();?>.png" />
              <?php } ?>
            </div>

            <div class="name"><?php echo osc_category_name(); ?></div>
          </a>
        <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>


<?php if(osc_is_search_page() && $search_cat_id > 0) { ?>

  <!-- SUBCATEGORIES -->
  <?php if(count($subcats) > 0) { ?>
    <div id="sub-cat">
      <div class="wrap">
        <div class="navi alpBgAf">
          <?php unset($search_params['sCategory']); ?>
          <a href="<?php echo osc_search_url($search_params); ?>"><?php _e('All categories', 'alpha'); ?></a>
          <i class="fa fa-angle-right"></i>

          <?php foreach($hierarchy as $h) { ?>
            <?php $search_params['sCategory'] = $h['pk_i_id']; ?>

            <?php if($h['pk_i_id'] <> $search_cat_id) { ?>
              <a href="<?php echo osc_search_url($search_params); ?>"">
                <span class="name"><?php echo $h['s_name']; ?></span>
              </a>

              <i class="fa fa-angle-right"></i>

            <?php } else { ?>
              <span><?php echo $h['s_name']; ?></span>

            <?php } ?>
          <?php } ?>
        </div>

        <div class="list">
          <?php foreach($subcats as $c) { ?>
            <?php $search_params['sCategory'] = $c['pk_i_id']; ?>

            <a href="<?php echo osc_search_url($search_params); ?>" class="<?php if($c['pk_i_id'] == $search_cat_id) { ?> active<?php } ?>">
              <span class="name"><?php echo $c['s_name']; ?></span>
            </a>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php } ?>

<?php } ?>
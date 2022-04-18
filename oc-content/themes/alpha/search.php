<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <?php if( osc_count_items() == 0 || Params::getParam('iPage') > 0 || stripos($_SERVER['REQUEST_URI'], 'search') )  { ?>
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, nofollow" />
  <?php } else { ?>
    <meta name="robots" content="index, follow" />
    <meta name="googlebot" content="index, follow" />
  <?php } ?>
</head>

<body id="body-search">
<?php osc_current_web_theme_path('header.php') ; ?>

<?php 
  $params_spec = alp_search_params();
  $params_all = alp_search_params_all();

  $search_cat_id = osc_search_category_id();
  $search_cat_id = isset($search_cat_id[0]) ? $search_cat_id[0] : '';

  $def_view = alp_param('def_view') == 0 ? 'grid' : 'list';
  $show = Params::getParam('sShowAs') == '' ? $def_view : Params::getParam('sShowAs');
  $show = ($show == 'gallery' ? 'grid' : $show);

  $def_cur = (alp_param('def_cur') <> '' ? alp_param('def_cur') : '$');

  $search_params_remove = alp_search_param_remove();

  $exclude_tr_con = explode(',', alp_param('post_extra_exclude'));

  // Get search hooks
  GLOBAL $search_hooks;
  ob_start(); 

  if(osc_search_category_id()) { 
    osc_run_hook('search_form', osc_search_category_id());
  } else { 
    osc_run_hook('search_form');
  }

  $search_hooks = trim(ob_get_contents());
  ob_end_clean();
?>


<div class="content">
  <div class="inside search">

    <div id="filter">
      <div class="wrap">
        <form action="<?php echo osc_base_url(true); ?>" method="get" class="search-side-form nocsrf" id="search-form">
          <input type="hidden" name="page" value="search" />
          <input type="hidden" name="ajaxRun" value="" />
          <input type="hidden" name="sOrder" value="<?php echo osc_search_order(); ?>" />
          <input type="hidden" name="iOrderType" value="<?php $allowedTypesForSorting = Search::getAllowedTypesForSorting(); echo isset($allowedTypesForSorting[osc_search_order_type()]) ? $allowedTypesForSorting[osc_search_order_type()] : ''; ?>" />
          <input type="hidden" name="sCompany" class="sCompany" id="sCompany" value="<?php echo Params::getParam('sCompany');?>" />
          <input type="hidden" name="sCountry" id="sCountry" value="<?php echo Params::getParam('sCountry'); ?>"/>
          <input type="hidden" name="sRegion" id="sRegion" value="<?php echo Params::getParam('sRegion'); ?>"/>
          <input type="hidden" name="sCity" id="sCity" value="<?php echo Params::getParam('sCity'); ?>"/>
          <input type="hidden" name="iPage" id="iPage" value=""/>
          <input type="hidden" name="sShowAs" id="sShowAs" value="<?php echo Params::getParam('sShowAs'); ?>"/>
          <input type="hidden" name="showMore" id="showMore" value="<?php echo Params::getParam('showMore'); ?>"/>

          <div class="block">
            <div class="search-wrap">
              <h2><?php _e('Filter results', 'alpha'); ?></h2>

              <fieldset class="box location">
                <div class="row">
                  <div class="input-box">
                    <input type="text" name="sPattern" placeholder="<?php _e('What are you looking for?', 'alpha'); ?>" value="<?php echo Params::getParam('sPattern'); ?>" autocomplete="off"/>
                  </div>
                </div>

                <div class="row">
                  <div class="box">
                    <div id="location-picker" class="loc-picker ctr-<?php echo (alp_count_countries() == 1 ? 'one' : 'more'); ?>">
                      <input type="text" name="term" id="term" class="term" placeholder="<?php _e('Location', 'alpha'); ?>" value="<?php echo alp_get_term(Params::getParam('term'), Params::getParam('sCountry'), Params::getParam('sRegion'), Params::getParam('sCity')); ?>" autocomplete="off"/>
                      <i class="fa fa-angle-down"></i>

                      <div class="shower-wrap">
                        <div class="shower" id="shower">
                          <?php echo alp_def_location(); ?>
                        </div>
                      </div>

                      <div class="loader"></div>
                    </div>

                  </div>
                </div>

                <div class="row">
                  <div class="input-box">
                    <?php echo alp_simple_category(); ?>
                  </div>
                </div>

                <?php if(@!in_array($search_cat_id, $exclude_tr_con)) { ?>
                  <div class="row">
                    <div class="input-box">
                      <?php echo alp_simple_transaction(); ?>
                    </div>
                  </div>

                  <div class="row">
                    <div class="input-box">
                      <?php echo alp_simple_condition(); ?>
                    </div>
                  </div>
                <?php } ?>

                <div class="row">
                  <div class="input-box">
                    <?php echo alp_simple_period(); ?>
                  </div>
                </div>
              </fieldset>


              <?php if( alp_check_category_price($search_cat_id) ) { ?>
                <fieldset class="price-box">
                  <div class="row price">
                    <div class="input-box">
                      <input type="number" class="priceMin" name="sPriceMin" value="<?php echo osc_esc_html(Params::getParam('sPriceMin')); ?>" size="6" maxlength="6" placeholder="<?php echo osc_esc_js(__('Min', 'alpha')); ?>"/>
                      <span>TL<?php // echo $def_cur; ?></span>
                    </div>

                    <div class="input-box">
                      <input type="number" class="priceMax" name="sPriceMax" value="<?php echo osc_esc_html(Params::getParam('sPriceMax')); ?>" size="6" maxlength="6" placeholder="<?php echo osc_esc_js(__('Max', 'alpha')); ?>"/>
                      <span>TL<?php // echo $def_cur; ?></span>
                    </div>
                  </div>
                </fieldset>
              <?php } ?>


              <fieldset class="img-check">
                <?php if( osc_images_enabled_at_items() ) { ?>
                  <div class="row checkboxes">
                    <div class="input-box-check">
                      <input type="checkbox" name="bPic" id="bPic" value="1" <?php echo (osc_search_has_pic() ? 'checked="checked"' : ''); ?> />
                      <label for="bPic" class="with-pic-label"><?php _e('Only items with picture', 'alpha'); ?></label>
                    </div>
                  </div>
                <?php } ?>
              </fieldset>


              <?php if($search_hooks <> '') { ?>
                <a href="#" class="show-hooks<?php if(Params::getParam('showMore') == 1) { ?> opened<?php } ?>" data-opened="<?php echo osc_esc_html(__('Less filters', 'alpha')); ?>" data-closed="<?php echo osc_esc_html(__('More filters', 'alpha')); ?>"><i class="fa fa-<?php echo (Params::getParam('showMore') == 1 ? 'minus' : 'plus'); ?>"></i><span><?php echo (Params::getParam('showMore') == 1 ? __('Less filters', 'alpha') : __('More filters', 'alpha')); ?></span></a>

                <div class="sidebar-hooks" <?php if(Params::getParam('showMore') <> 1) { ?>style="display:none;"<?php } ?>>
                  <?php echo $search_hooks; ?>
                </div>
              <?php } ?>
            </div>
          </div>

          <div class="button-wrap">
            <button type="submit" class="btn alpBg init-search" id="search-button"><?php _e('Search', 'alpha') ; ?></button>
            <a href="#" class="ff-close isMobile"><?php _e('Close', 'alpha'); ?></a>
          </div>
        </form>
      </div>

      <?php osc_alert_form(); ?>

      <?php echo alp_banner('search_sidebar'); ?>
    </div>


    <div id="main">

      <?php
        $p1 = $params_all; $p1['sCompany'] = null;
        $p2 = $params_all; $p2['sCompany'] = 0;
        $p3 = $params_all; $p3['sCompany'] = 1;

        $us_type = Params::getParam('sCompany');
        
      ?>

      <?php osc_current_web_theme_path('inc.category2.php'); ?>


      <!-- REMOVE FILTER SECTION -->
      <?php  
        // count usable params
        $filter_check = 0;
        if(count($search_params_remove) > 0) {
          foreach($search_params_remove as $n => $v) { 
            if($v['name'] <> '' && $v['title'] <> '') { 
              $filter_check++;
            }
          }
        }
      ?>

      <?php if($filter_check > 0) { ?>
        <div class="filter-remove">
          <?php foreach($search_params_remove as $n => $v) { ?>
            <?php if($v['name'] <> '' && $v['title'] <> '') { ?>
              <?php
                $rem_param = $params_all;
                unset($rem_param[$n]);
              ?>

              <a href="<?php echo osc_search_url($rem_param); ?>" data-param="<?php echo $v['param']; ?>"><?php echo $v['title'] . ': ' . $v['name']; ?></a>
            <?php } ?>
          <?php } ?>

          <a class="bold" href="<?php echo osc_search_url(array('page' => 'search')); ?>"><?php _e('Remove all', 'alpha'); ?></a>
        </div>
      <?php } ?>

      <!-- SEARCH FILTERS - SORT / COMPANY / VIEW -->
      <div id="search-sort" class="">
        <div class="user-type">
          <a class="all<?php if(Params::getParam('sCompany') === '' || Params::getParam('sCompany') === null) { ?> active<?php } ?>" href="<?php echo osc_search_url($p1); ?>"><?php _e('All', 'alpha'); ?></a>
          <a class="personal<?php if(Params::getParam('sCompany') === '0') { ?> active<?php } ?>" href="<?php echo osc_search_url($p2); ?>"><?php _e('Personal', 'alpha'); ?></a>
          <a class="company<?php if(Params::getParam('sCompany') === '1') { ?> active<?php } ?>" href="<?php echo osc_search_url($p3); ?>"><?php _e('Company', 'alpha'); ?></a>
        </div>

        <?php if(osc_count_items() > 0) { ?>
          <div class="sort-it">
            <div class="sort-title">
              <div class="title-keep noselect">
                <?php $orders = osc_list_orders(); ?>
                <?php $current_order = osc_search_order(); ?>
                <?php foreach($orders as $label => $params) { ?>
                  <?php $orderType = ($params['iOrderType'] == 'asc') ? '0' : '1'; ?>
                  <?php if(osc_search_order() == $params['sOrder'] && osc_search_order_type() == $orderType) { ?>
                    <span>
                      <span class=""><?php echo $label; ?></span>
                    </span>
                  <?php } ?>
                <?php } ?>
              </div>

              <div id="sort-wrap">
                <div class="sort-content">
                  <?php $i = 0; ?>
                  <?php foreach($orders as $label => $params) { ?>
                    <?php $orderType = ($params['iOrderType'] == 'asc') ? '0' : '1'; ?>
                    <?php if(osc_search_order() == $params['sOrder'] && osc_search_order_type() == $orderType) { ?>
                      <a class="current" href="<?php echo osc_update_search_url($params) ; ?>"><span><?php echo $label; ?></span></a>
                    <?php } else { ?>
                      <a href="<?php echo osc_update_search_url($params) ; ?>"><span><?php echo $label; ?></span></a>
                    <?php } ?>
                    <?php $i++; ?>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>

          <div class="list-grid">
            <?php $show = Params::getParam('sShowAs') == '' ? $def_view : Params::getParam('sShowAs'); ?>
            <a href="<?php echo osc_update_search_url(array('sShowAs' => 'list')); ?>" title="<?php echo osc_esc_html(__('List view', 'alpha')); ?>" class="lg<?php echo ($show == 'list' ? ' active' : ''); ?>" data-view="list"><i class="fa fa-list-ul"></i></a>
            <a href="<?php echo osc_update_search_url(array('sShowAs' => 'grid')); ?>" title="<?php echo osc_esc_html(__('Grid view', 'alpha')); ?>" class="lg<?php echo ($show == 'grid' ? ' active' : ''); ?>" data-view="grid"><i class="fa fa-th-large"></i></a>
          </div>
        <?php } ?>
      </div>

      <!--
      <div class="filter-button isMobile">
        <i class="fa fa-sliders"></i>
        <span><?php _e('Refine results', 'alpha'); ?></span>
      </div>
      -->

      <div id="search-items">       
             
        <?php if(osc_count_items() == 0) { ?>
          <div class="list-empty round3" >
            <span class="titles"><?php _e('We could not find any results for your search...', 'alpha'); ?></span>

            <div class="tips">
              <div class="row"><?php _e('Following tips might help you to get better results', 'alpha'); ?></div>
              <div class="row"><i class="fa fa-circle"></i><?php _e('Use more general keywords', 'alpha'); ?></div>
              <div class="row"><i class="fa fa-circle"></i><?php _e('Check spelling of position', 'alpha'); ?></div>
              <div class="row"><i class="fa fa-circle"></i><?php _e('Reduce filters, use less of them', 'alpha'); ?></div>
              <div class="row last"><a href="<?php echo osc_search_url(array('page' => 'search'));?>"><?php _e('Reset filter', 'alpha'); ?></a></div>
            </div>
          </div>

        <?php } else { ?>

          <?php echo alp_banner('search_top'); ?>

          <div class="products <?php echo $show; ?>">
            <?php require('search_gallery.php') ; ?>
          </div>
        <?php } ?>

        <div class="paginate">
          <?php echo alp_fix_arrow(osc_search_pagination()); ?>
        </div>

        <?php echo alp_banner('search_bottom'); ?>
      </div>
    </div>

  </div>


  <div class="mobile-navi isMobile">
    <div class="top">
      <div class="full">
        <a href="#" class="alpBg filter-button">
          <i class="fa fa-sliders"></i>
          <span><?php _e('Refine results', 'alpha'); ?></span>
        </a>
      </div>
    </div>
  </div>

</div>


<?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>
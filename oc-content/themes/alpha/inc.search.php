<div id="home-search">
  <div class="inside">
    <div class="cover"></div>

    <?php /*
      $slide_path = osc_base_path() . 'oc-content/themes/' . osc_current_web_theme() . '/images/slide/';
      $slide_url = osc_base_url() . 'oc-content/themes/' . osc_current_web_theme() . '/images/slide/';
      $slide_images = glob($slide_path . '*.{jpg,jpeg,gif,png}', GLOB_BRACE);
      $max = (count($slide_images) > 3 ? 3 : count($slide_images));


      $sc = 1;
      if(isset($slide_images) && !empty($slide_images) && $slide_images <> '') {
        foreach($slide_images as $img) {
          $ext = strtolower(pathinfo($slide_path . '/' . $img, PATHINFO_EXTENSION));

          if($sc <= 3) {
            echo '<div class="slide count' . $max . ' slide' . $sc . '" style="background-image:url(\'' . ($slide_url . basename($img)) . '\');"></div>';
          }

          $sc++;
        }
      }
    */?>


    <div class="box">
      <!--<h3><?php // _e('Hepsi Makul AllMenkul', 'alpha'); ?></h3> -->

      <div class="wrap">
        <form action="<?php echo osc_base_url(true); ?>" method="GET" class="nocsrf" id="home-form" >
          <input type="hidden" name="page" value="search" />
          <input type="hidden" name="sCountry" id="sCountry" value="<?php echo Params::getParam('sCountry'); ?>"/>
          <input type="hidden" name="sRegion" id="sRegion" value="<?php echo Params::getParam('sRegion'); ?>"/>
          <input type="hidden" name="sCity" id="sCity" value="<?php echo Params::getParam('sCity'); ?>"/>

          <div class="line1">
            <div class="col1">
              <div class="box">
                <div id="query-picker" class="query-picker">
                  <input type="text" name="sPattern" class="pattern" placeholder="<?php _e('What are you looking for?', 'alpha'); ?>" value="<?php echo Params::getParam('sPattern'); ?>" autocomplete="off"/>

                  <div class="shower-wrap">
                    <div class="shower"></div>
                  </div>

                  <div class="loader"></div>
                </div>
              </div>
            </div>

            <div class="col2">
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

            <div class="col3">
              <div class="box">
                <?php echo alp_simple_category(false, 0); ?>
              </div>
            </div>

            <div class="col4">
              <div class="box"><button type="submit" class="btn alpBg"><?php _e('Search', 'alpha'); ?> <i class="fa fa-angle-right"></i></button></div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
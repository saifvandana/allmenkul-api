</section>

<?php if (osc_is_home_page()) { ?>
    <a class="mobile-post isMobile alpBg" href="<?php echo osc_item_post_url(); ?>"><i class="fa fa-plus"></i></a>
<?php } ?>


<footer>
    <div class="inside">
        <div class="line1">
            <div class="col1 share">
                <strong><?php _e('Stay connected', 'alpha'); ?></strong>


                <?php
                osc_reset_resources();

                if (osc_is_ad_page()) {
                    $share_url = osc_item_url();
                } else {
                    $share_url = osc_base_url();
                }

                $share_url = urlencode($share_url);
                ?>

                <div class="cont">
                    <?php if (osc_is_ad_page()) { ?>
                        <span class="whatsapp"><a href="whatsapp://send?text=<?php echo $share_url; ?>"
                                                  title="<?php echo osc_esc_html(__('Share us on Whatsapp', 'alpha')); ?>"
                                                  data-action="share/whatsapp/share"><i class="fa fa-whatsapp"></i></a></span>
                    <?php } ?>

                    <span class="facebook"><a
                                href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>"
                                title="<?php echo osc_esc_html(__('Share us on Facebook', 'alpha')); ?>"
                                target="_blank"><i class="fa fa-facebook"></i></a></span>
                    <span class="pinterest"><a
                                href="https://pinterest.com/pin/create/button/?url=<?php echo $share_url; ?>&media=<?php echo osc_base_url(); ?>oc-content/themes/<?php echo osc_current_web_theme(); ?>/images/logo.jpg&description="
                                title="<?php echo osc_esc_html(__('Share us on Pinterest', 'alpha')); ?>"
                                target="_blank"><i class="fa fa-pinterest"></i></a></span>
                    <span class="twitter"><a
                                href="https://twitter.com/home?status=<?php echo $share_url; ?>%20-%20<?php _e('your', 'alpha'); ?>%20<?php _e('classifieds', 'alpha'); ?>"
                                title="<?php echo osc_esc_html(__('Tweet us', 'alpha')); ?>" target="_blank"><i
                                    class="fa fa-twitter"></i></a></span>

                </div>
            </div>


            <div class="col2">
                <div class="cont"><?php  echo "<p style='text-align: center'>Amacımız, dünyadaki herkesin çevrimiçi olarak alıcılar ve
                    satıcılarla bağımsız olarak bağlantı kurmasını sağlamaktır.
                    <br><b> Dünyanın bir numaralı çevrimiçi seri ilan platformu : AllMenkul.</b></p>"; //echo alp_param('site_info'); ?></div>
            </div>

            <div class="col3">
                <?/*php if (osc_is_web_user_logged_in()) { ?>
                    <a class="profile is-logged btn alpBg"
                       href="<?php echo osc_user_dashboard_url(); ?>"><?php _e('My account', 'alpha'); ?></a>
                <?php } else { ?>
                    <a class="profile not-logged btn alpBg"
                       href="<?php echo osc_user_login_url(); ?>"><?php _e('Sign in', 'alpha'); ?></a>
                <?php } */?>

                <?php
                $c = osc_current_user_locale();
                $locales = osc_get_locales();
                ?>

                <?php if (count($locales) > 0) { ?>
                    <div class="locale">
                        <?php foreach ($locales as $l) { ?>
                            <a href="<?php echo osc_change_language_url($l['pk_c_code']); ?>"
                               class="<?php if ($l['pk_c_code'] == $c) { ?>active<?php } ?>"
                               title="<?php echo $l['s_short_name']; ?>"><?php echo mb_strtoupper(substr($l['pk_c_code'], 0,2),"UTF-8"); ?></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="line2">
            <div class="left">
                &copy; <?php echo date("Y"); ?> <?php echo osc_esc_html('allmenkul.com'); ?>
                . <?php _e('All rights reserved', 'alpha'); ?>.
            </div>

            <div class="right">
                <?php $pages = Page::newInstance()->listAll($indelible = 0, $b_link = 1, $locale = null, $start = null, $limit = 5); ?>

                <?php foreach ($pages as $p) { ?>
                    <?php View::newInstance()->_exportVariableToView('page', $p); ?>
                    <a href="<?php echo osc_static_page_url(); ?>"><?php echo ucfirst(osc_static_page_title()); ?></a>
                <?php } ?>


                <a href="<?php echo osc_contact_url(); ?>"><?php _e('Contact us', 'alpha'); ?></a>
            </div>
        </div>

        <div class="footer-hook"><?php osc_run_hook('footer'); ?></div>
    </div>
</footer>


<?php if (alp_param('scrolltop') == 1) { ?>
    <a id="scroll-to-top"><img src="<?php echo osc_current_web_theme_url('images/scroll-to-top.png'); ?>"/></a>
<?php } ?>


<?php if (OSC_DEBUG || OSC_DEBUG_DB) { ?>
    <div id="debug-mode"
         class="noselect"><?php _e('You have enabled DEBUG MODE, autocomplete for locations and items will not work! Disable it in your config.php.', 'veronka'); ?></div>
<?php } ?>


<!-- MOBILE BLOCKS -->
<div id="menu-cover" class="mobile-box"></div>


<div id="menu-options" class="mobile-box">
    <div class="head alpBg">
        <strong>
            <?php
            if (!osc_is_web_user_logged_in()) {
                _e('Welcome!', 'alpha');
            } else {
                echo sprintf(__('Hi %s', 'alpha'), osc_logged_user_name());
            }
            ?>
        </strong>

        <a href="#" class="mclose"><i class="fa fa-times"></i></a>
    </div>

    <div class="body">
        <a class="publish" href="<?php echo osc_item_post_url(); ?>"><?php _e('Add a new listing', 'alpha'); ?></a>

        <?php if (!osc_is_web_user_logged_in()) { ?>
            <a href="<?php echo alp_reg_url('login'); ?>"><?php _e('Log in', 'alpha'); ?></a>
            <a href="<?php echo alp_reg_url('register'); ?>"><?php _e('Register a new account', 'alpha'); ?></a>

        <?php } else { ?>
            <a href="<?php echo osc_user_list_items_url(); ?>"><?php _e('My listings', 'alpha'); ?></a>
            <a href="<?php echo osc_user_profile_url(); ?>"><?php _e('My profile', 'alpha'); ?></a>
            <a href="<?php echo osc_user_alerts_url(); ?>"><?php _e('My alerts', 'alpha'); ?></a>

        <?php } ?>

        <a href="<?php echo osc_contact_url(); ?>"><?php _e('Contact', 'alpha'); ?></a>

        <?php if (osc_is_web_user_logged_in()) { ?>
            <a href="<?php echo osc_user_logout_url(); ?>"><?php _e('Log out', 'alpha'); ?></a>
        <?php } ?>

    </div>
</div>


<script>
    $(document).ready(function () {

        // JAVASCRIPT AJAX LOADER FOR LOCATIONS
        var termClicked = false;
        var currentCountry = "<?php echo alp_ajax_country(); ?>";
        var currentRegion = "<?php echo alp_ajax_region(); ?>";
        var currentCity = "<?php echo alp_ajax_city(); ?>";


        // On first click initiate loading
        /*
        $('body').on('click', '.loc-picker .term', function() {
          if( !termClicked ) {
            $(this).keyup();
          }

          termClicked = true;
        });
        */

        // Create delay
        var delay = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();


        $(document).ajaxSend(function (evt, request, settings) {
            var url = settings.url;

            if (url.indexOf("ajaxLoc") >= 0) {
                $(".loc-picker, .location-picker").addClass('searching');
            }
        });

        $(document).ajaxStop(function () {
            $(".loc-picker, .location-picker").removeClass('searching');
        });


        $('body').on('keyup', '.loc-picker .term', function (e) {

            delay(function () {
                var min_length = 1;
                var elem = $(e.target);
                var term = encodeURIComponent(elem.val());

                // If comma entered, remove characters after comma including
                if (term.indexOf(',') > 1) {
                    term = term.substr(0, term.indexOf(','));
                }

                // If comma entered, remove characters after - including (because city is shown in format City - Region)
                if (term.indexOf(' - ') > 1) {
                    term = term.substr(0, term.indexOf(' - '));
                }

                var block = elem.closest('.loc-picker');
                var shower = elem.closest('.loc-picker').find('.shower');

                shower.html('');

                if (term != '' && term.length >= min_length) {
                    // Combined ajax for country, region & city
                    $.ajax({
                        type: "POST",
                        url: baseAjaxUrl + "&ajaxLoc=1&term=" + term,
                        dataType: 'json',
                        success: function (data) {
                            var length = data.length;
                            var result = '';
                            var result_first = '';
                            var countCountry = 0;
                            var countRegion = 0;
                            var countCity = 0;


                            if (shower.find('.service.min-char').length <= 0) {
                                for (key in data) {

                                    // Prepare location IDs
                                    var id = '';
                                    var country_code = '';
                                    if (data[key].country_code) {
                                        country_code = data[key].country_code;
                                        id = country_code;
                                    }

                                    var region_id = '';
                                    if (data[key].region_id) {
                                        region_id = data[key].region_id;
                                        id = region_id;
                                    }

                                    var city_id = '';
                                    if (data[key].city_id) {
                                        city_id = data[key].city_id;
                                        id = city_id;
                                    }


                                    // Count cities, regions & countries
                                    if (data[key].type == 'city') {
                                        countCity = countCity + 1;
                                    } else if (data[key].type == 'region') {
                                        countRegion = countRegion + 1;
                                    } else if (data[key].type == 'country') {
                                        countCountry = countCountry + 1;
                                    }


                                    // Find currently selected element
                                    var selectedClass = '';
                                    if (
                                        data[key].type == 'country' && parseInt(currentCountry) == parseInt(data[key].country_code)
                                        || data[key].type == 'region' && parseInt(currentRegion) == parseInt(data[key].region_id)
                                        || data[key].type == 'city' && parseInt(currentCity) == parseInt(data[key].city_id)
                                    ) {
                                        selectedClass = ' selected';
                                    }


                                    // For cities, get region name
                                    var nameTop = '';
                                    if (data[key].name_top) {
                                        nameTop = ' <span>' + data[key].name_top + '</span>';
                                    }


                                    if (data[key].type != 'city_more') {

                                        // When classic city, region or country in loop and same does not already exists
                                        if (shower.find('div[data-code="' + data[key].type + data[key].id + '"]').length <= 0) {
                                            result += '<div class="option ' + data[key].type + selectedClass + '" data-country="' + country_code + '" data-region="' + region_id + '" data-city="' + city_id + '" data-code="' + data[key].type + id + '" id="' + id + '"><strong>' + data[key].name + '</strong>' + nameTop + '</div>';
                                        }

                                    } else {

                                        // When city counter and there is more than 12 cities for search
                                        /*
                                        if(shower.find('.more-city').length <= 0) {
                                          if( parseInt(data[key].name) > 0 ) {
                                            result += '<div class="option service more-pick more-city city">... ' + (data[key].name) + ' <?php echo osc_esc_js(__('more cities, specify your location', 'alpha')); ?></div>';
                      }
                    }
                    */
                                    }
                                }


                                // No city, region or country found
                                /*
                                if( countCountry == 0 && shower.find('.empty-country').length <= 0 && shower.find('.service.min-char').length <= 0) {
                                  shower.find('.option.country').remove();
                                  result_first += '<div class="option service empty-pick empty-country country"><?php echo osc_esc_js(__('No country match to your criteria', 'alpha')); ?></div>';
                }

                if( countRegion == 0 && shower.find('.empty-region').length <= 0 && shower.find('.service.min-char').length <= 0) {
                  shower.find('.option.region').remove();
                  result_first += '<div class="option service empty-pick empty-region region"><?php echo osc_esc_js(__('No region match to your criteria', 'alpha')); ?></div>';
                }

                if( countCity == 0 && shower.find('.empty-city').length <= 0 && shower.find('.service.min-char').length <= 0) {
                  shower.find('.option.city').remove();
                  result_first += '<div class="option service empty-pick empty-city city"><?php echo osc_esc_js(__('No city match to your criteria', 'alpha')); ?></div>';
                }
                */

                                if (countCity == 0 && countRegion == 0 && countCountry == 0 && shower.find('.empty-loc').length <= 0 && shower.find('.service.min-char').length <= 0) {
                                    shower.find('.option').remove();
                                    result_first += '<div class="option service empty-pick empty-loc"><?php echo osc_esc_js(__('No location match to your criteria', 'alpha')); ?></div>';
                                }
                            }

                            shower.html(result_first + result);
                        }
                    });

                } else {
                    // Term is not length enough, show default content
                    //shower.html('<div class="option service min-char"><?php echo osc_esc_js(__('Enter at least', 'alpha')); ?> ' + (min_length - term.length) + ' <?php echo osc_esc_js(__('more letter(s)', 'alpha')); ?></div>');

                    shower.html('<?php echo osc_esc_js(alp_def_location()); ?>');
                }
            }, 500);
        });


    });
</script>
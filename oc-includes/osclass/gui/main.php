<?php
/*
 * Copyright 2014 Osclass
 * Copyright 2021 Osclass by OsclassPoint.com
 *
 * Osclass maintained & developed by OsclassPoint.com
 * You may not use this file except in compliance with the License.
 * You may download copy of Osclass at
 *
 *     https://osclass-classifieds.com/download
 *
 * Do not edit or add to this file if you wish to upgrade Osclass to newer
 * versions in the future. Software is distributed on an "AS IS" basis, without
 * warranties or conditions of any kind, either express or implied. Do not remove
 * this NOTICE section as it contains license information and copyrights.
 */


    // meta tag robots
    osc_add_hook('header','bender_follow_construct');

    bender_add_body_class('home');


    $buttonClass = '';
    $listClass   = '';
    if( bender_show_as() === 'gallery'){
          $listClass = 'listing-grid';
          $buttonClass = 'active';
    }
?>
<?php osc_current_web_theme_path('header.php') ; ?>
<div class="clear"></div>
<div class="latest_ads">
<h1><strong><?php _e('Latest Listings', 'bender') ; ?></strong></h1>
 <?php try {
	 if ( osc_count_latest_items() == 0 ) { ?>
         <div class="clear"></div>
         <p class="empty"><?php _e( "There aren't listings available at this moment" , 'bender' ); ?></p>
	 <?php } else { ?>
         <div class="actions">
	      <span class="doublebutton <?php echo $buttonClass; ?>">
	           <a href="<?php echo osc_base_url( true ); ?>?sShowAs=list" class="list-button"
                  data-class-toggle="listing-grid"
                  data-destination="#listing-card-list"><span><?php _e( 'List' , 'bender' ); ?></span></a>
	           <a href="<?php echo osc_base_url( true ); ?>?sShowAs=gallery" class="grid-button"
                  data-class-toggle="listing-grid"
                  data-destination="#listing-card-list"><span><?php _e( 'Grid' , 'bender' ); ?></span></a>
	      </span>
         </div>
		 <?php
		 View::newInstance()->_exportVariableToView( 'listType' , 'latestItems' );
		 View::newInstance()->_exportVariableToView( 'listClass' , $listClass );
		 osc_current_web_theme_path( 'loop.php' );
		 ?>
         <div class="clear"></div>
		 <?php try {
			 if ( osc_count_latest_items() == osc_max_latest_items() ) { ?>
                 <p class="see_more_link"><a href="<?php try {
						 echo osc_search_show_all_url();
					 } catch ( Exception $e ) {
					 } ?>">
                         <strong><?php _e( 'See all listings' , 'bender' ); ?> &raquo;</strong></a>
                 </p>
			 <?php }
		 } catch ( Exception $e ) {
		 } ?>
	 <?php }
 } catch ( Exception $e ) {
 } ?>
</div>
</div><!-- main -->
<div id="sidebar">
    <?php if( osc_get_preference('sidebar-300x250', 'bender') != '') {?>
    <!-- sidebar ad 350x250 -->
    <div class="ads_300">
        <?php echo osc_get_preference('sidebar-300x250', 'bender'); ?>
    </div>
    <!-- /sidebar ad 350x250 -->
    <?php } ?>
    <div class="widget-box">
        <?php try {
	        if ( osc_count_list_regions() > 0 ) { ?>
                <div class="box location">
                    <h3><strong><?php _e( 'Location' , 'bender' ); ?></strong></h3>
                    <ul>
				        <?php try {
					        while ( osc_has_list_regions() ) { ?>
                                <li><a href="<?php try {
								        echo osc_list_region_url();
							        } catch ( Exception $e ) {
							        } ?>"><?php echo osc_list_region_name(); ?>
                                        <em>(<?php echo osc_list_region_items(); ?>
                                            )</em></a></li>
					        <?php }
				        } catch ( Exception $e ) {
				        } ?>
                    </ul>
                </div>
	        <?php }
        } catch ( Exception $e ) {
        } ?>
    </div>
</div>
<div class="clear"><!-- do not close, use main clossing tag for this case -->
<?php if( osc_get_preference('homepage-728x90', 'bender') != '') { ?>
<!-- homepage ad 728x60-->
<div class="ads_728">
    <?php echo osc_get_preference('homepage-728x90', 'bender'); ?>
</div>
<!-- /homepage ad 728x60-->
<?php } ?>
<?php osc_current_web_theme_path('footer.php') ; ?>
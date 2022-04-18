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

?>

<?php
    // meta tag robots
    osc_add_hook('header','bender_nofollow_construct');
    bender_add_body_class('error not-found');
    osc_current_web_theme_path('header.php') ;
?>
<div class="flashmessage-404">
    <h1><?php _e("Sorry but I can't find the page you're looking for", 'bender') ; ?></h1>

    <p><?php _e( 'Let us help you, we have got a few tips for you to find it.' , 'bender') ; ?></p>
    <ul>
        <li>
            <?php _e( '<strong>Search</strong> for it:' , 'bender') ; ?>
            <form action="<?php echo osc_base_url(true) ; ?>" method="get" class="search">
                <input type="hidden" name="page" value="search" />
                <fieldset class="main">
                    <input type="text" name="sPattern"  id="query" value="" />
                    <button type="submit" class="ui-button ui-button-middle"><?php _e('Search', 'bender') ; ?></button>
                </fieldset>
            </form>
        </li>
        <li><?php _e( '<strong>Look</strong> for it in the most popular categories.' , 'bender') ; ?>
            <div class="categories">
                <?php osc_goto_first_category() ; ?>
                <?php try {
	                while ( osc_has_categories() ) { ?>
                        <h2><a class="category <?php echo osc_category_slug(); ?>" href="<?php try {
				                echo osc_search_category_url();
			                } catch ( Exception $e ) {
			                } ?>"><?php echo osc_category_name(); ?></a>
                            <span>(<?php echo osc_category_total_items(); ?>)</h2>
		                <?php if ( osc_count_subcategories() > 0 ) { ?>
			                <?php while ( osc_has_subcategories() ) { ?>
				                <?php if ( osc_category_total_items() > 0 ) { ?>
                                    <h3><a class="category <?php echo osc_category_slug(); ?>" href="<?php try {
							                echo osc_search_category_url();
						                } catch ( Exception $e ) {
						                } ?>"><?php echo osc_category_name(); ?></a>
                                        <span>(<?php echo osc_category_total_items(); ?>)</h3>
				                <?php } ?>
			                <?php } ?>
		                <?php } ?>
	                <?php }
                } catch ( Exception $e ) {
                } ?>
           </div>
           <div class="clear"></div>
        </li>
    </ul>
</div>
<?php osc_current_web_theme_path('footer.php') ; ?>
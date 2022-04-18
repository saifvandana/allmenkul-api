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


    osc_get_premiums();
    if(osc_count_premiums() > 0) {
?>
<table border="0" cellspacing="0">
     <tbody>
        <?php $class = 'even'; ?>
        <?php while(osc_has_premiums()) { ?>
            <tr class="premium_<?php echo $class; ?>">
                <?php if( osc_images_enabled_at_items() ) { ?>
                 <td class="photo">
                     <?php try {
	                     if ( osc_count_premium_resources() ) { ?>
                             <a href="<?php try {
			                     echo osc_premium_url();
		                     } catch ( Exception $e ) {
		                     } ?>"><img src="<?php echo osc_resource_thumbnail_url(); ?>" width="75" height="56"
                                        title="<?php echo osc_item_title(); ?>" alt="<?php echo osc_item_title(); ?>"/></a>
	                     <?php } else { ?>
                             <img src="<?php echo osc_current_web_theme_url( 'images/no_photo.gif' ); ?>" title=""
                                  alt=""/>
	                     <?php }
                     } catch ( Exception $e ) {
                     } ?>
                 </td>
                 <?php } ?>
                 <td class="text">
                     <h3>
                         <span style="float:left;"><a href="<?php try {
		                         echo osc_premium_url();
	                         } catch ( Exception $e ) {
	                         } ?>"><?php echo osc_premium_title(); ?></a></span><span style="float:right;"><?php _e( 'Sponsored ad' , 'modern' ); ?></span>
                     </h3>
                     <p style="clear: left;">
                         <strong><?php try {
		                         if ( osc_price_enabled_at_items() && osc_item_category_price_enabled() ) {
			                         echo osc_premium_formated_price(); ?> - <?php }
	                         } catch ( Exception $e ) {
	                         }
		                         echo osc_premium_city(); ?> (<?php echo osc_premium_region(); ?>) - <?php echo osc_format_date( osc_premium_pub_date()); ?></strong>
                     </p>
                     <p><?php echo osc_highlight( strip_tags( osc_premium_description() ) ); ?></p>
                 </td>
             </tr>
            <?php $class = ( $class === 'even') ? 'odd' : 'even'; ?>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
<table border="0" cellspacing="0">
    <tbody>
        <?php $class = 'even'; ?>
        <?php while(osc_has_items()) { ?>
            <tr class="<?php echo $class; ?>">
                <?php if( osc_images_enabled_at_items() ) { ?>
                 <td class="photo">
                     <?php try {
	                     if ( osc_count_item_resources() ) { ?>
                             <a href="<?php try {
			                     echo osc_item_url();
		                     } catch ( Exception $e ) {
		                     } ?>"><img src="<?php echo osc_resource_thumbnail_url(); ?>" width="75" height="56"
                                        title="<?php echo osc_item_title(); ?>" alt="<?php echo osc_item_title(); ?>"/></a>
	                     <?php } else { ?>
                             <img src="<?php echo osc_current_web_theme_url( 'images/no_photo.gif' ); ?>" title=""
                                  alt=""/>
	                     <?php }
                     } catch ( Exception $e ) {
                     } ?>
                 </td>
                 <?php } ?>
                 <td class="text">
                     <h3>
                         <a href="<?php try {
	                         echo osc_item_url();
                         } catch ( Exception $e ) {
                         } ?>"><?php echo osc_item_title(); ?></a>
                     </h3>
                     <p>
                         <strong><?php try {
		                         if ( osc_price_enabled_at_items() && osc_item_category_price_enabled() ) {
			                         echo osc_item_formated_price(); ?> - <?php }
	                         } catch ( Exception $e ) {
	                         }
		                         echo osc_item_city(); ?> (<?php echo osc_item_region(); ?>) - <?php echo osc_format_date( osc_item_pub_date()); ?></strong>
                     </p>
                     <p><?php echo osc_highlight( strip_tags( osc_item_description() ) ); ?></p>
                 </td>
             </tr>
            <?php $class = ( $class === 'even') ? 'odd' : 'even'; ?>
        <?php } ?>
    </tbody>
</table>

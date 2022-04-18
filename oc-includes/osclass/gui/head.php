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
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

    <title><?php try {
		    echo meta_title();
	    } catch ( Exception $e ) {
	    } ?></title>
    <meta name="title" content="<?php try {
	    echo osc_esc_html( meta_title() );
    } catch ( Exception $e ) {
    } ?>" />
<?php try {
	if ( meta_description() != '' ) { ?>
        <meta name="description" content="<?php try {
			echo osc_esc_html( meta_description() );
		} catch ( Exception $e ) {
		} ?>"/>
	<?php }
} catch ( Exception $e ) {
} ?>
<?php if( function_exists('meta_keywords') ) { ?>
    <?php try {
		if ( meta_keywords() != '' ) { ?>
            <meta name="keywords" content="<?php try {
				echo osc_esc_html( meta_keywords() );
			} catch ( Exception $e ) {
			} ?>"/>
		<?php }
	} catch ( Exception $e ) {
	} ?>
<?php } ?>
<?php if( osc_get_canonical() != '' ) { ?>
    <link rel="canonical" href="<?php echo osc_get_canonical(); ?>"/>
<?php } ?>
    <meta http-equiv="Cache-Control" content="no-cache" />
    <meta http-equiv="Expires" content="Fri, Jan 01 1970 00:00:00 GMT" />

    <script type="text/javascript">
        var fileDefaultText = '<?php echo osc_esc_js( __('No file selected', 'modern') ); ?>';
        var fileBtnText     = '<?php echo osc_esc_js( __('Choose File', 'modern') ); ?>';
    </script>

<?php
osc_enqueue_style('style', osc_current_web_theme_url('style.css'));
osc_enqueue_style('tabs', osc_current_web_theme_url('tabs.css'));
osc_enqueue_style('jquery-ui-datepicker', osc_assets_url('css/jquery-ui/jquery-ui.css'));

osc_register_script('jquery-uniform', osc_current_web_theme_js_url('jquery.uniform.js'), 'jquery');
osc_register_script('global', osc_current_web_theme_js_url('global.js'));

osc_enqueue_script('jquery');
osc_enqueue_script('jquery-ui');
osc_enqueue_script('jquery-uniform');
osc_enqueue_script('tabber');
osc_enqueue_script('global');

osc_run_hook('header');

FieldForm::i18n_datePicker();

?>
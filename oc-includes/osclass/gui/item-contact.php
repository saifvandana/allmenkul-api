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


osc_enqueue_script('jquery-validate');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
    <head>
        <?php osc_current_web_theme_path('head.php'); ?>
        <meta name="robots" content="noindex, nofollow" />
        <meta name="googlebot" content="noindex, nofollow" />
    </head>
    <body>
        <?php osc_current_web_theme_path('header.php'); ?>
        <div class="content user_forms">
            <div id="contact" class="inner">
                <h1><?php _e('Contact seller', 'modern'); ?></h1>
                <ul id="error_list"></ul>
                <?php ContactForm::js_validation(); ?>
                <form action="<?php echo osc_base_url(true); ?>" method="post" name="contact_form" id="contact_form">
                    <fieldset>
                        <?php ContactForm::primary_input_hidden(); ?>
                        <?php ContactForm::action_hidden(); ?>
                        <?php ContactForm::page_hidden(); ?>
                        <label><?php _e('To (seller)', 'modern'); ?>: <?php echo osc_item_contact_name();?></label><br />
                        <label><?php _e('Listing', 'modern'); ?>: <a href="<?php try {
		                        echo osc_item_url();
	                        } catch ( Exception $e ) {
	                        } ?>"><?php echo osc_item_title(); ?></a></label><br />
                        <?php if(osc_is_web_user_logged_in()) { ?>
                            <input type="hidden" name="yourName" value="<?php echo osc_esc_html( osc_logged_user_name() ); ?>" />
                            <input type="hidden" name="yourEmail" value="<?php echo osc_logged_user_email();?>" />
                        <?php } else { ?>
                            <label for="yourName"><?php _e('Your name', 'modern'); ?></label> <?php ContactForm::your_name(); ?><br />
                            <label for="yourEmail"><?php _e('Your e-mail address', 'modern'); ?></label> <?php ContactForm::your_email(); ?><br />
                        <?php } ?>
                        <label for="phoneNumber"><?php _e('Phone number', 'modern'); ?> (<?php _e('optional', 'modern'); ?>)</label> <?php ContactForm::your_phone_number(); ?><br />
                        <label for="message"><?php _e('Message', 'modern'); ?></label> <?php ContactForm::your_message(); ?><br />
                        <?php osc_show_recaptcha(); ?>
                        <button type="submit"><?php _e('Send message', 'modern'); ?></button>
                    </fieldset>
                </form>
            </div>
        </div>
        <?php osc_current_web_theme_path('footer.php'); ?>
    </body>
</html>
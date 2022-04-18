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
<div id="sidebar">
    <?php if(osc_logged_user_id() !=  osc_user_id()) { ?>
	    <?php if ( ( osc_reg_user_can_contact() && osc_is_web_user_logged_in() ) || ! osc_reg_user_can_contact() ) { ?>
        <div id="contact" class="widget-box form-container form-vertical">
            <h2><?php _e( 'Contact' , 'bender'); ?></h2>
                <ul id="error_list"></ul>
                <form action="<?php echo osc_base_url(true); ?>" method="post" name="contact_form" id="contact_form">
                    <input type="hidden" name="action" value="contact_post" />
                    <input type="hidden" name="page" value="user" />
                    <input type="hidden" name="id" value="<?php echo osc_user_id();?>" />
                    <div class="control-group">
                        <label class="control-label" for="yourName"><?php _e('Your name', 'bender'); ?>:</label>
                        <div class="controls"><?php ContactForm::your_name(); ?></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="yourEmail"><?php _e('Your email address', 'bender'); ?>:</label>
                        <div class="controls"><?php ContactForm::your_email(); ?></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="phoneNumber"><?php _e('Phone number', 'bender'); ?> (<?php _e('optional', 'bender'); ?>):</label>
                        <div class="controls"><?php ContactForm::your_phone_number(); ?></div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="message"><?php _e('Message', 'bender'); ?>:</label>
                        <div class="controls textarea"><?php ContactForm::your_message(); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <?php osc_run_hook('item_contact_form', osc_item_id()); ?>
                            <?php osc_show_recaptcha(); ?>
                            <button type="submit" class="ui-button ui-button-middle ui-button-main"><?php _e( 'Send' , 'bender');?></button>
                        </div>
                    </div>
                </form>
                <?php ContactForm::js_validation(); ?>
        </div>
        <?php
        }
    }
    ?>

</div><!-- /sidebar -->
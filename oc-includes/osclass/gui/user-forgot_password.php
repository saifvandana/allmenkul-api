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
    osc_add_hook('header','bender_nofollow_construct');

    bender_add_body_class('forgot');
    osc_current_web_theme_path('header.php');
?>
<div class="form-container form-horizontal form-container-box">
    <div class="header">
        <h1><?php _e('Recover your password', 'bender'); ?></h1>
    </div>
    <div class="resp-wrapper">
        <form name="reset_password" id="reset_password" action="<?php echo osc_base_url(true); ?>" method="post" >
            <input type="hidden" name="page" value="login" />
            <input type="hidden" name="action" value="forgot_post" />
            <input type="hidden" name="userId" value="<?php echo osc_esc_html(Params::getParam('userId')); ?>" />
            <input type="hidden" name="code" value="<?php echo osc_esc_html(Params::getParam('code')); ?>" />
            <div class="control-group">
                <label class="control-label" for="new_password"><?php _e('New password', 'bender'); ?></label>
                <div class="controls">
                    <input type="password" name="new_password" value="" autocomplete="off" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="new_password2"><?php _e('Repeat new password', 'bender'); ?></label>
                <div class="controls">
                    <input type="password" name="new_password2" value="" autocomplete="off" />
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="ui-button ui-button-middle ui-button-main"><?php _e( 'Change password' , 'bender');?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php osc_current_web_theme_path('footer.php') ; ?>

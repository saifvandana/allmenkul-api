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

    bender_add_body_class('login');
    osc_current_web_theme_path('header.php');
?>
<div class="form-container form-horizontal form-container-box">
    <div class="header">
        <h1><?php _e('Access to your account', 'bender'); ?></h1>
    </div>
    <div class="resp-wrapper">
        <form name="login" id="login" action="<?php echo osc_base_url(true); ?>" method="post" >
            <input type="hidden" name="page" value="login" />
            <input type="hidden" name="action" value="login_post" />

            <div class="control-group">
                <label class="control-label" for="email"><?php _e('E-mail', 'bender'); ?></label>
                <div class="controls">
                    <?php UserForm::email_login_text(); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password"><?php _e('Password', 'bender'); ?></label>
                <div class="controls">
                    <?php UserForm::password_login_text(); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="controls checkbox">
                    <?php UserForm::rememberme_login_checkbox();?> <label for="remember"><?php _e('Remember me', 'bender'); ?></label>
                </div>
                <div class="controls">
                    <button type="submit" class="ui-button ui-button-middle ui-button-main"><?php _e( 'Log in' , 'bender');?></button>
                </div>
            </div>
            <div class="actions">
                <a href="<?php echo osc_register_account_url(); ?>"><?php _e( 'Register for a free account' , 'bender'); ?></a><br /><a href="<?php echo osc_recover_user_password_url(); ?>"><?php _e( 'Forgot password?' , 'bender'); ?></a>
            </div>
        </form>
    </div>
</div>
<?php osc_current_web_theme_path('footer.php') ; ?>

<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');
/*
 *      OSCLass – software for creating and publishing online classified
 *                           advertising platforms
 *
 *                        Copyright (C) 2010 OSCLASS
 *
 *       This program is free software: you can redistribute it and/or
 *     modify it under the terms of the GNU Affero General Public License
 *     as published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful, but
 *         WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Affero General Public License for more details.
 *
 *      You should have received a copy of the GNU Affero General Public
 * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<?php


if(Params::getParam('plugin_action')=='done') {
    osc_set_preference('cv_email', Params::getParam('cv_email'), 'jobs_plugin', 'STRING');
    osc_set_preference('allow_cv_upload', (Params::getParam('allow_cv_upload')!=1)?0:1, 'jobs_plugin', 'BOOLEAN');
    osc_set_preference('allow_cv_unreg', (Params::getParam('allow_cv_unreg')!=1)?0:1, 'jobs_plugin', 'BOOLEAN');
    osc_set_preference('send_me_cv', (Params::getParam('send_me_cv')!=1)?0:1, 'jobs_plugin', 'BOOLEAN');
    osc_reset_preferences();
    osc_add_flash_ok_message( __('Settings updated', 'jobs_plugin'), 'admin');

}

?>
<?php osc_show_flash_message('admin') ; ?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 50%;">
            <fieldset>
                <legend><?php _e('Jobs Options', 'jobs_attributes'); ?></legend>
                    <form name="jobs_form" id="jobs_form" action="<?php echo osc_admin_base_url(true);?>" method="GET" enctype="multipart/form-data" >
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <?php if(osc_version()<320) { ?>
                        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf.php" />
                    <?php } else { ?>
                        <input type="hidden" name="route" value="jobs-attr-admin-conf" />
                    <?php }; ?>
                    <input type="hidden" name="plugin_action" value="done" />

                    <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('allow_cv_upload', 'jobs_plugin') ? 'checked="true"' : ''); ?> name="allow_cv_upload" id="allow_cv_upload" value="1" />
                    <label for="enabled_comments"><?php _e('Allow upload of resumes', 'jobs_attributes'); ?></label>
                    <br/>

                    <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('allow_cv_unreg', 'jobs_plugin') ? 'checked="true"' : ''); ?> name="allow_cv_unreg" id="allow_cv_unreg" value="1" />
                    <label for="enabled_comments"><?php _e('Allow unregistered users to upload their resumes', 'jobs_attributes'); ?></label>
                    <br/>

                    <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('send_me_cv', 'jobs_plugin') ? 'checked="true"' : ''); ?> name="send_me_cv" id="send_me_cv" value="1" />
                    <label for="enabled_comments"><?php _e('Send all emails to the following email (if not checked the resumes will be send to ad\'s author)', 'jobs_attributes'); ?></label>
                    <br/>

                    <label><?php _e('E-mail', 'jobs_attributes');?></label><input type="text" name="cv_email" id="cv_email" value="<?php echo osc_get_preference('cv_email', 'jobs_plugin'); ?>" />
                    <br/>

                    <button type="submit"><?php _e('Update', 'jobs_attributes'); ?></button>
                    </form>
            </fieldset>
        </div>
        <div style="float: left; width: 50%;">
            <fieldset>
                <legend><?php _e('Help', 'jobs_attributes'); ?></legend>
                <p>
                    <label>
                        <?php _e('You could allow users to send their resumes to a specific email address or to send them to the author of the ad. Also you could specify is unregistered users could or could not upload their resumes', 'jobs_attributes'); ?>.
                    </label>
                </p>
            </fieldset>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>

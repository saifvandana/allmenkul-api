<?php if ( ! defined('OC_ADMIN')) exit('Direct access is not allowed.');
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


$perms = osc_save_permissions();
$ok  = osc_change_permissions();


//customize Head
function customHead(){
  ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $("#steps_div").hide();
    });
  <?php
  $perms = osc_save_permissions();
  $ok  = osc_change_permissions();

  foreach($perms as $k => $v) {
    @chmod($k, $v);
  }
  if( $ok ) {
  ?>
    $(function() {
      var steps_div = document.getElementById('steps_div');
      steps_div.style.display = '';
      var steps = document.getElementById('steps');
      var version = <?php echo osc_version(); ?>;
      var fileToUnzip = '';
      steps.innerHTML += '<div class="one"><?php echo osc_esc_js( sprintf( __('Checking for updates (Current version %s)'), osc_version() )); ?></div>';

      $.getJSON("https://osclass-classifieds.com/api/latest_version.php", function(data) {
        if(data.version <= version) {
          steps.innerHTML += '<div class="one strong"><?php echo osc_esc_js( __('Congratulations! Your Osclass installation is up to date!')); ?></div>';
        } else {
          steps.innerHTML += '<div class="one strong"><?php echo osc_esc_js( __('New version to update:')); ?> ' + oscEscapeHTML(data.version) + '</div>';

          <?php if(Params::getParam('confirm')=='true') {?>
            steps.innerHTML += '<div class="one load-div"><img id="loading_image" src="<?php echo osc_current_admin_theme_url('images/loading.gif'); ?>" /><?php echo osc_esc_js(__('Upgrading your Osclass installation (this could take a while). If upgrade does not finish in 10 minutes, simply try to refresh page. If you get successful flash message, everything is alright.')); ?></div>';

            var tempAr = data.url.split('/');
            fileToUnzip = tempAr.pop();

            $.ajax({
              url: '<?php echo osc_admin_base_url(true); ?>?page=ajax&action=upgrade&<?php echo osc_csrf_token_url(); ?>',
              dataType: 'text',
              timeout: 120000,
              success: function(data){
                console.log(data);

                try {
                  var data = JSON.parse(data);

                  // successful
                  if(data.error==0 || data.error==6) {
                    window.location = "<?php echo osc_admin_base_url(true); ?>?page=tools&action=version";

                  // failed
                  } else {
                    $('.load-div').hide(0);
                    steps.innerHTML += '<div class="one"><?php echo osc_esc_js( __('Upgrade has failed with following error:')); ?> <strong>' + oscEscapeHTML(data.message) + '</strong></div>';
                  }
                } catch (e) {
                  steps.innerHTML += '<div class="one"><?php echo osc_esc_js( __('Upgrade has failed with following error:')); ?> ' + oscEscapeHTML(data) + '</div>';
                }
              }, 
              error: function(xhr, status){
                console.log(status);
                $('.load-div').hide(0);

                if(status === 'timeout') {
                  steps.innerHTML += '<div class="one"><?php echo osc_esc_js( __('Upgrade has failed from timeout')); ?></div>';
                } else {
                  steps.innerHTML += '<div class="one"><?php echo osc_esc_js( __('Upgrade has failed with following error:')); ?> ' + oscEscapeHTML(status) + ' - ' + xhr.responseText + '</div>';
                }
              }
            });
          <?php } else { ?>
            steps.innerHTML += '<div class="one"><input type="button" class="btn btn-submit" value="<?php echo osc_esc_html( __('Upgrade')); ?>" onclick="window.location.href=\'<?php echo osc_admin_base_url(true); ?>?page=tools&action=upgrade&confirm=true\';" /></div>';
          <?php } ?>
        }
      });
    });
  <?php } ?>
  </script>
  <?php
}

osc_add_hook('admin_header','customHead', 10);


function render_offset(){
  return 'row-offset';
}

function addHelp() {
  echo '<p>' . __("Check to see if you're using the latest version of Osclass. If you're not, the system will let you know so you can update and use the newest features.") . '</p>';
}

osc_add_hook('help_box','addHelp');


osc_add_hook('admin_page_header','customPageHeader');
function customPageHeader() { 
  ?>
  <h1><?php _e('Tools'); ?>
    <a href="#" class="btn ico ico-32 ico-help float-right"></a>
  </h1>
  <?php
}

function customPageTitle($string) {
  return sprintf(__('Upgrade - %s'), $string);
}

osc_add_filter('admin_title', 'customPageTitle');


osc_current_admin_theme_path( 'parts/header.php' ); 
?>

<div id="backup-setting">
  <!-- settings form -->
    <div id="backup-settings">
      <h2 class="render-title"><?php _e('Upgrade'); ?></h2>
      <form>
        <fieldset>
        <div class="form-horizontal">
        <div class="form-row">
          <div class="tools upgrade">
          <?php if( $ok ) { ?>
            <p class="text">
              <?php printf( __('Your Osclass installation can be auto-upgraded. Please, back up your database and the folder oc-content before attempting to upgrade your Osclass installation. You can also upgrade Osclass manually, more information in the %s'), '<a href="https://docs.osclasspoint.com/">Documentation</a>'); ?>.
            </p>
          <?php } else { ?>
            <p class="text">
              <?php _e("Your Osclass installation can't be auto-upgraded. Files and folders need to be writable. You can apply write permissions via SSH with the command \"chmod -R a+w *\" (without quotes) or via an FTP client, it depends on the program so we can not provide more information. You can also upgrade Osclass by downloading the upgrade package, unzipping it and replacing the files on your server with the ones in the package."); ?>
            </p>
          <?php } ?>
            <div id="steps_div">
              <div id="steps">

              </div>
            </div>
          </div>
        </div>
        </div>
      </fieldset>
    </form>
  </div>
  <!-- /settings form -->
</div>
<?php osc_current_admin_theme_path( 'parts/footer.php' ); ?>
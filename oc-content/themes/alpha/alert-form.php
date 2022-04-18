<script type="text/javascript">
$(document).ready(function(){
  <?php if(!osc_is_web_user_logged_in()) { ?>$('#alert_email').val('');<?php } ?>
  $('#alert_email').attr('placeholder', '<?php echo osc_esc_js(__('Email', 'alpha')) ; ?>');

  $('body').on('click', 'button.alert-notify', function(e){
    e.preventDefault();

    if($('#alert_email').val() == '' && $("#alert_userId").val() <= 0) {
      alpAddFlash('<?php echo osc_esc_js(__('Please enter your email address!', 'alpha')); ?>', 'error');
      return false;
    }


    $.post(
      '<?php echo osc_base_url(true); ?>', 
      {
        email: $("#alert_email").val(), 
        userid: $("#alert_userId").val(), 
        alert: $("#alert").val(), 
        page:"ajax", 
        action:"alerts"
      }, 
      function(data){
        if(data==1) {
          alpAddFlash('<?php echo osc_esc_js(__('You have successfully subscribed to alert!', 'alpha')); ?>', 'ok');

        } else if(data==-1) { 
          alpAddFlash('<?php echo osc_esc_js(__('There was error during subscription process - incorrect email address format!', 'alpha')); ?>', 'error');

        } else if(data==0) { 
          alpAddFlash('<?php echo osc_esc_js(__('You have already subscribed to this search!', 'alpha')); ?>', 'info');

        }
    });

    return false;
  });
});
</script>

<div id="n-block" class="block <?php if(osc_is_web_user_logged_in()) { ?>is-logged<?php } else { ?>not-logged<?php } ?>">
  <div class="n-wrap">
    <form action="<?php echo osc_base_url(true); ?>" method="post" name="sub_alert" id="sub_alert" class="nocsrf">
      <?php AlertForm::page_hidden(); ?>
      <?php AlertForm::alert_hidden(); ?>
      <?php AlertForm::user_id_hidden(); ?>

      <h2><?php _e('Subscribe to search', 'alpha'); ?></h2>

      <?php 
        if(osc_is_web_user_logged_in()) {
          AlertForm::email_hidden();
        } else {
          AlertForm::email_text();
        }
      ?>

      <button type="button" class="btn alpBg alert-notify"><?php _e('Subscribe', 'alpha'); ?></button>
    </form>
  </div>
</div>
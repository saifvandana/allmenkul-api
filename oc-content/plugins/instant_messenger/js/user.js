$(document).ready(function(){

  // AUTO-EXPAND TEXTAREA
  $('body').on('change keyup keydown paste cut', 'textarea#im-message', function () {
    $(this).height(0).height(this.scrollHeight - 30);
    
    if(this.scrollHeight > 455) {
      $(this).css('overflow-y', 'scroll');
      $('body #im-message-form button.im-button-alt').css('right', '17px');
      $('body #im-message-form .im-attachment').css('right', '68px');
    }
  });


  // TOOLTIPS IN USER ACCOUNT
  Tipped.create('.im-has-tooltip', { maxWidth: 200, radius: false });
  Tipped.create('.im-has-tooltip-left', { maxWidth: 200, radius: false } );


  // ATTACHMENT NAME
  $('input[name="im-file"]').change(function() {
    if( $(this)[0].files[0]['name'] != '' ) {
      $('.im-attachment .im-att-box .im-status .im-wrap span').text( $(this)[0].files[0]['name'] );
    }
  });


  // FORM VALIDATION
  $('form.im-form-validate').validate({
    rules: {
      "im-from-user-name": {
        required: true,
        minlength: 3
      },
      
      "im-from-user-email": {
        required: true,
        email: true
      },
      
      "im-title": {
        required: true,
        minlength: 2
      },
      
      "im-message": {
        required: true,
        minlength: 2
      }
    },
    
    messages: {
      "im-from-user-name": {
        required: imRqName,
        minlength: imDsName
      },
      
      "im-from-user-email": {
        required: imRqEmail,
        email: imDsEmail
      },
      
      "im-title": {
        required: imRqTitle,
        minlength: imDsTitle
      },
      
      "im-message": {
        required: imRqMessage,
        minlength: imDsMessage
      },
    },
    
    wrapper: "li",
    errorLabelContainer: "#im-error-list",
    invalidHandler: function(form, validator) {
      $('html,body').animate({ scrollTop: $('#im-error-list').offset().top - 100 }, { duration: 250, easing: 'swing'});
    },
    submitHandler: function(form){
      $('button[type=submit], input[type=submit]').attr('disabled', 'disabled');
      form.submit();
    }
  });

});


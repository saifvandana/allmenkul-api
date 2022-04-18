$(document).ready(function(){

  // COLLAPSE-EXPAND ITEM OFFERS
  $('body').on('click', 'a.mo-item-showhide', function(e){
    e.preventDefault();
    var status = $(this).attr('data-status');
    var box = $(this).closest('.mo-item');
    
    if(status == 'expanded') {
      box.find('.mo-two-wrap').slideUp(200);
      $(this).find('i').removeClass('fa-angle-down').addClass('fa-angle-up');
      $(this).attr('data-status', 'collapsed');
      box.addClass('mo-collapsed');
    } else {
      box.find('.mo-two-wrap').slideDown(200);
      $(this).find('i').removeClass('fa-angle-up').addClass('fa-angle-down');
      $(this).attr('data-status', 'expanded');
      box.removeClass('mo-collapsed');
    }
    
    return false;
  });
  

  // USER ACCOUNT TABS
  $('body').on('click', '.mo-nav a', function(e) {
    e.preventDefault();
    
    var id = $(this).attr('data-tab');
    
    $('.mo-nav a').removeClass('active');
    $(this).addClass('active');
  
    $('.mo-tab').hide(0);
    $('.mo-tab[data-tab="' + id + '"]').show(0);
  });
  
  
  // CLOSE BOX
  $('body').on('click', 'button.mo-close', function(){
    moModalClose('', $(this));
  });


  // GO TO NEW OFFER
  $('body').on('click', 'a.mo-goto-new', function(e) {
    e.preventDefault();
    $('#mo-list').hide(0);
    $('#mo-new').show(0);
  });


  // GO TO OFFER LIST
  $('body').on('click', 'i.mo-back', function(e) {
    e.preventDefault();
    $('#mo-new').hide(0);
    $('#mo-list').show(0);
  });



  // OFFER SUBMISSION VIA AJAX 
  $('body').on('click', 'button.mo-submit', function(e){
    e.preventDefault();

    // FORM VALIDATION
    $('form.mo-form-new').validate({
      rules: {
        "price": {
          required: true
        },

        "name": {
          required: true,
          minlength: 3
        },

        "email": {
          required: true,
          email: true
        }      
      },
      
      messages: {
        "price": {
          required: moValidPriceReq
        },

        "name": {
          required: moValidNameReq,
          minlength: moValidNameShort
        },
        
        "email": {
          required: moValidEmailReq,
          email: moValidEmailShort
        }
      },
      
      wrapper: "li",
      errorLabelContainer: ".mo-error-list",
      invalidHandler: function(form, validator) {
        $('.mo-box-content').animate({scrollTop:0}, 300);
      },
      submitHandler: function(form){
        $('button[type=submit], input[type=submit]').prop('disabled', true).addClass('mo-loading');
        $('button[type=submit]').prepend('<i class="fa fa-spinner fa-pulse"></i>');
        form.submit();
      }
    });
 


    if($('form.mo-form-new').valid())  {
      var form = $(this).closest('form');

      form.find('button.mo-submit').addClass('mo-loading').prop('disabled', true).prepend('<i class="fa fa-spinner fa-pulse"></i>');

      $.ajax({
        url: form.attr('action'),
        type: "POST",
        data: form.find(":input").filter(function () { return $.trim(this.value).length > 0}).serialize(),
        success: function(response){
          form.find('button.mo-submit').removeClass('mo-loading').prop('disabled', false);
          form.find('button.mo-submit i').remove();
          form.hide(0);
          form.siblings('.mo-status.mo-success').fadeIn(200);
        },
        error: function(response){
          form.find('button.mo-submit').removeClass('mo-loading').prop('disabled', false);
          form.find('button.mo-submit i').remove();
          form.hide(0);
          form.siblings('.mo-status.mo-error').fadeIn(200);
        }
      });
    }

  });


  // DO NOT ALLOW TO SUBMIT MANAGE OFFER FORM WITHOUT SELECTION
  $(document).on('submit','form.mo-form-reply',function(e){
    if($(this).find('input[name="statusId"]').val() == '') {
      e.preventDefault();
      return false;
    }
  });


  // SELLER APPROVAL/REJECTION BY AJAX 
  $('body').on('click', 'a.mo-respond-button', function(e){
    e.preventDefault();

    var box = $(this).closest('.mo-two');
    var status = $(this).attr('data-accept');
    var form = $(this).closest('form');

    form.find('a.mo-respond-button').addClass('disabled').attr('disabled', true);
    form.find('input[name="statusId"]').val(status);

    $.ajax({
      url: form.attr('action'),
      type: "POST",
      data: form.find(":input").filter(function () { return $.trim(this.value).length > 0}).serialize(),
      success: function(response){
        var replyText = form.find('input[name="respond"]').val();
        
        if(replyText.trim() == '') {
          replyText = '-';
        }
        
        form.find('a.mo-respond-button').removeClass('disabled').attr('disabled', false);
        form.find('.mo-input-wrap').hide(0);
        form.parent().addClass('mo-line-reply-text').html('<strong>' + moYourReply + ':</strong> ' + replyText);
        box.find('.mo-box-right').append('<div class="mo-line-status">' + $('.mo-placeholders .mo-status-' + status).html() + '</div>');
      }
    });
  });



  // UPDATE UNIT PRICE ON CHANGE
  $('body').on('change', '#price, #quantity', function(){
    var price = $('#mo-new #price').val();
    var quantity = $('#mo-new #quantity').val();

    if( price != '' && price > 0 && quantity > 1) {
      $('#mo-new .unit-price').show(0);
      $('#mo-new .unit-price .mo-top').text( (price/quantity).toFixed(1) + $('#mo-new .mo-input-wrap > span').text() );

    } else {
      $('#mo-new .unit-price').hide(0);
    }
  });


  $('body').on('click', 'a.make-offer-link, .mo-open-offer', function(e) {
    e.preventDefault();
   
    var url = $(this).attr('href');

    moModal({
      width: 420,
      height: 500,
      fromUrl: true,
      content: url, 
      wrapClass: 'show-offer-wrap',
      closeBtn: true, 
      iframe: false, 
      fullscreen: false,
      transition: 200,
      delay: 0,
      lockScroll: true
    });
  });
});




// CUSTOM MODAL BOX
function moModal(opt) {
  width = (typeof opt.width !== 'undefined' ? opt.width : 480);
  height = (typeof opt.height !== 'undefined' ? opt.height : 480);
  content = (typeof opt.content !== 'undefined' ? opt.content : '');
  wrapClass = (typeof opt.wrapClass !== 'undefined' ? ' ' + opt.wrapClass : '');
  closeBtn = (typeof opt.closeBtn !== 'undefined' ? opt.closeBtn : true);
  iframe = (typeof opt.iframe !== 'undefined' ? opt.iframe : true); 
  fromUrl = (typeof opt.fromUrl !== 'undefined' ? opt.fromUrl : false); 
  fullscreen = (typeof opt.fullscreen !== 'undefined' ? opt.fullscreen : false); 
  transition = (typeof opt.transition !== 'undefined' ? opt.transition : 200); 
  delay = (typeof opt.delay !== 'undefined' ? opt.delay : 0);
  lockScroll = (typeof opt.lockScroll !== 'undefined' ? opt.lockScroll : true); 

  var id = Math.floor(Math.random() * 100) + 10;
  width = moAdjustModalSize(width, 'width') + 'px';
  height = moAdjustModalSize(height, 'height') + 'px';

  var fullscreenClass = '';
  if(fullscreen === 'mobile') {
    if (($(window).width() + scrollCompensate()) < 768) {
      width = 'auto'; height = 'auto'; fullscreenClass = ' modal-fullscreen';
    }
  } else if (fullscreen === true) {
    width = 'auto'; height = 'auto'; fullscreenClass = ' modal-fullscreen';
  }

  var html = '';
  html += '<div class="modal-cover" data-modal-id="' + id + '" onclick="moModalClose(\'' + id + '\');"></div>';
  html += '<div id="moModal" class="modal-box' + wrapClass + fullscreenClass + '" style="width:' + width + ';height:' + height + ';" data-modal-id="' + id + '">';
  html += '<div class="modal-inside">';
  
  if(closeBtn) {
    html += '<div class="modal-close" onclick="moModalClose(\'' + id + '\');"><i class="fa fa-times"></i></div>';
  }
    
  html += '<div class="modal-body ' + (iframe === true ? 'modal-is-iframe' : 'modal-is-inline') + '">';
  
  if(iframe === true) {
    html += '<div class="modal-content"><iframe class="modal-iframe" data-modal-id="' + id + '" src="' + content + '"/></div>';
  } else if(fromUrl === true) {
    html += '<div class="modal-content"><div class="modal-loader"></div></div>';
  } else {
    html += '<div class="modal-content">' + content + '</div>';
  }
  
  html += '</div>';
  html += '</div>';
  html += '</div>';
  
  if(lockScroll) {
    $('body').css('overflow', 'hidden');
  }
  
  $('body').append(html);
  $('div[data-modal-id="' + id + '"].modal-cover').fadeIn(transition);
  $('div[data-modal-id="' + id + '"].modal-box').delay(delay).fadeIn(transition);
  
  if(fromUrl === true) {
    $.ajax({
      url: content,
      type: "GET",
      success: function(response){
        $('.modal-box[data-modal-id="' + id + '"] .modal-content').html(response);
      },
      error: function(response){
        $('.modal-box[data-modal-id="' + id + '"] .modal-content').html(response);
      }
    });
  }        
}


// Close modal by clicking on close button
function moModalClose(id = '', elem = null) {
  if(id == '') {
    id = $(elem).closest('.modal-box').attr('data-modal-id');
  }
  
  $('body').css('overflow', 'initial');
  $('div[data-modal-id="' + id + '"]').fadeOut(200, function(e) {
    $(this).remove(); 
  });
  
  return false;
}


// Close modal by some action inside iframe
function moModalCloseParent() {
  var boxId = $(window.frameElement, window.parent.document).attr('data-modal-id');
  window.parent.moModalClose(boxId);
}


// Calculate maximum width/height of modal in case original width/height is larger than window width/height
function moAdjustModalSize(size, type = 'width') {
  var size = parseInt(size);
  var windowSize = (type == 'width' ? $(window).width() : $(window).height());
  
  if(size <= 0) {
    size = (type == 'width' ? 640 : 480);  
  }
  
  if(size*0.9 > windowSize) {
    size = windowSize*0.9;
  }
  
  return Math.floor(size);
}

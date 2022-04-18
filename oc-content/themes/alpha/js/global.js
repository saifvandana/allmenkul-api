$(document).ready(function() {

  // LATEST SEARCH BOX
  var delayQuery = (function(){
    var timer = 0;
    return function(callback, ms){
      clearTimeout (timer);
      timer = setTimeout(callback, ms);
    };
  })();

  $('body').on('click', '.query-picker .pattern', function() {
    if(!$('.query-picker .shower .option').length) {
      $(this).keyup();
    } else {
      if(!$(this).hasClass('open')) {
        $(this).closest('.query-picker').find('.shower').show(0).css('opacity', 0).css('margin-top', '30px').css('margin-bottom', '-30px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} , 300);
        $(this).addClass('open');
        $(this).closest('.line1').addClass('open');
      }
    }
  });


  $('body').on('keyup', '.query-picker .pattern', function(e) {
    delayQuery(function(){
      var min_length = 1;
      var elem = $(e.target);
      var query = encodeURIComponent(elem.val());
      var queryOriginal = elem.val();

      var block = elem.closest('.query-picker');
      var shower = elem.closest('.query-picker').find('.shower');

      //shower.html('');

      if(query != '' && query.length >= min_length) {
        $.ajax({
          type: "POST",
          url: baseAjaxUrl + "&ajaxQuery=1&pattern=" + query,
          dataType: 'json',
          success: function(data) {
            shower.html('');

            var length = data.length;
            var result = '';

            for(key in data) {
              if(!shower.find('div[data-hash="' + data[key].hash + '"]').length) {

                result += '<div class="option query" data-hash="' + data[key].string_hash + '">' + data[key].string_format + '</div>';
              }
            }

            if(length <= 0) {
              result += '<div class="option query" data-hash="blank"><b>' + queryOriginal + '</b></div>';
            }

            shower.html(result);

            if(!elem.hasClass('open') && queryOriginal != '') {
              shower.show(0).css('opacity', 0).css('margin-top', '30px').css('margin-bottom', '-30px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} , 300);
              elem.addClass('open');
              elem.closest('.line1').addClass('open');
            }
          }
        });
      } else {
        shower.html('');

        if(elem.hasClass('open')) {
          shower.hide(0);
          elem.removeClass('open');
          elem.closest('.line1').removeClass('open');
        }
      }
    }, 100);
  });


  // QUERY PICKER - WHEN CLICK OUTSIDE LOCATION PICKER, HIDE SELECTION
  $(document).mouseup(function (e){
    var container = $('.query-picker');

    if(!container.is(e.target) && container.has(e.target).length === 0) {
      container.find('.shower').fadeOut(0);
      container.find('.pattern').removeClass('open');
      container.closest('.line1').removeClass('open');
    }
  });


  // QUERY PICKER - PICK OPTION
  $(document).on('click', '.query-picker .shower .option', function(e){
    $('.query-picker .pattern').removeClass('open');
    $('.query-picker .shower').fadeOut(0);
    $('.query-picker .pattern').val($(this).text());
    $('.query-picker').closest('.line1').removeClass('open');
  });


  // QUERY PICKER - OPEN ON CLICK IF NEEDED
  $(document).on('click', '.query-picker .pattern', function(e){
    if(!$(this).hasClass('open') && $(this).val() != '' && $(this).closest('.query-picker').find('.shower .option').length) {
      $(this).closest('.query-picker').find('.shower').show(0).css('opacity', 0).css('margin-top', '30px').css('margin-bottom', '-30px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} , 300);
      $(this).addClass('open');
      $(this).closest('.line1').addClass('open');
    }
  });


  // ARROW CLICK OPEN BOX
  $(document).on('click', '#location-picker .fa-angle-down', function(e){
    $(this).siblings('input[type="text"]').click();
  });


  // FANCYBOX - LISTING PREVIEW
  $(document).on('click', '.simple-prod .preview:not(.disabled)', function(e){
    e.preventDefault();
    var url = this.href;

    var maxWidth = 960;
    var windowsWidth = parseInt($(window).outerWidth()) - 40;
    var windowsHeight = parseInt($(window).outerHeight()) - 40;

    if(windowsWidth > maxWidth) {
      windowsWidth = maxWidth;
    }

    if(jqueryVersion == '1') {
      if (!!$.prototype.fancybox) {
        $.fancybox({
          'padding': 0,
          'width': windowsWidth,
          'height': windowsHeight,
          'scrolling': 'yes',
          'wrapCSS': 'imgviewer',
          'type': 'iframe',
          'href': url
        });
      }
    } else {
      if (!!$.prototype.fancybox) {
        windowsWidth = windowsWidth.toString() + 'px';
        windowsHeight = windowsHeight.toString() + 'px';
        
        $.fancybox.open({
          toolbar : true,
          type: 'iframe',
          src: url,
          baseClass: 'imgviewer',
          iframe: {
            css: {
              width : windowsWidth,
              height : windowsHeight,
              padding: 0
            }
          }
        });
      }      
    }
  });


  // Handle no pictures
  $(document).on('click', '.orange-but.open-image.disabled', function(e){
    e.preventDefault();
    return false;
  });


  // ITEM MOBILE MENU
  $('body').on('click', '.mobile-navi .middle', function(e){
    e.preventDefault();
    
    $('.mobile-navi .bottom').slideToggle(200);
  });


  // HIDE ITEM MOBILE MENU WHEN CLICK OUTSIDE
  if (($(window).width() + scrollCompensate()) < 768) {
    $(document).mouseup(function (e){
      var container = $('.mobile-navi');

      if (!container.is(e.target) && container.has(e.target).length === 0) {
        $('.mobile-navi .bottom').slideUp(200);
      }
    });
  }


  // ITEM MOBILE MENU SHOW
  $(window).scroll(function(){
    var cHeight = $(window).height();
    var cScroll = $(window).scrollTop();

    if(cScroll > cHeight/2 && !$('.mobile-navi').hasClass('shown')) {
      $('.mobile-navi').addClass('shown');
      $('.mobile-navi').show(0).css('margin-top', '40px').css('margin-bottom', '-40px').css('opacity', 0).animate( { opacity: 1, marginTop:'0px', marginBottom:'0px'}, 200);
    } 

    if(cScroll <= cHeight/2 && $('.mobile-navi').hasClass('shown')) {
      $('.mobile-navi').removeClass('shown');
      $('.mobile-navi').css('margin-top', '0px').css('margin-bottom', '0px').css('opacity', 1).animate( { opacity: 0, marginTop:'40px', marginBottom:'-40px'}, 200, function() { $('.mobile-navi, .mobile-navi .bottom').hide(0); });
    }
  });


  // HOME SEARCH - LOCATION PICK AVOID EMPTY
  $(document).one('submit', 'form#home-form, form#search-form', function(e){
    if(locationPick == "1" && 1==2) {  // disabled now
      e.preventDefault();

      if($(this).find('input.term').val() != '') {
        if($(this).find('.shower .option:not(.service):not(.info)').length) {
          $(this).find('.shower .option:not(.service):not(.info)').first().click();
        }
      }

      $(this).submit();
    }
  });


  // MASONRY - CREATE GRID WHEN IMAGES HAS DIFFERENT SIZE (height)
  if(alpMasonry == "1") {
    var $grid = $('.products .prod-wrap, #search-items .products, .products .wrap').masonry({
      itemSelector: '.simple-prod'
    });

    $grid.imagesLoaded().progress(function(){
      $grid.masonry('layout');
    });
  }


  // LAZY LOADING OF IMAGES
  if(alpLazy == "1" && alpMasonry == "0" ) {
    $('img.lazy').Lazy({
      effect: "fadeIn",
      effectTime: 300,
      afterLoad: function(element) {
        setTimeout(function() {
          element.css('transition', '0.2s');
        }, 300);
      }
    });
  }


  // PRINT ITEM
  $('body').on('click', 'a.print', function(e){
    e.preventDefault();
    window.print();
  });


  // IF LABEL CONTAINS LINK, OPEN IT WITHOUT ANY ACTION
  $(document).on('click', 'label a', function(e){
    if($(this).attr('href') != '#') {
      var newWin = window.open($(this).attr('href'), '_blank');
      newWin.focus();
      return false;
    }
  });


  // ENSURE ATTRIBUTE PLUGIN LABEL CLICK WORKS CORRECTLY
  $(document).on('click', 'input[type="checkbox"]:not([id^="bpr-cat-"]) + label', function(e){
    var inpId = $(this).attr('for');

    if(inpId != '') {
      var checkBox = $('input[type="checkbox"][id="' + inpId + '"]');

      if(!checkBox.length) {
        e.preventDefault();
        checkBox = $('input[type="checkbox"][name="' + inpId + '"]');
      }

      if(!checkBox.length) {
        e.preventDefault();
        checkBox = $(this).parent().find('input[type="checkbox"]');
      }

      if(checkBox.length) {
        e.preventDefault();
        checkBox.prop('checked', !checkBox.prop('checked'));
      }
    }
  });


  // ENSURE ATTRIBUTE PLUGIN LABEL CLICK WORKS CORRECTLY
  $(document).on('click', '.atr-radio label[for^="atr_"]', function(e){
    var checkBox = $('input[type="radio"][name="' + $(this).attr('for') + '"]');

    if(checkBox.length) {
      e.preventDefault();
      $(this).closest('ul.atr-ul-radio').find('input[type="radio"]:checked').not(this).prop('checked', false);
      checkBox.prop('checked', !checkBox.prop('checked'));
    }
  });


  // MORE FILTERS ON SEARCH PAGE
  $('body').on('click', '.show-hooks', function(e) {
    e.preventDefault();

    var textOpened = $(this).attr('data-opened');
    var textClosed = $(this).attr('data-closed');
 
    if($(this).hasClass('opened')) {
      $(this).removeClass('opened').find('span').text(textClosed);
      $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
      $('input[name="showMore"]').val(0);
      $('.sidebar-hooks').css('margin-top', '0px').css('margin-bottom', '0px').css('opacity', 1).animate( { opacity: 0, marginTop:'40px', marginBottom:'-40px'}, 300, function() { $('.sidebar-hooks').hide(0); });


    } else {
      $(this).addClass('opened').find('span').text(textOpened);
      $(this).find('i').addClass('fa-minus').removeClass('fa-plus');
      $('input[name="showMore"]').val(1);
      $('.sidebar-hooks').show(0).css('margin-top', '40px').css('margin-bottom', '-40px').css('opacity', 0).animate( { opacity: 1, marginTop:'0px', marginBottom:'0px'}, 300);

    }

  });

  
  // SCROLL TO TOP
  $('body').on('click', '#scroll-to-top', function(e) {
    e.preventDefault();
    $('html, body').animate({scrollTop: 0}, 600);
  });


/*
  // FIX LABEL FUNCTIONALITY
  $('body').on('click', 'label', function(e) {
    var elemFor = $(this).attr('for');
    var elem1 = $(this).parent().find('input[type="checkbox"][id="' + elemFor + '"]');
    var elem2 = $(this).find('input[type="radio"][id="' + elemFor + '"]');
    var elem3 = $(this).parent().find('input[type="radio"][id="' + elemFor + '"]');
    var elem4 = $(this).parent().find('input[type="checkbox"][name="' + elemFor + '"]');

    if(elem1.length) {
      elem1.prop('checked', !elem1.prop('checked')).change();

    } else if (elem2.length) {
      elem2.prop('checked', !elem2.prop('checked')).change();

    } else if (elem3.length) {
      elem3.prop('checked', !elem3.prop('checked')).change();

    } else if (elem4.length) {
      elem4.prop('checked', !elem4.prop('checked')).change();

    }
  });
*/


  // REFINE SEARCH - CLOSE BUTTON
  $('body').on('click', '.ff-close', function(e) {
    e.preventDefault();
    
    if(jqueryVersion == '1') {
      parent.$.fancybox.close();
    } else {
      parent.$.fancybox.close();
    }
  });


  // REFINE SEARCH - MOBILE
  $('body').on('click', '.filter-button', function(e) {
    e.preventDefault();

    
    if(jqueryVersion == '1') {
      if (!!$.prototype.fancybox) {
        $.fancybox({
          'padding':  0,
          'width':    320,
          'height':   640,
          'autoSize': false,
          'autoDimensions': false,
          'scrolling': 'yes',
          'closeBtn': true,
          'wrapCSS':  'pict-func',
          'content':  '<div class="filter-fancy"">' + $('#filter').html() + '</div>'
        });
      }
    } else {
      if (!!$.prototype.fancybox) {
        $.fancybox.open({
          toolbar : true,
          type: 'inline',
          smallBtn: false,
          src: '<div style="width:320px;height:640px;padding:0;"><div class="pict-func"><div class="filter filter-fancy"">' + $('#filter').html() + '</div></div></div>'
        });
      }      
    }
  });



  // MOBILE USER MENU
  $('body').on('click', '.user-button', function(e) {
    e.preventDefault();

    var elem = $(this);

    if(elem.hasClass('opened')) {
      $('#user-menu').css('margin-top', '0px').css('margin-bottom', '0px').css('opacity', 1).animate( { opacity: 0, marginTop:'40px', marginBottom:'-40px'}, 300, function() { $('#user-menu').hide(0); });
      elem.removeClass('opened');

    } else {
      $('#user-menu').show(0).css('margin-top', '40px').css('margin-bottom', '-40px').css('opacity', 0).animate( { opacity: 1, marginTop:'0px', marginBottom:'0px'}, 300);
      elem.addClass('opened');

    }    
  });


  // MOBILE USER MENU - CLICK OUTSIDE
  if (($(window).width() + scrollCompensate()) < 768) {
    $(document).mouseup(function (e){
      var container = $('.user-menu-wrap');
      var elem = container.find('.user-button');

      if (!container.is(e.target) && container.has(e.target).length === 0) {
        $('#user-menu').css('margin-top', '0px').css('margin-bottom', '0px').css('opacity', 1).animate( { opacity: 0, marginTop:'40px', marginBottom:'-40px'}, 300, function() { $('#user-menu').hide(0); });
        elem.removeClass('opened');
      }
    });
  }



  // MOBILE BLOCKS
  $('body').on('click', '.mobile-block a', function(e) {
    e.preventDefault();

    var elem = $(this);
    var elemId = elem.attr('id');
    var elemMenuId = elem.attr('data-menu-id');

    if(elem.hasClass('opened')) {
      var isOpened = true;
    } else {
      var isOpened = false;
    }

    if(isOpened) {
      $('#menu-cover').fadeOut(300);
      elem.removeClass('opened');
      $('.mobile-box' + elemMenuId).removeClass('opened').css('margin-left', '0px').css('margin-right', '0px').css('opacity', 1).animate( { opacity: 0, marginLeft:'80px', marginRight:'-80px'}, 300, function() { $('.mobile-box' + elemMenuId).hide(0); });

    } else {
      $('#menu-cover').fadeIn(300);
      elem.addClass('opened');
      $('.mobile-box' + elemMenuId).show(0).addClass('opened').css('margin-left', '80px').css('margin-right', '-80px').css('opacity', 0).animate( { opacity: 1, marginLeft:'0px', marginRight:'0px'}, 300);

    }    

  });


  // CLOSE MOBILE MENU
  $('body').on('click', '.mobile-box a.mclose, #menu-cover', function(e) {
    e.preventDefault();
    $('#menu-cover').fadeOut(300);
    $('.mobile-block a').removeClass('opened');
    $('.mobile-box.opened').removeClass('opened').css('margin-left', '0px').css('margin-right', '0px').css('opacity', 1).animate( { opacity: 0, marginLeft:'80px', marginRight:'-80px'}, 300, function() { $('.mobile-box').hide(0); });
  });





  // USER ACCOUNT - ALERTS SHOW HIDE
  $('body').on('click', '.alerts .alert .menu', function(e) {
    e.preventDefault();

    var elem = $(this).closest('.alert');
    var blocks = elem.find('.param, #alert-items');

    if(elem.hasClass('opened')) {
      blocks.css('opacity', 1).css('margin-top', '0px').css('margin-bottom', '0px').animate( { opacity: 0, marginTop:'40px', marginBottom:'-40px'}, 300, function() { blocks.hide(0); });
      elem.removeClass('opened');

    } else {
      blocks.show(0).css('opacity', 0).css('margin-top', '40px').css('margin-bottom', '-40px').animate( { opacity: 1, marginTop:'0px', marginBottom:'0px'}, 300);
      elem.addClass('opened');

    }

    return false;
  });


  // PROFILE PICTURE - OPEN BOX
  $(document).on('click', '.update-avatar', function(e){
    e.preventDefault();

   
    if(jqueryVersion == '1') {
      if (!!$.prototype.fancybox) {
        $.fancybox({
          'padding':  0,
          'width':    320,
          'height':   425,
          'autoSize': false,
          'autoDimensions': false,
          'closeBtn' : true,
          'wrapCSS':  '',
          'content':  $('#show-update-picture-content').html()
        });
      }
    } else {
      if (!!$.prototype.fancybox) {
        $.fancybox.open({
          toolbar : true,
          type: 'inline',
          smallBtn: false,
          src: '<div style="width:320px;height:425px;padding:0;">' + $('#show-update-picture-content').html() + '</div>'
        });
      }      
    }
  });


  // USER ACCOUNT - MY PROFILE SHOW HIDE
  $('body').on('click', '.body-ua #main.profile h3', function(e) {
    e.preventDefault();
    $(this).siblings('form').slideToggle(200);
  });


  // POST-EDIT - CHANGE LOCALE
  $('body').on('click', '.locale-links a', function(e) {
    e.preventDefault();

    var locale = $(this).attr('data-locale');
    var localeText = $(this).attr('data-name');
    $('.locale-links a').removeClass('active');
    $(this).addClass('active');

    if($('.tabbertab').length > 0) {
      $('.tabbertab').each(function() {
        if($(this).find('[id*="' + locale + '"]').length || $(this).find('h2').text() == localeText) {
          $(this).removeClass('tabbertabhide').show(0).css('opacity', 0).css('margin-top', '40px').css('margin-bottom', '-40px').animate( { opacity: 1, marginTop:'0px', marginBottom:'0px'}, 300);
        } else {
          $(this).addClass('tabbertabhide').hide(0);
        }
      });
    }

  });


  // PUBLISH PAGE - SWITCH PRICE
  $('body').on('click', '.price-wrap .selection a', function(e) {
    e.preventDefault();

    var price = $(this).attr('data-price');

    $('.price-wrap .selection a').removeClass('active');
    $(this).addClass('active');
    $('.price-wrap .enter').addClass('disable');
    $('.post-edit .price-wrap .enter #price').val(price).attr('placeholder', '');
  });

  $('body').on('click', '.price-wrap .enter .input-box', function(e) {
    $('.price-wrap .selection a').removeClass('active');
    $(this).parent().removeClass('disable');
    $('.post-edit .price-wrap .enter #price').val('').attr('placeholder', '');

  });


  // ITEM LIGHTBOX
  if(typeof $.fn.lightGallery !== 'undefined') { 
    $('.bx-slider').lightGallery({
      mode: 'lg-slide',
      thumbnail:true,
      cssEasing : 'cubic-bezier(0.25, 0, 0.25, 1)',
      selector: 'a',
      getCaptionFromTitleOrAlt: false,
      download: false,
      thumbWidth: 90,
      thumbContHeight: 80,
      share: false
    }); 
  }


  // ITEM BX SLIDER
  if(typeof $.fn.bxSlider !== 'undefined') { 
    $('.bx-slider').bxSlider({
      slideWidth: $(window).outerWidth(),
      infiniteLoop: false,
      slideMargin: 0,
      pager: true,
      pagerCustom: '.item-bx-pager',
      touchEnabled: false,
      onSlideBefore: function($elem, oldIndex, newIndex) {

        if(newIndex == 0) { 
          $('a.bx-prev').stop(true,true).fadeOut(200);
          $('a.bx-next').stop(true,true).fadeIn(200);
        } else if(newIndex+1 == parseInt($('ul.bx-slider').find('li').length)) { 
          $('a.bx-prev').stop(true,true).fadeIn(200);
          $('a.bx-next').stop(true,true).fadeOut(200);
        } else {
          $('a.bx-prev').stop(true,true).fadeIn(200);
          $('a.bx-next').stop(true,true).fadeIn(200);
        }

      }
    });

    if($('ul.bx-slider').find('li').length <= 1) {
      $('.bx-controls').hide(0);
    }
  }



  // AJAX - SUBMIT ITEM FORM (COMMENT / SEND FRIEND / PUBLIC CONTACT / SELLER CONTACT)
  $('body').on('click', 'button.item-form-submit', function(e){
    if(ajaxForms == 1) {

      var button = $(this);
      var form = $(this).closest('form');
      var inputs = form.find('input, select, textarea');
      var formType = $(this).attr('data-type');

      // Validate form first
      inputs.each(function(){
        form.validate().element($(this));
      });


      // non functional: (form.serialize()).replace(/&?[^=]+=&|&[^=]+=$/g,'')
      // functional:     (form.serialize()).replace(/[^&]+=\.?(?:&|$)/g, '') 
      // alternative:    form.find(":input").filter(function () { return $.trim(this.value).length > 0}).serialize()
      if(form.valid()) {
        button.addClass('btn-loading').attr('disabled', true);

        $.ajax({
          url: form.attr('action'),
          type: "POST",
          data: form.find(":input").filter(function () { return $.trim(this.value).length > 0}).serialize(),
          success: function(response){
            button.removeClass('btn-loading').attr('disabled', false);

            var type = $(response).contents().find('.flashmessage');
            var message = $(response).contents().find('.flash-wrap').text().trim();

            message = message.substring(1, message.length);
            inputs.val("").removeClass('valid');

            if(form.find('#recaptcha').length) { 
              grecaptcha.reset(); 
            }

            if(type.hasClass('flashmessage-error')) {
              alpAddFlash(message, 'error', true); 
            } else {
              alpAddFlash(message, 'ok', true); 
            }

            if(jqueryVersion == '1') {
              parent.$.fancybox.close();
            } else {
              parent.$.fancybox.close();
            }
          }
        });
      }
    }
  });



  // FANCYBOX - OPEN ITEM FORM (COMMENT / SEND FRIEND / PUBLIC CONTACT / SELLER CONTACT)
  $('body').on('click', '.open-form', function(e) {
    e.preventDefault();
    var height = 540;
    var url = $(this).attr('href');
    var formType = $(this).attr('data-type');

    if(formType == 'friend') {
      height = 640;
    }

    
    if(jqueryVersion == '1') {
      if (!!$.prototype.fancybox) {
        $.fancybox({
          'padding': 0,
          'width': 400,
          'height': height,
          'scrolling': 'yes',
          'wrapCSS': 'fancy-form',
          'closeBtn': true,
          'type': 'iframe',
          'href': url
        });

      }
    } else { 
      height = height.toString() + 'px';

      if (!!$.prototype.fancybox) {
        $.fancybox.open({
          toolbar : true,
          type: 'iframe',
          src: url,
          baseClass: 'fancy-form',
          iframe: {
            css: {
              width : '400px',
              height : height,
              padding: 0
            }
          }
        });
      }
    }
  });

  // CONTACT FORM - ADD REQUIRED PROPERTY
  $('body#body-contact input[name="subject"], body#body-contact textarea[name="message"]').prop('required', true);


  // ATTACHMENT - FIX FILE NAME
  $('body').on('change', '.att-box input[type="file"]', function(e) {
    if( $(this)[0].files[0]['name'] != '' ) {
      $(this).closest('.att-box').find('.att-text').text($(this)[0].files[0]['name']);
    }
  });


  // HIDE FLASH MESSAGE MANUALLY
  $('body').on('click', '.flashmessage .ico-close', function(e) {
    e.preventDefault();

    var elem = $(this).closest('.flashmessage');

    elem.show(0).css('opacity', 1).css('margin-top', '0px').css('margin-bottom', '0px').animate( { opacity: 0, marginTop:'30px', marginBottom:'-30px'}, 300);

    window.setTimeout(function() {
      elem.remove();
    }, 300);

    return false;
  });


  // HIDE FLASH MESSAGES AUTOMATICALLY
  window.setTimeout(function(){ 
    $('.flash-wrap .flashmessage:not(.js)').css('opacity', 1).css('margin-top', '0px').css('margin-bottom', '0px').animate( { opacity: 0, marginTop:'30px', marginBottom:'-30px'}, 300);

    window.setTimeout(function() {
      $('.flash-wrap .flashmessage:not(.js)').remove();
    }, 300);
  }, 10000);


  // LOCATION PICKER - SHOW LIST OF LOCATIONS WHEN CLICK ON TERM
  $('body').on('click', '.loc-picker .term', function() {
    if(!$(this).hasClass('open')) {
      $(this).closest('.loc-picker').find('.shower').show(0).css('opacity', 0).css('margin-top', '30px').css('margin-bottom', '-30px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} , 300);
      $(this).closest('.loc-picker').find('.term').addClass('open');
    }
  });


  // LOCATION PICKER - WHEN CLICK OUTSIDE LOCATION PICKER, HIDE SELECTION
  $(document).mouseup(function (e){
    var container = $('.loc-picker');
    var form = container.closest('form');

    if (!container.is(e.target) && container.has(e.target).length === 0 && container.find('.term').hasClass('open')) {
      if(container.find('.term').val() == '' && container.find('.term').hasClass('open') && ( form.find('input[name="sCountry"]').val() != '' || form.find('input.sCountry').val() != '' || form.find('input[name="sRegion"]').val() != '' || form.find('input.sRegion').val() != '' || form.find('input[name="sCity"]').val() != '' || form.find('input.sCity').val() != '' )) {
        $('input[name="sCountry"], input.sCountry, input[name="sRegion"], input.sRegion, input[name="sCity"], input.sCity').val("");
        $('input[name="sCity"]').change();
      }

      container.find('.shower').fadeOut(0);
      container.find('.term').removeClass('open');
    }
  });



  // LOCATION PICKER - CLICK FUNCTIONALITY
  $('body').on('click', '.loc-picker .shower .option', function() {
    var container = $(this).closest('.loc-picker');

    if( !$(this).hasClass('empty-pick') && !$(this).hasClass('more-pick') && !$(this).hasClass('service') ) {

      container.find('.shower .option').removeClass('selected');
      $(this).addClass('selected');
      container.find('.shower').fadeOut(0);
      container.find('.term').removeClass('open');


      var term = $(this).find('strong').text();
      $('input.term').val( term );

      $('input[name="sCountry"], input.sCountry').val( $(this).attr('data-country') );
      $('input[name="sRegion"], input.sRegion').val( $(this).attr('data-region') );
      $('input[name="sCity"], input.sCity').val( $(this).attr('data-city') );
      $('input[name="sCity"]').change();
    }
  });



  // SIMPLE SELECT - CLICK ELEMENT FUNCTIONALITY
  $('body').on('click', '.simple-select:not(.disabled) .option:not(.info):not(.nonclickable)', function() {
    $(this).parent().parent().find('input.input-hidden').val( $(this).attr('data-id') ).change();
    $(this).parent().parent().find('.text span').html( $(this).html() );
    $(this).parent().parent().find('.option').removeClass('selected');
    $(this).addClass('selected');
    $(this).parent().hide(0).removeClass('opened');

    $(this).closest('.simple-select').removeClass('opened');
  });


  // SIMPLE SELECT - OPEN MENU
  $('body').on('click', '.simple-select', function(e) {
    if(!$(this).hasClass('disabled') && !$(this).hasClass('opened') && !$(e.target).hasClass('option')) {
      $('.simple-select').not(this).removeClass('opened');

      $('.simple-select .list').hide(0);
      $(this).addClass('opened');
      $(this).find('.list').show(0).css('opacity', 0).css('margin-top', '30px').css('margin-bottom', '-30px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} , 300);
    }
  });


  // SIMPLE SELECT - HIDE WHEN CLICK OUTSIDE
  $(document).mouseup(function(e){
    var container = $('.simple-select');

    if (!container.is(e.target) && container.has(e.target).length === 0) {
      $('.simple-select').removeClass('opened');
      $('.simple-select .list').hide(0);
    }
  });


  // SIMPLE SELECT - NONCLICKABLE, ADD TITLE
  $('.simple-select .option.nonclickable').attr('title', alpTitleNc);


  
  // REGISTER FORM - SWAP FUNCTIONALITY
  $('body').on('click', '#i-forms .swap a', function(e){
    e.preventDefault();

    var boxType = $(this).attr('data-type');

    $('#i-forms .box').hide(0);
    $('#i-forms .box[data-type="' + boxType + '"]').show(0).css('opacity', 0).css('margin-top', '50px').css('margin-bottom', '-50px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} );

    $('html, body').animate({ scrollTop: 0}, 300);
  });


  // TABS SWITCH - HOME PAGE
  $('body').on('click', '.home-container.tabs a.tab', function(e){
    e.preventDefault();

    var tabId = $(this).attr('data-tab');

    $('.home-container.tabs a.tab').removeClass('active');
    $(this).addClass('active');

    $('.home-container .single-tab').hide(0);
    $('.home-container .single-tab[data-tab="' + tabId + '"]').show(0).css('opacity', 0).css('margin-top', '50px').css('margin-bottom', '-50px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} );


    // Trigger images to lazy load
    if(alpLazy == "1") {
      $(window).scrollTop($(window).scrollTop()+1);
      $(window).scrollTop($(window).scrollTop()-1);
    }

    // Resize when masonry
    if(alpMasonry == "1") {
      $grid.masonry();
    }
  });


  // LIST OR GRID VIEW
  $('body').on('click', '.list-grid a', function(e){
    e.preventDefault();

    if(!$(this).hasClass('active')) {
      var show = $(this).attr('data-view');

      $('.list-grid a').removeClass('active');
      $(this).addClass('active');

      $('#search-items .products').removeClass('list').removeClass('grid').addClass(show);
      $('input[name="sShowAs"]').val(show);

      if(alpMasonry == "1") {
        $('#search-items').addClass('no-transition');
        setTimeout(function() {
          $('#search-items').removeClass('no-transition')
        }, 500);
      }

      var href = $(this).attr('href');

      if(href != '') {
        var newUrl = href;
      } else {
        var newUrl = baseDir + 'index.php?' + $('form.search-side-form :input[value!=""], form.search-side-form select, form.search-side-form textarea').serialize();
      }

      window.history.pushState(null, null, newUrl);

    }

    if($('.paginate').length) {
      $('.paginate a, .user-type a, .sort-it a').each(function() {
        var url = $(this).attr('href');

        if(!url.indexOf("index.php") >= 0 && url.match(/\/$/)) {
          if(url.substr(-1) !== '/') {
            url = url + '/'; 
          }
        }

        if(url.indexOf("sShowAs") >= 0) {
          var newUrl = url.replace(/(sShowAs,).*?(\/)/,'$1' + show + '$2').replace(/(sShowAs,).*?(\/)/,'$1' + show + '$2');
        } else {
          if(url.indexOf("index.php") >= 0) {
            var newUrl = url + '&sShowAs=' + show;
          } else {
             var newUrl = url + '/sShowAs,' + show + '/';
          }
        }

        $(this).attr('href', newUrl);
      });
    }

    // MASONRY - CREATE GRID WHEN IMAGES HAS DIFFERENT SIZE (height)
    if(alpMasonry == "1") {
      var $grid = $('.products .prod-wrap, #search-items .products, .products .wrap').masonry({
        itemSelector: '.simple-prod'
      });

      $grid.imagesLoaded().progress(function(){
        $grid.masonry('layout');
      });
    }
  });





  // AJAX SEARCH
  $('body#body-search').on('change click', '.filter-remove a, form.search-side-form input:not(.term), body#body-search #sub-nav a, #home-cat a, #sub-cat a, form.search-side-form select, .sort-it a, .user-type a, .paginate a', function(event) {
    if(ajaxSearch == 1 && event.type != 'change') {
      event.preventDefault();
    }

    // Disable on mobile devices when input selected from fancybox
    if(($(window).width() + scrollCompensate()) < 768) {
      if($(event.target).closest('.filter-fancy').length) {
        return false;
      }
    }

    var sidebarReload = true;

    if($(this).closest('.sidebar-hooks').length) {
      sidebarReload = false;
    }

    var sidebar = $('#filter form.search-side-form');
    var ajaxSearchUrl = '';

    if (event.type == 'click') {
      if(typeof $(this).attr('href') !== typeof undefined && $(this).attr('href') !== false) {
        ajaxSearchUrl = $(this).attr('href');
      }
    } else if (event.type == 'change') {
      //ajaxSearchUrl = baseDir + "index.php?" + sidebar.find(':input[value!=""]').serialize();
      ajaxSearchUrl = baseDir + "index.php?" + sidebar.find(":input").filter(function () { return $.trim(this.value).length > 0}).serialize();
    }


    if(ajaxSearch == 1 && $('input[name="ajaxRun"]').val() != "1" && (ajaxSearchUrl != '#' && ajaxSearchUrl != '') ) {
      if(ajaxSearchUrl == $(location).attr('href')) {
        return false;
      }

      sidebar.find('.init-search').addClass('btn-loading').addClass('disabled').attr('disabled', true);
      sidebar.find('input[name="ajaxRun"]').val("1");
      $('#search-items').addClass('loading');


      $.ajax({
        url: ajaxSearchUrl,
        type: "GET",
        success: function(response){
          var length = response.length;

          var data = $(response).contents().find('#main').html();
          var bread = $(response).contents().find('ul.breadcrumb');
          var filter = $(response).contents().find('#filter').html();

          sidebar.find('.init-search').removeClass('btn-loading').removeClass('disabled').attr('disabled', false);
          sidebar.find('input[name="ajaxRun"]').val("");

          $('#main').fadeOut(0, function(){ 
            $('#main').html(data).show(0);

            $('#search-items').hide(0);
            $('#search-items').removeClass('loading');
            $('#search-items').show(0).css('opacity', 0).css('margin-top', '50px').css('margin-bottom', '-50px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} , 300);

          });

          if(sidebarReload) {
            $('#filter').html(filter);
          }
          
          $('ul.breadcrumb').html(bread);


          // LAZY LOADING OF IMAGES
          if(alpLazy == "1" && alpMasonry == "0" ) {
            $('#search-items img.lazy').Lazy({
              effect: "fadeIn",
              effectTime: 300,
              afterLoad: function(element) {
                setTimeout(function() {
                  element.css('transition', '0.2s');
                }, 300);
              }
            });
          }
          
          // Update URL
          window.history.pushState(null, null, ajaxSearchUrl);

          if (($(window).width() + scrollCompensate()) >= 768) {
            $('body,html').animate({ scrollTop: $('#filter').offset().top-15 }, 800);
          } else {
            $('body,html').animate({ scrollTop: $('#search-sort').offset().top-10 }, 800);
          }
        },

        error: function(response){
          sidebar.find('.init-search').removeClass('btn-loading').removeClass('disabled').attr('disabled', false);
          sidebar.find('input[name="ajaxRun"]').val("");

          response = response.responseText;

          var data = $(response).contents().find('#main').html();
          var bread = $(response).contents().find('ul.breadcrumb');
          var filter = $(response).contents().find('#filter').html();

          $('#main').fadeOut(0, function(){ 
            $('#main').html(data).show(0);

            $('#search-items').hide(0);
            $('#search-items').removeClass('loading');
            $('#search-items').show(0).css('opacity', 0).css('margin-top', '50px').css('margin-bottom', '-50px').animate( { opacity: 1, marginTop:'0', marginBottom:'0'} , 300);

          });

          if(sidebarReload) {
            $('#filter').html(filter);
          }

          $('ul.breadcrumb').html(bread);


          // LAZY LOADING OF IMAGES
          if(alpLazy == "1" && alpMasonry == "0" ) {
            $('#search-items img.lazy').Lazy({
              effect: "fadeIn",
              effectTime: 300,
              afterLoad: function(element) {
                setTimeout(function() {
                  element.css('transition', '0.2s');
                }, 300);
              }
            });
          }

          // Update URL
          window.history.pushState(null, null, ajaxSearchUrl);

          if (($(window).width() + scrollCompensate()) >= 768) {
            $('body,html').animate({ scrollTop: $('#filter').offset().top-15 }, 800);
          } else {
            $('body,html').animate({ scrollTop: $('#search-sort').offset().top-10 }, 800);
          }
        }
      });

      return false;
    }
  });


});



// THEME FUNCTIONS
function alpAddFlash(text, type, parent = false) {
  var rand = Math.floor(Math.random() * 1000);
  var html = '<div id="flashmessage" class="flashmessage js flashmessage-' + type + ' rand-' + rand + '"><a class="btn ico btn-mini ico-close">x</a>' + text + '</div>';

  if(!parent) {
    $('.flash-box .flash-wrap').append(html);
  } else {
    $('.flash-box .flash-wrap', window.parent.document).append(html);
  }

}


// CALCULATE SCROLL WIDTH
function scrollCompensate() {
  var inner = document.createElement('p');
  inner.style.width = "100%";
  inner.style.height = "200px";

  var outer = document.createElement('div');
  outer.style.position = "absolute";
  outer.style.top = "0px";
  outer.style.left = "0px";
  outer.style.visibility = "hidden";
  outer.style.width = "200px";
  outer.style.height = "150px";
  outer.style.overflow = "hidden";
  outer.appendChild(inner);

  document.body.appendChild(outer);
  var w1 = inner.offsetWidth;
  outer.style.overflow = 'scroll';
  var w2 = inner.offsetWidth;
  if (w1 == w2) w2 = outer.clientWidth;

  document.body.removeChild(outer);

  return (w1 - w2);
}
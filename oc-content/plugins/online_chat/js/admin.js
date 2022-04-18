$(document).ready(function(){
  // CATEGORY MULTI SELECT
  $('.mb-row-select-multiple select').change(function(){
    $(this).closest('.mb-row-select-multiple').find('input[type="hidden"]').val($(this).val());
  });


  // IMAGE - FILE NAME
  $('input[type="file"]').change(function() {
    if( $(this)[0].files[0]['name'] != '' ) {
      $(this).parent().find('.wrap span').text( $(this)[0].files[0]['name'] );
    }
  });

  
  // TYPE OF BANNER SWITCH
  $('.mb-row select#type').change(function(){
    $('.mb-row').show(0);

    if( $(this).val() == 1 ) {
      // HTML banner
      $('.mb-row.image').hide(0);
      $('.mb-row label span.html').show(0);
      $('.mb-row label span.adsense').hide(0);
    } else if ( $(this).val() == 2 ) {
      // Image banner
      $('.mb-row.code').hide(0);
    } else {
      // Adsense banner
      $('.mb-row.image, .mb-row.key, .mb-row.url, .mb-row.price, .mb-row.date').hide(0);
      $('.mb-row label span.html').hide(0);
      $('.mb-row label span.adsense').show(0);
    }
  });

  $('.mb-row select#type').change();



  // HELP TOPICS
  $('#mb-help > .mb-inside > .mb-row.mb-help > div').each(function(){
    var cl = $(this).attr('class');
    $('label.' + cl + ' span').addClass('mb-has-tooltip').prop('title', $(this).text());
  });

  $('.mb-row label').click(function() {
    var cl = $(this).attr('class');
    var pos = $('#mb-help > .mb-inside > .mb-row.mb-help > div.' + cl).offset().top - $('.navbar').outerHeight() - 12;;
    $('html, body').animate({
      scrollTop: pos
    }, 1400, function(){
      $('#mb-help > .mb-inside > .mb-row.mb-help > div.' + cl).addClass('mb-help-highlight');
    });

    return false;
  });


  // ON-CLICK ANY ELEMENT REMOVE HIGHLIGHT
  $('body, body *').click(function(){
    $('.mb-help-highlight').removeClass('mb-help-highlight');
  });


  // GENERATE TOOLTIPS
  Tipped.create('.mb-has-tooltip', { maxWidth: 200, radius: false });
  Tipped.create('.mb-has-tooltip-user', { maxWidth: 350, radius: false, size: 'medium' });
  Tipped.create('.mb-add-tooltip', { maxWidth: 200, radius: false });


  // CHECKBOX & RADIO SWITCH
  $.fn.bootstrapSwitch.defaults.size = 'small';
  $.fn.bootstrapSwitch.defaults.labelWidth = '0px';
  $.fn.bootstrapSwitch.defaults.handleWidth = '50px';

  $(".element-slide").bootstrapSwitch();



  // MARK ALL
  $('input.mb_mark_all').click(function(){
    if ($(this).is(':checked')) {
      $('input[name^="' + $(this).val() + '"]').prop( "checked", true );
    } else {
      $('input[name^="' + $(this).val() + '"]').prop( "checked", false );
    }
  });

});
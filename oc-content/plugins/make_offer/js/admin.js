$(document).ready(function(){

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


  // CATEGORY MULTI SELECT
  $('body').on('change', '.mb-row-select-multiple select', function(e){
    $(this).closest('.mb-row-select-multiple').find('input[type="hidden"]').val($(this).val());
  });


  // ON-CLICK ANY ELEMENT REMOVE HIGHLIGHT
  $('body, body *').click(function(){
    $('.mb-help-highlight').removeClass('mb-help-highlight');
  });


  // GENERATE TOOLTIPS
  Tipped.create('.mb-has-tooltip', { maxWidth: 200, radius: false });
  Tipped.create('.mb-has-tooltip-user', { maxWidth: 350, radius: false, size: 'medium' });


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
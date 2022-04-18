<?php
  $attributes = ModelATR::newInstance()->getRequiredAttributes($cat_id);
  $fields = array();
  $used_ids = array();

  if(count($attributes) > 0) { 
    foreach($attributes as $a) { 
      if(!in_array($a['pk_i_id'], $used_ids)) { 
    
        $type = $a['s_type'];
        $name = ($a['s_name'] <> '' ? $a['s_name'] : ($a['s_identifier'] <> '' ? $a['s_identifier'] : __('Field ID:', 'attributes') . ' ' . $a['pk_i_id']));

        if($type == 'TEXT' || $type == 'TEXTAREA' || $type == 'DATE' || $type == 'EMAIL' || $type == 'URL' || $type == 'PHONE' || $type == 'NUMBER') {
          $identifier = '#atr_' . $a['pk_i_id'];
          $fields[] = array('identifier' => $identifier, 'name' => $name);

        } else if ($type == 'DATERANGE') {
          $identifier1 = '#atr_' . $a['pk_i_id'] . '_start';
          $identifier2 = '#atr_' . $a['pk_i_id'] . '_end';
          $name1 = $name . ' ' . __('(Start)', 'attributes');
          $name2 = $name . ' ' . __('(End)', 'attributes');

          $fields[] = array('identifier' => $identifier1, 'name' => $name1);
          $fields[] = array('identifier' => $identifier2, 'name' => $name2);

        } else if ($type == 'SELECT') {
          if(isset($a['values']) && count($a['values']) > 0) {
            //$identifier = 'input[name="atr_' . $a['pk_i_id'] . '"] + select';
            //$identifier = '#select_' . $a['pk_i_id'];    // both problematic, it shows message multiple times
            $identifier = 'input[name="atr_' . $a['pk_i_id'] . '"]';


            $fields[] = array('identifier' => $identifier, 'name' => $name);
          }

        } else if ($type == 'RADIO' || $type == 'CHECKBOX') {
          if(isset($a['values']) && count($a['values']) > 0) {
            $identifier = 'atr_' . $a['pk_i_id'] . '_';
            $fields[] = array('type' => $type, 'identifier' => $identifier, 'name' => $name);
          }
        }

        $used_ids[] = $a['pk_i_id'];
      }
    }
  } 
?>


<?php if(count($fields) > 0) { ?>
  <script>
    $(document).ready(function(){
      $.validator.addMethod("require_group", function(value, elem, options) {
        var selector = options[0];

        if($(selector + ":checked").length > 0){
          return true;
        } else {
          return false;
        }
      }, "<?php echo osc_esc_html(__('Select at least one option on field', 'attributes')); ?> {1}");



      // Uncaught TypeError: Cannot read property 'settings' of undefined  ==> means rule is added on element that does not exist
      setTimeout(function(){
        setInterval(function(){
          if ($.validator || $('form[name="item"]').data('validator')) {
            <?php foreach($fields as $f) { ?>
              <?php if(isset($f['type']) && ($f['type'] == 'RADIO' || $f['type'] == 'CHECKBOX')) { ?>
                if($('input[name^="<?php echo $f['identifier']; ?>"]').length) {
                  $('input[name^="<?php echo $f['identifier']; ?>"]').rules('add', {
                    require_group: ['input[name^="<?php echo $f['identifier']; ?>"]', '<?php echo osc_esc_js($f['name']); ?>']
                  });
                }
              <?php } else { ?>
                if($('<?php echo $f['identifier']; ?>').length) {
                  $('<?php echo $f['identifier']; ?>').rules('add', {
                    required: true,
                    messages: {required: '<?php echo osc_esc_js(sprintf(__('Required field: %s', 'attributes'), $f['name'])); ?>'}
                  });
                }
              <?php } ?>
            <?php } ?>

          }
        }, 500);
      }, 1500);
    });
  </script>
<?php } ?>
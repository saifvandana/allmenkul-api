<?php
  define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
  require_once ABS_PATH . 'oc-load.php';

  if(OSP_DEBUG) {
    $emailtext = "status => " . $status . "\r\n";
    $emailtext .= osp_array_to_string(Params::getParamsAsArray());
    mail(osc_contact_email() , 'OSCLASS PAY - PAYS.CZ CANCEL DEBUG RESPONSE', $emailtext);
  }

  $data = osp_get_custom(Params::getParam('extra'));
  $product_type = explode('x', $data['product']);

  osc_add_flash_error_message(__('You cancel the payment process or there was an error. If the error continue, please contact the administrator', 'osclass_pay'));
  osp_js_redirect_to(osp_pay_url_redirect($product_type));
?>
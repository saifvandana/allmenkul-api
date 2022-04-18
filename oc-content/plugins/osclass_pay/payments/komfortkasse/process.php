<?php
  define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
  require_once ABS_PATH . 'oc-load.php';

  $orders = KomfortkassePayment::readOrders();

  //print_r($orders);

  if(count($orders) > 0) {
    foreach($orders as $o) {

      // Known statues: UNPAID, PAID, CANCELLED
      if($o['paymentStatus'] <> 'UNPAID') {
        $status = ($o['paymentStatus'] == 'PAID' ? 'PAID' : 'FAILED');

        KomfortkassePayment::processPayment($o['number'], $status);
      }
    }
  }

?>
<?php
EmailVariables::newInstance()->add('{TO_NAME}', __('Name of user that will receive email notification', 'make_offer'));
EmailVariables::newInstance()->add('{FROM_NAME}', __('Name of user that has send offer', 'make_offer'));
EmailVariables::newInstance()->add('{ITEM_LINK}', __('Link to listing', 'make_offer'));
EmailVariables::newInstance()->add('{OFFER}', __('Offer details (price, quantity)', 'make_offer'));
EmailVariables::newInstance()->add('{OFFER_LINK}', __('Link to offer', 'make_offer'));



// Notify buyer about status on offer
function mo_notify_buyer( $offer_id ) {
  mo_include_mailer();

  $page = new Page() ;
  $page = $page->findByInternalName('mo_notify_buyer');
  if(empty($page)) { exit(); }

  $locale = osc_current_user_locale() ;
  $content = array();
  if(isset($page['locale'][$locale]['s_title'])) {
    $content = $page['locale'][$locale];
  } else {
    $content = current($page['locale']);
  }

  $offer = ModelMO::newInstance()->getOfferById($offer_id);
  $item_id = $offer['fk_i_item_id'];
  $item = Item::newInstance()->findByPrimaryKey($item_id);

  $show_quantity = osc_get_preference('show_quantity', 'plugin-make_offer') <> '' ? osc_get_preference('show_quantity', 'plugin-make_offer') : 0;

  $currency_full = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);
  $currency_symbol = isset($currency_full['s_description']) ? $currency_full['s_description'] : '';



  $item_title = stripslashes(strip_tags(osc_highlight($item['s_title'], 35)));
  $item_url  = '<a href="' . osc_item_url_ns($item_id) . '" >' . $item_title . '</a>';
  $offer_url  = osc_route_url('mo-offers');

  $offer_detail  = '';

  if($show_quantity == 1) {
    $offer_detail .= __('Quantity', 'make_offer') . ': <strong>' . $offer['i_quantity'] . 'x</strong><br/>';
  }

  $offer_detail .= __('Price', 'make_offer') . ': <strong>' . round($offer['i_price']/1000000, 2) . $currency_symbol . '</strong><br/>';
  $offer_detail .= __('User', 'make_offer') . ': <strong>' . $offer['s_user_name'] . '</strong><br/>';
  //$offer_detail .= __('User Email', 'make_offer') . ': <strong>' . $offer['s_user_email'] . '</strong><br/>';
  //$offer_detail .= __('User Phone', 'make_offer') . ': <strong>' . $offer['s_user_phone'] . '</strong><br/>';
  $offer_detail .= __('Comment', 'make_offer') . ': <strong>' . $offer['s_comment'] . '</strong><br/>';
  $offer_detail .= '<br/>';
  $offer_detail .= __('Respond', 'make_offer') . ': <strong>' . $offer['s_respond'] . '</strong><br/>';

  $offer_status = mo_status_name($offer['i_status']);

  $words   = array();
  $words[] = array( '{TO_NAME}', '{FROM_NAME}', '{ITEM_TITLE}', '{ITEM_LINK}', '{OFFER}', '{OFFER_STATUS}', '{OFFER_LINK}', '{WEB_TITLE}' );
  $words[] = array( $offer['s_user_name'], $item['s_contact_name'], $item_title, $item_url, $offer_detail, $offer_status, $offer_url, stripslashes(strip_tags(osc_page_title())) ) ;

  $title = osc_mailBeauty($content['s_title'], $words) ;
  $body  = osc_mailBeauty($content['s_text'], $words) ;

  $email_build = array(
    'subject'  => $title, 
    'to' => $offer['s_user_email'], 
    'to_name'  => $offer['s_user_name'],
    'body' => $body,
    'alt_body' => $body
  );

  osc_sendMail($email_build);
}



// Notify seller about new offer
function mo_notify_seller( $offer_id ) {
  mo_include_mailer();

  $page = new Page() ;
  $page = $page->findByInternalName('mo_notify_seller');
  if(empty($page)) { exit(); }

  $locale = osc_current_user_locale() ;
  $content = array();
  if(isset($page['locale'][$locale]['s_title'])) {
    $content = $page['locale'][$locale];
  } else {
    $content = current($page['locale']);
  }

  $offer = ModelMO::newInstance()->getOfferById($offer_id);
  $item_id = $offer['fk_i_item_id'];
  $item = Item::newInstance()->findByPrimaryKey($item_id);

  $show_quantity = osc_get_preference('show_quantity', 'plugin-make_offer') <> '' ? osc_get_preference('show_quantity', 'plugin-make_offer') : 0;

  $currency_full = Currency::newInstance()->findByPrimaryKey($item['fk_c_currency_code']);
  $currency_symbol = isset($currency_full['s_description']) ? $currency_full['s_description'] : '';



  $item_title = stripslashes(strip_tags(osc_highlight($item['s_title'], 35)));
  $item_url  = '<a href="' . osc_item_url_ns($item_id) . '" >' . $item_title . '</a>';
  $offer_url  = osc_route_url('mo-offers');

  $offer_detail  = '';

  if($show_quantity == 1) {
    $offer_detail .= __('Quantity', 'make_offer') . ': <strong>' . $offer['i_quantity'] . 'x</strong><br/>';
  }

  $offer_detail .= __('Price', 'make_offer') . ': <strong>' . round($offer['i_price']/1000000, 2) . $currency_symbol . '</strong><br/>';
  $offer_detail .= __('User', 'make_offer') . ': <strong>' . $offer['s_user_name'] . '</strong><br/>';
  //$offer_detail .= __('User Email', 'make_offer') . ': <strong>' . $offer['s_user_email'] . '</strong><br/>';
  //$offer_detail .= __('User Phone', 'make_offer') . ': <strong>' . $offer['s_user_phone'] . '</strong><br/>';
  $offer_detail .= __('Comment', 'make_offer') . ': <strong>' . $offer['s_comment'] . '</strong><br/>';


  $words   = array();
  $words[] = array( '{TO_NAME}', '{FROM_NAME}', '{ITEM_TITLE}', '{ITEM_LINK}', '{OFFER}', '{OFFER_LINK}', '{WEB_TITLE}' );
  $words[] = array( $item['s_contact_name'], $offer['s_user_name'], $item_title, $item_url, $offer_detail, $offer_url, stripslashes(strip_tags(osc_page_title())) ) ;

  $title = osc_mailBeauty($content['s_title'], $words) ;
  $body  = osc_mailBeauty($content['s_text'], $words) ;

  $email_build = array(
    'subject'  => $title, 
    'to' => $item['s_contact_email'], 
    'to_name'  => $item['s_contact_name'],
    'body' => $body,
    'alt_body' => $body
  );

  osc_sendMail($email_build);
}

?>
<?php

class ModelMO extends DAO {
private static $instance;

public static function newInstance() {
  if( !self::$instance instanceof self ) {
    self::$instance = new self ;
  }
  return self::$instance ;
}

function __construct() {
  parent::__construct();
}

public function import($file) {
  $path = osc_plugin_resource($file);
  $sql = file_get_contents($path);
  if(!$this->dao->importSQL($sql)){ throw new Exception("Error importSQL::ModelMO<br>".$file.'<br>'.$path.'<br><br>Please check your database for if there are no plugin tables. <br>If any of those tables exists in your database, drop them!');} 
}
 
public function uninstall() {
  $this->dao->query('DROP TABLE '. $this->getTable_Offer());
}

public function getTable_Offer() {
  return DB_TABLE_PREFIX.'t_item_offer';
}

public function getTable_OfferSetting() {
  return DB_TABLE_PREFIX.'t_item_offer_setting';
}

public function getTable_User() {
  return DB_TABLE_PREFIX.'t_user';
}

public function getTable_Item() {
  return DB_TABLE_PREFIX.'t_item';
}

public function getTable_ItemDescription() {
  return DB_TABLE_PREFIX.'t_item_description';
}

public function getTable_Page() {
  return DB_TABLE_PREFIX.'t_pages';
}

public function getTable_Picture(){
  return DB_TABLE_PREFIX.'t_profile_picture';
}



public function getPages() {
  $this->dao->select('pk_i_id');
  $this->dao->from( $this->getTable_Page() );
  $this->dao->where('s_internal_name like "mo_%"');

  $result = $this->dao->get();

  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}


// GET PICTURE FROM PROFILE PICTURE PLUGIN
public function getPictureByUserId( $user_id ) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Picture() );
  $this->dao->where('user_id', $user_id);

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}


// INSERT NEW OFFER TO DATABASE
public function insertOffer( $item_id, $quantity, $price, $status, $validate, $comment, $user_id, $user_name, $user_email, $user_phone ) {
  $aSet = array(
    'fk_i_item_id' => $item_id,
    'i_quantity' => $quantity,
    'i_price' => $price,
    'i_status' => $status,
    'i_validate' => $validate,
    's_comment' => $comment,
    'i_user_id' => $user_id,
    's_user_name' => $user_name,
    's_user_email' => $user_email,
    's_user_phone' => $user_phone,
    'd_datetime' => date("Y/m/d H:i:s")
  );

  $this->dao->insert( $this->getTable_Offer(), $aSet);
  return $this->dao->insertedId();
}


public function sellerManageOffer( $offer_id, $status_id, $respond ) {
  $aSet = array(
    'i_status' => $status_id,
    's_respond' => $respond
  );

  $aWhere = array('i_offer_id' => $offer_id);

  return $this->_update($this->getTable_Offer(), $aSet, $aWhere);
}



public function updateOffer( $id, $field, $value ) {
  $aSet = array(
    $field => $value
  );

  $aWhere = array('i_offer_id' => $id);

  return $this->_update($this->getTable_Offer(), $aSet, $aWhere);
}


public function getOfferSettingByItemId( $item_id ) {
  $this->dao->select();
  $this->dao->from( $this->getTable_OfferSetting() );
  $this->dao->where('fk_i_item_id', $item_id);

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}


public function insertOfferSetting( $item_id, $enabled ) {
  $aSet = array(
    'fk_i_item_id' => $item_id,
    'i_enabled' => $enabled    
  );

  $this->dao->insert( $this->getTable_OfferSetting(), $aSet);
  return $this->dao->insertedId();
}


public function updateOfferSetting( $item_id, $enabled ) {
  $setting = $this->getOfferSettingByItemId($item_id);

  if(!isset($setting['i_enabled'])) {
    return $this->insertOfferSetting( $item_id, $enabled );
  } else {
    $aSet = array(
      'i_enabled' => $enabled
    );

    $aWhere = array('fk_i_item_id' => $item_id);
    return $this->_update($this->getTable_OfferSetting(), $aSet, $aWhere);
  }
}

public function removeOfferSetting( $item_id ) {
  $this->dao->query('DELETE FROM '. $this->getTable_OfferSetting() . ' WHERE fk_i_item_id = ' . $item_id);
}


// GET ALL ITEMS OF USER WITH OFFERS
public function getItemsWithOffersByUserId( $user_id, $validate = NULL ) {
  $this->dao->select('distinct i.*, d.*');
  $this->dao->from( $this->getTable_Offer() . ' o, ' . $this->getTable_Item() . ' i, ' . $this->getTable_ItemDescription() . ' d' );
  $this->dao->where('i.pk_i_id = o.fk_i_item_id' );
  $this->dao->where('i.pk_i_id = d.fk_i_item_id' );
  $this->dao->where('d.fk_c_locale_code', osc_current_user_locale() );
  $this->dao->where('i.fk_i_user_id', $user_id );

  if($validate <> '') {
    $this->dao->where('o.i_validate', $validate);
  }

  $this->dao->orderby('i.pk_i_id desc');

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}


// GET ALL ITEMS OF USER WITH OFFERS
public function getYourOffersByUserId( $user_id, $validate = NULL ) {
  //$this->dao->select('distinct i.pk_i_id, i.s_title, i.s_price, i.fk_c_currency_code');
  $this->dao->select('o.*, o.i_price as i_price_offered, i.*, d.*');
  $this->dao->from( $this->getTable_Offer() . ' o, ' . $this->getTable_Item() . ' i, ' . $this->getTable_ItemDescription() . ' d' );
  $this->dao->where('i.pk_i_id = o.fk_i_item_id' );
  $this->dao->where('i.pk_i_id = d.fk_i_item_id' );
  $this->dao->where('d.fk_c_locale_code', osc_current_user_locale() );
  $this->dao->where('o.i_user_id', $user_id );

  if($validate <> '') {
    $this->dao->where('o.i_validate', $validate);
  }

  $this->dao->orderby('o.i_offer_id desc');

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}


// GET ALL OFFERS ON ITEM
public function getOffersByItemId( $item_id, $validate = NULL ) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Offer() );
  $this->dao->where('fk_i_item_id', $item_id );

  if($validate <> '' && $validate == 1) {
    $this->dao->where('i_validate', $validate);
  }

  $this->dao->orderby('i_offer_id desc');

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}


public function countOffers( $item_id, $user_id, $validate ) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from( $this->getTable_Offer() );
  $this->dao->where('fk_i_item_id', $item_id );
  $this->dao->where('i_user_id', $user_id );

  if($validate <> '' && $validate == 1) {
    $this->dao->where('i_validate', $validate);
  }

  $this->dao->where('i_status <> 1');


  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}


public function getOfferById( $offer_id ) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Offer() );
  $this->dao->where('i_offer_id', $offer_id );

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}


// COUNT ALL OFFERS ON ITEM
public function countOffersByItemId( $item_id, $validate ) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from( $this->getTable_Offer() );
  $this->dao->where('fk_i_item_id', $item_id );

  if($validate <> '' && $validate == 1) {
    $this->dao->where('i_validate', $validate);
  }

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}



// GET ALL OFFERS BY VALIDATE TYPE
public function getAllOffers( $validate, $limit = NULL ) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Offer() );

  if($validate <> '' && ($validate == 1 || $validate == 2)) {
    if($validate == 1) {
      $this->dao->where('i_validate = 1');
    }

    if($validate == 2) {
      $this->dao->where('i_validate = 0');
    }
  }

  if($limit <> '' && $limit > 0) {
    $this->dao->limit( $limit );
  }


  $this->dao->orderby('i_offer_id DESC');

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}



public function validateOfferById( $id ) {
  $aSet = array(
    'i_validate' => 1
  );

  $aWhere = array( 'i_offer_id' => $id);

  return $this->_update($this->getTable_Offer(), $aSet, $aWhere);
}


public function removeOfferById( $id ) {
  $this->dao->query('DELETE FROM '. $this->getTable_Offer() . ' WHERE i_offer_id = ' . $id);
}



// update function
function _update($table, $values, $where) {
  $this->dao->from($table);
  $this->dao->set($values);
  $this->dao->where($where);
  return $this->dao->update();
}

// End of DAO Class
}
?>
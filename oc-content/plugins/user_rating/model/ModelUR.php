<?php

class ModelUR extends DAO {
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
  if(!$this->dao->importSQL($sql)){ throw new Exception("Error importSQL::ModelUR<br>".$file.'<br>'.$path.'<br><br>Please check your database for if there are no plugin tables. <br>If any of those tables exists in your database, drop them!');} 
}
 
public function uninstall() {
  $this->dao->query('DROP TABLE '. $this->getTable_Rating());
}

public function getTable_Rating() {
  return DB_TABLE_PREFIX.'t_user_rating_ur';
}

public function getTable_User() {
  return DB_TABLE_PREFIX.'t_user';
}

public function getTable_Item() {
  return DB_TABLE_PREFIX.'t_item';
}

public function getTable_Page() {
  return DB_TABLE_PREFIX.'t_pages';
}

public function getTable_Picture(){
  return DB_TABLE_PREFIX.'t_profile_picture';
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


// INSERT NEW RATING TO DATABASE
public function insertRating( $user_id, $email, $from_user_id, $type, $cat0, $cat1 = NULL, $cat2 = NULL, $cat3 = NULL, $cat4 = NULL, $cat5 = NULL, $response = '' ) {
  $aSet = array(
    'fk_i_user_id' => $user_id,
    's_user_email' => $email,
    'fk_i_from_user_id' => $from_user_id,
    'i_type' => $type,
    'i_cat0' => $cat0,
    'i_cat1' => $cat1,
    'i_cat2' => $cat2,
    'i_cat3' => $cat3,
    'i_cat4' => $cat4,
    'i_cat5' => $cat5,
    's_comment' => $response
  );

  $this->dao->insert( $this->getTable_Rating(), $aSet);
  return $this->dao->insertedId();
}


// COUNT RATINGS ON USER ACCOUNT FROM SOME USER (to check if user already left rating)
public function countRatingsByUserId( $user_id, $user_email, $from_user_id ) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from( $this->getTable_Rating() );

  if($user_id == 0 || $user_id == '') {
    $this->dao->where('s_user_email = "' . $user_email . '" AND fk_i_from_user_id = ' . $from_user_id );
  } else {
    $this->dao->where('fk_i_user_id = ' . $user_id . ' AND fk_i_from_user_id = ' . $from_user_id );
  }


  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}


// COUNT RATINGS ON USER
public function countRatingsByUserIdAll( $user_id, $user_email) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from( $this->getTable_Rating() );

  if($user_id == 0 || $user_id == '') {
    $this->dao->where('s_user_email',  $user_email);
  } else {
    $this->dao->where('fk_i_user_id', $user_id);
  }


  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}


// COUNT NOT VALIDATED RATINGS
public function countNotValidated() {
  $this->dao->select('count(*) as i_count');
  $this->dao->from( $this->getTable_Rating() );

  $this->dao->where('i_validate', 0 );

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}


// GET ALL RATINGS OF PARTICULAR USER
public function getRatingByUserId( $user_id, $user_email, $type, $validate ) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Rating() );

  if($user_id == 0 || $user_id == '') {
    $this->dao->where('s_user_email', $user_email);
  } else {
    $this->dao->where('fk_i_user_id', $user_id );
  }

  // if($type == 0 || $type == 1) {
    // $this->dao->where('i_type', $type);
  // }

  if($validate <> '' && $validate == 1) {
    $this->dao->where('i_validate', $validate);
  }

  $this->dao->orderby('d_datetime DESC');

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}


// GET ALL RATINGS BY VALIDATE TYPE
public function getAllRatings( $validate, $limit = NULL ) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Rating() );

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


  $this->dao->orderby('d_datetime DESC');

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}


public function getRatingCounts($user_id, $user_email = NULL, $cat = NULL, $stars = NULL, $type = NULL, $validate = NULL) {
  $this->dao->select('count(*) as i_count' );
  $this->dao->from( $this->getTable_Rating() );

  if($stars <> '' && $cat <> '') {
    $this->dao->where($cat, $stars);
  }

  if($user_id == 0 || $user_id == '') {
    if($user_email <> '') {
      $this->dao->where('s_user_email = "' . $user_email . '"' );
    }
  } else {
    $this->dao->where('fk_i_user_id', $user_id );
  }

  // if($type == 0 || $type == 1) {
    // $this->dao->where('i_type', $type);
  // }

  if($validate <> '' && $validate == 1) {
    $this->dao->where('i_validate', $validate);
  }

  $result = $this->dao->get();
  if( !$result ) { return 0; }
  $counts = $result->row();
  return $counts['i_count'];
}


// GET AVERAGES
public function getRatingAverageByUserId( $user_id, $user_email, $type = NULL, $validate = NULL) {
  $this->dao->select('fk_i_user_id, s_user_email, avg((i_cat0 + i_cat1 + i_cat2 + i_cat3 + i_cat4 + i_cat5) / ( 6 - (case i_cat0 when 0 then 1 else 0 end) - (case i_cat1 when 0 then 1 else 0 end) - (case i_cat2 when 0 then 1 else 0 end) - (case i_cat3 when 0 then 1 else 0 end) - (case i_cat4 when 0 then 1 else 0 end) - (case i_cat5 when 0 then 1 else 0 end))) as d_average');
  $this->dao->from( $this->getTable_Rating() );

  if($user_id == 0 || $user_id == '') {
    $this->dao->where('s_user_email = "' . $user_email . '"' );
  } else {
    $this->dao->where('fk_i_user_id ', $user_id );
  }

  // if($type == 0 || $type == 1) {
    // $this->dao->where('i_type', $type);
  // }

  if($validate <> '' && $validate == 1) {
    $this->dao->where('i_validate', $validate);
  }

  $result = $this->dao->get();
  if( !$result ) { return 0; }
  $average = $result->row();
  return $average['d_average'];
}


// GET AVERAGES BY RATING ID
public function getRatingAverageByRatingId( $rating_id ) {
  $this->dao->select('i_rating_id, ((i_cat0 + i_cat1 + i_cat2 + i_cat3 + i_cat4 + i_cat5) / ( 6 - (case i_cat0 when 0 then 1 else 0 end) - (case i_cat1 when 0 then 1 else 0 end) - (case i_cat2 when 0 then 1 else 0 end) - (case i_cat3 when 0 then 1 else 0 end) - (case i_cat4 when 0 then 1 else 0 end) - (case i_cat5 when 0 then 1 else 0 end))) as d_average');
  $this->dao->from( $this->getTable_Rating() );

  $this->dao->where('i_rating_id', $rating_id);

  $result = $this->dao->get();
  if( !$result ) { return 0; }
  $average = $result->row();
  return $average['d_average'];
}


public function validateRatingById( $id ) {
  $aSet = array(
    'i_validate' => 1
  );

  $aWhere = array( 'i_rating_id' => $id);

  return $this->_update($this->getTable_Rating(), $aSet, $aWhere);
}

public function removeRatingById( $id ) {
  $this->dao->query('DELETE FROM '. $this->getTable_Rating() . ' WHERE i_rating_id = ' . $id);
}


public function removeRatingByUser( $user_id ) {
  $this->dao->query('DELETE FROM '. $this->getTable_Rating() . ' WHERE fk_i_user_id = ' . $user_id);
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
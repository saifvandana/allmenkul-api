<?php

class ModelBA extends DAO {
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
  if(!$this->dao->importSQL($sql)){ throw new Exception("Error importSQL::ModelBA<br>".$file.'<br>'.$path.'<br><br>Please check your database for if there are no plugin tables. <br>If any of those tables exists in your database, drop them!');} 
}
 
public function uninstall() {
  $this->dao->query('DROP TABLE '. $this->getTable_Banner());
  $this->dao->query('DROP TABLE '. $this->getTable_Advert());
}

public function getTable_Advert() {
  return DB_TABLE_PREFIX.'t_ba_advert';
}

public function getTable_Banner() {
  return DB_TABLE_PREFIX.'t_ba_banner';
}




public function insertAdvert($type, $banner_id, $name, $key, $url, $code, $price_view, $price_click, $budget, $expire, $category, $size_width, $size_height) {
  $aSet = array(
    'i_type' => $type,
    'fk_s_banner_id' => $banner_id,
    's_name' => $name,
    's_key' => $key,
    's_url' => $url,
    's_code' => $code,
    'd_price_view' => $price_view,
    'd_price_click' => $price_click,
    'd_budget' => $budget,
    'dt_expire' => $expire,
    's_category' => $category,
    's_size_width' => $size_width,
    's_size_height' => $size_height
  );

  $this->dao->insert( $this->getTable_Advert(), $aSet);
  return $this->dao->insertedId();
}


public function updateAdvert($id, $type, $banner_id, $name, $key, $url, $code, $price_view, $price_click, $budget, $expire, $category, $size_width, $size_height) {
  $aSet = array(
    'i_type' => $type,
    'fk_s_banner_id' => $banner_id,
    's_name' => $name,
    's_key' => $key,
    's_url' => $url,
    's_code' => $code,
    'd_price_view' => $price_view,
    'd_price_click' => $price_click,
    'd_budget' => $budget,
    'dt_expire' => $expire,
    's_category' => $category,
    's_size_width' => $size_width,
    's_size_height' => $size_height
  );

  $aWhere = array('pk_i_id' => $id);

  return $this->_update($this->getTable_Advert(), $aSet, $aWhere);
}



public function updateAdvertBanners($id, $banner_id) {
  $aSet = array(
    'fk_s_banner_id' => $banner_id
  );

  $aWhere = array('pk_i_id' => $id);

  return $this->_update($this->getTable_Advert(), $aSet, $aWhere);
}


public function updateAdvertImage($id, $image) {
  $aSet = array(
    's_image' => $image
  );

  $aWhere = array('pk_i_id' => $id);

  return $this->_update($this->getTable_Advert(), $aSet, $aWhere);
}


public function updateViews( $id ) {
  if($id <> '' && $id > 0) {
    return $this->dao->query('UPDATE '.$this->getTable_Advert().' SET i_views=coalesce(i_views, 0)+1 WHERE pk_i_id='.$id);
  }
}


public function updateClicks( $id ) {
  if($id <> '' && $id > 0) {
    return $this->dao->query('UPDATE '.$this->getTable_Advert().' SET i_clicks=coalesce(i_clicks, 0)+1 WHERE pk_i_id='.$id);
  }
}


public function removeAdvert( $id ) {
  $this->dao->query('DELETE FROM '. $this->getTable_Advert() . ' WHERE pk_i_id = ' . $id);
}


public function removeBanner( $id ) {
  $this->dao->query('DELETE FROM '. $this->getTable_Banner() . ' WHERE pk_i_id = ' . $id);
}


public function getAdvert($id) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Advert() );

  $this->dao->where('pk_i_id', $id);


  $result = $this->dao->get();
  if( !$result ) { return array(); }
  return $result->row();
}


public function getAdverts($with_banners = false) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Advert() );

  $result = $this->dao->get();
  
  if(!$result) { 
    return array(); 
  }
  
  
  $prepare = $result->result();
  
  if(is_array($prepare) && count($prepare) > 0 && $with_banners === true) {
    $output = array();
    
    foreach($prepare as $p) {
      $output[$p['pk_i_id']] = $p;
      $output[$p['pk_i_id']]['banners'] = array();
      
      if(@$p['fk_s_banner_id'] != '') {
        $banner_ids = explode(',', $p['fk_s_banner_id']);
        
        if(is_array($banner_ids) && count($banner_ids) > 0) {
          foreach($banner_ids as $bid) {
            $banner = $this->getBanner($bid);
            
            if($banner !== false) {
              $output[$p['pk_i_id']]['banners'][$bid] = $banner;
              $output[$p['pk_i_id']]['banners'][$bid]['advert_count'] = $this->countAdvertByBannerId($bid);
            }
          }
        }
      } 
    }
    
    return $output;
  }
  
  return $prepare;
}




public function getAdvertsByKey($key) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Advert() );

  $this->dao->where('s_key', $key);

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}




public function getBanner($id) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Banner() );

  $this->dao->where('pk_i_id', $id);


  $result = $this->dao->get();
  if( !$result ) { return false; }
  return $result->row();
}


public function getAdvertByBannerId($id) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Advert() );

  $id = ',' . $id . ',';
  $this->dao->where('concat(concat(",", fk_s_banner_id), ",") like "%' . $id . '%"');

  if(osc_is_search_page()) { 
    $cat_id = osc_search_category_id();
    $cat_id = isset($cat_id[0]) ? $cat_id[0] : '';
  } else if (osc_is_ad_page()) {
    $cat_id = osc_item_category_id();
  }


  if((osc_is_ad_page() || osc_is_search_page()) && $cat_id > 0) {
    $root = Category::newInstance()->toRootTree( $cat_id);
    $root_ids = array_column($root, 'pk_i_id');
    $root_id = $root_ids[0];

    $root_id = ',' . $root_id . ',';
    $this->dao->where('((concat(concat(",", s_category), ",") like "%' . $root_id . '%") OR s_category = "")');

    //$this->dao->where('((s_category <> "" AND concat(concat(",", s_category), ",") like "%' . $cat_id . '%") OR s_category = "")');
  }

  $this->dao->where('((d_budget > 0 AND (i_views*d_price_view + i_clicks*d_price_click) < d_budget) OR (d_budget <= 0))');
  $this->dao->where('((dt_expire <> "0000-00-00" AND dt_expire > NOW()) OR (year(dt_expire) < 2000))');


  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}



public function getAdvertByBannerId2($id) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Advert() );

  $id = ',' . $id . ',';
  $this->dao->where('concat(concat(",", fk_s_banner_id), ",") like "%' . $id . '%"');


  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}


public function countAdvertByBannerId($id) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from( $this->getTable_Advert() );

  $id = ',' . $id . ',';
  $this->dao->where('concat(concat(",", fk_s_banner_id), ",") like "%' . $id . '%"');

  $result = $this->dao->get();
  if( !$result ) { return 0; }
  $prepare = $result->row();
  return isset($prepare['i_count']) ? $prepare['i_count'] : 0;
}

public function getBannersByHook($hook) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Banner() );

  $this->dao->where('concat(concat(",", s_hook), ",") like "%' . osc_esc_html($hook) . '%"');

  $result = $this->dao->get();
  if( !$result ) { return array(); }
  $prepare = $result->result();
  return $prepare;
}


public function getBanners($with_adverts = false) {
  $this->dao->select();
  $this->dao->from( $this->getTable_Banner() );

  $result = $this->dao->get();
  
  if(!$result) { 
    return array(); 
  }
  
  $prepare = $result->result();
  
  if(is_array($prepare) && count($prepare) > 0 && $with_adverts === true) {
    $output = array();
    
    foreach($prepare as $p) {
      $output[$p['pk_i_id']] = $p;
      $output[$p['pk_i_id']]['adverts'] = $this->getAdvertByBannerId2($p['pk_i_id']);
    }
    
    return $output;
  }
  
  
  return $prepare;
}


public function insertBanner($name, $type, $hook) {
  $aSet = array(
    's_name' => $name,
    'i_type' => $type,
    's_hook' => $hook
  );

  $this->dao->insert( $this->getTable_Banner(), $aSet);
  return $this->dao->insertedId();
}


public function updateBanner($id, $name, $type, $hook) {
  $aSet = array(
    's_name' => $name,
    'i_type' => $type,
    's_hook' => $hook
  );

  $aWhere = array('pk_i_id' => $id);

  return $this->_update($this->getTable_Banner(), $aSet, $aWhere);
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
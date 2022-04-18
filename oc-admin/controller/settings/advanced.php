<?php
if(!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/*
 * Copyright 2014 Osclass
 * Copyright 2021 Osclass by OsclassPoint.com
 *
 * Osclass maintained & developed by OsclassPoint.com
 * You may not use this file except in compliance with the License.
 * You may download copy of Osclass at
 *
 *     https://osclass-classifieds.com/download
 *
 * Do not edit or add to this file if you wish to upgrade Osclass to newer
 * versions in the future. Software is distributed on an "AS IS" basis, without
 * warranties or conditions of any kind, either express or implied. Do not remove
 * this NOTICE section as it contains license information and copyrights.
 */


class CAdminSettingsAdvanced extends AdminSecBaseModel
{
  //Business Layer...
  function doModel()
  {
    switch($this->action) {
      case('advanced'):
        //calling the advanced settings view
        $this->doView('settings/advanced.php');
      break;
      case('advanced_post'):
        // updating advanced settings
        if( defined('DEMO') ) {
          osc_add_flash_warning_message( _m("This action can't be done because it's a demo site"), 'admin');
          $this->redirectTo(osc_admin_base_url(true) . '?page=settings&action=advanced');
        }
        osc_csrf_check();
        $subdomain_type = Params::getParam('e_type');
        if(!in_array($subdomain_type, array('category', 'country', 'region', 'city', 'user'))) {
          $subdomain_type = '';
        }
        $iUpdated = osc_set_preference('subdomain_type', $subdomain_type);
        $iUpdated += osc_set_preference('subdomain_host', Params::getParam('s_host'));

        if($iUpdated > 0) {
          osc_add_flash_ok_message( _m("Advanced settings have been updated"), 'admin');
        }
        osc_calculate_location_slug(osc_subdomain_type());
        $this->redirectTo(osc_admin_base_url(true) . '?page=settings&action=advanced');
      break;
      case('advanced_cache_flush'):
        osc_cache_flush();
        osc_add_flash_ok_message( _m("Cache flushed correctly"), 'admin');
        $this->redirectTo(osc_admin_base_url(true) . '?page=settings&action=advanced');
      break;
    }
  }
}

// EOF: ./oc-admin/controller/settings/main.php
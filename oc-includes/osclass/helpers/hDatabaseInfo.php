<?php
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


/**
* Helper Database Info
* @package Osclass
* @subpackage Helpers
* @author Osclass
*/

/**
 * Gets database name
 *
 * @return string
 */
function osc_db_name() {
  return getSiteInfo('s_db_name', DB_NAME);
}

/**
 * Gets database host
 *
 * @return string
 */
function osc_db_host() {
  return getSiteInfo('s_db_host', DB_HOST);
}

/**
 * Gets database user
 *
 * @return string
 */
function osc_db_user() {
  return getSiteInfo('s_db_user', DB_USER);
}

/**
 * Gets database password
 *
 * @return string
 */
function osc_db_password() {
  return getSiteInfo('s_db_password', DB_PASSWORD);
}

/**
 * Gets multisite url
 *
 * @return string
 */
function osc_multisite_url() {
  if( getSiteInfo('s_site_mapping', '') !== '' ) {
    return getSiteInfo('s_site_mapping', '');
  }
  return getSiteInfo('s_site', '');
}

/**
 * Gets multisite url
 *
 * @return string
 */
function osc_multisite_upload_path() {
  return getSiteInfo('s_upload_path', '');
}

//PRIVATE FUNCTION FOR GETTING NO BOOLEAN INFORMATION (if there was a class :P)
/**
 * Gets site info
 *
 * @param string $key
 * @param string $default_value
 * @return string
 */
function getSiteInfo($key, $default_value) {
  if (MULTISITE) {
    $_P = SiteInfo::newInstance();
    return $_P->get( $key);
  }

  return $default_value;
}
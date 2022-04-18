<?php if ( ! defined( 'ABS_PATH' ) ) {
	exit( 'ABS_PATH is not loaded. Direct access is not allowed.' );
}

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
 * Class CWebLanguage
 */
class CWebLanguage extends BaseModel
{
  public function __construct() {
    parent::__construct();
    osc_run_hook('init_language');
  }

  // business layer...
  public function doModel()
  {
    $locale = Params::getParam('locale');

    if(preg_match('/.{2}_.{2}/', $locale)) {
      Session::newInstance()->_set('userLocale', $locale);
    }

    $redirect_url = '';
    if(Params::getServerParam('HTTP_REFERER', false, false) != '') {
      $redirect_url = Params::getServerParam('HTTP_REFERER', false, false);
    } else {
      $redirect_url = osc_base_url(true);
    }

    $this->redirectTo($redirect_url);
  }

  // hopefully generic...

  /**
   * @param $file
   *
   * @return mixed|void
   */
  public function doView( $file ) {
  }
}

/* file end: ./language.php */
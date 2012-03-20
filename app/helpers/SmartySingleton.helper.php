<?php

/**
 * Make Smarty singleton aware.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Hauke Schade <http://hauke-schade.de>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Helper;

use Smarty;
use lessc;

require_once PATH_STANDARD . '/lib/smarty/Smarty.class.php';

class SmartySingleton extends Smarty {

  /**
   *
   * @var static
	 * @access private
   *
   */
  private static $_oInstance = null;

  /**
   * Get the Smarty instance
   *
	 * @static
	 * @access public
   * @return object self::$_oInstance Smarty instance that was found or generated
   *
   */
  public static function getInstance() {
    if (self::$_oInstance === null)
      self::$_oInstance = new self();

    return self::$_oInstance;
  }

  /**
   * Set all default smarty values.
	 *
	 * @access public
   * @todo add session / request stuff from main
	 *
   */
  public function __construct() {
    parent::__construct();

    if (!defined('WEBSITE_LANGUAGE'))
      define('WEBSITE_LANGUAGE', 'en');

    new I18n(WEBSITE_LANGUAGE);

    $this->setCacheDir(PATH_STANDARD . '/' . CACHE_DIR);
    $this->setCompileDir(PATH_STANDARD . '/' . COMPILE_DIR);
    $this->setPluginsDir(PATH_STANDARD . '/lib/smarty/plugins');
    $this->setTemplateDir(PATH_STANDARD . '/app/views');

    # See http://www.smarty.net/docs/en/variable.merge.compiled.includes.tpl
    $this->merge_compiled_includes = true;

    # Use a readable structure
    $this->use_sub_dirs = true;

    # Only compile our templates on production mode.
    if (WEBSITE_MODE == 'production' || WEBSITE_MODE == 'staging') {
      $this->setCompileCheck(false);
      $this->setCacheModifiedCheck(true);
    }

    $bUseFacebook = class_exists('\CandyCMS\Plugin\Controller\FacebookCMS') ? true : false;

    if($bUseFacebook === true) {
       # Required for meta only
      $this->assign('PLUGIN_FACEBOOK_ADMIN_ID', PLUGIN_FACEBOOK_ADMIN_ID);

      # Required for facebook actions
      $this->assign('PLUGIN_FACEBOOK_APP_ID', PLUGIN_FACEBOOK_APP_ID);
    }

    # Define smarty constants
    $this->assign('CURRENT_URL', CURRENT_URL);
    $this->assign('MOBILE', MOBILE);
    $this->assign('MOBILE_DEVICE', MOBILE_DEVICE);
    $this->assign('THUMB_DEFAULT_X', THUMB_DEFAULT_X);
    $this->assign('VERSION', VERSION);
    $this->assign('WEBSITE_COMPRESS_FILES', WEBSITE_COMPRESS_FILES);
    $this->assign('WEBSITE_LANGUAGE', WEBSITE_LANGUAGE);
    $this->assign('WEBSITE_LOCALE', WEBSITE_LOCALE);
    $this->assign('WEBSITE_MODE', WEBSITE_MODE);
    $this->assign('WEBSITE_NAME', WEBSITE_NAME);
    $this->assign('WEBSITE_URL', WEBSITE_URL);

    # Define system variables
    $this->assign('_PATH', $this->getPaths());
    $this->assign('_SYSTEM', array(
        'date'                  => date('Y-m-d'),
        'compress_files_suffix' => WEBSITE_COMPRESS_FILES === true ? '.min' : '',
        'facebook_plugin'       => $bUseFacebook,
        'json_language'         => I18n::getJson()));

    $this->assign('lang', I18n::getArray());
  }

  /**
   * Delete this variable from memory...
	 *
	 * @access public
   *
   */
  public function __destruct() {
    parent::__destruct();

    self::$_oInstance = null;
  }

  /**
   * Generate all path variables that could be useful for Smarty templates.
	 *
	 * @access public
   * @return array Array with Paths for 'images', 'js', 'less', 'css', 'templates', 'upload', 'public'
	 *
   */
  public function getPaths() {
    $aPaths = array('css' => 'css', 'less' => 'less', 'images' => 'images', 'js' => 'js');

    # Use an external CDN within a custom template
    if (PATH_TEMPLATE !== '' && substr(WEBSITE_CDN, 0, 4) == 'http') {
      $sPath = WEBSITE_CDN . '/templates/' . PATH_TEMPLATE;

      foreach ($aPaths as $sKey => $sValue)
        $aPaths[$sKey] = $sPath . '/' . $sValue;
    }

    # Use our public folder within a custom template
    elseif (PATH_TEMPLATE !== '' && substr(WEBSITE_CDN, 0, 4) !== 'http') {
      $sPath = WEBSITE_CDN . '/templates/' . PATH_TEMPLATE;

      foreach ($aPaths as $sKey => $sValue)
        $aPaths[$sKey] = ( @is_dir(substr($sPath, 1) . '/css') ? $sPath : WEBSITE_CDN ) . '/' . $sValue;
    }

    # Use standard folders
    else {
      foreach ($aPaths as $sKey => $sValue)
        $aPaths[$sKey] = WEBSITE_CDN . '/' . $sValue;
    }

    # Compile CSS when in development mode and clearing the cache
    if (WEBSITE_MODE == 'development' && file_exists(Helper::removeSlash($aPaths['less'] . '/core/application.less'))) {
      require_once PATH_STANDARD . '/lib/lessphp/lessc.inc.php';

      try {
        @unlink(Helper::removeSlash($aPaths['css'] . '/core/application.css'));
        lessc::ccompile(Helper::removeSlash($aPaths['less'] . '/core/application.less'),
                Helper::removeSlash($aPaths['css'] . '/core/application.css'));
      }
      catch (AdvancedException $e) {
        AdvancedException::reportBoth($e->getMessage());
      }
    }

    return $aPaths + array(
        'public'    => WEBSITE_CDN,
        'template'  => WEBSITE_CDN . '/templates/' . PATH_TEMPLATE,
        'upload'    => PATH_UPLOAD);
  }

  /**
   * Clear the controller cache.
   *
   * @access public
   * @param string $sController
   *
   */
  public function clearCacheForController($sController) {
    $this->clearCache(null, WEBSITE_MODE . '|' . $sController);
  }
}
<?php

/**
 * Make Smarty Singleton Aware
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Hauke Schade <http://hauke-schade.de>
 * @license MIT
 * @since 2.0
 * @todo documentation
 *
 */

namespace CandyCMS\Helper;

use Smarty;
use lessc;

require_once PATH_STANDARD . '/lib/smarty/Smarty.class.php';

class SmartySingleton extends Smarty {

  private static $oInstance = null;

  /**
   * Get the Smarty Instance
   *
   * @return Object the Smarty Instance that was found or generated
   */
  public static function getInstance() {
    if (self::$oInstance === null)
      self::$oInstance = new self();

    return self::$oInstance;
  }

  /**
   * Construct, sets all default smarty values
   *
   * @todo add session / request stuff from main
   * @todo test
   */
  public function __construct() {
    parent::__construct();

    $this->setCacheDir(PATH_STANDARD . '/' . CACHE_DIR);
    $this->setCompileDir(PATH_STANDARD . '/' . COMPILE_DIR);
    $this->setPluginsDir(PATH_STANDARD . '/lib/smarty/plugins');
    $this->setTemplateDir(PATH_STANDARD . '/app/views');

    #$this->merge_compiled_includes = true;
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
    $this->assign('_SYSTEM', array(
        'date'                  => date('Y-m-d'),
        'compress_files_suffix' => WEBSITE_COMPRESS_FILES === true ? '.min' : '',
        'facebook_plugin'       => $bUseFacebook,
        'json_language'         => I18n::getJson()));

    # @todo
    $this->assign('lang', I18n::getArray());

    $this->assign('_PATH', $this->getPaths());
  }

  /**
   * Generate all Path-Variables that could be useful for Smarty Templates
   *
   * @return array Array with Paths for 'images', 'js', 'less', 'css', 'templates', 'upload', 'public'
   */
  public function getPaths() {
    # Use an external CDN within a custom template
    $aPaths = array('css' => 'css', 'less' => 'less', 'images' => 'images', 'js' => 'js');

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
      require PATH_STANDARD . '/lib/lessphp/lessc.inc.php';

      try {
        @unlink(Helper::removeSlash($aPaths['css'] . '/core/application.css'));
        lessc::ccompile(Helper::removeSlash($aPaths['less'] . '/core/application.less'), Helper::removeSlash($aPaths['css'] . '/core/application.css'));
      }
      catch (AdvancedException $e) {
        die($e->getMessage());
      }
    }

    return $aPaths + array(
        'public'    => WEBSITE_CDN,
        'template'  => WEBSITE_CDN . '/templates/' . PATH_TEMPLATE,
        'upload'    => PATH_UPLOAD);
  }
}

?>
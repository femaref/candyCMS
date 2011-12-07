<?php

/**
 * Manage configs and route incoming request.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Addon\Addon as Addon;
use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Section as Section;
use CandyCMS\Model\Session as Model_Session;
use CandyCMS\Plugin\Cronjob as Cronjob;
use CandyCMS\Plugin\FacebookCMS as FacebookCMS;

class Index {

  /**
	 * @var array
	 * @access protected
	 */
	protected $_aRequest;

	/**
	 * @var array
	 * @access protected
	 */
	protected $_aSession;

	/**
	 * @var array
	 * @access protected
	 */
	protected $_aFile;

	/**
	 * @var array
	 * @access protected
	 */
	protected $_aCookie;

	/**
	 * name of the selected template (via request)
	 *
	 * @var string
	 * @access private
	 */
	private $_sTemplate;

	/**
	 * name of the selected language (via request)
	 *
	 * @var string
	 * @access private
	 */
	private $_sLanguage;

	/**
	 * Initialize the software by adding input params.
	 *
	 * @access public
	 * @param array $aRequest alias for the combination of $_GET and $_POST
	 * @param array $aSession alias for $_SESSION
	 * @param array $aFile alias for $_FILE
	 * @param array $aCookie alias for $_COOKIE
	 *
	 */
	public function __construct($aRequest, $aSession, $aFile = '', $aCookie = '') {
		$this->_aRequest	= & $aRequest;
		$this->_aSession	= & $aSession;
		$this->_aFile			= & $aFile;
		$this->_aCookie		= & $aCookie;

    # Does the user want to view the normal website instead of the mobile one?
    $_SESSION['webview'] = isset($this->_aRequest['mobile']) && $this->_aRequest['mobile'] == false ? true : false;
	}

  /**
   * Load all config files.
   *
   * @access public
   * @param array $aConfigs array of config files
   * @param string $sPath Path prefix. Needed when not in root path
   *
   */
  public function getConfigFiles($aConfigs, $sPath = '') {
    foreach ($aConfigs as $sConfig) {
      try {
        if (!file_exists($sPath . 'config/' . ucfirst($sConfig) . '.inc.php'))
          throw new AdvancedException('Missing ' . ucfirst($sConfig) . ' config file.');
        else
          require_once $sPath . 'config/' . ucfirst($sConfig) . '.inc.php';
      }
      catch (AdvancedException $e) {
        die($e->getMessage());
      }
    }
  }

  /**
   * Load all defined plugins.
   *
   * @access public
   * @param string $sAllowedPlugins comma separated plugin names
   * @param string $sPath Path prefix. Needed when not in root path
   * @see config/Candy.inc.php
   *
   */
  public function getPlugins($sAllowedPlugins, $sPath = '') {
    $aPlugins = preg_split("/[\s]*[,][\s]*/", $sAllowedPlugins);

    foreach ($aPlugins as $sPluginName) {
      if (file_exists('plugins/controllers/' . (string) ucfirst($sPluginName) . '.controller.php'))
        require_once 'plugins/controllers/' . (string) ucfirst($sPluginName) . '.controller.php';
    }
  }

  /**
   * Sets the language. This can be done via a language request and be temporarily saved in a cookie.
   *
   * @access public
   * @see config/Candy.inc.php
   *
   */
  public function getLanguage($sPath = '') {
    # We got a language request? Let's change it!
    if (isset($this->_aRequest['language'])) {
      if (is_file('languages/' . (string) $this->_aRequest['language'] . '.language.yml')) {
        $this->_sLanguage = (string) $this->_aRequest['language'];
        setcookie('default_language', (string) $this->_aRequest['language'], time() + 2592000, '/');
      }

      else
        $this->_sLanguage = '';
    }
    # There is no request, but there might be a cookie instead
    else {
      $aRequest = isset($this->_aCookie) ? array_merge($this->_aRequest, $this->_aCookie) : $this->_aRequest;

      $this->_sLanguage = isset($aRequest['default_language']) &&
              is_file('languages/' . (string) $aRequest['default_language'] . '.language.yml') ?
              (string) $aRequest['default_language'] :
              substr(DEFAULT_LANGUAGE, 0, 2);
    }

    # Set iso language codes
    switch ($this->_sLanguage) {
      case 'de':
        $sLocale = 'de_DE';
        break;

      default:
      case 'en':
        $sLocale = 'en_US';
        break;

      case 'es':
        $sLocale = 'es_ES';
        break;

      case 'fr':
        $sLocale = 'fr_FR';
        break;

      case 'pt':
        $sLocale = 'pt_PT';
        break;
    }

		define('WEBSITE_LANGUAGE', $this->_sLanguage);
		define('WEBSITE_LOCALE', $sLocale);
	}

  /**
   * Get the cronjob working. Check for last execution and plan next cleanup, optimization and backup.
   *
   * @access public
   * @see config/Candy.inc.php
   *
   */
  public function getCronjob() {
    if (class_exists('Cronjob')) {
      if (Cronjob::getNextUpdate() === true) {
        Cronjob::cleanup();
        Cronjob::optimize();
        Cronjob::backup(USER_ID);
      }
    }
  }

  /**
   * Give the users the ability to interact with facebook. Facebook is used as a plugin an loaded in the method above.
   *
   * @access public
   * @see config/Candy.inc.php
   * @see plugins/controllers/Facebook.controller.php
   * @return object FacebookCMS
   *
   */
  public function getFacebookExtension() {
    if (class_exists('FacebookCMS')) {
      return new FacebookCMS(array(
                  'appId' => FACEBOOK_APP_ID,
                  'secret' => FACEBOOK_SECRET,
                  'cookie' => true,
              ));
    }
  }

  /**
   * Store and show flash status messages in the application.
   *
   * @access protected
   * @see config/Candy.inc.php
   * @return array $aFlashMessage The message, its type and the headline of the message.
   *
   */
  protected function _getFlashMessage() {
    $aFlashMessage['type'] = isset($this->_aSession['flash_message']['type']) &&
            !empty($this->_aSession['flash_message']['type']) ?
            $this->_aSession['flash_message']['type'] :
            '';
    $aFlashMessage['message'] = isset($this->_aSession['flash_message']['message']) &&
            !empty($this->_aSession['flash_message']['message']) ?
            $this->_aSession['flash_message']['message'] :
            '';
    $aFlashMessage['headline'] = isset($this->_aSession['flash_message']['headline']) &&
            !empty($this->_aSession['flash_message']['headline']) ?
            $this->_aSession['flash_message']['headline'] :
            '';

    unset($_SESSION['flash_message']);
    return $aFlashMessage;
  }

  /**
   * Sets the template. This can be done via a template request and be temporarily saved in a cookie.
   *
   * @access public
   * @see config/Candy.inc.php
   *
   */
  public function setTemplate() {
    # We got a template request? Let's change it!
    if (isset($this->_aRequest['template'])) {

      if (is_dir(WEBSITE_CDN . '/templates/' . (string) $this->_aRequest['template'])) {
        $this->_sTemplate = (string) $this->_aRequest['template'];
        setcookie('default_template', (string) $this->_aRequest['template'], time() + 2592000, '/');
      }

      else
        $this->_sTemplate = '';
    }

    # There is no request, but there might be a cookie instead
    else {
      $aRequest = isset($this->_aCookie) ? array_merge($this->_aRequest, $this->_aCookie) : $this->_aRequest;
      $this->_sTemplate = isset($aRequest['default_template']) &&
              is_dir(WEBSITE_CDN . '/templates/' . $aRequest['default_template']) ?
              (string) $aRequest['default_template'] :
              '';
    }

    return $this->_sTemplate;
  }

  /**
   * Checks the empuxa server for a new CandyCMS version.
   *
   * @access private
   * @return string string with info message and link to download.
   *
   */
  private function checkForNewVersion() {
    if (USER_RIGHT == 4 && ALLOW_VERSION_CHECK == true) {
      $oFile = @fopen('http://www.empuxa.com/misc/candycms/version.txt', 'rb');
      $sVersionContent = @stream_get_contents($oFile);
      @fclose($oFile);

      $sVersionContent &= $sVersionContent > VERSION ? (int) $sVersionContent : '';
    }

    $sLangUpdateAvaiable = isset($sVersionContent) && !empty($sVersionContent) ?
            str_replace('%v', $sVersionContent, $this->oI18n->get('global.update.avaiable')) :
            '';
    return str_replace('%l', Helper::createLinkTo('http://candycms.com', true), $sLangUpdateAvaiable);
  }

  /**
   * Define user constants for global use.
   *
   * List of user rights:
   * 0 = Guests / unregistered users
   * 1 = Members
   * 2 = Facebook users
   * 3 = Moderators
   * 4 = Administrators
   *
   * @access public
   * @see index.php
   *
   */
	public function setUser() {
    $_SESSION['userdata'] = Model_Session::getSessionData();

    define('USER_ID', (int) $_SESSION['userdata']['id']);
    define('USER_PASSWORD', isset($_SESSION['userdata']['password']) ? $_SESSION['userdata']['password'] : '');

    # Try to get facebook data
    if (USER_ID == 0) {
      $oFacebook = $this->getFacebookExtension();
      if ($oFacebook == true)
        $aFacebookData = $oFacebook->getUserData();
    }

    define('USER_RIGHT', isset($aFacebookData[0]['uid']) ? 2 : (int) $_SESSION['userdata']['user_right']);
    define('USER_FACEBOOK_ID', isset($aFacebookData[0]['uid']) ? $aFacebookData[0]['uid'] : '');
    define('USER_EMAIL', isset($aFacebookData[0]['email']) ? $aFacebookData[0]['email'] : $_SESSION['userdata']['email']);
    define('USER_NAME', isset($aFacebookData[0]['first_name']) ? $aFacebookData[0]['first_name'] : $_SESSION['userdata']['name']);
    define('USER_SURNAME', isset($aFacebookData[0]['last_name']) ? $aFacebookData[0]['last_name'] : $_SESSION['userdata']['surname']);
    define('USER_FULL_NAME', USER_NAME . ' ' . USER_SURNAME);
  }

  /**
  * Show the application.tpl with all header and footer data such as meta tags etc.
  *
  * @access public
  * @return string $sCachedHTML The whole HTML code of our application.
  *
  */
  public function show() {
    # Define out core modules. All of them are separately handled in app/helper/Section.helper.php
		if (!isset($this->_aRequest['section']) ||
						empty($this->_aRequest['section']) ||
						strtolower($this->_aRequest['section']) == 'blog' ||
						strtolower($this->_aRequest['section']) == 'calendar' ||
						strtolower($this->_aRequest['section']) == 'comment' ||
						strtolower($this->_aRequest['section']) == 'content' ||
						strtolower($this->_aRequest['section']) == 'download' ||
						strtolower($this->_aRequest['section']) == 'error' ||
						strtolower($this->_aRequest['section']) == 'gallery' ||
						strtolower($this->_aRequest['section']) == 'log' ||
						strtolower($this->_aRequest['section']) == 'mail' ||
						strtolower($this->_aRequest['section']) == 'media' ||
						strtolower($this->_aRequest['section']) == 'newsletter' ||
            strtolower($this->_aRequest['section']) == 'rss' ||
            strtolower($this->_aRequest['section']) == 'search' ||
						strtolower($this->_aRequest['section']) == 'session' ||
            strtolower($this->_aRequest['section']) == 'sitemap' ||
            strtolower($this->_aRequest['section']) == 'static' ||
						strtolower($this->_aRequest['section']) == 'user') {

			$oSection = new Section($this->_aRequest, $this->_aSession, $this->_aFile);
			$oSection->getSection();
		}

    elseif (strtolower($this->_aRequest['section']) == 'install')
      Helper::redirectTo('/install');

		# We do not have a standard action, so fetch it from the addon folder.
    # If addon exists, proceed with override.
    elseif (ALLOW_ADDONS === true) {
      $oSection = new Addon($this->_aRequest, $this->_aSession, $this->_aFile);
      $oSection->getSection();
    }

    # Redirect to start page
    elseif (strtolower($this->_aRequest['section']) == 'start')
      Helper::redirectTo('/');

    # There's no request on a core module and addons are disabled. */
    else {
      header('Status: 404 Not Found');
      Helper::redirectTo('/error/404');
    }

    # Minimal settings for AJAX-request
		if ((isset($this->_aRequest['section']) && 'rss' == strtolower($this->_aRequest['section'])) ||
						(isset($this->_aRequest['ajax']) && true == $this->_aRequest['ajax']))
			$sCachedHTML = $oSection->getContent();

    # HTML with template
		else {

      # Get flash messages (success and error)
      # *********************************************
      $aFlashMessages = $this->_getFlashMessage();
      $oSection->oSmarty->assign('_flash_type_', $aFlashMessages['type']);
      $oSection->oSmarty->assign('_flash_message_', $aFlashMessages['message']);
      $oSection->oSmarty->assign('_flash_headline_', $aFlashMessages['headline']);

      # Define meta elements
      # *********************************************
			$oSection->oSmarty->assign('meta_expires', gmdate('D, d M Y H:i:s', time() + 60) . ' GMT');
			$oSection->oSmarty->assign('meta_description', $oSection->getDescription());
			$oSection->oSmarty->assign('meta_keywords', $oSection->getKeywords());
			$oSection->oSmarty->assign('meta_og_description', $oSection->getDescription());
			$oSection->oSmarty->assign('meta_og_site_name', WEBSITE_NAME);
			$oSection->oSmarty->assign('meta_og_title', $oSection->getTitle());
			$oSection->oSmarty->assign('meta_og_url', CURRENT_URL);

      # System required variables
      # *********************************************
			$oSection->oSmarty->assign('_content_', $oSection->getContent());
			$oSection->oSmarty->assign('_title_', $oSection->getTitle() . ' - ' . $oSection->oI18n->get('website.title'));
      $oSection->oSmarty->assign('_update_avaiable_', $this->checkForNewVersion());

      $sTemplateDir = Helper::getTemplateDir('layouts', 'application');
      $oSection->oSmarty->template_dir = $sTemplateDir;
      $sCachedHTML = $oSection->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'application'));
		}

		# Build absolute URLs
    # *********************************************
		$sCachedHTML = str_replace('%PATH_PUBLIC%', WEBSITE_CDN, $sCachedHTML);
		$sCachedHTML = str_replace('%PATH_TEMPLATE%', WEBSITE_CDN . '/templates/' . PATH_TEMPLATE, $sCachedHTML);
		$sCachedHTML = str_replace('%PATH_UPLOAD%', PATH_UPLOAD, $sCachedHTML);

		# Check for template files
    # *********************************************
    if (!empty($this->_sTemplate)) {
      $sPath = WEBSITE_CDN . '/templates/' . $this->_sTemplate;

      $sCachedCss     = $sPath . '/css';
      $sCachedImages  = $sPath . '/images';
      $sCachedJs      = $sPath . '/js';
    }
    elseif (PATH_TEMPLATE !== '') {
      $sPath = WEBSITE_CDN . '/templates/' . PATH_TEMPLATE;

      $sCachedCss     = $sPath . '/css';
      $sCachedImages  = $sPath . '/images';
      $sCachedJs      = $sPath . '/js';
    }
    else {
      $sCachedCss     = WEBSITE_CDN . '/css';
      $sCachedImages  = WEBSITE_CDN . '/images';
      $sCachedJs      = WEBSITE_CDN . '/js';
    }

    $sCachedHTML = & str_replace('%PATH_CSS%', $sCachedCss, $sCachedHTML);
    $sCachedHTML = & str_replace('%PATH_IMAGES%', $sCachedImages, $sCachedHTML);
    $sCachedHTML = & str_replace('%PATH_JS%', $sCachedJs, $sCachedHTML);

		# Cut spaces to minimize filesize
    # *********************************************
		$sCachedHTML = str_replace('	', '', $sCachedHTML); # Normal tab
		$sCachedHTML = str_replace('  ', '', $sCachedHTML); # Tab as two spaces

		$oSection->oI18n->unsetLanguage();

    header("Content-Type: text/html; charset=utf-8");
		return $sCachedHTML;
	}
}
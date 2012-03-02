<?php

/**
 * Manage configs and route incoming request.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Addon\Controller\Addon as Addon;
use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Helper\Section as Section;
use CandyCMS\Model\Session as Model_Session;
use CandyCMS\Model\User as Model_User;
use CandyCMS\Plugin\Cronjob as Cronjob;
use CandyCMS\Plugin\FacebookCMS as FacebookCMS;

class Index {

  /**
	 * @var array
	 * @access protected
   *
	 */
	protected $_aRequest;

	/**
	 * @var array
	 * @access protected
   *
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
   *
	 */
	protected $_aCookie;

	/**
	 * name of the selected template (via request)
	 *
	 * @var string
	 * @access private
   *
	 */
	private $_sTemplate;

	/**
	 * name of the selected language (via request)
	 *
	 * @var string
	 * @access private
   *
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
	public function __construct($aRequest, $aSession = '', $aFile = '', $aCookie = '') {
		$this->_aRequest	= & $aRequest;
		$this->_aSession	= & $aSession;
		$this->_aFile			= & $aFile;
		$this->_aCookie		= & $aCookie;
	}

	/**
	 * Init the software.
	 *
	 * @access public
	 *
	 */
	public function __init() {
		require PATH_STANDARD . '/app/models/Main.model.php';
		require PATH_STANDARD . '/app/models/Session.model.php';
		require PATH_STANDARD . '/app/controllers/Main.controller.php';
		require PATH_STANDARD . '/app/controllers/Session.controller.php';
		require PATH_STANDARD . '/app/controllers/Log.controller.php';
		require PATH_STANDARD . '/app/helpers/AdvancedException.helper.php';
		require PATH_STANDARD . '/app/helpers/Section.helper.php';
		require PATH_STANDARD . '/app/helpers/I18n.helper.php';
		require PATH_STANDARD . '/lib/smarty/Smarty.class.php';

    # Load actions.
    self::getConfigFiles(array('Plugins', 'Facebook', 'Mailchimp'));
    self::getPlugins(ALLOW_PLUGINS);
    $this->getLanguage();
    $this->getCronjob();
    $this->getFacebookExtension();
    $this->setUser();
    $this->setTemplate();
	}

  /**
   * Reset all data
   *
   * @access public
   *
   */
  public function __destruct() {
    unset($this->_aRequest, $this->_aSession, $this->_aFile, $this->_aCookie);
  }

  /**
   * Load all config files.
   *
   * @static
   * @access public
   * @param array $aConfigs array of config files
   * @return boolean true if no errors occurred.
   *
   */
  public static function getConfigFiles($aConfigs) {
    foreach ($aConfigs as $sConfig) {
      try {
        if (!file_exists(PATH_STANDARD . '/config/' . ucfirst($sConfig) . '.inc.php'))
          throw new AdvancedException('Missing ' . ucfirst($sConfig) . ' config file.');
        else
          require_once PATH_STANDARD . '/config/' . ucfirst($sConfig) . '.inc.php';
      }
      catch (AdvancedException $e) {
        die($e->getMessage());
      }
    }

    return true;
  }

  /**
   * Load all defined plugins.
   *
   * @static
   * @access public
   * @param string $sAllowedPlugins comma separated plugin names
   * @see config/Candy.inc.php
   * @return boolean true if no errors occurred.
   *
   */
  public static function getPlugins($sAllowedPlugins) {
    $aPlugins = preg_split("/[\s]*[,][\s]*/", $sAllowedPlugins);

    foreach ($aPlugins as $sPluginName) {
      try {
        if (!file_exists(PATH_STANDARD . '/plugins/controllers/' . (string) ucfirst($sPluginName) . '.controller.php'))
          throw new AdvancedException('Missing plugin: ' . ucfirst($sConfig));
        else
          require_once PATH_STANDARD . '/plugins/controllers/' . (string) ucfirst($sPluginName) . '.controller.php';
      }
      catch (AdvancedException $e) {
        die($e->getMessage());
      }
    }

    return true;
  }

  /**
   * Sets the language. This can be done via a language request and be temporarily saved in a cookie.
   *
   * @access public
   * @see config/Candy.inc.php
   *
   */
  public function getLanguage() {
    if (!defined('DEFAULT_LANGUAGE'))
      define('DEFAULT_LANGUAGE', 'en');

    # We got a language request? Let's switch the language!
    if (isset($this->_aRequest['language']) &&
						file_exists(PATH_STANDARD . '/languages/' . (string) $this->_aRequest['language'] . '.language.yml') &&
						!isset($this->_aRequest['section'])) {
      $this->_sLanguage = (string) $this->_aRequest['language'];
      setcookie('default_language', (string) $this->_aRequest['language'], time() + 2592000, '/');
      Helper::redirectTo('/');
			exit();
    }

    # There is no request, but there might be a cookie instead.
    else {
      $aRequest = isset($this->_aCookie) && is_array($this->_aCookie) ? array_merge($this->_aRequest, $this->_aCookie) : $this->_aRequest;

      $this->_sLanguage = isset($aRequest['default_language']) &&
              file_exists(PATH_STANDARD . '/languages/' . (string) $aRequest['default_language'] . '.language.yml') ?
              (string) $aRequest['default_language'] :
              strtolower(substr(DEFAULT_LANGUAGE, 0, 2));
    }

    # Set iso language codes
    switch ($this->_sLanguage) {
      case 'de':
        $sLocale = 'de_DE';
        break;

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

    if (!defined('WEBSITE_LANGUAGE'))
      define('WEBSITE_LANGUAGE', $this->_sLanguage);

    if (!defined('WEBSITE_LOCALE'))
      define('WEBSITE_LOCALE', $sLocale);

    return WEBSITE_LOCALE;
	}

  /**
   * Get the cronjob working. Check for last execution and plan next cleanup, optimization and backup.
   *
   * @access public
   * @param boolean $bForceAction force the cronjob to be executed.
   * @see config/Candy.inc.php
   *
   */
  public function getCronjob($bForceAction = false) {
    if (class_exists('\CandyCMS\Plugin\Cronjob')) {
      if (Cronjob::getNextUpdate() == true || $bForceAction === true) {
					Cronjob::cleanup(array('media', 'bbcode'));
					Cronjob::optimize();
					Cronjob::backup($this->_aSession['userdata']['id']);
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
    if (class_exists('\CandyCMS\Plugin\FacebookCMS')) {
      $this->_aSession['facebook'] = new FacebookCMS(array(
					'appId' => FACEBOOK_APP_ID,
					'secret' => FACEBOOK_SECRET,
					'cookie' => true
					));

      return $this->_aSession['facebook'];
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
    $aFlashMessage = array();
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
   * @return string $this->_sTemplate template name
   *
   */
  public function setTemplate() {
    # We got a template request? Let's change it!
    if (isset($this->_aRequest['template']) && is_dir(WEBSITE_CDN . '/templates/' . (string) $this->_aRequest['template'])) {
      $this->_sTemplate = (string) $this->_aRequest['template'];
      setcookie('default_template', (string) $this->_aRequest['template'], time() + 2592000, '/');
      Helper::redirectTo('/');
    }

    # There is no request, but there might be a cookie instead
    else {
      $aRequest = isset($this->_aCookie) && is_array($this->_aCookie) ? array_merge($this->_aRequest, $this->_aCookie) : $this->_aRequest;
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
  private function _checkForNewVersion() {
    if ($this->_aSession['userdata']['role'] == 4 && ALLOW_VERSION_CHECK == true) {
      $oFile = @fopen('http://www.empuxa.com/misc/candycms/version.txt', 'rb');
      $sVersionContent = @stream_get_contents($oFile);
      @fclose($oFile);

      $sVersionContent = $sVersionContent > VERSION ? (int) $sVersionContent : '';
    }

    $sLangUpdateAvaiable = isset($sVersionContent) && !empty($sVersionContent) ?
            str_replace('%v', $sVersionContent, I18n::get('global.update.avaiable')) :
            '';

    return str_replace('%l', Helper::createLinkTo('http://www.candycms.com', true), $sLangUpdateAvaiable);
  }

  /**
   * Return default user data.
   *
   * @static
   * @access protected
   * @return array default user data
   *
   */
  protected static function _resetUser() {
		return array(
				'email' => '',
				'facebook_id' => '',
				'id' => 0,
				'name' => '',
				'surname' => '',
				'password' => '',
				'role' => 0,
				'full_name' => ''
		);
	}

  /**
   * Define user constants for global use.
   *
   * List of user roles:
   * 0 = Guests / unregistered users
   * 1 = Members
   * 2 = Facebook users
   * 3 = Moderators
   * 4 = Administrators
   *
   * @access public
   * @see index.php
   * @return array $this->_aSession['userdata']
   *
   */
	public function setUser() {
    # Set standard variables
    $this->_aSession['userdata'] = & self::_resetUser();

    # Override them with user data
    # Get user data by token
    if (isset($this->_aRequest['api_token']) && !empty($this->_aRequest['api_token']))
      $aUserData = & Model_User::getUserDataByToken(Helper::formatInput($this->_aRequest['api_token']));

    # Get user data by session
    else
      $aUserData = & Model_Session::getUserDataBySession();

    $this->_aSession['userdata'] = & array_merge($this->_aSession['userdata'], is_array($aUserData) ? $aUserData : array());

    # Try to get facebook data
    if ($this->_aSession['userdata']['id'] == 0) {
      $oFacebook = $this->getFacebookExtension();

      if ($oFacebook == true)
        $aFacebookData = & $oFacebook->getUserData();

      # Override empty data with facebook data
      if (isset($aFacebookData)) {
        $this->_aSession['userdata']['facebook_id'] = isset($aFacebookData[0]['uid']) ?
                $aFacebookData[0]['uid'] :
                '';
        $this->_aSession['userdata']['email'] = isset($aFacebookData[0]['email']) ?
                $aFacebookData[0]['email'] :
                $this->_aSession['userdata']['email'];
        $this->_aSession['userdata']['name'] = isset($aFacebookData[0]['first_name']) ?
                $aFacebookData[0]['first_name'] :
                $this->_aSession['userdata']['name'];
        $this->_aSession['userdata']['surname'] = isset($aFacebookData[0]['last_name']) ?
                $aFacebookData[0]['last_name'] :
                $this->_aSession['userdata']['surname'];
        $this->_aSession['userdata']['role'] = isset($aFacebookData[0]['uid']) ?
                2 :
                (int) $this->_aSession['userdata']['role'];

        unset($aFacebookData);
      }
    }

    # Set up full name finally
    $this->_aSession['userdata']['full_name'] = & $this->_aSession['userdata']['name'] . ' ' . $this->_aSession['userdata']['surname'];

    return $this->_aSession['userdata'];
  }

  /**
  * Show the application.tpl with all header and footer data such as meta tags etc.
  *
  * @access public
  * @return string $sCachedHTML The whole HTML code of our application.
  *
  */
  public function show() {
    # Set a caching / compile ID
		# Ask if defined because of unit tests.
		if (!defined('UNIQUE_ID'))
			define('UNIQUE_ID', $this->_aRequest['section'] . '|' . substr(md5($this->_aSession['userdata']['role'] .
              WEBSITE_LOCALE . PATH_TEMPLATE), 0, 10) . '|' . substr(md5(CURRENT_URL), 0, 10));

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
      Helper::redirectTo('/install/index.php');

		# We do not have a standard action, so fetch it from the addon folder.
    # If addon exists, proceed with override.
    elseif (ALLOW_ADDONS === true || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test') {
			require PATH_STANDARD . '/addons/controllers/Addon.controller.php';

      $oSection = new Addon($this->_aRequest, $this->_aSession, $this->_aFile);
      $oSection->getSection();
    }

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
      $aFlashMessages = & $this->_getFlashMessage();
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
			$oSection->oSmarty->assign('_title_', $oSection->getTitle());
      $oSection->oSmarty->assign('_update_avaiable_', $this->_checkForNewVersion());

      $sTemplateDir		= Helper::getTemplateDir('layouts', 'application');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'application');

      $oSection->oSmarty->setTemplateDir($sTemplateDir);
      $sCachedHTML = $oSection->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
		}

		# Build absolute URLs
    # *********************************************
		$sCachedHTML = str_replace('%PATH_PUBLIC%', WEBSITE_CDN, $sCachedHTML);
		$sCachedHTML = str_replace('%PATH_TEMPLATE%', WEBSITE_CDN . '/templates/' . PATH_TEMPLATE, $sCachedHTML);
		$sCachedHTML = str_replace('%PATH_UPLOAD%', PATH_UPLOAD, $sCachedHTML);

		# Check for template files
    # *********************************************
    # We use templates via template request.
    if (!empty($this->_sTemplate) && substr(WEBSITE_CDN, 0, 4) == 'http') {
      $sPath    = WEBSITE_CDN . '/templates/' . $this->_sTemplate;

      $sCachedCss     = $sPath . '/css';
      $sCachedImages  = $sPath . '/images';
      $sCachedLess    = $sPath . '/less';
      $sCachedJs      = $sPath . '/js';
    }

    # Intern use with template request
    elseif (!empty($this->_sTemplate) && substr(WEBSITE_CDN, 0, 4) !== 'http') {
      $sPath    = WEBSITE_CDN . '/templates/' . $this->_sTemplate;

      $sCachedCss     = @is_dir(substr($sPath, 1) . '/css') ? $sPath . '/css' : WEBSITE_CDN . '/css';
      $sCachedImages  = @is_dir(substr($sPath, 1) . '/images') ? $sPath . '/images' : WEBSITE_CDN . '/images';
      $sCachedLess    = @is_dir(substr($sPath, 1) . '/less') ? $sPath . '/less' : WEBSITE_CDN . '/less';
      $sCachedJs      = @is_dir(substr($sPath, 1) . '/js') ? $sPath . '/js' : WEBSITE_CDN . '/js';
    }

    # We use templates defined in our Candy.inc.php
    elseif (PATH_TEMPLATE !== '' && substr(WEBSITE_CDN, 0, 4) == 'http') {
      $sPath    = WEBSITE_CDN . '/templates/' . PATH_TEMPLATE;

      $sCachedCss     = $sPath . '/css';
      $sCachedImages  = $sPath . '/images';
      $sCachedLess    = $sPath . '/less';
      $sCachedJs      = $sPath . '/js';
    }
    elseif(PATH_TEMPLATE !== '' && substr(WEBSITE_CDN, 0, 4) !== 'http') {
      $sPath    = WEBSITE_CDN . '/templates/' . PATH_TEMPLATE;

      $sCachedCss     = @is_dir(substr($sPath, 1) . '/css') ? $sPath . '/css' : WEBSITE_CDN . '/css';
      $sCachedImages  = @is_dir(substr($sPath, 1) . '/images') ? $sPath . '/images' : WEBSITE_CDN . '/images';
      $sCachedLess    = @is_dir(substr($sPath, 1) . '/less') ? $sPath . '/less' : WEBSITE_CDN . '/less';
      $sCachedJs      = @is_dir(substr($sPath, 1) . '/js') ? $sPath . '/js' : WEBSITE_CDN . '/js';
    }
    else {
      $sCachedCss     = WEBSITE_CDN . '/css';
      $sCachedImages  = WEBSITE_CDN . '/images';
      $sCachedLess    = WEBSITE_CDN . '/less';
      $sCachedJs      = WEBSITE_CDN . '/js';
    }

    $sCachedHTML = & str_replace('%PATH_CSS%', $sCachedCss, $sCachedHTML);
    $sCachedHTML = & str_replace('%PATH_IMAGES%', $sCachedImages, $sCachedHTML);
    $sCachedHTML = & str_replace('%PATH_LESS%', $sCachedLess, $sCachedHTML);
    $sCachedHTML = & str_replace('%PATH_JS%', $sCachedJs, $sCachedHTML);


		# Load plugins
    # *********************************************
    if (ALLOW_PLUGINS !== '' && WEBSITE_MODE !== 'test')
      $sCachedHTML = $this->_showPlugins($sCachedHTML);

    # Do only send html charset if we are really sure. This caused problems with .ics files.
    if (AJAX_REQUEST === false)
      header("Content-Type: text/html; charset=utf-8");

    return $sCachedHTML;
	}

  /**
   * Get and replace plugin placeholders. This is done at the end of execution for performance reasons.
   *
   * @access private
   * @param string $sCachedHTML
   * @return string HTML content
   *
   */
  private function _showPlugins($sCachedHTML) {
    # Fix search bug
    unset($this->_aRequest['id'], $this->_aRequest['search'], $this->_aRequest['page']);
    $this->_aSession['userdata'] = self::_resetUser();

    if (preg_match('/<!-- plugin:adsense -->/', $sCachedHTML) && class_exists('\CandyCMS\Plugin\Adsense')) {
      $oAdsense = new \CandyCMS\Plugin\Adsense();
      $sCachedHTML = & str_replace('<!-- plugin:adsense -->', $oAdsense->show(), $sCachedHTML);
    }

    if (preg_match('/<!-- plugin:archive -->/', $sCachedHTML) && class_exists('\CandyCMS\Plugin\Archive')) {
      $oArchive = new \CandyCMS\Plugin\Archive($this->_aRequest, $this->_aSession);
      $oArchive->__init();
      $sCachedHTML = & str_replace('<!-- plugin:archive -->', $oArchive->show(), $sCachedHTML);
    }

    if (preg_match('/<!-- plugin:headlines -->/', $sCachedHTML) && class_exists('\CandyCMS\Plugin\Headlines')) {
      $oHeadlines = new \CandyCMS\Plugin\Headlines($this->_aRequest, $this->_aSession);
      $oHeadlines->__init();
      $sCachedHTML = & str_replace('<!-- plugin:headlines -->', $oHeadlines->show(), $sCachedHTML);
    }

    return $sCachedHTML;
  }
}
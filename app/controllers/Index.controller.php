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
	}

  /**
  * Load all config files.
  *
  * @access public
  * @param array $aConfigs array of config files
  * @param string $sPath Path prefix. Needed when not in root path
  * @see install/index.php
  *
  */
  public function loadConfigFiles($aConfigs, $sPath = '') {
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
  public function loadPlugins($sAllowedPlugins, $sPath = '') {
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
  public function loadLanguage($sPath = '') {
		# We got a language request? Let's change it!
		if (isset($this->_aRequest['language'])) {
			setcookie('default_language', (string) $this->_aRequest['language'], time() + 2592000, '/');
			$this->_sLanguage = (string) $this->_aRequest['language'];
		}
    # There is no request, but there might be a cookie instead
		else {
			$aRequest = isset($this->_aCookie) ? array_merge($this->_aRequest, $this->_aCookie) : $this->_aRequest;
			$this->_sLanguage = isset($aRequest['default_language']) ?
							(string) $aRequest['default_language'] :
							substr(DEFAULT_LANGUAGE, 0, 2);
		}

		# Set iso language codes
    switch ($this->_sLanguage) {
      default:
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

		define('WEBSITE_LANGUAGE',  $this->_sLanguage);
		define('WEBSITE_LOCALE',    $sLocale);

    # @deprecated
    # Include language if possible
		try {
      if (!file_exists($sPath . 'languages/' . $this->_sLanguage . '/' . $this->_sLanguage . '.language.php'))
        throw new AdvancedException('Missing language file.');
      else
        require_once $sPath . 'languages/' . $this->_sLanguage . '/' . $this->_sLanguage . '.language.php';
    }
    catch (AdvancedException $e) {
      die($e->getMessage());
    }
	}

  /**
  * Get the cronjob working. Check for last execution and plan next cleanup, optimization and backup.
  *
  * @access public
  * @see config/Candy.inc.php
  *
  */
  public function loadCronjob() {
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
  * @todo better return
  *
  */
	public function loadFacebookExtension() {
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
    $aFlashMessage['type'] = isset($this->_aSession['flash_message']['type']) && !empty($this->_aSession['flash_message']['type']) ?
            $this->_aSession['flash_message']['type'] :
            '';
    $aFlashMessage['message'] = isset($this->_aSession['flash_message']['message']) && !empty($this->_aSession['flash_message']['message']) ?
            $this->_aSession['flash_message']['message'] :
            '';
    $aFlashMessage['headline'] = isset($this->_aSession['flash_message']['headline']) && !empty($this->_aSession['flash_message']['headline']) ?
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
  protected function _setTemplate() {
		# We got a template request? Let's change it!
    if (isset($this->_aRequest['template'])) {
      setcookie('default_template', (string) $this->_aRequest['template'], time() + 2592000, '/');
      $this->_sTemplate = (string) $this->_aRequest['template'];
    }
    # There is no request, but there might be a cookie instead
    else {
      $aRequest = isset($this->_aCookie) ? array_merge($this->_aRequest, $this->_aCookie) : $this->_aRequest;
      $this->_sTemplate = isset($aRequest['default_template']) ?
              (string) $aRequest['default_template'] :
              '';
    }

    return $this->_sTemplate;
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
   * @see index.php
   *
   */
	public function setUser() {
    $aUser = Model_Session::getSessionData();

    define('USER_ID', (int) $aUser['id']);
    define('USER_PASSWORD', isset($aUser['password']) ? $aUser['password'] : '');

    # Try to get facebook data
    if (USER_ID == 0) {
      $oFacebook = $this->loadFacebookExtension();
      if ($oFacebook == true)
        $aFacebookData = $oFacebook->getUserData();
    }

    define('USER_RIGHT', isset($aFacebookData[0]['uid']) ? 2 : (int) $aUser['user_right']);
    define('USER_FACEBOOK_ID', isset($aFacebookData[0]['uid']) ? $aFacebookData[0]['uid'] : '');
    define('USER_EMAIL', isset($aFacebookData[0]['email']) ? $aFacebookData[0]['email'] : $aUser['email']);
    define('USER_NAME', isset($aFacebookData[0]['first_name']) ? $aFacebookData[0]['first_name'] : $aUser['name']);
    define('USER_SURNAME', isset($aFacebookData[0]['last_name']) ? $aFacebookData[0]['last_name'] : $aUser['surname']);
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
		# Redirect to landing page if we got no section
    if (!isset($this->_aRequest['section'])) {
      Helper::redirectTo('/' . WEBSITE_LANDING_PAGE);
      exit();
    }

    # Define out core modules. All of them are separately handled in app/helper/Section.helper.php
		if (!isset($this->_aRequest['section']) ||
						empty($this->_aRequest['section']) ||
						strtolower($this->_aRequest['section']) == 'blog' ||
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

		# We do not have a standard action, so fetch it from the addon folder.
		# If addon exists, proceed with override.
		elseif (ALLOW_ADDONS === true) {
      $oSection = new Addon($this->_aRequest, $this->_aSession, $this->_aFile);
      $oSection->getSection();
    }

    # Redirect to start page
    elseif (strtolower($this->_aRequest['section']) == 'start')
      Helper::redirectTo('/');

		# There's no request on a core module and Addons are disabled. */
		else {
			header('Status: 404 Not Found');
      Helper::redirectTo('/error/404');
    }

    # Set template
    $this->_setTemplate();

		# Load JS language
    $sLangVars = '';
    $oFile = fopen('languages/' . $this->_sLanguage . '/' . $this->_sLanguage . '.language.js', 'rb');

    while (!feof($oFile)) {
      $sLangVars .= fgets($oFile);
    }

		# Header.tpl
		$oSection->oSmarty->assign('user', USER_NAME);

		# Check for new version of script
		if (USER_RIGHT == 4 && ALLOW_VERSION_CHECK == true) {
      $oFile = @fopen('http://www.empuxa.com/misc/candycms/version.txt', 'rb');
      $sVersionContent = @stream_get_contents($oFile);
      @fclose($oFile);

      $sVersionContent &= ( $sVersionContent > VERSION ) ? (int) $sVersionContent : '';
    }

    $sLangUpdateAvaiable = isset($sVersionContent) && !empty($sVersionContent) ?
            str_replace('%v', $sVersionContent, LANG_GLOBAL_UPDATE_AVAIABLE) :
            '';
    $sLangUpdateAvaiable = str_replace('%l', Helper::createLinkTo('http://candycms.com', true), $sLangUpdateAvaiable);

		# System variables
		$oSection->oSmarty->assign('_javascript_language_file_', $sLangVars);
		$oSection->oSmarty->assign('_update_avaiable_', $sLangUpdateAvaiable);

		# Get possible flash messages
		$aFlashMessages = $this->_getFlashMessage();

		# Replace flash message with content
		$oSection->oSmarty->assign('_flash_type_', $aFlashMessages['type']);
		$oSection->oSmarty->assign('_flash_message_', $aFlashMessages['message']);
		$oSection->oSmarty->assign('_flash_headline_', $aFlashMessages['headline']);

		# Global language
		$oSection->oSmarty->assign('lang_newsletter_handle', LANG_NEWSLETTER_HANDLE_TITLE);
		$oSection->oSmarty->assign('lang_newsletter_create', LANG_NEWSLETTER_CREATE_TITLE);


		# Avoid Header and Footer HTML if RSS or AJAX are requested
		if ((isset($this->_aRequest['section']) && 'rss' == strtolower($this->_aRequest['section'])) ||
						(isset($this->_aRequest['ajax']) && true == $this->_aRequest['ajax']))
			$sCachedHTML = $oSection->getContent();

		else {
			$oSection->oSmarty->assign('meta_expires', gmdate('D, d M Y H:i:s', time() + 60) . ' GMT');
			$oSection->oSmarty->assign('meta_description', $oSection->getDescription());
			$oSection->oSmarty->assign('meta_keywords', $oSection->getKeywords());
			$oSection->oSmarty->assign('meta_og_description', $oSection->getDescription());
			$oSection->oSmarty->assign('meta_og_site_name', WEBSITE_NAME);
			$oSection->oSmarty->assign('meta_og_title', $oSection->getTitle());
			$oSection->oSmarty->assign('meta_og_url', CURRENT_URL);

      # We must recreate the request id because it's yet only set in the Main.controller.php
      $oSection->oSmarty->assign('_request_id_', isset($this->_aRequest['id']) ? (int)$this->_aRequest['id'] : '');
			$oSection->oSmarty->assign('_content_', $oSection->getContent());
			$oSection->oSmarty->assign('_title_', $oSection->getTitle() . ' - ' . LANG_WEBSITE_TITLE);

			$oSection->oSmarty->template_dir = Helper::getTemplateDir('layouts', 'application');
			$sCachedHTML = $oSection->oSmarty->fetch('application.tpl');
		}

		# Build absolute Path because of pretty URLs
		$sCachedHTML = str_replace('%PATH_PUBLIC%', WEBSITE_CDN . '/public', $sCachedHTML);
		$sCachedHTML = str_replace('%PATH_TEMPLATE%', WEBSITE_CDN . '/public/templates/' . PATH_TEMPLATE, $sCachedHTML);
		$sCachedHTML = str_replace('%PATH_UPLOAD%', WEBSITE_URL . '/' . PATH_UPLOAD, $sCachedHTML);

		# Check for user custom css
		$sCachedCss = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/css', $sCachedHTML);
		if (!empty($this->_sTemplate))
			$sCachedCss = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/templates/' . $this->_sTemplate . '/css', $sCachedHTML);

		elseif (PATH_TEMPLATE !== '')
			$sCachedCss = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/templates/' . PATH_TEMPLATE . '/css', $sCachedHTML);

		$sCachedHTML = & $sCachedCss;

		# Check for user custom icons etc.
		$sCachedImages = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/images', $sCachedHTML);
		if (!empty($this->_sTemplate))
			$sCachedImages = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/templates/' . $this->_sTemplate . '/images', $sCachedHTML);

		elseif (PATH_TEMPLATE !== '')
			$sCachedImages = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/templates/' . PATH_TEMPLATE . '/images', $sCachedHTML);

		$sCachedHTML = & $sCachedImages;

		# Cut spaces to minimize filesize
		$sCachedHTML = str_replace('	', '', $sCachedHTML); # Normal tab
		$sCachedHTML = str_replace('  ', '', $sCachedHTML); # Tab as two spaces

		# Compress Data
		if (extension_loaded('zlib'))
			@ob_start('ob_gzhandler');

		return $sCachedHTML;
	}
}
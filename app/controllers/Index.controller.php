<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Index extends Main {

  protected $_aRequest;
  protected $_aSession;
  protected $_aFile;
  protected $_aCookie;
  private $_sSkin;
  private $_sLanguage;

  public final function __construct($aRequest, $aSession, $aFile = '', $aCookie = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;
    $this->_aCookie 	= & $aCookie;
  }

  public final function loadConfig($sPath = '') {
		try {
			if (!file_exists($sPath . 'config/Config.inc.php'))
				throw new AdvancedException('Missing config file.');
			else
				require_once $sPath . 'config/Config.inc.php';
		}
		catch (AdvancedException $e) {
			die($e->getMessage());
		}

		if (file_exists($sPath . 'config/Facebook.inc.php'))
			require_once $sPath . 'config/Facebook.inc.php';
	}

  public final function setBasicConfiguration() {
		try {
			if (is_dir('install') && WEBSITE_DEV == false)
				throw new AdvancedException('Please install software via <strong>install/</strong> and delete the folder afterwards!');
		}
		catch (AdvancedException $e) {
			die($e->getMessage());
		}
	}

  public final function setSkin() {
		# We got a skin request? Let's change it!
		if (isset($this->_aRequest['skin'])) {
			setcookie('default_skin', (string) $this->_aRequest['skin'], time() + 2592000, '/');
			$this->_sSkin = (string) $this->_aRequest['skin'];

    # There is no request, but there might be a cookie
		}
		else {
			$aRequest = isset($this->_aCookie) ? array_merge($this->_aRequest, $this->_aCookie) : $this->_aRequest;
			$this->_sSkin = isset($aRequest['default_skin']) ?
							(string) $aRequest['default_skin'] :
							'';
		}
  }

  public final function setLanguage($sPath = '') {
		# We got a language request? Let's change it!
		if (isset($this->_aRequest['language'])) {
			setcookie('default_language', (string) $this->_aRequest['language'], time() + 2592000, '/');
			$this->_sLanguage = (string) $this->_aRequest['language'];

    # There is no request, but there might be a cookie
		}
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

		define('WEBSITE_LANGUAGE', $this->_sLanguage);
		define('WEBSITE_LOCALE', $sLocale);

		if (file_exists($sPath . 'languages/' . $this->_sLanguage . '/' . $this->_sLanguage . '.language.php'))
			require_once $sPath . 'languages/' . $this->_sLanguage . '/' . $this->_sLanguage . '.language.php';

		else
			die(LANG_ERROR_GLOBAL_NO_LANGUAGE);
	}

  public final function loadPlugins() {
		$sPlugins = ALLOW_PLUGINS;
		$aPlugins = preg_split("/[\s]*[,][\s]*/", $sPlugins);

		foreach ($aPlugins as $sPluginName) {
			if (file_exists('plugins/' . (string) ucfirst($sPluginName) . '.class.php'))
				require_once 'plugins/' . (string) ucfirst($sPluginName) . '.class.php';
		}
	}

	# Give the users the ability to login via their facebook information
	public final function loadFacebookPlugin() {
		if (class_exists('FacebookCMS')) {
			return new FacebookCMS(array(
					'appId' => FACEBOOK_APP_ID,
					'secret' => FACEBOOK_SECRET,
					'cookie' => true,
			));
		}
	}

  public final function getActiveUser($iSessionId = '') {
		$this->_aSession['userdata'] = Model_Session::getSessionData($iSessionId);
		return $this->_aSession['userdata'];
	}

  private final function _getFlashMessage() {
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

  public final function loadCronjob() {
		if (class_exists('Cronjob')) {
			if (Cronjob::getNextUpdate() === true) {
				Cronjob::cleanup();
				Cronjob::optimize();
				Cronjob::backup(USER_ID);
			}
		}
	}

  public final function show() {
		# Redirect to landing page if we got no section
		if (!isset($this->_aRequest['section'])) {
			Helper::redirectTo('/' . WEBSITE_LANDING_PAGE);
			die();
		}

		# Load JS language
		$sLangVars = '';
		$oFile = fopen('languages/' . $this->_sLanguage . '/' . $this->_sLanguage . '.language.js', 'rb');

		while (!feof($oFile)) {
			$sLangVars .= fgets($oFile);
		}

		# Check for new version of script
		if (USER_RIGHT == 4 && ALLOW_VERSION_CHECK == true) {
			$oFile = fopen('http://candycms.marcoraddatz.com/version.txt', 'rb');
			$sVersionContent = stream_get_contents($oFile);
			fclose($oFile);

			$sVersionContent &= ( $sVersionContent > VERSION ) ? (int) $sVersionContent : '';
		}

		# Header.tpl
		$oSmarty = $this->_setSmarty();
		$oSmarty->assign('user', USER_NAME);

		# Check for update
		$sLangUpdateAvaiable = isset($sVersionContent) && !empty($sVersionContent) ?
						str_replace('%v', $sVersionContent, LANG_GLOBAL_UPDATE_AVAIABLE) :
						'';

		$sLangUpdateAvaiable = str_replace(
										'%l',
										Helper::createLinkTo('http://candycms.com', true),
										$sLangUpdateAvaiable);

		# System variables
		$oSmarty->assign('_javascript_language_file_', $sLangVars);
		$oSmarty->assign('_update_avaiable_', $sLangUpdateAvaiable);

		# Get possible flash messages
		$aFlashMessages = $this->_getFlashMessage();

		# Replace Flash Message with Content
		$oSmarty->assign('_flash_type_', $aFlashMessages['type']);
		$oSmarty->assign('_flash_message_', $aFlashMessages['message']);
		$oSmarty->assign('_flash_headline_', $aFlashMessages['headline']);

		# Language
		$oSmarty->assign('lang_newsletter_handle', LANG_NEWSLETTER_HANDLE_TITLE);
		$oSmarty->assign('lang_newsletter_create', LANG_NEWSLETTER_CREATE_TITLE);

		# Define out core modules. All of them are separately handled in app/helper/Section.helper.php
		if (!isset($this->_aRequest['section']) ||
						empty($this->_aRequest['section']) ||
						strtolower($this->_aRequest['section']) == 'blog' ||
						strtolower($this->_aRequest['section']) == 'comment' ||
						strtolower($this->_aRequest['section']) == 'content' ||
						strtolower($this->_aRequest['section']) == 'gallery' ||
						strtolower($this->_aRequest['section']) == 'language' ||
						strtolower($this->_aRequest['section']) == 'mail' ||
						strtolower($this->_aRequest['section']) == 'media' ||
						strtolower($this->_aRequest['section']) == 'newsletter' ||
            strtolower($this->_aRequest['section']) == 'rss' ||
            strtolower($this->_aRequest['section']) == 'search' ||
						strtolower($this->_aRequest['section']) == 'session' ||
            strtolower($this->_aRequest['section']) == 'static' ||
						strtolower($this->_aRequest['section']) == 'user') {

			$oSection = new Section($this->_aRequest, $this->_aSession, $this->_aFile);
			$oSection->getSection();
		}

		# We do not have a standard action, so fetch it from the addon folder.
		# If addon exists, proceed with override.
		elseif (ALLOW_ADDONS === true && file_exists('app/helpers/Addon.helper.php')) {
			require_once 'app/helpers/Addon.helper.php';
      $oSection = new Addon($this->_aRequest, $this->_aSession, $this->_aFile);
      $oSection->setModules();
      $oSection->getSection();
    }

    # Redirect to start page
    elseif (strtolower($this->_aRequest['section']) == 'start')
      Helper::redirectTo('/');

		# There's no request on a core module and Addons are disabled. */
		else {
			header('Status: 404 Not Found');
      Helper::redirectTo('/public/404.html');
    }

		# Avoid Header and Footer HTML if RSS or AJAX are requested
		if ((isset($this->_aRequest['section']) && 'rss' == strtolower($this->_aRequest['section'])) ||
						(isset($this->_aRequest['ajax']) && true == $this->_aRequest['ajax']))
			$sCachedHTML = $oSection->getContent();

		else {
			$oSmarty->assign('meta_expires', gmdate('D, d M Y H:i:s', time() + 60) . ' GMT');
			$oSmarty->assign('meta_description', LANG_WEBSITE_SLOGAN);
			$oSmarty->assign('meta_keywords', LANG_WEBSITE_KEYWORDS);
			$oSmarty->assign('meta_og_title', $oSection->getTitle());
			$oSmarty->assign('meta_og_url', CURRENT_URL);
			$oSmarty->assign('meta_og_site_name', WEBSITE_NAME);

			$oSmarty->assign('_content_', $oSection->getContent());
			$oSmarty->assign('_title_', $oSection->getTitle() . ' - ' . LANG_WEBSITE_TITLE);

			$oSmarty->template_dir = Helper::getTemplateDir('layouts/application');
			$sCachedHTML = $oSmarty->fetch('layouts/application.tpl');
		}

		# Build absolute Path because of Pretty URLs
		$sCachedHTML = str_replace('%PATH_PUBLIC%', WEBSITE_CDN . '/public', $sCachedHTML);
		$sCachedHTML = str_replace('%PATH_UPLOAD%', WEBSITE_URL . '/' . PATH_UPLOAD, $sCachedHTML);

		# Check for user custom css
		$sCachedCss = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/css', $sCachedHTML);
		if (!empty($this->_sSkin))
			$sCachedCss = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/skins/' . $this->_sSkin . '/css', $sCachedHTML);
		elseif (PATH_CSS !== '')
			$sCachedCss = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/skins/' . PATH_CSS . '/css', $sCachedHTML);

		$sCachedHTML = & $sCachedCss;

		# Check for user custom icons etc.
		$sCachedImages = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/images', $sCachedHTML);
		if (!empty($this->_sSkin))
			$sCachedImages = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/skins/' . $this->_sSkin . '/images', $sCachedHTML);
		elseif (PATH_IMAGES !== '')
			$sCachedImages = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/skins/' . PATH_CSS . '/images', $sCachedHTML);

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
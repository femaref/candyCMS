<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Index {

  protected $_aRequest;
  protected $_aSession;
  protected $_aFile;
  protected $_aCookie;

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

  public final function setLanguage($sPath = '') {
    if (isset($this->_aRequest['language']))
      setcookie('language', (string) $this->_aRequest['language'], time() + 2592000, '/');

    $this->_sLanguage = isset($this->_aCookie['language']) ?
            (string) $this->_aCookie['language'] :
            substr(DEFAULT_LANGUAGE, 0, 2);

    define( 'WEBSITE_LANGUAGE', $this->_sLanguage);

    if (file_exists($sPath . 'languages/' . $this->_sLanguage . '/' . $this->_sLanguage . '.language.php'))
      require_once $sPath . 'languages/' . $this->_sLanguage . '/' . $this->_sLanguage . '.language.php';

    else
      die(LANG_ERROR_GLOBAL_NO_LANGUAGE);
  }

  public final function loadAddons() {
    if (ALLOW_ADDONS == true && file_exists('helpers/Addon.helper.php'))
      require_once 'helpers/Addon.helper.php';
  }

  public final function loadPlugins() {
		$sPlugins = ALLOW_PLUGINS;
		$aPlugins = preg_split("/[\s]*[,][\s]*/", $sPlugins);

		foreach($aPlugins as $sPluginName) {
			if (file_exists('plugins/' . (string) ucfirst($sPluginName) . '.class.php'))
				require_once 'plugins/' . (string) ucfirst($sPluginName) . '.class.php';
		}
  }

  public final function setActiveUser($iSessionId = '') {
    $this->_aSession['userdata'] =  Model_Session::getSessionData($iSessionId);
    return $this->_aSession['userdata'];
  }

  protected final function _getFlashMessage() {
    $aFlashMessage['type'] = isset($this->_aSession['flash_message']['type']) && !empty($this->_aSession['flash_message']['type']) ?
						$this->_aSession['flash_message']['type'] :
						'';
		$aFlashMessage['message'] = isset($this->_aSession['flash_message']['message']) && !empty($this->_aSession['flash_message']['message']) ?
						$this->_aSession['flash_message']['message'] :
						'';
		$aFlashMessage['headline'] = isset($this->_aSession['flash_message']['headline']) && !empty($this->_aSession['flash_message']['headline']) ?
						$this->_aSession['flash_message']['headline'] :
						'';

		# This must be a global
    unset($_SESSION['flash_message']);
    return $aFlashMessage;
  }

  public final function loadCronjob() {
    if (class_exists('Cronjob')) {
      if(Cronjob::getNextUpdate() === true) {
        Cronjob::cleanup();
        Cronjob::optimize();
        Cronjob::backup(USER_ID);
      }
    }
  }

  public final function show() {
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

      $sVersionContent &= ($sVersionContent > VERSION) ? (int) $sVersionContent : '';
    }

    # Set expiration date for header
    $sHeaderExpires = gmdate('D, d M Y H:i:s', time() + 60) . ' GMT';

    # Header.tpl
    $oSmarty = new Smarty();
    $oSmarty->assign('user', Helper::formatOutput($this->_aSession['userdata']['name']));
    $oSmarty->assign('USER_ID', USER_ID);
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);

    # System variables
    $oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');
    $oSmarty->assign('_javascript_language_file_', $sLangVars);
    $oSmarty->assign('_language_', WEBSITE_LANGUAGE);
    $oSmarty->assign('_website_name_', WEBSITE_NAME);
    $oSmarty->assign('_website_url_', WEBSITE_URL);

    # Language
    $sLangUpdateAvaiable = isset($sVersionContent) && !empty($sVersionContent) ?
            str_replace('%v', $sVersionContent, LANG_GLOBAL_UPDATE_AVAIABLE) :
            '';

    $sLangUpdateAvaiable = str_replace('%l', Helper::createLinkTo('http://candycms.com', true), $sLangUpdateAvaiable);

    $oSmarty->assign('lang_about', LANG_GLOBAL_ABOUT);
    $oSmarty->assign('lang_blog', LANG_GLOBAL_BLOG);
    $oSmarty->assign('lang_contentmanager', LANG_GLOBAL_CONTENTMANAGER);
    $oSmarty->assign('lang_cronjob_exec', LANG_GLOBAL_CRONJOB_EXEC);
    $oSmarty->assign('lang_disclaimer', LANG_GLOBAL_DISCLAIMER);
    $oSmarty->assign('lang_filemanager', LANG_GLOBAL_FILEMANAGER);
    $oSmarty->assign('lang_gallery', LANG_GLOBAL_GALLERY);
    $oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);
    $oSmarty->assign('lang_logout', LANG_GLOBAL_LOGOUT);
    $oSmarty->assign('lang_message_close', LANG_GLOBAL_MESSAGE_CLOSE);
    $oSmarty->assign('lang_newsletter_handle', LANG_NEWSLETTER_HANDLE_TITLE);
    $oSmarty->assign('lang_newsletter_send', LANG_NEWSLETTER_CREATE_TITLE);
    $oSmarty->assign('lang_register', LANG_GLOBAL_REGISTER);
    $oSmarty->assign('lang_report_error', LANG_GLOBAL_REPORT_ERROR);
    $oSmarty->assign('lang_search', LANG_GLOBAL_SEARCH);
    $oSmarty->assign('lang_settings', LANG_GLOBAL_SETTINGS);
    $oSmarty->assign('lang_update_avaiable', $sLangUpdateAvaiable);
    $oSmarty->assign('lang_usermanager', LANG_GLOBAL_USERMANAGER);
    $oSmarty->assign('lang_welcome', LANG_GLOBAL_WELCOME);

    /* Define Core Modules - check if we use a standard action. If yes, forward to
     * Section.class.php where we check for an override of this core modules. If we
     * want to override the core module, we got to load Addons later in Section.class.php */
    if (!isset($this->_aRequest['section']) ||
						empty($this->_aRequest['section']) ||
						ucfirst($this->_aRequest['section']) == 'Blog' ||
						ucfirst($this->_aRequest['section']) == 'Comment' ||
						ucfirst($this->_aRequest['section']) == 'Content' ||
						ucfirst($this->_aRequest['section']) == 'Gallery' ||
						ucfirst($this->_aRequest['section']) == 'Language' ||
						ucfirst($this->_aRequest['section']) == 'Mail' ||
						ucfirst($this->_aRequest['section']) == 'Media' ||
						ucfirst($this->_aRequest['section']) == 'Newsletter' ||
						ucfirst($this->_aRequest['section']) == 'RSS' ||
						ucfirst($this->_aRequest['section']) == 'Search' ||
						ucfirst($this->_aRequest['section']) == 'Session' ||
						ucfirst($this->_aRequest['section']) == 'Static' ||
						ucfirst($this->_aRequest['section']) == 'User') {

      $oSection = new Section($this->_aRequest, $this->_aSession, $this->_aFile);
      $oSection->getSection();
    }
    /* We do not have a standard action, so let's take a look, if we have the required
     * Addon in addon. If we do have, proceed with own action. */ elseif (ALLOW_ADDONS == true)
      $oSection = new Addon($this->_aRequest, $this->_aSession, $this->_aFile);
    # There's no request on a core module and Addons are disabled. */
    else {
      header('Status: 404 Not Found');
      die(LANG_ERROR_GLOBAL_404);
    }

    # Avoid Header and Footer HTML if RSS or AJAX are requested
    if ((isset($this->_aRequest['section']) && 'RSS' == $this->_aRequest['section']) ||
            (isset($this->_aRequest['ajax']) && true == $this->_aRequest['ajax']))
      $sCachedHTML = $oSection->getContent();

    else {
      $oSmarty->assign('_title_', $oSection->getTitle() . ' - ' . LANG_WEBSITE_TITLE);
      $oSmarty->assign('meta_expires', $sHeaderExpires);
      $oSmarty->assign('meta_description', LANG_WEBSITE_SLOGAN);
      $oSmarty->assign('meta_keywords', LANG_WEBSITE_KEYWORDS);

      # Include optional plugins
      if (class_exists('Archive')) {
        $oArchive = new Archive($this->_aRequest, $this->_aSession);
        $oSmarty->assign('_plugin_archive_', $oArchive->show());
      }

      if (class_exists('Headlines')) {
        $oHeadlines = new Headlines($this->_aRequest, $this->_aSession);
        $oSmarty->assign('_plugin_headlines_', $oHeadlines->show());
      }

      $oSmarty->assign('_content_', $oSection->getContent());

      $oSmarty->cache_dir = CACHE_DIR;
      $oSmarty->compile_dir = COMPILE_DIR;
      $oSmarty->template_dir = Helper::getTemplateDir('layouts/application');
      $oSmarty->isCached('layouts/application.tpl');
      $sCachedHTML = $oSmarty->fetch('layouts/application.tpl');
    }

    # Get possible flash messages
    $aFlashMessages = $this->_getFlashMessage();

    # Replace Flash Message with Content
    $sCachedHTML = str_replace('%FLASH_TYPE%', $aFlashMessages['type'], $sCachedHTML);
    $sCachedHTML = str_replace('%FLASH_MESSAGE%', $aFlashMessages['message'], $sCachedHTML);
    $sCachedHTML = str_replace('%FLASH_HEADLINE%', $aFlashMessages['headline'], $sCachedHTML);

    # Build absolute Path because of Pretty URLs
    $sCachedHTML = str_replace('%PATH_PUBLIC%', WEBSITE_CDN . '/public', $sCachedHTML);
    $sCachedHTML = str_replace('%PATH_UPLOAD%', WEBSITE_URL . '/' . PATH_UPLOAD, $sCachedHTML);

    # Check for user custom css
    $sCachedCss = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/css', $sCachedHTML);
    if (PATH_CSS !== '')
      $sCachedCss = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/skins/' . PATH_CSS . '/css', $sCachedHTML);

    $sCachedHTML = & $sCachedCss;

    # Check for user custom icons etc.
    $sCachedImages = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/images', $sCachedHTML);
    if (PATH_IMAGES !== '')
      $sCachedImages = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/skins/' . PATH_CSS . '/images', $sCachedHTML);

    $sCachedHTML = & $sCachedImages;

    # Cut spaces to minimize filesize
    # Normal tab
    $sCachedHTML = str_replace('	', '', $sCachedHTML);

    # Tab as two spaces
    $sCachedHTML = str_replace('  ', '', $sCachedHTML);

    # Compress Data
    if (extension_loaded('zlib'))
      @ob_start('ob_gzhandler');

    return $sCachedHTML;
  }
}
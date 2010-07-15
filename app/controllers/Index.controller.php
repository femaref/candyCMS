<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Index {
  protected $m_aRequest;
  protected $m_oSession;
  protected $m_aFile;
  protected $m_aCookie;

  public final function __construct($aRequest, $oSession, $aFile = '', $aCookie = '') {
    $this->m_aRequest	=& $aRequest;
    $this->m_oSession	=& $oSession;
    $this->m_aFile		=& $aFile;
    $this->m_aCookie	=& $aCookie;
  }

  public final function loadConfig($sPath = '') {
    if( file_exists($sPath. 'config/Config.inc.php') )
      require_once $sPath. 'config/Config.inc.php';
    else
      die('Config could not be loaded.');
  }

  public final function checkURL() {
    if(file_exists('install/index.php') && WEBSITE_DEV == false)
      die('Please install software via <strong>install/index.php</strong> and delete the folder afterwards!');
    # TODO: LANG!

    if(	WEBSITE_DEV == false &&
            (WEBSITE_URL !== 'http://'	.$_SERVER['SERVER_NAME'] ||
                    !isset($this->m_aRequest['section']) ) )
      Helper::redirectTo('/Start');
  }

  public final function setLanguage($sPath = '') {
    if( isset($this->m_aRequest['lang'])) {
      setCookie('lang', (string)$this->m_aRequest['lang'], time() + 2592000, '/');
      Helper::redirectTo('/Start');
      die();
    }

    $sLang = isset($this->m_aCookie['lang']) ?
            (string)$this->m_aCookie['lang'] :
            DEFAULT_LANGUAGE;

    if( file_exists($sPath. 'config/language/'	.$sLang.	'.lang.php') )
      require_once $sPath. 'config/language/'	.$sLang.	'.lang.php';
    else
      die(LANG_ERROR_GLOBAL_NO_LANGUAGE);
  }

  public final function loadAddons() {
    if( ALLOW_ADDONS == true && file_exists('addon/Addon.class.php'))
      require_once 'addon/Addon.class.php';
  }

  public final function connectDB() {
    SQLCONNECT::connect(SQL_HOST, SQL_USER, SQL_PASSWORD);
    SQLCONNECT::selectDB(SQL_DB);
  }

  public final function setUser($SessionId = '') {
    if(empty($SessionId))
      $SessionId = session_id();
      
    # TODO: Besser QueryModel
    $this->m_oSession['userdata'] = Model_Main::simpleQuery(
            '*',
            'user',
            "session = '"	.$SessionId.	"' AND ip = '"	.$_SERVER['REMOTE_ADDR'].	"'",
            '1');
    return $this->m_oSession['userdata'];
  }

  protected final function _getFlashMessage() {
    $aFlashMessage['type']      = isset($_SESSION['flash_message']['type']) && !empty($_SESSION['flash_message']['type']) ? $_SESSION['flash_message']['type'] : '';
    $aFlashMessage['message']   = isset($_SESSION['flash_message']['message']) && !empty($_SESSION['flash_message']['message']) ? $_SESSION['flash_message']['message'] : '';
    $aFlashMessage['headline']  = isset($_SESSION['flash_message']['headline']) && !empty($_SESSION['flash_message']['headline']) ? $_SESSION['flash_message']['headline'] : '';

    unset($_SESSION['flash_message']);
    return $aFlashMessage;
  }

  public final function show() {
    # Set expiration date for header
    $sHeaderExpires = gmdate('D, d M Y H:i:s', time() + 60).	' GMT';

    # Header.tpl
    $oSmarty = new Smarty();
    $oSmarty->assign('dev', WEBSITE_DEV);
    $oSmarty->assign('name', WEBSITE_NAME);
    $oSmarty->assign('uid', (int)$this->m_oSession['userdata']['id']);
    $oSmarty->assign('UR', USERRIGHT);
    $oSmarty->assign('url', WEBSITE_URL);
    $oSmarty->assign('user', Helper::formatBBCode($this->m_oSession['userdata']['name']));

    # Language
    # Header (in default)
    $oSmarty->assign('lang_blog', LANG_GLOBAL_BLOG);
    $oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);
    $oSmarty->assign('lang_logout', LANG_GLOBAL_LOGOUT);
    $oSmarty->assign('lang_message_close', LANG_GLOBAL_MESSAGE_CLOSE);
    $oSmarty->assign('lang_gallery', LANG_GLOBAL_GALLERY);
    $oSmarty->assign('lang_register', LANG_GLOBAL_REGISTER);
    $oSmarty->assign('lang_settings', LANG_GLOBAL_SETTINGS);
    $oSmarty->assign('lang_welcome', LANG_GLOBAL_WELCOME);

    # Footer (in default)
    $oSmarty->assign('lang_about', LANG_GLOBAL_ABOUT);
    $oSmarty->assign('lang_contentmanager', LANG_GLOBAL_CONTENTMANAGER);
    $oSmarty->assign('lang_cronjob_exec', LANG_GLOBAL_CRONJOB_EXEC);
    $oSmarty->assign('lang_disclaimer', LANG_GLOBAL_DISCLAIMER);
    $oSmarty->assign('lang_filemanager', LANG_GLOBAL_FILEMANAGER);
    $oSmarty->assign('lang_newsletter_create_destroy', LANG_NEWSLETTER_CREATE_DESTROY);
    $oSmarty->assign('lang_newsletter_send', LANG_NEWSLETTER_CREATE);
    $oSmarty->assign('lang_report_error', LANG_GLOBAL_REPORT_ERROR);
    $oSmarty->assign('lang_usermanager', LANG_GLOBAL_USERMANAGER);

    /* Define Core Modules - check if we use a standard action. If yes, forward to
		 * Section.class.php where we check for an override of this core modules. If we
		 * want to override the core module, we got to load Addons later in Section.class.php */
    if(	!isset($this->m_aRequest['section']) ||
            empty($this->m_aRequest['section']) ||
            $this->m_aRequest['section'] == 'blog' ||
            $this->m_aRequest['section'] == 'comment' ||
            $this->m_aRequest['section'] == 'content' ||
            $this->m_aRequest['section'] == 'gallery' ||
            $this->m_aRequest['section'] == 'login' ||
            $this->m_aRequest['section'] == 'mail' ||
            $this->m_aRequest['section'] == 'media' ||
            $this->m_aRequest['section'] == 'newsletter' ||
            $this->m_aRequest['section'] == 'rss' ||
            $this->m_aRequest['section'] == 'static' ||
            $this->m_aRequest['section'] == 'user') {

      $oSection = new Section($this->m_aRequest, $this->m_oSession, $this->m_aFile);
      $oSection->getSection();
    }
    /* We do not have a standard action, so let's take a look, if we have the required
		 * Addon in addon. If we do have, proceed with own action. */
    elseif( ALLOW_ADDONS == true)
      $oSection = new Addon($this->m_aRequest, $this->m_oSession, $this->m_aFile);
    # There's no request on a core module and Addons are disabled. */
    else {
      header('Status: 404 Not Found');
      die();
    }

    # Avoid Header and Footer HTML if RSS or AJAX are requested
    if(	(isset( $this->m_aRequest['section'] )  && 'rss' == $this->m_aRequest['section']) ||
            (isset( $this->m_aRequest['ajax'] )  && true == $this->m_aRequest['ajax'])  )
      $sCachedHTML = $oSection->getContent();
    else {
      $oSmarty->assign('title', $oSection->getTitle().
              ' - '	.WEBSITE_TITLE);
      $oSmarty->assign('meta_expires', $sHeaderExpires);
      $oSmarty->assign('meta_description', WEBSITE_SLOGAN);

      $sCachedHTML  = $oSmarty->fetch('Header.tpl');
      $sCachedHTML .= $oSection->getContent();
      $sCachedHTML .= $oSmarty->fetch('Footer.tpl');
    }

    # Get possible flash messages
    $aFlashMessages = $this->_getFlashMessage();

    # Replace Flash Message with Content
    $sCachedHTML = str_replace('%FLASH_TYPE%', $aFlashMessages['type'], $sCachedHTML);
    $sCachedHTML = str_replace('%FLASH_MESSAGE%', $aFlashMessages['message'], $sCachedHTML);
    $sCachedHTML = str_replace('%FLASH_HEADLINE%', $aFlashMessages['headline'], $sCachedHTML);

    # Build absolute Path because of Pretty URLs
    $sCachedHTML = str_replace('%PATH_PUBLIC%', PATH_PUBLIC, $sCachedHTML);
    $sCachedHTML = str_replace('%PATH_UPLOAD%', WEBSITE_URL. '/' .PATH_UPLOAD, $sCachedHTML);

    if( SKIN_CSS == '' )
      $sCachedHTML = str_replace('%PATH_CSS%', PATH_PUBLIC.	'/css', $sCachedHTML);
    else
      $sCachedHTML = str_replace('%PATH_CSS%',
              'skins/'	.SKIN_CSS.	'/css', $sCachedHTML);

    if( SKIN_IMAGES == '' )
      $sCachedHTML = str_replace('%PATH_IMAGES%', PATH_IMAGES, $sCachedHTML);
    else
      $sCachedHTML = str_replace('%PATH_IMAGES%',
              PATH_IMAGES.	'/skins/'	.SKIN_GFX.	'/gfx', $sCachedHTML);

    # Cut spaces to minimize filesize
    # Normal tab
    $sCachedHTML = str_replace('	', '', $sCachedHTML);

    # Tab as two spaces
    $sCachedHTML = str_replace('  ', '', $sCachedHTML);

    # Compress Data
    if( extension_loaded('zlib') )
      @ob_start('ob_gzhandler');

    header('Cache-Control: must-revalidate');
    header('Content-Type: text/html; charset=utf-8');
    header('Expires: '	.$sHeaderExpires);

    return $sCachedHTML;
  }
}
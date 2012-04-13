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

namespace CandyCMS\Core\Controllers;

use CandyCMS\Core\Helpers\AdvancedException;
use CandyCMS\Core\Helpers\Dispatcher;
use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\I18n;
use CandyCMS\Core\Helpers\SmartySingleton;
use CandyCMS\Plugins\Cronjob;
use CandyCMS\Plugins\FacebookCMS;
use Routes;

require_once PATH_STANDARD . '/vendor/.composer/autoload.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Main.model.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Main.controller.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Sessions.controller.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Logs.controller.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/AdvancedException.helper.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/Dispatcher.helper.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/I18n.helper.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/SmartySingleton.helper.php';

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
   * Saves the object.
   *
   * @var object
   * @access protected
   *
   */
  protected $_oObject;

  /**
   * Saves loaded Plugins
   *
   * @var array
   * @access private
   *
   */
  private $_aPlugins;

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
  public function __construct(&$aRequest, &$aSession = '', &$aFile = '', &$aCookie = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;
    $this->_aCookie   = & $aCookie;

    $this->getConfigFiles(array('Plugins'));
    $this->_aPlugins = $this->getPlugins(ALLOW_PLUGINS);
    $this->getRoutes();
    $this->getLanguage();
    $this->getFacebookExtension();
    $this->setUser();
    $this->getCronjob();
  }

  /**
   * Reset all data
   *
   * @access public
   *
   */
  public function __destruct() {
    # Only reload language each time the controller is activated in development mode.
    if (WEBSITE_MODE == 'development')
      I18n::unsetLanguage();

    # Close database connection
    $sModel = Main::__autoload('Main', true);
    $sModel::disconnectFromDatabase();
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
        if (!file_exists(PATH_STANDARD . '/app/config/' . ucfirst($sConfig) . '.inc.php'))
          throw new AdvancedException('Missing ' . ucfirst($sConfig) . ' config file.');

        else
          require_once PATH_STANDARD . '/app/config/' . ucfirst($sConfig) . '.inc.php';
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
   * @see app/config/Candy.inc.php
   * @return boolean true if no errors occurred.
   *
   */
  public static function getPlugins($sAllowedPlugins) {
    if (WEBSITE_MODE !== 'test') {
      if (!empty($sAllowedPlugins)) {
        $aPlugins = explode(',', $sAllowedPlugins);

        foreach ($aPlugins as $sPluginName) {
          try {
            if (!file_exists(PATH_STANDARD . '/vendor/candyCMS/plugins/' . (string) ucfirst($sPluginName) . '/' .
                            (string) ucfirst($sPluginName) . '.controller.php'))
              throw new AdvancedException('Missing plugin: ' . ucfirst($sPluginName));

            else
              require_once PATH_STANDARD . '/vendor/candyCMS/plugins/' . (string) ucfirst($sPluginName) . '/' .
                      (string) ucfirst($sPluginName) . '.controller.php';
          }
          catch (AdvancedException $e) {
            die($e->getMessage());
          }
        }

        return $aPlugins;
      }
      else
        return array();
    }
  }



  /**
   * Read the routes from Routes.yml and set request params.
   *
   * @access public
   * @return array $this->_aRequest
   * @see app/config/Routes.yml
   *
   */
  public function getRoutes() {
    require_once PATH_STANDARD . '/vendor/routes/Routes.php';

    # Cache routes for performance reasons
    if(!isset($this->_aSession['routes']) || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test')
      $this->_aSession['routes'] = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(PATH_STANDARD . '/app/config/Routes.yml'));

    Routes::add($this->_aSession['routes']);

    if (!defined('WEBSITE_LANDING_PAGE'))
      define('WEBSITE_LANDING_PAGE', Routes::route('/'));

    $sURI         = isset($_SERVER['REQUEST_URI']) ? Helper::removeSlash($_SERVER['REQUEST_URI']) : '';
    $sRoutemap    = Routes::route($sURI);
    $aRouteParts  = explode('&', $sRoutemap);

    if (count($aRouteParts) > 1) {
      foreach ($aRouteParts as $sRoutes) {
        $aRoute = explode('=', $sRoutes);

        if(!isset($this->_aRequest[$aRoute[0]]))
          $this->_aRequest[$aRoute[0]] = $aRoute[1];
      }
    }
    else
      $this->_aRequest['controller'] = isset($this->_aRequest['controller']) ? $this->_aRequest['controller'] : $sRoutemap;


    # Show files from public folder (robots.txt, human.txt and favicon.ico)
    if(preg_match('/\./', $this->_aRequest['controller']))
      exit(file_get_contents(Helper::removeSlash(WEBSITE_CDN) . '/' . $this->_aRequest['controller']));

    return $this->_aRequest;
  }

  /**
   * Sets the language. This can be done via a language request and be temporarily saved in a cookie.
   *
   * @access public
   * @return string language
   * @see app/config/Candy.inc.php
   *
   */
  public function getLanguage() {
    if (!defined('DEFAULT_LANGUAGE'))
      define('DEFAULT_LANGUAGE', 'en');

    # We got a language request? Let's switch the language!
    # Bugfix: Added "$this->_aRequest['controller']" to make a blog update possible.
    if (isset($this->_aRequest['language']) &&
            file_exists(PATH_STANDARD . '/app/languages/' . (string) $this->_aRequest['language'] . '.language.yml') &&
            !isset($this->_aRequest['controller'])) {
      $sLanguage = (string) $this->_aRequest['language'];
      setcookie('default_language', (string) $this->_aRequest['language'], time() + 2592000, '/');
      return Helper::redirectTo('/');
    }

    # There is no request, but there might be a cookie instead.
    else {
      $aRequest = isset($this->_aCookie) && is_array($this->_aCookie) ?
              array_merge($this->_aRequest, $this->_aCookie) :
              $this->_aRequest;

      $sLanguage = isset($aRequest['default_language']) &&
              file_exists(PATH_STANDARD . '/app/languages/' . strtolower((string) $aRequest['default_language']) . '.language.yml') ?
              strtolower((string) $aRequest['default_language']) :
              strtolower(DEFAULT_LANGUAGE);
    }

    # Set iso language codes
    switch (substr($sLanguage, 0, 2)) {
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
      define('WEBSITE_LANGUAGE', $sLanguage);

    if (!defined('WEBSITE_LOCALE'))
      define('WEBSITE_LOCALE', $sLocale);

    setlocale(LC_ALL, WEBSITE_LOCALE);
    new I18n(WEBSITE_LANGUAGE, $this->_aSession);

    return WEBSITE_LOCALE;
  }

  /**
   * Get the cronjob working. Check for last execution and plan next cleanup, optimization and backup.
   *
   * @access public
   * @param boolean $bForceAction force the cronjob to be executed.
   * @see app/config/Candy.inc.php
   *
   */
  public function getCronjob($bForceAction = false) {
    if (class_exists('\CandyCMS\Plugins\Cronjob')) {
      if (Cronjob::getNextUpdate() == true || $bForceAction === true) {
        $oCronjob = new Cronjob(isset($this->_aSession['user']['id']) ? (int) $this->_aSession['user']['id'] : 0);
        $oCronjob->cleanup(array('medias', 'bbcode'));
        $oCronjob->optimize();
        $oCronjob->backup();
        $oCronjob = null;
      }
    }
  }

  /**
   * Give the users the ability to interact with facebook. Facebook is used as a plugin and loaded in the method above.
   *
   * @access public
   * @see app/config/Candy.inc.php
   * @see vendor/candyCMS/plugins/Facebook/Facebook.controller.php
   * @return object FacebookCMS
   *
   */
  public function getFacebookExtension() {
    if (PLUGIN_FACEBOOK_APP_ID && class_exists('\CandyCMS\Plugins\FacebookCMS')) {
      $this->_aSession['facebook'] = new FacebookCMS(array(
          'appId' => PLUGIN_FACEBOOK_APP_ID,
          'secret' => PLUGIN_FACEBOOK_SECRET,
          'cookie' => true
          ));

      return $this->_aSession['facebook'];
    }
  }

  /**
   * Store and show flash status messages in the application.
   *
   * @access protected
   * @see app/config/Candy.inc.php
   * @return array $aFlashMessage The message, its type and the headline of the message.
   *
   */
  protected function _getFlashMessage() {
    $aFlashMessage = isset($this->_aSession['flash_message']) ? $this->_aSession['flash_message'] : array(
        'type'      => '',
        'message'    => '',
        'headline'  => '');

    unset($this->_aSession['flash_message']);
    return $aFlashMessage;
  }

  /**
   * Checks the empuxa server for a new CandyCMS version.
   *
   * @access private
   * @return string string with info message and link to download.
   *
   */
  private function _checkForNewVersion() {
    if ($this->_aSession['user']['role'] == 4 && ALLOW_VERSION_CHECK === true &&
            (WEBSITE_MODE == 'staging' || WEBSITE_MODE == 'production')) {
      $oFile = @fopen('http://www.candycms.com/version.txt', 'rb');
      $sVersionContent = @stream_get_contents($oFile);
      @fclose($oFile);

      $sVersionContent = $sVersionContent > VERSION ? (int) $sVersionContent : '';
    }

    return isset($sVersionContent) && !empty($sVersionContent) ?
            I18n::get('global.update.available', $sVersionContent, Helper::createLinkTo('http://www.candycms.com', true)) :
            '';
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
        'facebook_id' => NULL,
        'id' => (int) 0,
        'name' => '',
        'surname' => '',
        'password' => '',
        'role' => (int) 0,
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
   * @return array $this->_aSession['user']
   *
   */
  public function setUser() {
    # Set standard variables
    $this->_aSession['user'] = self::_resetUser();

    # Get user by token
    if (isset($this->_aRequest['api_token']) && !empty($this->_aRequest['api_token'])) {
      if (EXTENSION_CHECK && file_exists(PATH_STANDARD . '/app/extensions/models/Users.model.php')) {
        require_once PATH_STANDARD . '/app/extensions/models/Users.model.php';
        $aUser = \CandyCMS\Models\Users::getUserByToken(Helper::formatInput($this->_aRequest['api_token']));
      }
      else {
        require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Users.model.php';
        $aUser = \CandyCMS\Core\Models\Users::getUserByToken(Helper::formatInput($this->_aRequest['api_token']));
      }
    }

    # Get user by session
    else {
      if (EXTENSION_CHECK && file_exists(PATH_STANDARD . '/app/extensions/models/Sessions.model.php')) {
        require_once PATH_STANDARD . '/app/extensions/models/Sessions.model.php';
        $aUser = \CandyCMS\Models\Sessions::getUserBySession();
      }
      else {
        require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Sessions.model.php';
        $aUser = \CandyCMS\Core\Models\Sessions::getUserBySession();
      }
    }

    if (is_array($aUser))
      $this->_aSession['user'] = & array_merge($this->_aSession['user'], $aUser);

    # Try to get facebook data
    if ($this->_aSession['user']['id'] == 0) {
      $oFacebook = $this->getFacebookExtension();

      if ($oFacebook == true)
        $aFacebookData = $oFacebook->getUserData();

      # Override empty data with facebook data
      if (isset($aFacebookData)) {
        $this->_aSession['user']['facebook_id'] = isset($aFacebookData[0]['uid']) ?
                (int) $aFacebookData[0]['uid'] :
                '';
        $this->_aSession['user']['email'] = isset($aFacebookData[0]['email']) ?
                $aFacebookData[0]['email'] :
                $this->_aSession['user']['email'];
        $this->_aSession['user']['name'] = isset($aFacebookData[0]['first_name']) ?
                $aFacebookData[0]['first_name'] :
                $this->_aSession['user']['name'];
        $this->_aSession['user']['surname'] = isset($aFacebookData[0]['last_name']) ?
                $aFacebookData[0]['last_name'] :
                $this->_aSession['user']['surname'];
        $this->_aSession['user']['role'] = isset($aFacebookData[0]['uid']) ?
                2 :
                (int) $this->_aSession['user']['role'];

        unset($aFacebookData);
      }
    }

    # Set up full name finally
    $this->_aSession['user']['full_name'] = $this->_aSession['user']['name'] . ' ' . $this->_aSession['user']['surname'];

    return $this->_aSession['user'];
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
    if (!defined('UNIQUE_PREFIX'))
      define('UNIQUE_PREFIX', WEBSITE_MODE . '|' . $this->_aRequest['controller'] . '|' . WEBSITE_LOCALE);

    if (!defined('UNIQUE_ID')) {
      define('UNIQUE_ID', UNIQUE_PREFIX . '|' . (MOBILE ? 'mob|' : 'tpl|') .
              substr(md5($this->_aSession['user']['role'] . PATH_TEMPLATE), 0, 10) . '|' .
              substr(md5(CURRENT_URL), 0, 10));
    }

    # Start the dispatcher and grab the controller.
    $oSmarty = SmartySingleton::getInstance();
    $oSmarty->setRequestAndSession($this->_aRequest, $this->_aSession);
    $oDispatcher = new Dispatcher($this->_aRequest, $this->_aSession, $this->_aFile, $this->_aCookie);
    $oDispatcher->getController();
    $oDispatcher->getAction();

    # Minimal settings for AJAX-request
    if (isset($this->_aRequest['ajax']) && true == $this->_aRequest['ajax'])
      $sCachedHTML = $oDispatcher->oController->getContent();

    # HTML with template
    else {
      $sTemplateDir   = Helper::getTemplateDir('layouts', 'application');
      $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'application');

      # Get flash messages (success and error)
      $aFlashMessages = $this->_getFlashMessage();
      $oSmarty->assign('_flash_type_', $aFlashMessages['type']);
      $oSmarty->assign('_flash_message_', $aFlashMessages['message']);
      $oSmarty->assign('_flash_headline_', $aFlashMessages['headline']);

      # Define meta elements
      $oSmarty->assign('meta_og_description', $oDispatcher->oController->getDescription());
      $oSmarty->assign('meta_og_site_name', WEBSITE_NAME);
      $oSmarty->assign('meta_og_title', $oDispatcher->oController->getTitle());
      $oSmarty->assign('meta_og_url', CURRENT_URL);

      $oSmarty->assign('meta_description', $oDispatcher->oController->getDescription());
      $oSmarty->assign('meta_expires', gmdate('D, d M Y H:i:s', time() + 60) . ' GMT');
      $oSmarty->assign('meta_keywords', $oDispatcher->oController->getKeywords());

      # System required variables
      $oSmarty->assign('_content_', $oDispatcher->oController->getContent());
      $oSmarty->assign('_title_', $oDispatcher->oController->getTitle());
      $oSmarty->assign('_update_available_', $this->_checkForNewVersion());

      $oSmarty->setTemplateDir($sTemplateDir);
      $oSmarty->setCaching(\CandyCMS\Core\Helpers\SmartySingleton::CACHING_OFF);
      $sCachedHTML = $oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }

    if (ALLOW_PLUGINS !== '' && WEBSITE_MODE !== 'test')
      $sCachedHTML = $this->_showPlugins($sCachedHTML);

    header('Content-Type: text/html; charset=utf-8');
    return $sCachedHTML;
  }

  /**
   * Get plugin placeholders, Render needed Plugins and Replace Placeholders.
   * This is done at the end of execution for performance reasons.
   *
   * @access private
   * @param string $sCachedHTML
   * @return string HTML content
   *
   */
  private function _showPlugins($sCachedHTML) {
    # Bugfix: Fix search bug
    unset($this->_aRequest['id'], $this->_aRequest['search'], $this->_aRequest['page']);

    foreach ($this->_aPlugins as $sPlugin) {
      if($sPlugin == 'Bbcode' || $sPlugin == 'FormatTimestamp' || $sPlugin == 'Cronjob' ||  $sPlugin == 'Recaptcha')
        continue;

      if($sPlugin == 'Facebook')
        $sPlugin = 'FacebookCMS';

      $sPluginNamespace = '\CandyCMS\Plugins\\' . $sPlugin;

      if (class_exists($sPluginNamespace)) {
        $oPlugin = new $sPluginNamespace();

        if (preg_match('/<!-- plugin:' . $oPlugin::IDENTIFIER . ' -->/', $sCachedHTML))
          $sCachedHTML = str_replace('<!-- plugin:' . $oPlugin::IDENTIFIER . ' -->', $oPlugin->show($this->_aRequest, $this->_aSession), $sCachedHTML);
      }
    }

    return $sCachedHTML;
  }
}
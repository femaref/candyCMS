<?php

/**
 * Website entry.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @version 2.0
 * @since 1.0
 *
 */

namespace CandyCMS;

use \CandyCMS\Core\Controllers\Index;
use \CandyCMS\Core\Helpers\Helper;
use \CandyCMS\Core\Helpers\SmartySingleton;
use \CandyCMS\Core\Helpers\I18n;
use PDO;

define('PATH_STANDARD', dirname(__FILE__) . '/..');

require PATH_STANDARD . '/vendor/candyCMS/core/controllers/Index.controller.php';
require PATH_STANDARD . '/vendor/candyCMS/plugins/Cronjob/Cronjob.controller.php';

class Install extends Index {

  /**
   * Set up setup ;)
   *
   * @access public
   * @param array $aRequest
   * @param array $aSession
   * @param array $aFile
   * @param array $aCookie
   *
   */
  public function __construct(&$aRequest, &$aSession = '', &$aFile = '', &$aCookie = '') {
    $this->_aRequest = & $aRequest;
    $this->_aSession = & $aSession;
    $this->_aFile    = & $aFile;
    $this->_aCookie  = & $aCookie;

    if (file_exists(PATH_STANDARD . '/app/config/Candy.inc.php'))
      require PATH_STANDARD . '/app/config/Candy.inc.php';

    if (file_exists(PATH_STANDARD . '/app/config/Plugins.inc.php'))
      $this->getConfigFiles(array('Plugins'));

    $this->_defines();
    $this->getLanguage();

    $this->oSmarty = SmartySingleton::getInstance();
    $this->oSmarty->template_dir = PATH_STANDARD . '/install/views';
    $this->oSmarty->setCaching(SmartySingleton::CACHING_OFF);
    $this->oSmarty->setCompileCheck(true);

    # Direct actions
    if (isset($this->_aRequest['action']) && 'install' == $this->_aRequest['action'])
      $this->showInstallation();

    elseif (isset($this->_aRequest['action']) && 'migrate' == $this->_aRequest['action'])
      $this->showMigration();

    else
      $this->showIndex();
  }

  /**
   * Set constants.
   *
   * @access private
   *
   */
  private function _defines() {
    if (!defined('WEBSITE_URL'))
      define('WEBSITE_URL', 'http://' . $_SERVER['SERVER_NAME']);

    if (!defined('CACHE_DIR'))
      define('CACHE_DIR', 'cache');

    if (!defined('COMPILE_DIR'))
      define('COMPILE_DIR', 'compile');

    define('CURRENT_URL', isset($_SERVER['REQUEST_URI']) ? WEBSITE_URL . $_SERVER['REQUEST_URI'] : WEBSITE_URL);
    define('EXTENSION_CHECK', false);
    define('MOBILE', false);
    define('MOBILE_DEVICE', false);
    define('VERSION', '20120410');
  }

  /**
   * Create all Folders specified in given Array
   *
   * @access private
   * @param array $aFolders array of Folders to create, can also contain subarrays
   * @param string $sPrefix prefix for folder creations, default: '/'
   * @param string $iPermissions the permissions to create the folders with, default: 0777
   *
   */
  private function _createFoldersIfNotExistent($aFolders, $sPrefix = '/', $iPermissions = 0777) {
    foreach ($aFolders as $sKey => $mFolder) {
      # create multiple folders
      if (is_array($mFolder))
        $this->_createFoldersIfNotExistent($mFolder, $sPrefix . $sKey . '/', $iPermissions);

      # create single Folder
      elseif (!is_dir(PATH_STANDARD . $sPrefix . $mFolder)) {
        $oldUMask = umask(0);
        @mkdir(PATH_STANDARD . $sPrefix . $mFolder, $iPermissions, true);
        umask($oldUMask);
      }
    }
  }

  /**
   * Check all Folders specified in given Array and assign result to smarty
   *
   * @param array $aFolders array of Folders to check for, can also contain subarrays
   * @param array $aReturn array of bool return values for smarty
   * @param string $sPrefix prefix for assigns and checks, default: '/'
   * @param string $sPermissions the permissions to create the folders with, default: '0777'
   *
   */
  private function _checkFoldersAndAssign($aFolders, &$aReturn, $sPrefix = '/', $sPermissions = '0777') {
    $bReturn = true;

    foreach ($aFolders as $sKey => $mFolder) {

      # check multiple folders
      if (is_array($mFolder)) {
        # check root folder
        $bReturnSub = $this->_checkFoldersAndAssign(array($sKey), $aReturn, $sPrefix, $sPermissions);
        # and check all subfolders
        $bReturnRoot = $this->_checkFoldersAndAssign($mFolder, $aReturn, $sPrefix . $sKey . '/', $sPermissions);

        $bReturn = $bReturn && $bReturnRoot && $bReturnSub;
      }

      # check single Folder
      else {
        $aReturn[$sPrefix . $mFolder] = substr(decoct(fileperms(PATH_STANDARD . $sPrefix . $mFolder)), 1) == $sPermissions;
        $bReturn = $bReturn && $aReturn[$sPrefix . $mFolder];
      }
    }

    return $bReturn;
  }

  /**
   * Show installation steps.
   *
   * @access public
   *
   */
  public function showInstallation() {
    switch ($this->_aRequest['step']) {

      default:
      case '1':

        $aHasConfigFiles = array(
            'main'    => file_exists(PATH_STANDARD . '/app/config/Candy.inc.php'),
            'plugins' => file_exists(PATH_STANDARD . '/app/config/Plugins.inc.php'));

        $bRandomHashChanged = defined('RANDOM_HASH') && RANDOM_HASH !== '';
        $this->oSmarty->assign('_hash_changed_', $bRandomHashChanged);
        $this->oSmarty->assign('_configs_exist_', $aHasConfigFiles);

        $bHasNoErrors = $bRandomHashChanged;

        foreach ($aHasConfigFiles as $bConfigFileExists)
          $bHasNoErrors = $bHasNoErrors && $bConfigFileExists;

        $this->oSmarty->assign('_has_errors_', !$bHasNoErrors);

        $this->oSmarty->assign('title', 'Installation - Step 1 - Preparation');
        $this->oSmarty->assign('content', $this->oSmarty->fetch('install/step1.tpl'));

        break;

      case '2':

        # Try to create folders (if not avaiable)
        $sUpload = Helper::removeSlash(PATH_UPLOAD);
        $aFolders = array(
            'app/backup',
            Helper::removeSlash(CACHE_DIR),
            Helper::removeSlash(COMPILE_DIR),
            'app/logs',
            $sUpload => array(
                'galleries',
                'medias',
                'temp' => array(
                    'medias', 'bbcode'),
                'users' => array(
                    '32', '64', '100', 'thumbnail', 'popup', 'original')
            )
        );

        $this->_createFoldersIfNotExistent($aFolders);

        $aFolderChecks = array();
        $bHasNoErrors = $this->_checkFoldersAndAssign($aFolders, $aFolderChecks);

        $this->oSmarty->assign('_folder_checks_', $aFolderChecks);
        $this->oSmarty->assign('_has_errors_', !$bHasNoErrors);

        $this->oSmarty->assign('title', 'Installation - Step 2 - Folder rights');
        $this->oSmarty->assign('content', $this->oSmarty->fetch('install/step2.tpl'));

        break;

      case '3':

        $sUrl = PATH_STANDARD . '/install/sql/install/tables.sql';
        $bHasErrors = true;

        if (file_exists($sUrl)) {
          $oFo = fopen($sUrl, 'r');
          $sData = str_replace('%SQL_PREFIX%', SQL_PREFIX, stream_get_contents($oFo));
          fclose($oFo);
          $bHasErrors = false;

          # Create tables
          try {
            $oDb = \CandyCMS\Core\Models\Main::connectToDatabase();
            $oDb->query($sData);
          }
          catch (\AdvancedException $e) {
            die($e->getMessage());
          }
        }

        $this->oSmarty->assign('_has_errors_', $bHasErrors);
        $this->oSmarty->assign('title', 'Installation - Step 3 - Create database');
        $this->oSmarty->assign('content', $this->oSmarty->fetch('install/step3.tpl'));

        break;

      case '4':

        if (isset($this->_aRequest['create_admin'])) {
          $this->_setError('name')->_setError('surname')->_setError('email')->_setError('password');

          if ($this->_aRequest['password'] !== $this->_aRequest['password2'])
            $this->_aError['password'] = I18n::get('error.passwords');

          if ($this->_aError) {
            $this->oSmarty->assign('error', $this->_aError);

            $this->oSmarty->assign('name', isset($this->_aRequest['name']) ?
                            Helper::formatInput($this->_aRequest['name']) :
                            '');

            $this->oSmarty->assign('surname', isset($this->_aRequest['surname']) ?
                            Helper::formatInput($this->_aRequest['surname']) :
                            '');

            $this->oSmarty->assign('email', isset($this->_aRequest['email']) ?
                            Helper::formatInput($this->_aRequest['email']) :
                            '');
          }
          else {
            $sUsers = \CandyCMS\Core\Models\Main::__autoload('Users');
            $oUsers = new $sUsers($this->_aRequest, $this->_aSession);
            $bResult = $oUsers->create('', 4);
            Helper::redirectTo('/install/?action=install&step=5&result=' . ($bResult ? '1' : '0'));
          }
        }

        $this->oSmarty->assign('title', 'Installation - Step 4 - Create admin');
        $this->oSmarty->assign('content', $this->oSmarty->fetch('install/step4.tpl'));

        break;

      case '5':

        $this->oSmarty->assign('_result_', $this->_aRequest['result'] ? true : false);
        $this->oSmarty->assign('title', 'Installation finished');
        $this->oSmarty->assign('content', $this->oSmarty->fetch('install/step5.tpl'));

        break;
    }
  }

	/**
	 * Set error messages. This is a copy from Main.controller.php
	 *
	 * @access protected
	 * @param string $sField field to be checked
	 * @param string $sMessage error to be displayed
   * @return object $this due to method chaining
	 *
	 */
	protected function _setError($sField, $sMessage = '') {
    if ($sField == 'file' || $sField == 'image') {
      if (!isset($this->_aFile[$sField]) || empty($this->_aFile[$sField]['name']))
          $this->_aError[$sField] = $sMessage ?
                $sMessage :
                I18n::get('error.form.missing.file');
    }

    else {
      if (!isset($this->_aRequest[$sField]) || empty($this->_aRequest[$sField]))
          $sError = I18n::get('error.form.missing.' . strtolower($sField)) ?
                I18n::get('error.form.missing.' . strtolower($sField)) :
                I18n::get('error.form.missing.standard');

      if ('email' == $sField && !Helper::checkEmailAddress($this->_aRequest['email']))
          $sError = $sError ? $sError : I18n::get('error.mail.format');

      if ($sError)
        $this->_aError[$sField] = !$sMessage ? $sError : $sMessage;
    }

    return $this;
  }

  /**
   *
   * @access public
   *
   */
  public function showIndex() {
    $this->oSmarty->assign('title', 'Welcome!');
    $this->oSmarty->assign('content', $this->oSmarty->fetch('index.tpl'));
  }

  /**
   * @access public
   * @return string HTML
   *
   */
  public function show() {
    return $this->oSmarty->fetch('layout.tpl');
  }

  /**
   * @access private
   *
   */
  private function _showMigrations() {
    $sDir = PATH_STANDARD . '/install/sql/migrate/';
    $oDir = opendir($sDir);

    $oDb = \CandyCMS\Core\Models\Main::connectToDatabase();

    $iI = 0;
    $aFiles = array();
    while ($sFile = readdir($oDir)) {
      try {
        $oQuery = $oDb->prepare('SELECT id FROM ' . SQL_PREFIX . 'migrations WHERE file = :file');
        $oQuery->bindParam(':file', $sFile, PDO::PARAM_STR);

        $bReturn = $oQuery->execute();

        if ($bReturn == true)
          $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (\AdvancedException $e) {
        $oDb->rollBack();
        die($e->getMessage());
      }

      $bAlreadyMigrated = isset($aResult['id']) && !empty($aResult['id']) ? true : false;

      if (substr($sFile, 0, 1) == '.' || $bAlreadyMigrated == true)
        continue;

      else {
        $oFo = fopen($sDir . '/' . $sFile, 'r');
        $sQuery = str_replace('%SQL_PREFIX%', SQL_PREFIX, fread($oFo, filesize($sDir . '/' . $sFile)));

        $aFiles[$iI]['name'] = $sFile;
        $aFiles[$iI]['query'] = $sQuery;
        $iI++;
      }

      unset($bAlreadyMigrated, $aResult);
    }

    sort($aFiles);

    $this->oSmarty->assign('files', $aFiles);
    $this->oSmarty->assign('title', 'Migrations');
    $this->oSmarty->assign('content', $this->oSmarty->fetch('migrate/index.tpl'));
  }

  /**
   *
   * @access private
   *
   */
  private function _doMigration() {
    $oFo = fopen(PATH_STANDARD . '/install/sql/migrate/' .$_REQUEST['file'], 'rb');

    try {
      $oDb = \CandyCMS\Core\Models\Main::connectToDatabase();
      $bResult = $oDb->query(str_replace('%SQL_PREFIX%', SQL_PREFIX, @stream_get_contents($oFo)));
      fclose($oFo);
    }
    catch (\AdvancedException $e) {
      Core\Helpers\AdvancedException::reportBoth($e->getMessage());
    }

    # Write migration into table
    if($bResult) {
      try {
        $oDb = \CandyCMS\Core\Models\Main::connectToDatabase();
        $oQuery = $oDb->prepare(" INSERT INTO
                                    " . SQL_PREFIX . "migrations (file, date)
                                  VALUES
                                    ( :file, :date )");

        $oQuery->bindParam('file', $_REQUEST['file']);
        $oQuery->bindParam('date', time());
        $bResult = $oQuery->execute();
      }
      catch (\AdvancedException $e) {
        $oDb->rollBack();
      }
    }
  }

  /**
   *
   * @access public
   *
   */
  public function showMigration() {
    $this->getCronjob(true);
    return isset($this->_aRequest['file']) ? $this->_doMigration($this->_aRequest['file']) : $this->_showMigrations();
  }
}

ini_set('display_errors', 1);
ini_set('error_reporting', 1);
ini_set('log_errors', 1);

$oInstall = new Install(array_merge($_GET, $_POST));
echo $oInstall->show();

?>
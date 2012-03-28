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

use CandyCMS\Controller\Index as Index;
use PDO;

define('PATH_STANDARD', dirname(__FILE__) . '/..');

require PATH_STANDARD . '/config/Candy.inc.php';
require PATH_STANDARD . '/app/controllers/Index.controller.php';

class Install extends Index {

  public function __construct(&$aRequest, &$aSession = '', &$aFile = '', &$aCookie = '') {
    $this->_aRequest = & $aRequest;
    $this->_aSession = & $aSession;
    $this->_aFile    = & $aFile;
    $this->_aCookie  = & $aCookie;

    $this->getConfigFiles(array('Plugins', 'Mailchimp'));
    $this->_aPlugins = $this->getPlugins(ALLOW_PLUGINS);
    $this->getLanguage();
    $this->getCronjob();

    # Load cronjob and start it.
    # TODO: enable
    #$this->getCronjob(true);

    $this->oSmarty = Helper\SmartySingleton::getInstance();
    $this->oSmarty->template_dir = PATH_STANDARD . '/install/views';
    $this->oSmarty->setCaching(Helper\SmartySingleton::CACHING_OFF);

    # Direct actions
    if (isset($this->_aRequest['action']) && 'install' == $this->_aRequest['action'])
      $this->showInstall();

    elseif (isset($this->_aRequest['action']) && 'migrate' == $this->_aRequest['action'])
      $this->showStart();

    else
      $this->showIndex();
  }

  /**
   * Create all Folders specified in given Array
   *
   * @param array $aFolders array of Folders to create, can also contain subarrays
   * @param string $sPrefix prefix for folder creations, default: '/'
   * @param string $sPermissions the permissions to create the folders with, default: '0777'
   */
  private function _createFoldersIfNotExistent($aFolders, $sPrefix = '/', $sPermissions = '0777') {
    foreach ($aFolders as $sKey => $mFolder) {
      # create multiple folders
      if (is_array($mFolder)) {
        # create root folder
        # not needed since mkdir has recursive flag set to true
        //$this->_createFoldersIfNotExistent(array($sKey), $sPrefix, $sPermissions);

        # and create all subfolders
        $this->_createFoldersIfNotExistent($mFolder, $sPrefix . $sKey . '/', $sPermissions);
      }

      # create single Folder
      else
        if (!is_dir(PATH_STANDARD . $sPrefix . $mFolder))
          @mkdir(PATH_STANDARD . $sPrefix . $mFolder, $sPermissions, true);
    }
  }

  /**
   * Check all Folders specified in given Array and assign result to smarty
   *
   * @param array $aFolders array of Folders to check for, can also contain subarrays
   * @param array $aReturn array of bool return values for smarty
   * @param string $sPrefix prefix for assigns and checks, default: '/'
   * @param string $sPermissions the permissions to create the folders with, default: '0777'
   */
  private function _checkFoldersAndAssign($aFolders, &$aReturn, $sPrefix = '/', $sPermissions = '0777') {
    foreach ($aFolders as $sKey => $mFolder) {
      # ccheck multiple folders
      if (is_array($mFolder)) {
        # check root folder
        $this->_checkFoldersAndAssign(array($sKey), $aReturn, $sPrefix, $sPermissions);

        # and check all subfolders
        $this->_checkFoldersAndAssign($mFolder, $aReturn, $sPrefix . $sKey . '/', $sPermissions);
      }

      # check single Folder
      else
        $aReturn[$sPrefix . $mFolder] = substr(decoct(fileperms(PATH_STANDARD . $sPrefix . 'backup')), 1) == $sPermissions;
    }
  }

  public function showInstall() {
    switch ($this->_aRequest['step']) {

      default:
      case '1':
        # Try to create folders (if not avaiable)
        $aFolders = array(
            'backup',
            'cache',
            'compile',
            'logs',
            'upload' => array(
                'galleries',
                'medias',
                'temp' => array(
                    'medias', 'bbcode'),
                'users' => array(
                    '32', '64', '100', THUMB_DEFAULT_X, 'popup', 'original')
                )
            );

        $this->_createFoldersIfNotExistent($aFolders);

        $aFolderChecks = array();
        $this->_checkFoldersAndAssign($aFolders, $aFolderChecks);

        $this->oSmarty->assign('_folder_checks_', $aFolderChecks);

        $this->oSmarty->assign('title', 'Installation - Step 1 - Preparation');
        $this->oSmarty->assign('content', $this->oSmarty->fetch('install/step1.tpl'));

        break;

      case '2':

        $sUrl = PATH_STANDARD . '/install/sql/install/tables.sql';
        if (file_exists($sUrl)) {
          $oFo = fopen($sUrl, 'r');
          $sData = str_replace('%SQL_PREFIX%', SQL_PREFIX, fread($oFo, filesize($sUrl)));

          # Create tables
          try {
            $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB . '_' . WEBSITE_MODE, SQL_USER, SQL_PASSWORD);
            $oDb->query($sData);
          }
          catch (\AdvancedException $e) {
            die($e->getMessage());
          }
        }

        $this->oSmarty->assign('title', 'Installation - Step 2 - Create admin user');
        $this->oSmarty->assign('content', $this->oSmarty->fetch('install/step2.tpl'));

        break;

      case '3':

        die(print_r($this->_aRequest));

        break;

    }
  }

  public function showIndex() {
    $this->oSmarty->assign('title', 'Welcome!');
    $this->oSmarty->assign('content', $this->oSmarty->fetch('index.tpl'));
  }

  public function show() {
    return $this->oSmarty->fetch('layout.tpl');
  }
}

$oInstall = new Install(array_merge($_GET, $_POST));
echo $oInstall->show();

?>
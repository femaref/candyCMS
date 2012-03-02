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
use Smarty;

define('PATH_STANDARD', dirname(__FILE__) . '/..');

require PATH_STANDARD . '/config/Candy.inc.php';
require PATH_STANDARD . '/app/controllers/Index.controller.php';

class Install extends Index {

  public function __init() {
		require PATH_STANDARD . '/app/controllers/Main.controller.php';
		require PATH_STANDARD . '/app/helpers/AdvancedException.helper.php';
		require PATH_STANDARD . '/app/helpers/I18n.helper.php';
		require PATH_STANDARD . '/lib/smarty/Smarty.class.php';
		require PATH_STANDARD . '/plugins/controllers/Cronjob.controller.php';

    # Load cronjob and start it.
    # TODO: enable
    #$this->getCronjob(true);

    $this->oSmarty = new Smarty();
		$this->oSmarty->cache_dir			= PATH_STANDARD . '/' . CACHE_DIR;
		$this->oSmarty->compile_dir		= PATH_STANDARD . '/' . COMPILE_DIR;
    $this->oSmarty->template_dir	= PATH_STANDARD . '/install/views';

    # Direct actions
    if (isset($this->_aRequest['action']) && 'install' == $this->_aRequest['action'])
      $this->showInstall();

    elseif (isset($this->_aRequest['action']) && 'migrate' == $this->_aRequest['action'])
      $this->showStart();

    else
      $this->showIndex();
  }

  public function showInstall() {
    switch ($this->_aRequest['step']) {

      default:
      case '1':
        # Try to create folders (if not avaiable)
        if (!is_dir(PATH_STANDARD . '/backup'))
          @mkdir(PATH_STANDARD . '/backup', '0777', true);

        if (!is_dir(PATH_STANDARD . '/cache'))
          @mkdir(PATH_STANDARD . '/cache', '0777', true);

        if (!is_dir(PATH_STANDARD . '/compile'))
          @mkdir(PATH_STANDARD . '/compile', '0777', true);

        if (!is_dir(PATH_STANDARD . '/logs'))
          @mkdir(PATH_STANDARD . '/logs', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload'))
          @mkdir(PATH_STANDARD . '/upload', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/gallery'))
          @mkdir(PATH_STANDARD . '/upload/gallery', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/media'))
          @mkdir(PATH_STANDARD . '/upload/media', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/temp'))
          @mkdir(PATH_STANDARD . '/upload/temp', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/temp/media'))
          @mkdir(PATH_STANDARD . '/upload/temp/media', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/temp/bbcode'))
          @mkdir(PATH_STANDARD . '/upload/temp/bbcode', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/user/32'))
          @mkdir(PATH_STANDARD . '/upload/user/32', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/user/64'))
          @mkdir(PATH_STANDARD . '/upload/user/64', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/user/100'))
          @mkdir(PATH_STANDARD . '/upload/user/100', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/user/' . THUMB_DEFAULT_X))
          @mkdir(PATH_STANDARD . '/upload/user/' . THUMB_DEFAULT_X, '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/user/popup'))
          @mkdir(PATH_STANDARD . '/upload/user/popup', '0777', true);

        if (!is_dir(PATH_STANDARD . '/upload/user/original'))
          @mkdir(PATH_STANDARD . '/upload/user/original/', '0777', true);

        $this->oSmarty->assign('_color_backup_', substr(decoct(fileperms(PATH_STANDARD . '/backup')), 2) == '777' ? 'green' : 'red');
        $this->oSmarty->assign('_color_cache_', substr(decoct(fileperms(PATH_STANDARD . '/cache')), 2) == '777' ? 'green' : 'red');
        $this->oSmarty->assign('_color_compile_', substr(decoct(fileperms(PATH_STANDARD . '/compile')), 2) == '777' ? 'green' : 'red');
        $this->oSmarty->assign('_color_logs_', substr(decoct(fileperms(PATH_STANDARD . '/logs')), 2) == '777' ? 'green' : 'red');
        $this->oSmarty->assign('_color_upload_', substr(decoct(fileperms(PATH_STANDARD . '/upload')), 2) == '777' ? 'green' : 'red');

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
$oInstall->__init();

echo $oInstall->show();

?>
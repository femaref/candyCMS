<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

namespace CandyCMS\Install;

use CandyCMS\Controller\Index as Index;
use PDO;
use Smarty;

date_default_timezone_set('Europe/Berlin');

try {
  #Load Parent
  if (!file_exists('../app/models/Main.model.php') ||
      !file_exists('../app/controllers/Main.controller.php') ||
      !file_exists('../app/controllers/Index.controller.php') ||
      !file_exists('../app/helpers/AdvancedException.helper.php') ||
      !file_exists('../plugins/controllers/Cronjob.controller.php') ||
      !file_exists('../lib/smarty/Smarty.class.php')
  )
    throw new Exception('Could not load required classes.');
  else {
    require_once '../app/models/Main.model.php';
    require_once '../app/controllers/Main.controller.php';
    require_once '../app/controllers/Index.controller.php';
    require_once '../app/helpers/AdvancedException.helper.php';
    require_once '../plugins/controllers/Cronjob.controller.php';
    require_once '../lib/smarty/Smarty.class.php';
  }
}
catch (Exception $e) {
  die($e->getMessage());
}

session_start();

$oIndex = new Index($_REQUEST, $_SESSION);
$oIndex->getConfigFiles(array('Candy'), '../');

$oSmarty = new Smarty();
$oSmarty->compile_dir = '../compile';
$oSmarty->cache_dir = '../cache';

define( 'LANG_WEBSITE_TITLE', 'CandyCMS' );

$sHTML = '';

if (!isset($_REQUEST['action']))
  $_REQUEST['action'] = '';

switch ($_REQUEST['action']) {
  default:
  case '':

    $oSmarty->template_dir = '/';
    $oSmarty->assign('title', LANG_WEBSITE_TITLE . ' - Choose action');

    break;

  case 'install':

    $_REQUEST['step'] = isset($_REQUEST['step']) ? (int) $_REQUEST['step'] : 1;
    $oSmarty->template_dir = 'install/';

    require_once 'install/install.php';

    $oSmarty->assign('title', LANG_WEBSITE_TITLE . ' - Installation');
    $oSmarty->assign('step', $iNextStep);
    $oSmarty->assign('action', $_SERVER['PHP_SELF']);

    break;

  case 'migrate':

    $oSmarty->template_dir = 'migrate/';
    $oSmarty->assign('title', LANG_WEBSITE_TITLE . ' - Migration');
    $oSmarty->assign('action', $_SERVER['PHP_SELF']);

    if (isset($_REQUEST['file'])) {
      $oFo = fopen('migrate/sql/' .$_REQUEST['file'], 'r');
      $sQuery = fread($oFo, filesize('migrate/sql/' .$_REQUEST['file']));

      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $bResult = $oDb->query($sQuery);

        $oDb = null;

      } catch (\AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
      }

      # Write migration into table
      if($bResult == true) {
        try {
          $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
          $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $oQuery = $oDb->prepare(" INSERT INTO
                                      " . SQL_PREFIX . "migrations (file, date)
                                    VALUES
                                      ( :file, :date )");

          $oQuery->bindParam('file', $_REQUEST['file']);
          $oQuery->bindParam('date', time());
          $bResult = $oQuery->execute();
          $oDb = null;
        }
        catch (\AdvancedException $e) {
          $oDb->rollBack();
        }
      }
    }
    else
      require_once 'migrate/migrate.php';

    break;
}


if(!isset($_REQUEST['file'])) {
  $sCachedHTML = $oSmarty->fetch('showLayout.tpl');
  $sCachedHTML = str_replace('%CONTENT%', $sHTML, $sCachedHTML);
} else
  $sCachedHTML = '';

$sCachedHTML = str_replace('%PATH_CSS%', '/public/css/', $sCachedHTML);
$sCachedHTML = str_replace('%PATH_IMAGES%', '/public/images/', $sCachedHTML);
$sCachedHTML = str_replace('%PATH_PUBLIC%', '/public/', $sCachedHTML);

echo $sCachedHTML;
?>
<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

date_default_timezone_set('Europe/Berlin');

try {
  #Load Parent
  if (!file_exists('../app/models/Main.model.php') ||
          !file_exists('../app/controllers/Main.controller.php') ||
          !file_exists('../app/controllers/Index.controller.php') ||
          !file_exists('../app/helpers/AdvancedException.helper.php') ||
          !file_exists('../app/helpers/SqlConnect.helper.php') ||
          !file_exists('../app/helpers/SqlQuery.helper.php') ||
          !file_exists('../lib/smarty/Smarty.class.php')
  )
    throw new Exception('Could not load required classes.');
  else {
    require_once '../app/models/Main.model.php';
    require_once '../app/controllers/Main.controller.php';
    require_once '../app/controllers/Index.controller.php';

    # All helpers
    require_once '../app/helpers/AdvancedException.helper.php';
    require_once '../app/helpers/SqlConnect.helper.php';
    require_once '../app/helpers/SqlQuery.helper.php';

    # Smarty template parsing
    require_once '../lib/smarty/Smarty.class.php';
  }
} catch (Exception $e) {
  die($e->getMessage());
}

session_start();

$oIndex = new Index($_REQUEST, $_SESSION);
$oIndex->loadConfig('../');
$oIndex->setLanguage('../');

$oSmarty = new Smarty();
$oSmarty->compile_dir = '../compile';
$oSmarty->cache_dir = '../cache';

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

    require_once 'install.php';

    $oSmarty->assign('title', LANG_WEBSITE_TITLE . ' - Installation');
    $oSmarty->assign('step', $iNextStep);
    $oSmarty->assign('action', $_SERVER['PHP_SELF']);

    break;

  case 'migrate':

    if (isset($_REQUEST['file'])) {
      $oFo = fopen('sql/migrations/' .$_REQUEST['file'], 'r');
      $sQuery = fread($oFo, filesize('sql/migrations/' .$_REQUEST['file']));
      new Query($sQuery);
    }

    $oSmarty->template_dir = 'migrate/';

    require_once 'migrate.php';

    $oSmarty->assign('title', LANG_WEBSITE_TITLE . ' - Migration');
    $oSmarty->assign('action', $_SERVER['PHP_SELF']);

    break;
}

$sCachedHTML = $oSmarty->fetch('showLayout.tpl');
$sCachedHTML = str_replace('%CONTENT%', $sHTML, $sCachedHTML);
$sCachedHTML = str_replace('%PATH_CSS%', WEBSITE_CDN . '/public/css/', $sCachedHTML);
$sCachedHTML = str_replace('%PATH_IMAGES%', WEBSITE_CDN . '/public/images/', $sCachedHTML);
$sCachedHTML = str_replace('%PATH_PUBLIC%', WEBSITE_CDN . '/public/', $sCachedHTML);

echo $sCachedHTML;
?>
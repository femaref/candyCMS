<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

try {
  #Load Parent
  if( !file_exists('../app/models/Main.model.php') ||
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
}
catch (Exception $e) {
  die($e->getMessage());
}

session_start();

$oIndex = new Index($_REQUEST, $_SESSION);
$oIndex->loadConfig('../');
$oIndex->setLanguage('../');

$_REQUEST['step'] = isset($_REQUEST['step']) ? (int)$_REQUEST['step'] : 1;

$oSmarty = new Smarty();
$oSmarty->template_dir = 'html/';
$oSmarty->compile_dir = '../compile';
$oSmarty->cache_dir = '../cache';

switch ($_REQUEST['step']) {
  default:
  case '1':

    $sHTML  = $oSmarty->fetch('showStep1.tpl');
    $iNextStep = 2;

    if(!is_dir('../backup'))
      @mkdir('../backup', '0777', true);

    if(!is_dir('../cache'))
      @mkdir('../cache', '0777', true);

    if(!is_dir('../compile'))
      @mkdir('../compile', '0777', true);

    if(!is_dir('../upload'))
      @mkdir('../upload', '0777', true);

    if(!is_dir('../upload/gallery'))
      @mkdir('../upload/gallery', '0777', true);

    if(!is_dir('../upload/media'))
      @mkdir('../upload/media', '0777', true);

    if(!is_dir('../upload/temp'))
      @mkdir('../upload/temp', '0777', true);

    if(!is_dir('../upload/user/32'))
      @mkdir('../upload/user/32', '0777', true);

    if(!is_dir('../upload/user/64'))
      @mkdir('../upload/user/64', '0777', true);

    if(!is_dir('../upload/user/100'))
      @mkdir('../upload/user/100', '0777', true);

    if(!is_dir('../upload/user/' .THUMB_DEFAULT_X))
      @mkdir('../upload/user/' .THUMB_DEFAULT_X, '0777', true);

    if(!is_dir('../upload/user/' .POPUP_DEFAULT_X))
      @mkdir('../upload/user/' .POPUP_DEFAULT_X, '0777', true);

    if(!is_dir('../upload/user/original'))
      @mkdir('../upload/user/original/', '0777', true);

    $sHTML .= "<ul>";
    $sColor = substr(decoct(fileperms("../backup")), 2) == '777' ? 'green' : 'red';
    $sHTML .= "<li style='color:" .$sColor.  "'>";
    $sHTML .= "backup/*";
    $sHTML .= "</li>";

    $sColor = substr(decoct(fileperms("../cache")), 2) == '777' ? 'green' : 'red';
    $sHTML .= "<li style='color:" .$sColor.  "'>";
    $sHTML .= "cache/*";
    $sHTML .= "</li>";

    $sColor = substr(decoct(fileperms("../compile")), 2) == '777' ? 'green' : 'red';
    $sHTML .= "<li style='color:" .$sColor.  "'>";
    $sHTML .= "compile/*";
    $sHTML .= "</li>";

    $sColor = substr(decoct(fileperms("../upload")), 2) == '777' ? 'green' : 'red';
    $sHTML .= "<li style='color:" .$sColor.  "'>";
    $sHTML .= "upload/*";
    $sHTML .= "</li>";
    $sHTML .= "</ul>";

    break;
  case '2':

    $sStatus = mysql_connect(SQL_HOST, SQL_USER, SQL_PASSWORD);
    $sStatus = mysql_select_db(SQL_DB);

    $oSmarty->assign('database', SQL_DB);
    $oSmarty->assign('status', $sStatus);

    $sHTML  = $oSmarty->fetch('showStep2.tpl');
    $iNextStep = 3;

    break;
  case '3':

    if(!$_POST)
      die('No data sent!');

    $oIndex->connectDB();

    $sHTML  = $oSmarty->fetch('showStep3.tpl');
    $iNextStep = 4;

    // Read MySQL tables
    function dumpData($sFile) {
      $oFile = file($sFile);
      $sQuery = "";

      foreach($oFile as $sSqlInline) {
        $sSql = trim($sSqlInline);
        if (($sSql != "") && (substr($sSql, 0, 2) != "--") && (substr($sSql, 0, 1) != "#")) {
          $sQuery .= $sSqlInline;
          if(preg_match("/;\s*$/", $sSqlInline)) {
            $oResult = mysql_query($sQuery);
            if(!$oResult) die(mysql_error());
            $sQuery = "";
          }
        }
      }
      $sQuery = "";
    }

    // Create MySQL tables
    dumpData("sql/tables.sql");

    $sUrl = "sql/data.sql";
    if(file_exists($sUrl)) {
      $oFo = fopen($sUrl, 'r');
      $sData = fread($oFo, filesize ($sUrl));
      $oSql = new Query($sData);
    }

    $_SESSION['install'] = $_POST;
    $sPassword = md5(RANDOM_HASH.$_SESSION['install']['password']);

    new Query(" INSERT INTO
                  user (  `id`,
                          `name`,
                          `surname`,
                          `password`,
                          `email`,
                          `session`,
                          `ip`,
                          `userright`,
                          `regdate`,
                          `last_login`,
                          `newsletter_default`,
                          `description`)
                VALUES (  1,
                          'Admin',
                          'Admin',
                          '"  .$sPassword.  "',
                          '"  .$_SESSION['install']['email'].  "',
                          '',
                          '"  .$_SERVER['REMOTE_ADDR'].  "',
                          4,
                          '"  .time().  "',
                          '',
                          1,
                          '')");
    break;
  case '4':

    header('Location:../Login');

    break;
}

$oSmarty->assign('title', WEBSITE_TITLE.  ' - Installation');
$oSmarty->assign('step', $iNextStep);
$oSmarty->assign('action', $_SERVER["PHP_SELF"]);

$sCachedHTML  = $oSmarty->fetch('showLayout.tpl');
$sCachedHTML = str_replace('%CONTENT%', $sHTML, $sCachedHTML);
$sCachedHTML = str_replace('%PATH_CSS%', PATH_PUBLIC.	'/css', $sCachedHTML);
$sCachedHTML = str_replace('%PATH_IMAGES%', PATH_IMAGES, $sCachedHTML);
$sCachedHTML = str_replace('%PATH_PUBLIC%', PATH_PUBLIC, $sCachedHTML);

echo $sCachedHTML;

?>
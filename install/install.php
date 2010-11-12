<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

switch ($_REQUEST['step']) {
  default:
  case '1':

    $sHTML = $oSmarty->fetch('showStep1.tpl');
    $iNextStep = 2;

    if (!is_dir('../backup'))
      @mkdir('../backup', '0777', true);

    if (!is_dir('../cache'))
      @mkdir('../cache', '0777', true);

    if (!is_dir('../compile'))
      @mkdir('../compile', '0777', true);

    if (!is_dir('../upload'))
      @mkdir('../upload', '0777', true);

    if (!is_dir('../upload/gallery'))
      @mkdir('../upload/gallery', '0777', true);

    if (!is_dir('../upload/media'))
      @mkdir('../upload/media', '0777', true);

    if (!is_dir('../upload/temp'))
      @mkdir('../upload/temp', '0777', true);

    if (!is_dir('../upload/temp/media'))
      @mkdir('../upload/temp/media', '0777', true);

    if (!is_dir('../upload/temp/bbcode'))
      @mkdir('../upload/temp/bbcode', '0777', true);

    if (!is_dir('../upload/user/32'))
      @mkdir('../upload/user/32', '0777', true);

    if (!is_dir('../upload/user/64'))
      @mkdir('../upload/user/64', '0777', true);

    if (!is_dir('../upload/user/100'))
      @mkdir('../upload/user/100', '0777', true);

    if (!is_dir('../upload/user/' . THUMB_DEFAULT_X))
      @mkdir('../upload/user/' . THUMB_DEFAULT_X, '0777', true);

    if (!is_dir('../upload/user/popup'))
      @mkdir('../upload/user/popup', '0777', true);

    if (!is_dir('../upload/user/original'))
      @mkdir('../upload/user/original/', '0777', true);

    $sHTML .= "<ul>";
    $sColor = substr(decoct(fileperms("../backup")), 2) == '777' ? 'green' : 'red';
    $sHTML .= "<li style='color:" . $sColor . "'>";
    $sHTML .= "backup/*";
    $sHTML .= "</li>";

    $sColor = substr(decoct(fileperms("../cache")), 2) == '777' ? 'green' : 'red';
    $sHTML .= "<li style='color:" . $sColor . "'>";
    $sHTML .= "cache/*";
    $sHTML .= "</li>";

    $sColor = substr(decoct(fileperms("../compile")), 2) == '777' ? 'green' : 'red';
    $sHTML .= "<li style='color:" . $sColor . "'>";
    $sHTML .= "compile/*";
    $sHTML .= "</li>";

    $sColor = substr(decoct(fileperms("../upload")), 2) == '777' ? 'green' : 'red';
    $sHTML .= "<li style='color:" . $sColor . "'>";
    $sHTML .= "upload/*";
    $sHTML .= "</li>";
    $sHTML .= "</ul>";

    break;
  case '2':

    $oSmarty->assign('database', SQL_DB);

    $sHTML = $oSmarty->fetch('showStep2.tpl');
    $iNextStep = 3;

    break;
  case '3':

    if (!$_POST)
      die('No data sent!');

    $sHTML = $oSmarty->fetch('showStep3.tpl');
    $iNextStep = 4;

    # We create the tables
    $sUrl = "sql/tables.sql";
    if (file_exists($sUrl)) {
      $oFo = fopen($sUrl, 'r');
      $sData = fread($oFo, filesize($sUrl));

      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
                    PDO::ATTR_PERSISTENT => true
                ));

        $oQuery = $oDb->query($sData);
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }
    }

    $_SESSION['install'] = $_POST;
    $sPassword = md5(RANDOM_HASH . $_SESSION['install']['password']);

    try {
      $oQuery = $oDb->prepare(" INSERT INTO
                                  " . SQL_PREFIX . "users ( id, name, surname, password, email, ip, user_right, date )
                                VALUES
                                  ( :id, :name, :surname, :password, :email, :ip, :user_right, :date )");

      $iId = 1;
      $iUserRight = 4;
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $oQuery->bindParam('name', $_SESSION['install']['name']);
      $oQuery->bindParam('surname', $_SESSION['install']['surname']);
      $oQuery->bindParam('password', $sPassword);
      $oQuery->bindParam('email', $_SESSION['install']['email']);
      $oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR']);
      $oQuery->bindParam('user_right', $iUserRight, PDO::PARAM_INT);
      $oQuery->bindParam('date', time());
      $bResult = $oQuery->execute();

      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    # Create sample content if wanted
    if (isset($_POST['create_content'])) {
      $sUrl = "sql/data.sql";
      if (file_exists($sUrl)) {
        $oFo = fopen($sUrl, 'r');
        $sData = fread($oFo, filesize($sUrl));
        try {
          $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
          $oQuery = $oDb->query($sData);
          $oDb = null;
        }
        catch (AdvancedException $e) {
          $oDb->rollBack();
        }
      }
    }

    break;
  case '4':

    header('Location:../Session/create');

    break;
}
?>
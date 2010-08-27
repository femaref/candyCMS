<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

final class Helper {

  # Check for plugins
  public static final function pluginExists($sPluginName = '') {
    if (!empty($sPluginName) && !class_exists(ucfirst($sPluginName))) {
      if (file_exists('plugins/' . (string) ucfirst($sPluginName) . '.class.php')) {
        require_once 'plugins/' . (string) ucfirst($sPluginName) . '.class.php';
        return true;
      }
      else
        return false;
    }
    elseif (class_exists(ucfirst($sPluginName)))
      return true;
    else
      return false;
  }

  public static final function successMessage($sMSG) {
    $_SESSION['flash_message']['type']      = 'success';
    $_SESSION['flash_message']['message']   = $sMSG;
    $_SESSION['flash_message']['headline']  = '';
  }

  public static final function errorMessage($sMSG = '', $sHL = '') {
    if(empty($sHL))
      $sHL = LANG_ERROR_GLOBAL;

    if(empty($sMSG))
      $sMSG = LANG_ERROR_GLOBAL;

    $_SESSION['flash_message']['type']      = 'error';
    $_SESSION['flash_message']['message']   = $sMSG;
    $_SESSION['flash_message']['headline']  = $sHL;
  }

  public static final function debugMessage($sMSG) {
    $_SESSION['flash_message']['type']      = 'debug';
    $_SESSION['flash_message']['message']   = $sMSG;
    $_SESSION['flash_message']['headline']  = '';
  }

  public static final function redirectTo($sURL) {
    header('Location:' . WEBSITE_URL . $sURL);
    die();
  }

  public static final function checkEmailAddress($sMail) {
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $sMail))
      return true;
    else
      return false;
  }

  public final static function createRandomChar($iLength, $bIntegerOnly = false) {
    $sChars = 'ABCDEFGHJKLMNOPQRSTUVWXYZabcedfghijkmnopqrstuvwxyz123456789';

    if ($bIntegerOnly == true)
      $sChars = '0123456789';

    srand(microtime() * 1000000);

    $sString = '';
    for ($iI = 1; $iI <= $iLength; $iI++) {
      $iTemp = rand(1, strlen($sChars));
      $iTemp--;
      $sString .= $sChars[$iTemp];
    }

    return $sString;
  }

	public final static function createLinkTo($sUrl, $bExtern = false) {
		if($bExtern == false)
			return '<a href=\'' . WEBSITE_URL . $sUrl . '\'>' . WEBSITE_URL . $sUrl . '</a>';
		else
			return '<a href=\'' . $sUrl . '\'>' . $sUrl . '</a>';
  }

  public final static function getAvatar($sPath, $iSize, $iUserId, $aGravatar = '') {
    if (!empty($aGravatar) && $aGravatar['use_gravatar'] == true) {
      $sMail = md5($aGravatar['email']);
      return 'http://www.gravatar.com/avatar/' . $sMail . '.jpg?s=' . $iSize;
    }
    else {
      $sFile = PATH_UPLOAD . '/' . $sPath . '/' . $iSize . '/' . $iUserId . '.jpg';
      if (is_file($sFile))
        return WEBSITE_URL . '/' . $sFile;
      else
        return WEBSITE_CDN . '/public/images/missing_avatar.jpg';
    }
  }

  public final static function getFileSize($sPath) {
    $iSize = filesize($sPath);

    if ($iSize > 1024 && $iSize < 1048576)
      $sReturn = round(($iSize / 1024), 2) . ' KB';

    elseif ($iSize >= 1048576 && $iSize < 1073741824)
      $sReturn = round(($iSize / 1048576), 2) . ' MB';

    elseif ($iSize >= 1073741824)
      $sReturn = round(($iSize / 1073741824), 2) . ' GB';

    else
      $sReturn = round($iSize, 2) . ' Byte';

    return $sReturn;
  }

  public final function getTemplateDir($sTemplate) {
    try {
      if (file_exists('public/skins/' . PATH_TPL . '/view/' . $sTemplate . '.tpl'))
        return 'public/skins/' . PATH_TPL . '/view/';
      elseif (file_exists('public/skins/_addon/view/' . $sTemplate . '.tpl'))
        return 'public/skins/_addon/view/';
      else {
        if (!file_exists('app/view/' . $sTemplate . '.tpl'))
          throw new AdvancedException(LANG_ERROR_GLOBAL_NO_TEMPLATE);
        else
          return 'app/view/';
      }
    }
    catch (Exception $e) {
      $e->getMessage();
    }
  }

  public final static function removeSlahes($sStr) {
    $sStr = str_replace('\&quot;', '"', $sStr);
    $sStr = str_replace('\"', '"', $sStr);
    $sStr = str_replace("\'", "'", $sStr);
    return $sStr;
  }

  public final static function formatInput($sStr, $bDisableHTML = true) {
    try {
      if (is_string($sStr) == false && is_int($sStr) == false && $bDisableHTML == true)
        throw new Exception('Input seems not valid.');
      else
        $sStr = addslashes($sStr);

      if ($bDisableHTML == true)
        $sStr = htmlspecialchars($sStr);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
      die();
    }

    return $sStr;
  }

  # Code for plugins
  # TODO: Use config for variables
  public static final function formatTimestamp($iTime) {
    if (Helper::pluginExists('FormatTimestamp') == true) {
      $oDate = new FormatTimestamp();
      return $oDate->getDate($iTime);
    }
    else
      return date('d.m.Y - H:i', $iTime);
  }

  public static final function formatOutput($sStr, $bUseParagraph = false) {
    $sStr = trim($sStr);
    $sStr = preg_replace('/\S{500}/', '\0 ', $sStr);

    # Remove Slashes
    $sStr = str_replace('\&quot;', '"', $sStr);
    $sStr = str_replace('\"', '"', $sStr);
    $sStr = str_replace("\'", "'", $sStr);

    # Format SpecialChars
    $sStr = str_replace('&quot;', '"', $sStr);

    if (Helper::pluginExists('Bbcode') == true) {
      $oBbcode = new Bbcode();
      return $oBbcode->getFormatedText($sStr, $bUseParagraph);
    }
    else
      return $sStr;
  }
}
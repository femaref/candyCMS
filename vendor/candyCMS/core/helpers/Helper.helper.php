<?php

/**
 * Provide many helper methods.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Core\Helpers;

use CandyCMS\Core\Controllers\Main;
use CandyCMS\Core\Helpers\AdvancedException;
use CandyCMS\Plugins\Bbcode;
use CandyCMS\Plugins\FormatTimestamp;
use PDO;

class Helper {

  /**
   * Display a success message after an action is done.
   *
   * @static
   * @access public
   * @param string $sMessage message to provide
   * @param string $sRedirectTo site to redirect to
   * @return boolean true
   * @todo store in main session object
   *
   */
  public static function successMessage($sMessage, $sRedirectTo = '') {
    $_SESSION['flash_message'] = array(
        'type'    => 'success',
        'message' => $sMessage,
        'headline'=> '');

    return $sRedirectTo ? Helper::redirectTo ($sRedirectTo) : true;
  }

  /**
   * Display an error message after an action is done.
   *
   * @static
   * @access public
   * @param string $sMessage message to provide
   * @param string $sRedirectTo site to redirect to
   * @return boolean false
   * @todo store in main session object
   *
   */
  public static function errorMessage($sMessage, $sRedirectTo = '') {
    $_SESSION['flash_message'] = array(
        'type'    => 'error',
        'message' => $sMessage,
        'headline'=> I18n::get('error.standard'));

    return $sRedirectTo ? Helper::redirectTo ($sRedirectTo) : false;
  }

  /**
   * Redirect user to a specified page.
   *
   * @static
   * @access public
   * @param string $sUrl URL to redirect the user to
   *
   */
  public static function redirectTo($sUrl) {
    if ($sUrl == '/errors/404') {
      header('Status: 404 Not Found');
      header('HTTP/1.0 404 Not Found');

      if (CRAWLER == false)
        exit(header('Location:' . $sUrl));
    }
    else
      exit(header('Location:' . $sUrl));
  }

  /**
   * Check if the provided email address is in a correct format.
   *
   * @static
   * @access public
   * @param string $sMail email address to check
   * @return boolean
   *
   */
  public static function checkEmailAddress($sMail) {
    return preg_match("/^([a-zA-Z0-9])+(\.?[a-zA-Z0-9_-]+)*@([a-zA-Z0-9_-]+\.)+[a-zA-Z]{2,6}$/", $sMail);
  }

  /**
   * Create a random charset.
   *
   * @static
   * @access public
   * @param integer $iLength length of the charset
   * @param boolean $bSpeakable charset is speakable by humans (every second char is a vocal)
   * @return string $sString created random charset
   *
   */
  public static function createRandomChar($iLength, $bSpeakable = false) {
    $sChars   = 'BCDFGHJKLMNPQRSTVWXZbcdfghjkmnpqrstvwxz';
    $sVocals  = 'AaEeiOoUuYy';
    $sNumbers = '123456789';

    $sString = '';

    if ($bSpeakable === false) {
      $sChars .= $sVocals . $sNumbers;
      for ($iI = 1; $iI <= $iLength; $iI++) {
        $iTemp = rand(0, strlen($sChars) - 1);
        $sString .= $sChars[$iTemp];
      }
    }
    else {
      $iI = 1;

      while ($iI < $iLength) {
        if ($iI % 5 == 0) {
          $sString .= $sNumbers[rand(0, strlen($sNumbers) - 1)];
          $iI++;
        }
        else {
          # Vocal
          $sString .= $sChars[rand(0, strlen($sChars) - 1)];

          # If we have more chars to put, use a vocal, otherwise use numbers to fill the string
          if ($iI < $iLength - 1)
            $sString .= $sVocals[rand(0, strlen($sVocals) - 1)];

          elseif ($iI < $iLength)
            $sString .= $sNumbers[rand(0, strlen($sNumbers) - 1)];

          else
            $iI--;

          $iI += 2;
        }
      }
    }

    return $sString;
  }

  /**
   * Create a simple link with provided params.
   *
   * @static
   * @access public
   * @param string $sUrl URL to create a link with
   * @param boolean $bExternal display a link to an external / absolute URL?
   * @return string HTML code with anchor
   *
   */
  public static function createLinkTo($sUrl, $bExternal = false) {
    return  $bExternal === true ?
            '<a href="' . $sUrl . '" rel="external">' . $sUrl . '</a>' :
            '<a href="' . WEBSITE_URL . '/' . $sUrl . '">' . WEBSITE_URL . '/' . $sUrl . '</a>';
  }

  /**
   * Return the URL of the user avatar.
   *
   * @static
   * @access public
   * @param integer $iSize avatar size
   * @param integer $iUserId user ID
   * @param string $sEmail email address to search gravatar for
   * @param boolean $bUseGravatar do we want to use gravatar?
   * @return string URL of the avatar
   *
   */
  public static function getAvatar($iSize, $iUserId, $sEmail = '', $bUseGravatar = false) {
    $sFilePath = Helper::removeSlash(PATH_UPLOAD . '/users/' . $iSize . '/' . $iUserId);

    if ($bUseGravatar == false && file_exists($sFilePath . '.jpg'))
      return '/' . $sFilePath . '.jpg';

    elseif ($bUseGravatar == false && file_exists($sFilePath . '.png'))
      return '/' . $sFilePath . '.png';

    elseif ($bUseGravatar == false && file_exists($sFilePath . '.gif'))
      return '/' . $sFilePath . '.gif';

    else {
      if (!is_int($iSize))
        $iSize = POPUP_DEFAULT_X;

      return 'http://www.gravatar.com/avatar/' . md5($sEmail) . '.jpg?s=' . $iSize . '&d=mm';
    }
  }

  /**
   * Add the avatar_* entries to $aData
   *
   * @static
   * @access public
   * @param array $aData array of user
   * @param integer $iUserId user ID
   * @param string $sEmail email address to search gravatar for
   * @param boolean $bUseGravatar do we want to use gravatar?
   * @param string $sPrefix optional prefix to prepend to keys
   * @return array $aData with all avatarURLs added
   *
   */
  public static function createAvatarURLs(&$aData, $iUserId, $sEmail, $bUseGravatar = false, $sPrefix = '') {
    $aData[$sPrefix . 'avatar_32']    = Helper::getAvatar(32, $iUserId, $sEmail, $bUseGravatar);
    $aData[$sPrefix . 'avatar_64']    = Helper::getAvatar(64, $iUserId, $sEmail, $bUseGravatar);
    $aData[$sPrefix . 'avatar_100']   = Helper::getAvatar(100, $iUserId, $sEmail, $bUseGravatar);
    $aData[$sPrefix . 'avatar_popup'] = Helper::getAvatar('popup', $iUserId, $sEmail, $bUseGravatar);

    return $aData;
  }

  /**
   * Count the file size.
   *
   * @static
   * @access public
   * @param string $sPath path of the file
   * @return string size of the file plus hardcoded ending
   *
   */
  public static function getFileSize($sPath) {
    $iSize = @filesize(Helper::removeSlash($sPath));

    if ($iSize > 1024 && $iSize < 1048576)
      return round(($iSize / 1024), 2) . ' KB';

    elseif ($iSize >= 1048576 && $iSize < 1073741824)
      return round(($iSize / 1048576), 2) . ' MB';

    elseif ($iSize >= 1073741824)
      return round(($iSize / 1073741824), 2) . ' GB';

    else
      return round($iSize, 2) . ' Byte';
  }

  /**
   * Get the template dir. Check if there are extension files and use them if available.
   *
   * @static
   * @access public
   * @param string $sFolder dir of the templates
   * @param string $sFile file name of the template
   * @return string path of the chosen template
   *
   */
  public static function getTemplateDir($sFolder, $sFile) {
    try {
      # Extensions
      if (EXTENSION_CHECK && file_exists(PATH_STANDARD . '/app/extensions/views/' . $sFolder . '/' . $sFile . '.tpl'))
        return PATH_STANDARD . '/app/extensions/views/' . $sFolder;

      # Template use
      elseif (file_exists(PATH_STANDARD . '/public/templates/' . PATH_TEMPLATE . '/views/' . $sFolder . '/' . $sFile . '.tpl'))
        return PATH_STANDARD . '/public/templates/' . PATH_TEMPLATE . '/views/' . $sFolder;

      # Standard views
      else {
        if (!file_exists(PATH_STANDARD . '/vendor/candyCMS/core/views/' . $sFolder . '/' . $sFile . '.tpl'))
          throw new AdvancedException('This template does not exist: ' . $sFolder . '/' . $sFile . '.tpl');

        else
          return PATH_STANDARD . '/vendor/candyCMS/core/views/' . $sFolder;
      }
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth($e->getMessage());
    }
  }

  /**
   * Get the template type. Check if mobile template is available and return standard if not.
   *
   * @static
   * @access public
   * @param string $sDir dir of the templates
   * @param string $sFile file name of the template
   * @return string path of the chosen template
   *
   */
  public static function getTemplateType($sDir, $sFile) {
    try {
      # Mobile device.
      if (file_exists($sDir . '/' . $sFile . '.mob') && MOBILE === true)
        return $sFile . '.mob';

      # Standard template
      else
        return $sFile . '.tpl';
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth($e->getMessage());
      exit($e->getMessage());
    }
  }

  /**
   * Get the template file. Check if there is a mobile device.
   *
   * @static
   * @access public
   * @param string $sFolder dir of the templates
   * @param string $sFile file name of the template
   * @return string path of the chosen template
   *
   */
  public static function getPluginTemplateDir($sFolder, $sFile) {
    try {
      # Template
      if (file_exists(PATH_STANDARD . '/public/templates/' . PATH_TEMPLATE . '/views/' . $sFolder . '/' . $sFile . '.tpl'))
        return PATH_STANDARD . '/public/templates/' . PATH_TEMPLATE . '/views/' . $sFolder;

      # Standard views
      else {
        if (!file_exists(PATH_STANDARD . '/vendor/candyCMS/plugins/' . ucfirst($sFolder) . '/views/' . $sFile . '.tpl'))
          throw new AdvancedException('This plugin template does not exist: ' . $sFile . '.tpl');

        else
          return PATH_STANDARD . '/vendor/candyCMS/plugins/' . ucfirst($sFolder) . '/views';
      }
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth($e->getMessage());
      exit($e->getMessage());
    }
  }

  /**
   * Check the input to avoid XSS and SQL injections.
   *
   * @static
   * @access public
   * @param string $sStr string to check
   * @param boolean $bDisableHTML remove HTML code
   * @return string cleaned input
   *
   */
  public static function formatInput($sStr, $bDisableHTML = true) {
    try {
      if (!is_string($sStr) && !is_int($sStr) && $bDisableHTML === true)
        throw new AdvancedException('Input \'' . $sStr . '\' does not seem valid.');

      if ($bDisableHTML === true)
        $sStr = htmlspecialchars($sStr);
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth($e->getMessage());
      exit($e->getMessage());
    }

    # remove multiple spaces and newlines (3+)
    $sStr = preg_replace('/\s(\s)\s+/', '$1$1', trim($sStr));
    # replace all newlines
 //   $sStr = str_replace("\n", "<br />", trim($sStr));

    # Fix quotes to avoid problems with inputs
    return str_replace('"', "&quot;", $sStr);
  }

  /**
   * Format the linux timestamp into a user friendly format.
   *
   * If the "FormatTimestamp" plugin is enabled, load plugin and do some advanced work.
   *
   * Options:
   * 0 = default dates
   * 1 = date only
   * 2 = time only
   *
   * @static
   * @access public
   * @param integer $iTime timestamp
   * @param integer $iOptions options see above
   * @see vendor/candyCMS/plugins/FormatTimestamp/FormatTimestamp.controller.php
   * @return string formatted timestamp
   *
   */
  public static function formatTimestamp($iTime, $iOptions = 0) {
    if ($iTime) {
      if (class_exists('\CandyCMS\Plugins\FormatTimestamp') == true) {
        $oDate = new FormatTimestamp();
        return $oDate->getDate($iTime, $iOptions);
      }
      else {
        if ($iOptions == 1)
          return strftime(DEFAULT_DATE_FORMAT, $iTime);

        elseif($iOptions == 2)
          return strftime(DEFAULT_TIME_FORMAT, $iTime);

        else
          return strftime(DEFAULT_DATE_FORMAT . ', ' . DEFAULT_TIME_FORMAT, $iTime);
      }
    }
  }

  /**
   * Format HTML output .
   *
   * If the "Bbcode" plugin is enabled, load plugin do some advanced work.
   *
   * @static
   * @access public
   * @param mixed $mStr string to format
   * @param string $sHighlight string to highlight
   * @return string $sStr formatted string
   * @see vendor/candyCMS/core/Bbcode/Bbcode.controller.php
   *
   */
  public static function formatOutput($mStr, $sHighlight = '') {
    if ($sHighlight)
      $mStr = str_ireplace($sHighlight, '<mark>' . $sHighlight . '</mark>', $mStr);

    if (class_exists('\CandyCMS\Plugins\Bbcode') == true) {
      $oBbcode = new Bbcode();
      return $oBbcode->getFormatedText($mStr);
    }

    return $mStr;
  }

  /**
   * Fetch the last entry from database.
   *
   * @static
   * @access public
   * @param string $sTable table to fetch data from
   * @return integer latest ID
   *
   */
  public static function getLastEntry($sTable) {
    try {
      $sModel = Main::__autoload('Main', true);
      $oDb = $sModel::connectToDatabase();
      $oQuery = $oDb->query("SELECT id FROM " . SQL_PREFIX . $sTable . " ORDER BY id DESC LIMIT 1");
      $aRow = $oQuery->fetch();

      return $aRow['id'];
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth('0104 - ' . $e->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Replace non alphachars with predefined values.
   *
   * @static
   * @access public
   * @param string $sStr string to replace chars
   * @return string string with formatted chars
   *
   */
  public static function replaceNonAlphachars($sStr) {
    $sStr = str_replace('"', '', $sStr);
    $sStr = str_replace('Ä', 'Ae', $sStr);
    $sStr = str_replace('ä', 'ae', $sStr);
    $sStr = str_replace('Ü', 'Ue', $sStr);
    $sStr = str_replace('ü', 'ue', $sStr);
    $sStr = str_replace('Ö', 'Oe', $sStr);
    $sStr = str_replace('ö', 'oe', $sStr);
    $sStr = str_replace('ß', 'ss', $sStr);

    # Remove non alpha chars exept the needed dot
    $sStr = preg_replace("/[^a-zA-Z0-9\.\s]/", '', $sStr);

    # Remove spaces
    $sStr = str_replace(' ', '_', $sStr);

    return $sStr;
  }

  /**
   * Removes first slash at dirs.
   *
   * @static
   * @access public
   * @param string $sStr
   * @return string without slash
   *
   */
  public static function removeSlash($sStr) {
    return substr($sStr, 0, 1) == '/' ? substr($sStr, 1) : $sStr;
  }

  /**
   * Adds slash for dirs.
   *
   * @static
   * @access public
   * @param string $sStr
   * @return string with slash
   *
   */
  public static function addSlash($sStr) {
    return substr($sStr, 0, 1) == '/' ? $sStr : '/' . $sStr;
  }

  /**
   * Pluralize a string.
   *
   * Note that this is just a rudimentary funtion. F.e. "death", "boy" and "kiss" will not be pluralized corrctly.
   *
   * @static
   * @access public
   * @param string $sStr
   * @return string pluralized string
   *
   */
  public static function pluralize($sStr) {
    if (substr($sStr, -1) == 'h' || substr($sStr, -2) == 'ss')
      return $sStr . 'es';

    elseif (substr($sStr, -1) == 's')
      return $sStr;

    elseif (substr($sStr, -1) == 'e')
      return $sStr . 's';

    elseif (substr($sStr, -1) == 'y')
      return substr($sStr, 0, -1) . 'ies';

    else
      return $sStr;
  }

  /**
   * Singleize a string.
   *
   * Note that this is just a rudimentary funtion. F.e. "phase" and "boy" will not be pluralized corrctly.
   *
   * @static
   * @access public
   * @param string $sStr
   * @return string singleize string
   * @see vendor/candyCMS/core/controllers/Main.controller.php
   *
   */
  public static function singleize($sStr) {
    if (substr($sStr, -3) == 'ies')
      return substr($sStr, 0, -3) . 'y';

    elseif (substr($sStr, -2) == 'es')
      return substr($sStr, 0, -2);

    elseif (substr($sStr, -1) == 's' && substr($sStr, -2) !== 'ss')
      return substr($sStr, 0, -1);

    else
      return $sStr;
  }
}